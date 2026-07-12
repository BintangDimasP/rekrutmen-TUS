<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Prodi, Dosen, User, Lowongan, Pelamar, Lamaran, JadwalSeleksi, Notifikasi};
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class MoreDummyDataSeeder extends Seeder
{
    /**
     * Seed BANYAK data dummy untuk screenshot yang lebih realistis.
     */
    public function run(): void
    {
        $this->command->info('🚀 Memulai seeding DATA TAMBAHAN...');

        // Ambil prodi yang sudah ada
        $prodi1 = Prodi::where('kode', 'IF')->first();
        $prodi2 = Prodi::where('kode', 'SI')->first();
        $prodi3 = Prodi::where('kode', 'TK')->first();

        if (!$prodi1 || !$prodi2) {
            $this->command->error('❌ Prodi IF atau SI tidak ditemukan! Jalankan DatabaseSeeder terlebih dahulu.');
            return;
        }

        // ========== TAMBAH DOSEN ==========
        $this->command->info('👨‍🏫 Menambah Dosen...');
        
        $dosenData = [
            ['nama' => 'Dr. Andi Prasetyo, M.T.', 'kode' => 'APR', 'prodi' => $prodi1, 'penguji' => true],
            ['nama' => 'Rina Kusumawati, S.Kom., M.Kom.', 'kode' => 'RKW', 'prodi' => $prodi1, 'penguji' => true],
            ['nama' => 'Prof. Ir. Bambang Sutejo, Ph.D.', 'kode' => 'BSJ', 'prodi' => $prodi2, 'penguji' => true],
            ['nama' => 'Dra. Linda Wijaya, M.Si.', 'kode' => 'LWY', 'prodi' => $prodi2, 'penguji' => true],
        ];

        $dosenList = [];
        foreach ($dosenData as $idx => $data) {
            $dosen = Dosen::firstOrCreate(
                ['kode' => $data['kode']],
                [
                    'nama' => $data['nama'],
                    'prodi_id' => $data['prodi']->id,
                    'nip' => '1988' . str_pad($idx + 10, 2, '0', STR_PAD_LEFT) . '012022011' . str_pad($idx + 10, 3, '0', STR_PAD_LEFT),
                    'nidn' => '0' . str_pad($idx + 10, 2, '0', STR_PAD_LEFT) . '01' . (1988 + $idx) . '03',
                    'email' => strtolower(str_replace([' ', '.', ','], '', explode(',', $data['nama'])[0])) . '@pengajar.telkomuniversity.ac.id',
                    'no_telepon' => '0812' . rand(10000000, 99999999),
                    'is_penguji' => $data['penguji'],
                    'is_kaprodi' => false,
                ]
            );
            $dosenList[] = $dosen;

            // Buat user untuk dosen penguji
            if ($data['penguji'] && !$dosen->user) {
                User::create([
                    'name' => $dosen->nama,
                    'email' => $dosen->email,
                    'password' => Hash::make('password'),
                    'role' => 'penguji',
                    'is_penguji' => true,
                    'is_kaprodi' => false,
                    'prodi_id' => $dosen->prodi_id,
                    'dosen_id' => $dosen->id,
                ]);
            }
        }

        // ========== TAMBAH LOWONGAN ==========
        $this->command->info('📢 Menambah Lowongan...');
        
        $lowonganData = [
            [
                'prodi' => $prodi1,
                'nama_posisi' => 'Dosen Tetap Cloud Computing',
                'minimal_ipk' => 3.60,
                'prodi_prioritas' => 'Teknik Informatika, Sistem Informasi',
                'skill' => 'AWS, Azure, Docker, Kubernetes, DevOps',
                'kuota' => 2,
                'deskripsi' => 'Membuka kesempatan untuk dosen tetap yang menguasai teknologi cloud computing dan containerization untuk mengajar mata kuliah Cloud Computing dan DevOps.',
            ],
            [
                'prodi' => $prodi1,
                'nama_posisi' => 'Dosen Kontrak Artificial Intelligence',
                'minimal_ipk' => 3.70,
                'prodi_prioritas' => 'Teknik Informatika, Ilmu Komputer',
                'skill' => 'Machine Learning, Deep Learning, Python, TensorFlow, PyTorch',
                'kuota' => 2,
                'deskripsi' => 'Mencari dosen kontrak dengan keahlian di bidang Artificial Intelligence dan Machine Learning untuk mengajar mata kuliah AI dan Deep Learning.',
            ],
            [
                'prodi' => $prodi2,
                'nama_posisi' => 'Dosen Tetap Business Intelligence',
                'minimal_ipk' => 3.40,
                'prodi_prioritas' => 'Sistem Informasi, Manajemen Informatika',
                'skill' => 'Data Analytics, Power BI, Tableau, SQL, Data Warehouse',
                'kuota' => 2,
                'deskripsi' => 'Membuka lowongan dosen tetap untuk mengajar Business Intelligence, Data Analytics, dan Data Visualization.',
            ],
            [
                'prodi' => $prodi2,
                'nama_posisi' => 'Dosen Kontrak Enterprise System',
                'minimal_ipk' => 3.30,
                'prodi_prioritas' => 'Sistem Informasi',
                'skill' => 'ERP, SAP, Oracle, Manajemen Proyek SI',
                'kuota' => 1,
                'deskripsi' => 'Membutuhkan dosen kontrak untuk mata kuliah Enterprise Resource Planning dan Sistem Informasi Perusahaan.',
            ],
            [
                'prodi' => $prodi1,
                'nama_posisi' => 'Dosen Tetap Cybersecurity',
                'minimal_ipk' => 3.65,
                'prodi_prioritas' => 'Teknik Informatika, Keamanan Informasi',
                'skill' => 'Network Security, Ethical Hacking, Cryptography, Security Audit',
                'kuota' => 2,
                'deskripsi' => 'Posisi dosen tetap untuk mengajar mata kuliah Keamanan Jaringan, Ethical Hacking, dan Cryptography.',
            ],
        ];

        $lowonganList = [];
        foreach ($lowonganData as $data) {
            $lowongan = Lowongan::create([
                'prodi_id' => $data['prodi']->id,
                'nama_posisi' => $data['nama_posisi'],
                'jenjang_minimal' => 'S2',
                'minimal_ipk' => $data['minimal_ipk'],
                'prodi_prioritas' => $data['prodi_prioritas'],
                'skill_dibutuhkan' => $data['skill'],
                'kuota' => $data['kuota'],
                'tanggal_tutup' => now()->addDays(rand(20, 60)),
                'deskripsi' => $data['deskripsi'],
                'status' => 'aktif',
            ]);
            $lowonganList[] = $lowongan;
        }

        // ========== TAMBAH PELAMAR ==========
        $this->command->info('👨‍🎓 Menambah Pelamar...');
        
        $pelamarData = [
            [
                'nama' => 'Muhammad Rizki Fadillah',
                'email' => 'rizki.fadillah@example.com',
                'nik' => '3175011505920001',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1992-05-15',
                'gender' => 'L',
                'institusi' => 'Universitas Gadjah Mada',
                'prodi' => 'Teknik Informatika',
                'ipk' => 3.78,
                'no_hp' => '085234567890',
            ],
            [
                'nama' => 'Putri Ayu Lestari',
                'email' => 'putri.ayu@example.com',
                'nik' => '3201016608930002',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1993-08-26',
                'gender' => 'P',
                'institusi' => 'Institut Teknologi Sepuluh Nopember',
                'prodi' => 'Sistem Informasi',
                'ipk' => 3.85,
                'no_hp' => '081345678901',
            ],
            [
                'nama' => 'Fajar Nugroho',
                'email' => 'fajar.nugroho@example.com',
                'nik' => '3273012201910003',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1991-01-22',
                'gender' => 'L',
                'institusi' => 'Universitas Padjadjaran',
                'prodi' => 'Teknik Informatika',
                'ipk' => 3.68,
                'no_hp' => '082456789012',
            ],
            [
                'nama' => 'Anisa Rahma Sari',
                'email' => 'anisa.rahma@example.com',
                'nik' => '3578015512940004',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '1994-12-15',
                'gender' => 'P',
                'institusi' => 'Universitas Airlangga',
                'prodi' => 'Sistem Informasi',
                'ipk' => 3.72,
                'no_hp' => '083567890123',
            ],
            [
                'nama' => 'Denny Wirawan Putra',
                'email' => 'denny.wirawan@example.com',
                'nik' => '3374011003920005',
                'tempat_lahir' => 'Semarang',
                'tanggal_lahir' => '1992-03-10',
                'gender' => 'L',
                'institusi' => 'Universitas Diponegoro',
                'prodi' => 'Teknik Informatika',
                'ipk' => 3.81,
                'no_hp' => '084678901234',
            ],
            [
                'nama' => 'Maya Puspita Dewi',
                'email' => 'maya.puspita@example.com',
                'nik' => '3471015509950006',
                'tempat_lahir' => 'Yogyakarta',
                'tanggal_lahir' => '1995-09-15',
                'gender' => 'P',
                'institusi' => 'Universitas Gadjah Mada',
                'prodi' => 'Ilmu Komputer',
                'ipk' => 3.90,
                'no_hp' => '085789012345',
            ],
            [
                'nama' => 'Arif Hidayat',
                'email' => 'arif.hidayat@example.com',
                'nik' => '3216012508930007',
                'tempat_lahir' => 'Bogor',
                'tanggal_lahir' => '1993-08-25',
                'gender' => 'L',
                'institusi' => 'Institut Pertanian Bogor',
                'prodi' => 'Ilmu Komputer',
                'ipk' => 3.55,
                'no_hp' => '086890123456',
            ],
            [
                'nama' => 'Rina Melati',
                'email' => 'rina.melati@example.com',
                'nik' => '3204016702940008',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1994-02-27',
                'gender' => 'P',
                'institusi' => 'Universitas Telkom',
                'prodi' => 'Sistem Informasi',
                'ipk' => 3.76,
                'no_hp' => '087901234567',
            ],
        ];

        $pelamarList = [];
        foreach ($pelamarData as $data) {
            // Skip jika email sudah ada
            if (User::where('email', $data['email'])->exists()) {
                $this->command->warn('   ⚠️ Email ' . $data['email'] . ' sudah ada, skip...');
                continue;
            }

            // Buat user
            $user = User::create([
                'name' => $data['nama'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
                'role' => 'pelamar',
                'email_verified_at' => now(),
            ]);

            // Buat pelamar
            $pelamar = Pelamar::create([
                'user_id' => $user->id,
                'nik' => $data['nik'],
                'nama' => $data['nama'],
                'tempat_lahir' => $data['tempat_lahir'],
                'tanggal_lahir' => $data['tanggal_lahir'],
                'jenis_kelamin' => $data['gender'],
                'kewarganegaraan' => 'Indonesia',
                'status_pernikahan' => rand(0, 1) ? 'Belum Menikah' : 'Menikah',
                'no_telepon' => $data['no_hp'],
                'phone_verified_at' => now(),
                'alamat_domisili' => 'Jl. Contoh No. ' . rand(1, 100) . ', ' . $data['tempat_lahir'],
                'alamat_ktp' => 'Jl. Contoh No. ' . rand(1, 100) . ', ' . $data['tempat_lahir'],
                
                // Pendidikan S2
                'jenjang' => 'S2',
                'institusi' => $data['institusi'],
                'prodi_pendidikan' => $data['prodi'],
                'akreditas' => 'A',
                'no_ijazah' => strtoupper(substr($data['institusi'], 0, 3)) . '/S2/2021/' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                'ipk' => $data['ipk'],
                'file_ijazah' => 'ijazah/dummy_s2.pdf',
                'file_transkrip' => 'transkrip/dummy_s2.pdf',
                
                // Pendidikan S1
                'jenjang_2' => 'S1',
                'institusi_2' => $data['institusi'],
                'prodi_pendidikan_2' => $data['prodi'],
                'akreditas_2' => 'A',
                'no_ijazah_2' => strtoupper(substr($data['institusi'], 0, 3)) . '/S1/2016/' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                'ipk_2' => round($data['ipk'] - rand(5, 15) / 100, 2),
                'file_ijazah_2' => 'ijazah/dummy_s1.pdf',
                'file_transkrip_2' => 'transkrip/dummy_s1.pdf',
                
                // Dokumen
                'file_cv' => 'cv/' . strtolower(str_replace(' ', '_', $data['nama'])) . '.pdf',
                'file_pas_foto' => 'foto/' . strtolower(str_replace(' ', '_', $data['nama'])) . '.jpg',
                'file_ktp' => 'ktp/' . strtolower(str_replace(' ', '_', $data['nama'])) . '.pdf',
                
                // Sertifikat (50% punya)
                'kategori_sertifikat' => rand(0, 1) ? 'kompetensi' : null,
                'file_sertifikat' => rand(0, 1) ? 'sertifikat/dummy_cert.pdf' : null,
                
                // Bahasa Inggris (80% punya)
                'jenis_tes_bahasa' => rand(0, 9) < 8 ? ['PBT', 'TOEFL_ITP', 'EPrT', 'IBT', 'IELTS'][rand(0, 4)] : null,
                'skor_bahasa' => rand(0, 9) < 8 ? rand(500, 600) : null,
                'tanggal_tes_bahasa' => rand(0, 9) < 8 ? now()->subMonths(rand(1, 12)) : null,
                'file_sertifikat_bahasa' => rand(0, 9) < 8 ? 'sertifikat/toefl_ielts.pdf' : null,
            ]);

            $pelamarList[] = $pelamar;
        }

        // ========== BUAT LAMARAN dengan BERBAGAI STATUS ==========
        $this->command->info('📝 Membuat Lamaran dengan berbagai status...');
        
        $statusList = ['menunggu', 'seleksi_tahap1', 'seleksi_tahap2', 'diterima', 'ditolak'];
        $lamaranList = [];

        foreach ($pelamarList as $idx => $pelamar) {
            // Setiap pelamar melamar ke 1-3 lowongan
            $jumlahLamaran = rand(1, 3);
            $lowonganSample = $lowonganList;
            shuffle($lowonganSample);

            for ($i = 0; $i < min($jumlahLamaran, count($lowonganSample)); $i++) {
                $status = $statusList[array_rand($statusList)];
                
                $lamaran = Lamaran::create([
                    'pelamar_id' => $pelamar->id,
                    'lowongan_id' => $lowonganSample[$i]->id,
                    'file_surat_lamaran' => 'surat_lamaran/' . strtolower(str_replace(' ', '_', $pelamar->nama)) . '_' . $i . '.pdf',
                    'status' => $status,
                    'tanggal_wawancara' => in_array($status, ['seleksi_tahap2', 'diterima']) ? now()->addDays(rand(5, 20)) : null,
                    'link_zoom' => in_array($status, ['seleksi_tahap2']) ? 'https://zoom.us/j/' . rand(100000000, 999999999) : null,
                    'catatan_admin' => $status === 'ditolak' ? 'Tidak memenuhi kualifikasi.' : null,
                ]);

                $lamaranList[] = $lamaran;

                // Buat jadwal seleksi untuk lamaran tahap 2 (30% chance)
                if ($status === 'seleksi_tahap2' && rand(0, 9) < 3) {
                    JadwalSeleksi::create([
                        'lamaran_id' => $lamaran->id,
                        'tanggal' => now()->addDays(rand(5, 15)),
                        'waktu_mulai' => '0' . rand(8, 15) . ':00:00',
                        'waktu_selesai' => '0' . rand(10, 17) . ':00:00',
                        'lokasi' => 'Ruang ' . chr(65 + rand(0, 5)) . ' Lantai ' . rand(2, 5),
                        'link_meeting' => 'https://zoom.us/j/' . rand(100000000, 999999999),
                        'catatan' => 'Harap datang 15 menit sebelum waktu yang ditentukan.',
                        'status' => 'dijadwalkan',
                    ]);
                }
            }
        }

        // ========== BUAT NOTIFIKASI ==========
        $this->command->info('🔔 Membuat Notifikasi...');
        
        $notifTemplates = [
            ['judul' => 'Lamaran Anda Diterima', 'pesan' => 'Selamat! Lamaran Anda telah kami terima dan sedang dalam proses review.', 'tipe' => 'info'],
            ['judul' => 'Lolos Seleksi Berkas', 'pesan' => 'Selamat! Anda lolos seleksi berkas. Silakan tunggu informasi selanjutnya.', 'tipe' => 'status'],
            ['judul' => 'Jadwal Wawancara', 'pesan' => 'Anda telah dijadwalkan untuk mengikuti wawancara. Silakan cek detail jadwal Anda.', 'tipe' => 'status'],
            ['judul' => 'Update Status Lamaran', 'pesan' => 'Status lamaran Anda telah diperbarui. Silakan cek dashboard Anda.', 'tipe' => 'info'],
            ['judul' => 'Pengumuman Hasil', 'pesan' => 'Hasil seleksi telah diumumkan. Silakan cek status lamaran Anda.', 'tipe' => 'status'],
        ];

        foreach ($pelamarList as $pelamar) {
            $jumlahNotif = rand(1, 3);
            for ($i = 0; $i < $jumlahNotif; $i++) {
                $template = $notifTemplates[array_rand($notifTemplates)];
                Notifikasi::create([
                    'user_id' => $pelamar->user_id,
                    'judul' => $template['judul'],
                    'pesan' => $template['pesan'],
                    'tipe' => $template['tipe'],
                    'dibaca' => rand(0, 9) < 5, // 50% sudah dibaca
                ]);
            }
        }

        // ========== RINGKASAN ==========
        $this->command->info('');
        $this->command->info('✅ Seeding data TAMBAHAN selesai!');
        $this->command->info('');
        $this->command->info('📊 Data Baru yang Ditambahkan:');
        $this->command->info('   - Dosen: ' . count($dosenData));
        $this->command->info('   - Lowongan: ' . count($lowonganData));
        $this->command->info('   - Pelamar: ' . count($pelamarData));
        $this->command->info('   - Lamaran: ' . count($lamaranList));
        $this->command->info('   - Notifikasi: ~' . (count($pelamarData) * 2));
        $this->command->info('');
        $this->command->info('📊 Total Data Sekarang:');
        $this->command->info('   - Prodi: ' . Prodi::count());
        $this->command->info('   - Dosen: ' . Dosen::count());
        $this->command->info('   - Lowongan: ' . Lowongan::count() . ' (' . Lowongan::where('status', 'aktif')->count() . ' aktif)');
        $this->command->info('   - Pelamar: ' . Pelamar::count());
        $this->command->info('   - Lamaran: ' . Lamaran::count());
        $this->command->info('   - Jadwal Seleksi: ' . JadwalSeleksi::count());
        $this->command->info('   - Notifikasi: ' . Notifikasi::count());
        $this->command->info('');
        $this->command->info('🔑 Semua password: password');
        $this->command->info('📖 Jalankan: php check-dummy-accounts.php untuk melihat semua akun');
    }
}
