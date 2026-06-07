<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lowongan;
use App\Models\Notifikasi;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LowonganController extends Controller
{
    private const DEFAULT_DESKRIPSI = "Dokumen yang perlu dipersiapkan :
- Pas Photo Formal Berwarna Berlatar Abu-Abu
- Scan KTP
- Surat Lamaran dan Curiculum Vitae/Resume/Riwayat Hidup
- Sertifikat Kemampuan Bahasa Inggris (PBT/TOEFL/EPrT/CBT/IBT/IELST/AcEPT)
- Scan Ijazah dan Transkrip lengkap, dan SK Penyetaraan bagi lulusan Luar Negeri 
(dapat mendafarkan melalui link: piln.kemdikbud.go.id)
- Sertifikat Kompetensi/Keahlian Khusus
- Contoh karya ilmiah yang relevan dan telah dipublikasikan
- Surat Pernyataan bersedia untuk mengurus Surat Pemberhentian apabila bekerja di Instansi Lain
(Format pada link: tel-u.ac.id/suratpernyataanpemberhentian)

Dokumen tambahan bagi pelamar yang sudah memiliki homebase:
- SK Jabatan Akademik Dosen (JAD) (apabila ada)
- SK Penetapan Angka Kredit (PAK) (apabila ada)
- Bukti Registrasi Dosen
- SK Penyetaraan Pangkat/Inpassing (apabila ada)
- Sertifikat Pendidik (apabila ada)
- Surat Keterangan Pemberhentian Pembayaran / SKPP Serdos (saat pemberkasan)
- Surat Pernyataan bersedia untuk mengurus Surat Lolos Butuh
(Format pada link: bit.ly/Surat-Pernyataan-Lolos-Butuh)";

    /** Daftar lowongan */
    public function index()
    {
        $lowongans = Lowongan::with('prodi')->latest()->paginate(10);
        $prodis    = Prodi::orderBy('nama')->get();
        return view('admin.lowongan.index', compact('lowongans', 'prodis'));
    }

    /** Form buat lowongan baru */
    public function create()
    {
        $prodis            = Prodi::orderBy('nama')->get();
        $defaultDeskripsi  = self::DEFAULT_DESKRIPSI;

        $prodiPrioritasOptions = [
            'Sistem Informasi', 'Teknik Informatika', 'Teknik Elektro',
            'Teknik Industri', 'Manajemen', 'Akuntansi', 'Desain Komunikasi Visual',
            'Ilmu Komunikasi', 'Administrasi Bisnis', 'Teknik Telekomunikasi',
        ];

        $skillOptions = [
            'IoT (Internet of Things)', 'Machine Learning', 'Deep Learning',
            'Data Science', 'Cloud Computing', 'Cybersecurity', 'Blockchain',
            'Mobile Development', 'Web Development', 'Embedded Systems',
            'Computer Networks', 'Artificial Intelligence', 'Robotics',
            'Business Intelligence', 'UI/UX Design',
        ];

        return view('admin.lowongan.create', compact(
            'prodis', 'defaultDeskripsi', 'prodiPrioritasOptions', 'skillOptions'
        ));
    }

    /** Simpan lowongan baru */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_posisi'      => 'required|string|max:255',
            'prodi_id'         => 'required|exists:prodis,id',
            'jenjang_minimal'  => 'required|in:D3,S1,S2,S3',
            'minimal_ipk'      => 'required|numeric|min:0|max:4',
            'prodi_prioritas'  => 'nullable',
            'skill_dibutuhkan' => 'nullable',
            'kuota'            => 'required|integer|min:1',
            'tanggal_tutup'    => 'required|date|after:today',
            'deskripsi'        => 'nullable|string',
            'status'           => 'required|in:aktif,ditutup,draft',
        ]);

        // Prodi prioritas: nilai dipisah '||' dari multi-select → simpan dengan ', '
        $prodiPrioritasRaw = $request->input('prodi_prioritas');
        if (is_string($prodiPrioritasRaw) && $prodiPrioritasRaw !== '') {
            $arr = array_filter(array_map('trim', explode('||', $prodiPrioritasRaw)));
            $validated['prodi_prioritas'] = !empty($arr) ? implode(', ', $arr) : null;
        } else {
            $validated['prodi_prioritas'] = null;
        }

        // Skill dibutuhkan: sama, multi-select dipisah '||'
        $skillRaw = $request->input('skill_dibutuhkan');
        if (is_string($skillRaw) && $skillRaw !== '') {
            $arr = array_filter(array_map('trim', explode('||', $skillRaw)));
            $validated['skill_dibutuhkan'] = !empty($arr) ? implode(', ', $arr) : null;
        } else {
            $validated['skill_dibutuhkan'] = null;
        }

        $lowongan = Lowongan::create($validated);
        $lowongan->load('prodi');

        $adminNama = auth()->user()->name ?? 'Admin';
        $prodiNama = $lowongan->prodi->nama ?? '-';
        $waktu = now()->translatedFormat('d F Y \p\u\k\u\l H:i');
        \App\Models\User::where('role', 'admin')->each(function($u) use ($adminNama, $lowongan, $prodiNama, $waktu) {
            \App\Models\Notifikasi::kirimSistem(
                $u->id,
                'Lowongan Dibuat',
                "Admin {$adminNama} membuat lowongan {$lowongan->nama_posisi} pada prodi {$prodiNama} pada {$waktu}."
            );
        });

        return redirect()->route('admin.lowongan.index')
                         ->with('success', 'Lowongan "' . $validated['nama_posisi'] . '" berhasil dibuat.');
    }

    /** Detail lowongan — redirect ke daftar lamaran */
    public function show(Lowongan $lowongan)
    {
        return redirect()->route('admin.lamaran.index', $lowongan);
    }

    /** Form edit lowongan */
    public function edit(Lowongan $lowongan)
    {
        $prodis = Prodi::orderBy('nama')->get();

        $prodiPrioritasOptions = [
            'Sistem Informasi', 'Teknik Informatika', 'Teknik Elektro',
            'Teknik Industri', 'Manajemen', 'Akuntansi', 'Desain Komunikasi Visual',
            'Ilmu Komunikasi', 'Administrasi Bisnis', 'Teknik Telekomunikasi',
        ];

        $skillOptions = [
            'IoT (Internet of Things)', 'Machine Learning', 'Deep Learning',
            'Data Science', 'Cloud Computing', 'Cybersecurity', 'Blockchain',
            'Mobile Development', 'Web Development', 'Embedded Systems',
            'Computer Networks', 'Artificial Intelligence', 'Robotics',
            'Business Intelligence', 'UI/UX Design',
        ];

        return view('admin.lowongan.edit', compact('lowongan', 'prodis', 'prodiPrioritasOptions', 'skillOptions'));
    }

    /** Update lowongan */
    public function update(Request $request, Lowongan $lowongan)
    {
        $validated = $request->validate([
            'nama_posisi'      => 'required|string|max:255',
            'prodi_id'         => 'required|exists:prodis,id',
            'jenjang_minimal'  => 'required|in:D3,S1,S2,S3',
            'minimal_ipk'      => 'required|numeric|min:0|max:4',
            'prodi_prioritas'  => 'nullable',
            'skill_dibutuhkan' => 'nullable',
            'kuota'            => 'required|integer|min:1',
            'tanggal_tutup'    => 'required|date',
            'deskripsi'        => 'nullable|string',
            'status'           => 'required|in:aktif,ditutup,draft',
        ]);

        // Normalisasi multi-select (dipisah '||') → simpan dengan ', '
        foreach (['prodi_prioritas', 'skill_dibutuhkan'] as $field) {
            $raw = $request->input($field);
            if (is_string($raw) && $raw !== '') {
                $arr = array_filter(array_map('trim', explode('||', $raw)));
                $validated[$field] = !empty($arr) ? implode(', ', $arr) : null;
            } else {
                $validated[$field] = null;
            }
        }

        $lowongan->update($validated);

        $lowongan->load('prodi');
        $adminNama = auth()->user()->name ?? 'Admin';
        $prodiNama = $lowongan->prodi->nama ?? '-';
        $waktu = now()->translatedFormat('d F Y \p\u\k\u\l H:i');
        \App\Models\User::where('role', 'admin')->each(function($u) use ($adminNama, $lowongan, $prodiNama, $waktu) {
            \App\Models\Notifikasi::kirimSistem($u->id, 'Lowongan Diperbarui', "Admin {$adminNama} memperbarui lowongan {$lowongan->nama_posisi} pada prodi {$prodiNama} pada {$waktu}.");
        });

        return redirect()->route('admin.lowongan.index')
                         ->with('success', 'Lowongan "' . $lowongan->nama_posisi . '" berhasil diperbarui.');
    }

    /** Toggle status lowongan */
    public function toggleStatus(Lowongan $lowongan)
    {
        $rawStatus = $lowongan->getRawOriginal('status');
        $newStatus = $rawStatus === 'aktif' ? 'ditutup' : 'aktif';

        if ($newStatus === 'aktif') {
            if ($lowongan->tanggal_tutup && $lowongan->tanggal_tutup->isPast()) {
                return redirect()->route('admin.lowongan.index')->with('error', 'Gagal mem-publish! Tanggal tutup lowongan sudah lewat. Silakan edit tanggal tutup terlebih dahulu.');
            }
            if ($lowongan->sisa_kuota <= 0) {
                return redirect()->route('admin.lowongan.index')->with('error', 'Gagal mem-publish! Kuota lowongan tidak mencukupi atau sudah habis.');
            }
        }

        $lowongan->update(['status' => $newStatus]);

        $adminNama = auth()->user()->name ?? 'Admin';
        $waktu = now()->translatedFormat('d F Y \p\u\k\u\l H:i');
        $statusLabel = $newStatus === 'aktif' ? 'dipublish (Aktif)' : 'ditutup';
        \App\Models\User::where('role', 'admin')->each(function($u) use ($adminNama, $lowongan, $statusLabel, $waktu) {
            \App\Models\Notifikasi::kirimSistem($u->id, 'Status Lowongan Diubah', "Admin {$adminNama} mengubah status lowongan {$lowongan->nama_posisi} menjadi {$statusLabel} pada {$waktu}.");
        });

        $message = $newStatus === 'aktif' ? 'Lowongan berhasil dipublish (Aktif).' : 'Lowongan berhasil di-unpublish (Ditutup).';
        return redirect()->route('admin.lowongan.index')->with('success', $message);
    }

    /** Hapus lowongan */
    public function destroy(Lowongan $lowongan)
    {
        $nama = $lowongan->nama_posisi;
        $lowongan->delete();

        $adminNama = auth()->user()->name ?? 'Admin';
        $waktu = now()->translatedFormat('d F Y \p\u\k\u\l H:i');
        \App\Models\User::where('role', 'admin')->each(function($u) use ($adminNama, $nama, $waktu) {
            \App\Models\Notifikasi::kirimSistem($u->id, 'Lowongan Dihapus', "Admin {$adminNama} menghapus lowongan {$nama} pada {$waktu}.");
        });

        return redirect()->route('admin.lowongan.index')
                         ->with('success', 'Lowongan "' . $nama . '" berhasil dihapus.');
    }

    /** Cetak Berita Acara Penetapan Hasil Akhir Microteaching & Wawancara */
    public function beritaAcara(Lowongan $lowongan)
    {
        $lowongan->load(['prodi', 'lamarans.pelamar']);

        // Ambil hanya pelamar yang diterima atau ditolak
        $lamaranList = $lowongan->lamarans
            ->whereIn('status', ['diterima', 'ditolak'])
            ->values();

        $now = now();

        $hariList = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu',
        ];
        $bulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $angkaList = [
            1 => 'Satu', 2 => 'Dua', 3 => 'Tiga', 4 => 'Empat', 5 => 'Lima',
            6 => 'Enam', 7 => 'Tujuh', 8 => 'Delapan', 9 => 'Sembilan', 10 => 'Sepuluh',
            11 => 'Sebelas', 12 => 'Dua Belas', 13 => 'Tiga Belas', 14 => 'Empat Belas',
            15 => 'Lima Belas', 16 => 'Enam Belas', 17 => 'Tujuh Belas', 18 => 'Delapan Belas',
            19 => 'Sembilan Belas', 20 => 'Dua Puluh', 21 => 'Dua Puluh Satu', 22 => 'Dua Puluh Dua',
            23 => 'Dua Puluh Tiga', 24 => 'Dua Puluh Empat', 25 => 'Dua Puluh Lima',
            26 => 'Dua Puluh Enam', 27 => 'Dua Puluh Tujuh', 28 => 'Dua Puluh Delapan',
            29 => 'Dua Puluh Sembilan', 30 => 'Tiga Puluh', 31 => 'Tiga Puluh Satu',
        ];
        $tahunList = [
            2020 => 'Dua Ribu Dua Puluh', 2021 => 'Dua Ribu Dua Puluh Satu',
            2022 => 'Dua Ribu Dua Puluh Dua', 2023 => 'Dua Ribu Dua Puluh Tiga',
            2024 => 'Dua Ribu Dua Puluh Empat', 2025 => 'Dua Ribu Dua Puluh Lima',
            2026 => 'Dua Ribu Dua Puluh Enam', 2027 => 'Dua Ribu Dua Puluh Tujuh',
        ];

        $hariStr   = $hariList[$now->format('l')] ?? $now->format('l');
        $tglStr    = $angkaList[$now->day] ?? $now->day;
        $bulanStr  = $bulanList[(int)$now->format('n')] ?? $now->format('F');
        $tahunStr  = $tahunList[$now->year] ?? $now->year;
        $tanggalFormatted = $now->day . ' ' . $bulanStr . ' ' . $now->year;

        // ── Map JFA & Kelompok ──────────────────────────────────────────
        $jfaSkorMap  = ['guru_besar' => 5, 'lektor_kepala' => 4, 'lektor' => 3, 'asisten_ahli' => 2, 'non_jabatan' => 1];
        $jfaLabelMap = ['guru_besar' => 'GB', 'lektor_kepala' => 'LK', 'lektor' => 'L', 'asisten_ahli' => 'AA', 'non_jabatan' => 'NJAD'];
        $kelompokMap = ['scout' => 'Smart Computing Technology (SCOUT)', 'ethes' => 'ETHES', 'riib' => 'RIIB'];

        // ── Hitung nilai per kandidat ───────────────────────────────────
        $kandidats = $lamaranList->map(function ($lamaran) use (
            $jfaSkorMap, $jfaLabelMap, $kelompokMap
        ) {
            $pelamar = $lamaran->effective_pelamar;

            // Load jadwal seleksi untuk pelamar ini
            $jadwals = \App\Models\JadwalSeleksi::with(['penilaian', 'penguji'])
                ->where('pelamar_id', $lamaran->pelamar_id)
                ->where('lowongan_id', $lamaran->lowongan_id)
                ->get();

            $microDinilai     = $jadwals->where('tipe_seleksi', 'micro_teaching')->filter(fn($j) => $j->penilaian !== null)->values();
            $wawancaraDinilai = $jadwals->where('tipe_seleksi', 'wawancara')->filter(fn($j) => $j->penilaian !== null)->values();

            $avgMicro     = $microDinilai->count() > 0 ? round($microDinilai->avg(fn($j) => $j->penilaian->total_nilai), 2) : null;
            $avgWawancara = $wawancaraDinilai->count() > 0 ? round($wawancaraDinilai->avg(fn($j) => $j->penilaian->total_nilai), 2) : null;

            // Kualifikasi
            $jenjangTertinggi = collect([$pelamar->jenjang_3 ?? null, $pelamar->jenjang_2 ?? null, $pelamar->jenjang ?? null])
                ->filter()->map(fn($j) => strtolower(trim($j)))->first();
            $isS3 = $jenjangTertinggi && (str_contains($jenjangTertinggi, 's3') || str_contains($jenjangTertinggi, 'doktor'));
            $isS2 = $jenjangTertinggi && (str_contains($jenjangTertinggi, 's2') || str_contains($jenjangTertinggi, 'magister') || str_contains($jenjangTertinggi, 'master'));
            $jenjangDisplay = $isS3 ? 'S3' : ($isS2 ? 'S2' : ($pelamar->jenjang ?? '-'));

            $statusRekrutmenNilai = $wawancaraDinilai->first()?->penilaian?->status_rekrutmen ?? null;
            $sptSkor = 0;
            if ($isS3) {
                if ($statusRekrutmenNilai === 'profesional_full_time') $sptSkor = 5;
                elseif ($statusRekrutmenNilai === 'praktisi_part_time') $sptSkor = 4;
                else $sptSkor = 3;
            } elseif ($isS2) {
                $sptSkor = $statusRekrutmenNilai === 'profesional_full_time' ? 2 : 1;
            }

            $jfaKey   = $pelamar->jabatan_akademik ?? 'non_jabatan';
            $jfaSkor  = $jfaSkorMap[$jfaKey] ?? 1;
            $jfaLabel = $jfaLabelMap[$jfaKey] ?? 'NJAD';

            $hIndex = (int)($pelamar->h_index ?? 0);
            if ($hIndex > 10) $hSkor = 5;
            elseif ($hIndex >= 5) $hSkor = 4;
            elseif ($hIndex >= 2) $hSkor = 3;
            elseif ($hIndex >= 1) $hSkor = 2;
            else $hSkor = 1;

            $avgKualifikasi = round(($sptSkor + $jfaSkor + $hSkor) / 3, 4);
            $hasilAkhir = null;
            if ($avgMicro !== null && $avgWawancara !== null) {
                $hasilAkhir = round(($avgKualifikasi * 0.40) + ($avgMicro * 0.20) + ($avgWawancara * 0.40), 2);
            }

            // Rekomendasi & catatan
            $rekAll = collect([...$microDinilai->pluck('penilaian'), ...$wawancaraDinilai->pluck('penilaian')])->pluck('rekomendasi')->filter();
            $direkomendasikan = $rekAll->contains('direkomendasikan');
            $tidakDirek       = $rekAll->contains('tidak_direkomendasikan');
            $rekomendasi      = $direkomendasikan && !$tidakDirek ? 'Direkomendasikan' : 'Tidak Direkomendasi';

            $catatan = collect([...$microDinilai->pluck('penilaian'), ...$wawancaraDinilai->pluck('penilaian')])
                ->pluck('catatan')->filter()->implode(' ');

            // Prodi tujuan & kelompok keahlian dari penilaian
            $rekDetail   = $wawancaraDinilai->first(fn($j) => $j->penilaian?->rekomendasi === 'direkomendasikan')?->penilaian
                        ?? $wawancaraDinilai->first()?->penilaian;
            $prodiTujuan = $rekDetail?->prodi_tujuan ?? '-';

            $kelompokDisplay = '';
            foreach ($microDinilai as $jm) {
                if (!$kelompokDisplay && $jm->penilaian?->kelompok_keahlian)
                    $kelompokDisplay = $kelompokMap[$jm->penilaian->kelompok_keahlian] ?? $jm->penilaian->kelompok_keahlian;
            }

            return (object)[
                'nama'           => $pelamar->nama,
                'avgKualifikasi' => $avgKualifikasi,
                'avgMicro'       => $avgMicro,
                'avgWawancara'   => $avgWawancara,
                'hasilAkhir'     => $hasilAkhir,
                'catatan'        => $catatan,
                'rekomendasi'    => $rekomendasi,
                'prodiTujuan'    => $prodiTujuan,
                'jfaLabel'       => $jfaLabel,
                'jenjangDisplay' => $jenjangDisplay,
                'kelompokKeahlian' => $kelompokDisplay ?: '-',
            ];
        });

        $adminNama = auth()->user()->name ?? 'Admin';
        $waktu = now()->translatedFormat('d F Y \p\u\k\u\l H:i');
        $posisiLog = $lowongan->nama_posisi;
        $prodiLog = $lowongan->prodi->nama ?? '-';
        \App\Models\User::where('role', 'admin')->each(function($u) use ($adminNama, $posisiLog, $prodiLog, $waktu) {
            \App\Models\Notifikasi::kirimSistem($u->id, 'Cetak Berita Acara', "Admin {$adminNama} mencetak berita acara lowongan {$posisiLog} ({$prodiLog}) pada {$waktu}.");
        });

        return view('admin.lowongan.berita_acara', compact(
            'lowongan', 'kandidats', 'hariStr', 'tglStr', 'bulanStr', 'tahunStr', 'tanggalFormatted'
        ));
    }
}
