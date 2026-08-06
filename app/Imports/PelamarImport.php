<?php

namespace App\Imports;

use App\Models\Pelamar;
use App\Models\User;
use App\Models\Lowongan;
use App\Models\Lamaran;
use App\Models\Prodi;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Illuminate\Support\Facades\Hash;

class PelamarImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows, SkipsOnFailure
{
    use SkipsFailures;

    private string $hashedPassword;

    public function __construct()
    {
        $this->hashedPassword = Hash::make('pelamar123');

        \Maatwebsite\Excel\Imports\HeadingRowFormatter::extend('custom_indexed', function($value, $key) {
            return \Illuminate\Support\Str::slug($value, '_') . '_' . $key;
        });
        \Maatwebsite\Excel\Imports\HeadingRowFormatter::default('custom_indexed');
    }

    /**
     * Lewati baris yang tidak punya email DAN nama.
     */
    public function isEmptyWhen(array $row): bool
    {
        $email = $this->col($row, ['email', 'email_address', 'alamat_email']);
        $nama  = $this->col($row, ['nama', 'nama_lengkap', 'name']);
        return empty($email) && empty($nama);
    }

    /**
     * Normalisasi kolom SEBELUM validasi dijalankan.
     */
    public function prepareForValidation(array $data, int $index): array
    {
        // ── Normalisasi email & nama ──────────────────────────────────
        $data['email'] = strtolower(trim((string) $this->col($data, [
            'email', 'email_address', 'alamat_email', 'e-mail', 'mail'
        ])));

        $data['nama'] = trim((string) $this->col($data, [
            'nama', 'nama_lengkap', 'name', 'full_name', 'nama_pelamar'
        ]));

        // ── Normalisasi NIK ────────────────────────────────────────────
        $nik = $this->col($data, ['nik', 'nomor_ktp', 'no_ktp', 'nomor_nik', 'ktp']);
        if ($nik !== null) {
            $data['nik'] = $this->cleanNumericString($nik);
        }

        // ── Normalisasi Jenis Kelamin ──────────────────────────────────
        $jk = $this->col($data, ['jenis_kelamin', 'jk', 'gender', 'sex']);
        if ($jk !== null) {
            $jkStr = strtolower(trim((string) $jk));
            if (str_contains($jkStr, 'laki') || str_contains($jkStr, 'pria') || $jkStr === 'l' || $jkStr === 'male') {
                $data['jenis_kelamin'] = 'L';
            } elseif (str_contains($jkStr, 'perempuan') || str_contains($jkStr, 'wanita') || $jkStr === 'p' || $jkStr === 'female') {
                $data['jenis_kelamin'] = 'P';
            }
        }

        // ── Normalisasi field numerik ──────────────────────────────────
        foreach (['no_telepon', 'no_hp', 'no_ijazah', 'no_ijazah_2', 'no_ijazah_3', 'nidn'] as $f) {
            if (isset($data[$f]) && $data[$f] !== null) {
                $data[$f] = $this->cleanNumericString($data[$f]);
            }
        }

        // ── Normalisasi tanggal ────────────────────────────────────────
        $data['tanggal_lahir'] = $this->resolveDate(
            $this->col($data, ['tanggal_lahir', 'tgl_lahir', 'dob', 'tanggal_la'])
        );
        $data['tanggal_tes_bahasa'] = $this->resolveDate(
            $this->col($data, ['tanggal_tes_bahasa', 'tanggal_tes_kemampuan_bahasa_inggris'])
        );

        // ── Normalisasi no_telepon (handle 628xx, 8x, 08x) ───────────
        if (empty($data['no_telepon'])) {
            $rawPhone = $this->col($data, [
                'no_telepon', 'no_hp', 'nomor_telepon',
                'nomor_ponsel_wa_aktif', 'phone', 'mobile'
            ]);
            if ($rawPhone !== null) {
                $data['no_telepon'] = $this->cleanPhone($rawPhone);
            }
        }

        // ── Normalisasi alamat ─────────────────────────────────────────
        if (empty($data['alamat_ktp'])) {
            $data['alamat_ktp'] = $this->col($data, ['alamat_di_ktp', 'alamat_ktp', 'alamat_sesuai_ktp']);
        }
        if (empty($data['alamat_domisili'])) {
            $data['alamat_domisili'] = $this->col($data, ['alamat_domisili', 'domisili']);
        }

        return $data;
    }

    public function model(array $row)
    {
        $email = strtolower(trim((string) ($row['email'] ?? '')));
        $nama  = trim((string) ($row['nama'] ?? ''));

        if (empty($email) || empty($nama)) {
            return null;
        }

        // 1. Buat atau dapatkan akun user
        $user = User::where('email', $email)->first();
        if (!$user) {
            $user = User::create([
                'name'           => $nama,
                'email'          => $email,
                'password'       => $this->hashedPassword,
                'password_plain' => 'pelamar123',
                'role'           => 'pelamar',
            ]);
        }

        // 2. Resolve data diri
        $nik         = $this->cleanNumericString($this->col($row, ['nik', 'nomor_ktp', 'no_ktp', 'ktp']));
        $noTelepon   = $this->cleanPhone($this->col($row, ['no_telepon', 'no_hp', 'nomor_telepon', 'nomor_ponsel_wa_aktif', 'phone']));
        $jk          = $row['jenis_kelamin'] ?? null;
        $tglLahir    = $row['tanggal_lahir'] ?? null;
        $tmpLahir    = $this->col($row, ['tempat_lahir', 'tmp_lahir']);
        $kewargaan   = $this->col($row, ['kewarganegaraan', 'kewarganeggaraan']);
        if ($kewargaan && str_contains(strtolower($kewargaan), 'wni')) {
            $kewargaan = 'WNI';
        }
        $statusNikah = $this->col($row, ['status_pernikahan', 'pernikahan']);

        // 3. Resolve jenjang pendidikan terakhir
        $pendidikanTerakhir = $this->col($row, ['pendidikan_terakhir', 'jenjang']);
        $jenjangLevel       = 'S1'; // default
        if ($pendidikanTerakhir) {
            $pUpper = strtoupper(trim((string)$pendidikanTerakhir));
            if (str_contains($pUpper, 'S3') || str_contains($pUpper, 'DOKTOR'))    $jenjangLevel = 'S3';
            elseif (str_contains($pUpper, 'S2') || str_contains($pUpper, 'MAGISTER')) $jenjangLevel = 'S2';
            elseif (str_contains($pUpper, 'S1') || str_contains($pUpper, 'SARJANA')) $jenjangLevel = 'S1';
        }

        // 4. Resolve data INSTITUSI, PRODI, IPK per jenjang (S1, S2, S3)
        // Col 33-37 (Kolom AH-AL di Excel) = S1 Education
        $institusiS1 = $this->col($row, ['nama_perguruan_tinggi_33']);
        $prodiS1     = $this->col($row, ['jurusanprogram_studi_34']);
        $akredS1     = $this->col($row, [
            'akreditasi_program_studiperingkat_perguruan_tinggi_2022_silahkan_cek_httpswwwtopuniversitiescomuniversity_rankingsworld_university_rankings2022_35',
        ]);
        $noIjzS1     = $this->cleanNumericString($this->col($row, ['nomor_ijazah_36']));
        $ipkS1       = $this->cleanIpk($this->col($row, ['ipk_skala_400_37']));

        // Col 41-45 (Kolom AP-AT di Excel) = S2 Education
        $institusiS2 = $this->col($row, ['nama_perguruan_tinggi_41']);
        $prodiS2     = $this->col($row, ['jurusanprogram_studi_42']);
        $akredS2     = $this->col($row, [
            'akreditasi_program_studiperingkat_perguruan_tinggi_2022_silahkan_cek_httpswwwtopuniversitiescomuniversity_rankingsworld_university_rankings2022_43',
        ]);
        $noIjzS2     = $this->cleanNumericString($this->col($row, ['nomor_ijazah_44']));
        $ipkS2       = $this->cleanIpk($this->col($row, ['ipk_skala_400_45']));

        // Col 23-29 di Excel (Indexed _23) = S3 Education
        $institusiS3 = $this->col($row, ['nama_perguruan_tinggi_23']);
        $prodiS3     = $this->col($row, ['jurusanprogram_studi_24']);
        $akredS3     = $this->col($row, [
            'akreditasi_program_studiperingkat_perguruan_tinggi_2022_silahkan_cek_httpswwwtopuniversitiescomuniversity_rankingsworld_university_rankings2022_25',
        ]);
        $noIjzS3     = $this->cleanNumericString($this->col($row, ['nomor_ijazah_28']));
        $ipkS3       = $this->cleanIpk($this->col($row, ['ipk_skala_400_29']));

        // Fallback untuk single-column format (misal format Book1.xlsx tanpa suffix)
        $institusiGeneric = $this->col($row, ['nama_perguruan_tinggi', 'institusi', 'universitas']);
        $prodiGeneric     = $this->col($row, ['jurusanprogram_studi', 'prodi_pendidikan', 'jurusan']);
        $akredGeneric     = $this->col($row, [
            'akreditasi_program_studiperingkat_perguruan_tinggi_2022_silahkan_cek_httpswwwtopuniversitiescomuniversity_rankingsworld_university_rankings2022',
            'akreditas',
        ]);
        $noIjzGeneric     = $this->cleanNumericString($this->col($row, ['nomor_ijazah', 'no_ijazah']));
        $ipkGeneric       = $this->cleanIpk($this->col($row, ['ipk_skala_400', 'ipk']));

        if (!$institusiS1 && !$institusiS2 && $institusiGeneric) {
            if ($jenjangLevel === 'S2') {
                if (str_contains($prodiGeneric ?? '', '/')) {
                    $parts = array_map('trim', explode('/', $prodiGeneric));
                    $prodiS1 = $parts[0] ?? $prodiGeneric;
                    $prodiS2 = $parts[1] ?? $prodiGeneric;
                } else {
                    $prodiS1 = $prodiGeneric;
                    $prodiS2 = $prodiGeneric;
                }
                $institusiS1 = $institusiGeneric;
                $institusiS2 = $institusiGeneric;
                $akredS1     = $akredGeneric;
                $akredS2     = $akredGeneric;
                $noIjzS2     = $noIjzGeneric;
                $ipkS2       = $ipkGeneric;
            } else {
                $institusiS1 = $institusiGeneric;
                $prodiS1     = $prodiGeneric;
                $akredS1     = $akredGeneric;
                $noIjzS1     = $noIjzGeneric;
                $ipkS1       = $ipkGeneric;
            }
        }

        // 5. File scan masing-masing jenjang
        $fileIjazahS1  = $this->col($row, [
            'scan_ijazah_s1_file_dalam_bentuk_pdfjpeg_dgn_ukuran_maksimal_1_mb',
            'scan_ijazah_s1', 'file_ijazah_s1'
        ]);
        $fileTranskripS1 = $this->col($row, [
            'scan_transkrip_nilai_ijazah_s1_file_dalam_bentuk_pdfjpeg_dgn_ukuran_maksimal_1_mb',
            'scan_transkrip_s1', 'file_transkrip_s1'
        ]);
        $fileIjazahS2  = $this->col($row, [
            'scan_ijazah_s2_file_dalam_bentuk_pdfjpeg_dgn_ukuran_maksimal_1_mb',
            'scan_ijazah_s2', 'file_ijazah_s2'
        ]);
        $fileTranskripS2 = $this->col($row, [
            'scan_trankrip_nilai_s2_file_dalam_bentuk_pdfjpeg_dgn_ukuran_maksimal_1_mb',
            'scan_transkrip_s2', 'file_transkrip_s2'
        ]);
        $fileIjazahS3  = $this->col($row, [
            'scan_ijazah_s3_file_dalam_bentuk_pdfjpeg_dgn_ukuran_maksimal_1_mb',
            'scan_ijazah_s3', 'file_ijazah_s3'
        ]);
        $fileTranskripS3 = $this->col($row, [
            'scan_transkrip_nilai_s3_file_dalam_bentuk_pdfjpeg_dgn_ukuran_maksimal_1_mb',
            'scan_transkrip_s3', 'file_transkrip_s3'
        ]);

        // 6. Susun data pendidikan
        $pendidikanData = [];

        if ($jenjangLevel === 'S3') {
            $pendidikanData = [
                'jenjang'            => 'S1',
                'institusi'          => $institusiS1,
                'prodi_pendidikan'   => $prodiS1,
                'akreditas'          => $akredS1,
                'no_ijazah'          => $noIjzS1,
                'ipk'                => $ipkS1,
                'file_ijazah'        => $fileIjazahS1,
                'file_transkrip'     => $fileTranskripS1,

                'jenjang_2'          => 'S2',
                'institusi_2'        => $institusiS2,
                'prodi_pendidikan_2' => $prodiS2,
                'akreditas_2'        => $akredS2,
                'no_ijazah_2'        => $noIjzS2,
                'ipk_2'              => $ipkS2,
                'file_ijazah_2'      => $fileIjazahS2,
                'file_transkrip_2'   => $fileTranskripS2,

                'jenjang_3'          => 'S3',
                'institusi_3'        => $institusiS3,
                'prodi_pendidikan_3' => $prodiS3,
                'akreditas_3'        => $akredS3,
                'no_ijazah_3'        => $noIjzS3,
                'ipk_3'              => $ipkS3,
                'file_ijazah_3'      => $fileIjazahS3,
                'file_transkrip_3'   => $fileTranskripS3,
            ];
        } elseif ($jenjangLevel === 'S2') {
            $pendidikanData = [
                'jenjang'            => 'S1',
                'institusi'          => $institusiS1,
                'prodi_pendidikan'   => $prodiS1,
                'akreditas'          => $akredS1,
                'no_ijazah'          => $noIjzS1,
                'ipk'                => $ipkS1,
                'file_ijazah'        => $fileIjazahS1,
                'file_transkrip'     => $fileTranskripS1,

                'jenjang_2'          => 'S2',
                'institusi_2'        => $institusiS2,
                'prodi_pendidikan_2' => $prodiS2,
                'akreditas_2'        => $akredS2,
                'no_ijazah_2'        => $noIjzS2,
                'ipk_2'              => $ipkS2,
                'file_ijazah_2'      => $fileIjazahS2,
                'file_transkrip_2'   => $fileTranskripS2,
            ];
        } else {
            $pendidikanData = [
                'jenjang'            => 'S1',
                'institusi'          => $institusiS1,
                'prodi_pendidikan'   => $prodiS1,
                'akreditas'          => $akredS1,
                'no_ijazah'          => $noIjzS1,
                'ipk'                => $ipkS1,
                'file_ijazah'        => $fileIjazahS1,
                'file_transkrip'     => $fileTranskripS1,
            ];
        }

        // 7. Dokumen Pendukung
        $fileCv         = $this->col($row, [
            'cvresumeriwayat_hidup_termasuk_publikasi_yang_pernah_terbit_file_dalam_bentuk_pdfjpeg_dengan_ukuran_maksimal_1_mb',
            'cvresume_riwayat_hidup_termasuk_publikasi_yang_pernah_terbit_file_dalam_bentuk_pdfjpeg_dengan_ukuran_maksimal_1_mb',
            'file_cv', 'cv'
        ]);
        $filePasFoto    = $this->col($row, [
            'pas_photo_formal_berwarna_latar_abu_abu_file_dalam_bentuk_pdfjpeg_dgn_ukuran_maksimal_1_mb',
            'file_pas_foto', 'pas_foto'
        ]);
        $fileKtp        = $this->col($row, [
            'ktp_file_dalam_bentuk_pdfjpeg_dgn_ukuran_maksimal_1_mb',
            'file_ktp', 'ktp'
        ]);
        $fileSerdik     = $this->col($row, [
            'sertifikat_pendidik_bagi_yang_sudah_memiliki_file_dalam_bentuk_pdfjpeg_dgn_ukuran_maksimal_1_mb',
            'file_serdik'
        ]);
        $fileBahasa     = $this->col($row, [
            'sertifikat_kemampuan_bahasa_inggris_file_dalam_bentuk_pdfjpeg_dgn_ukuran_maksimal_1_mb',
            'file_sertifikat_bahasa'
        ]);
        $fileKompetensi = $this->col($row, [
            'sertifikat_kompetensi_yang_masih_berlaku_file_dalam_bentuk_pdfjpeg_dgn_ukuran_maksimal_1_mb',
            'file_sertifikat'
        ]);
        $fileSuratLamaran = $this->col($row, [
            'surat_lamaran_ditujukan_ke_rektor_file_dalam_bentuk_pdfjpeg_dengan_ukuran_maksimal_1_mb',
            'file_surat_lamaran'
        ]);

        // 8. Data Akademik Dosen
        $nidn           = $this->cleanNumericString($this->col($row, [
            'nidnnidknupnuptk_bagi_yang_sudah_memilikinya',
            'nidnidknupnuptk_bagi_yang_sudah_memilikinya',
            'nidn'
        ]));
        $fileKartuDosen = $this->col($row, [
            'foto_kartu_dosen_nidnnidknupnuptk_bagi_yang_sudah_memilikinya_file_dalam_bentuk_pdfjpeg_dgn_ukuran_maksimal_1_mb',
            'foto_kartu_dosen_nidnidknupnuptk_bagi_yang_sudah_memilikinya_file_dalam_bentuk_pdfjpeg_dgn_ukuran_maksimal_1_mb',
            'file_kartu_dosen'
        ]);
        $homebase        = $this->col($row, [
            'homebase_asal_bagi_yang_sudah_ber_nidnnidknup',
            'homebase_asal_bagi_yang_sudah_ber_nidnnidknup',
            'homebase'
        ]);
        $bidangPengajaran  = $this->col($row, ['bidang_pengajaran']);
        $minatRiset        = $this->col($row, ['riset_yang_diminati', 'minat_riset']);
        $hIndex            = $this->col($row, ['nilai_h_index_scopus_bagi_yang_sudah_memiliki', 'h_index']);
        $jabatanAkademik   = $this->resolveJabatanAkademik($this->col($row, [
            'jabatan_akademik_dosen_bagi_yang_sudah_memiliki', 'jabatan_akademik'
        ]));
        $fileJad           = $this->col($row, [
            'upload_sk_jabatan_akademik_dosen_dan_pak_bagi_yang_sudah_memiliki_file_dalam_bentuk_pdfjpeg_dgn_ukuran_maksimal_1_mb',
            'file_jad'
        ]);
        $inpassing         = $this->col($row, ['tingkat_inpassing_bagi_yang_sudah_memilikinya', 'inpassing']);
        $fileInpassing     = $this->col($row, [
            'upload_sk_inpassing_bagi_yang_sudah_memilikinya_file_dalam_bentuk_pdfjpeg_dgn_ukuran_maksimal_1_mb',
            'file_inpassing'
        ]);

        $skorBahasa = $this->cleanSkor($this->col($row, ['skor_kemampuan_bahasa_inggris', 'skor_bahasa']));
        $tglBahasa  = $row['tanggal_tes_bahasa'] ?? null;
        $jenisTesBahasa = $this->col($row, ['jenis_tes_bahasa', 'jenis_tes', 'tipe_tes_bahasa']);
        if (!$jenisTesBahasa && $skorBahasa) {
            $jenisTesBahasa = ($skorBahasa <= 9.0) ? 'IELTS' : 'TOEFL_ITP';
        }

        // 9. Simpan / update Pelamar
        $pelamarData = array_merge([
            'nama'              => $nama,
            'nik'               => $nik,
            'tempat_lahir'      => $tmpLahir,
            'tanggal_lahir'     => $tglLahir,
            'no_telepon'        => $noTelepon,
            'jenis_kelamin'     => $jk,
            'kewarganegaraan'   => $kewargaan,
            'status_pernikahan' => $statusNikah,
            'alamat_domisili'   => $row['alamat_domisili'] ?? null,
            'alamat_ktp'        => $row['alamat_ktp'] ?? null,

            // Dokumen Pendukung
            'file_cv'                => $fileCv,
            'file_pas_foto'          => $filePasFoto,
            'file_ktp'               => $fileKtp,
            'file_serdik'            => $fileSerdik,
            'file_sertifikat'        => $fileKompetensi,
            'file_sertifikat_bahasa' => $fileBahasa,
            'jenis_tes_bahasa'       => $jenisTesBahasa,
            'skor_bahasa'            => $skorBahasa,
            'tanggal_tes_bahasa'     => $tglBahasa,

            // Akademik Dosen
            'nidn'             => $nidn,
            'file_kartu_dosen' => $fileKartuDosen,
            'homebase'         => $homebase,
            'minat_riset'      => $minatRiset,
            'h_index'          => is_numeric($hIndex) ? (int)$hIndex : null,
            'jabatan_akademik' => $jabatanAkademik,
            'file_jad'         => $fileJad,
            'file_inpassing'   => $fileInpassing,
        ], $pendidikanData);

        $pelamar = Pelamar::updateOrCreate(
            ['user_id' => $user->id],
            array_filter($pelamarData, fn($v) => $v !== null && $v !== '')
        );

        // 10. Tautkan ke Lowongan (Prodi-based matching, tanpa auto-create)
        $lowonganToApply = null;

        // Coba match dari kolom "Program Studi / Posisi yang Dilamar"
        $prodiDilamar = $this->col($row, [
            'program_studi_yang_dilamar', 'prodi_dilamar', 'lowongan',
            'posisi_yang_dilamar', 'posisi_pilihan', 'pilihan_prodi',
            'program_studi', 'prodi', 'posisi', 'jabatan_yang_dilamar'
        ]);

        if ($prodiDilamar) {
            $kw = (string) $prodiDilamar;
            $kw = preg_replace('/^TUS\s*[-–]\s*/i', '', $kw);
            $kw = preg_replace('/^S[123]\s+/i', '', $kw);
            $kw = preg_replace('/^Dosen\s+(Tetap\s+)?(S[123]\s+)?/i', '', $kw);
            $kw = trim($kw);
            $kwClean = strtolower($kw);

            // Auto-fix typos in Excel on-the-fly (e.g. "TENIK" -> "teknik", "SAIN" -> "sains")
            $kwClean = preg_replace('/\btenik\b/i', 'teknik', $kwClean);
            $kwClean = preg_replace('/\bsain\b/i', 'sains', $kwClean);

            // Alias normalization
            $aliasMap = [
                'sain data'        => 'sains data',
                'tenik komputer'   => 'teknik komputer',
                'sistem komputer'  => 'teknik komputer',
                'computer'         => 'teknik komputer',
            ];
            if (isset($aliasMap[$kwClean])) {
                $kwClean = $aliasMap[$kwClean];
            }

            if (!empty($kwClean)) {
                $prodis = Prodi::all();
                $prodiModel = null;

                // 1. Exact match by nama or kode
                foreach ($prodis as $p) {
                    if (strtolower($p->nama) === $kwClean || strtolower($p->kode) === $kwClean) {
                        $prodiModel = $p;
                        break;
                    }
                }

                // 2. Substring match
                if (!$prodiModel) {
                    foreach ($prodis as $p) {
                        $pName = strtolower($p->nama);
                        if (str_contains($pName, $kwClean) || str_contains($kwClean, $pName)) {
                            $prodiModel = $p;
                            break;
                        }
                    }
                }

                if ($prodiModel) {
                    // Step 2: Tentukan kategori dari kolom "Jalur" (jika ada & valid)
                    $jalurRaw   = $this->col($row, ['anda_melamar_untuk_jalur', 'jalur', 'jalur_lamaran']);
                    $kategori   = null;
                    if ($jalurRaw && strtolower(trim((string)$jalurRaw)) !== 'n/a') {
                        $jalurStr = strtolower(trim((string)$jalurRaw));
                        if (str_contains($jalurStr, 'tendik') || str_contains($jalurStr, 'kependidikan')) {
                            $kategori = 'Tenaga Kependidikan';
                        } else {
                            $kategori = 'Dosen';
                        }
                    }

                    // Step 3: Cari Lowongan berdasarkan prodi_id (+ kategori jika ada)
                    $q = Lowongan::where('prodi_id', $prodiModel->id)->where('status', 'aktif');
                    if ($kategori) {
                        $q->where('kategori', $kategori);
                    }
                    $lowonganToApply = $q->first();
                }
            }

            // Fallback: Jika belum ketemu via prodi, cari langsung berdasarkan nama_posisi Lowongan!
            if (!$lowonganToApply) {
                $rawClean = strtolower(trim((string) $prodiDilamar));
                foreach (Lowongan::where('status', 'aktif')->get() as $low) {
                    $lName = strtolower($low->nama_posisi);
                    if (str_contains($lName, $kwClean) || str_contains($rawClean, $lName) || str_contains($lName, $rawClean)) {
                        $lowonganToApply = $low;
                        break;
                    }
                }
            }
        }

        // 11. Buat Lamaran jika Lowongan ditemukan (Maksimal 4 lamaran per pelamar)
        if ($lowonganToApply) {
            $currentLamaranCount = Lamaran::where('pelamar_id', $pelamar->id)->count();
            if ($currentLamaranCount < 4) {
                Lamaran::firstOrCreate(
                    [
                        'lowongan_id' => $lowonganToApply->id,
                        'pelamar_id'  => $pelamar->id,
                    ],
                    [
                        'status'             => 'menunggu',
                        'file_surat_lamaran' => $fileSuratLamaran,
                        'snapshot_data'      => $pelamar->toArray(),
                    ]
                );
            }
        }

        return $pelamar;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email|max:255',
            'nama'  => 'required|string|max:255',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'email.required' => 'Kolom email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'nama.required'  => 'Kolom nama wajib diisi.',
        ];
    }

    // ── Private Helpers ─────────────────────────────────────────────────

    private function col(array $row, array $keys): mixed
    {
        foreach ($keys as $key) {
            $val = $row[$key] ?? null;
            if ($val !== null && trim((string) $val) !== '') {
                return $val;
            }
        }

        // Jika keys mengandung key spesifik bersuffix angka (misal _44), jangan fuzzy match ke index lain
        foreach ($keys as $key) {
            if (preg_match('/_\d+$/', $key)) {
                return null;
            }
        }

        // Fuzzy match: cari key yang mengandung keyword untuk key generic
        foreach ($row as $k => $val) {
            if ($val === null || trim((string)$val) === '') continue;
            $kLower = strtolower((string)$k);
            foreach ($keys as $key) {
                if (str_contains($kLower, strtolower($key))) {
                    return $val;
                }
            }
        }

        return null;
    }

    private function cleanNumericString(mixed $val): ?string
    {
        if ($val === null || trim((string)$val) === '') return null;
        $str = str_replace([' ', ','], ['', '.'], trim((string)$val));
        if (is_numeric($str)) {
            return sprintf('%.0f', (float)$str);
        }
        return trim((string)$val);
    }

    private function cleanPhone(mixed $val): ?string
    {
        if ($val === null || trim((string)$val) === '') return null;
        // Bersihkan semua karakter non-digit
        $phone = preg_replace('/[^0-9]/', '', (string)$val);
        if (empty($phone)) return null;

        // Handle format internasional: 628xx → 08xx
        if (str_starts_with($phone, '628')) {
            $phone = '0' . substr($phone, 2);
        }
        // Handle awalan 8 tanpa 0
        elseif (str_starts_with($phone, '8') && strlen($phone) >= 9) {
            $phone = '0' . $phone;
        }

        return $phone;
    }

    /**
     * Bersihkan & konversi nilai IPK.
     * Excel kadang menyimpan IPK sebagai time serial (0.17 = 4 jam 18 menit).
     * Jika nilai < 1.0, kemungkinan adalah Excel time serial → konversi ke jam (× 24).
     */
    private function cleanIpk(mixed $val): ?float
    {
        if ($val === null || trim((string)$val) === '') return null;
        $str = str_replace(',', '.', trim((string)$val));
        if (!is_numeric($str)) return null;
        $num = (float)$str;

        // Deteksi Excel time serial: nilai < 1.0 namun > 0 → konversi ke jam (× 24)
        if ($num > 0 && $num < 1.0) {
            $converted = round($num * 24, 2);
            if ($converted >= 1.0) {
                return min(4.00, $converted);
            }
            return null;
        }

        // Nilai > 4.0: normalisasi (legacy dari kode lama)
        if ($num > 4.0) {
            if ($num <= 40.0) $num = $num / 10.0;
            elseif ($num <= 100.0) $num = $num / 25.0;
        }

        return ($num >= 0.0 && $num <= 4.0) ? round($num, 2) : null;
    }

    /**
     * Bersihkan skor bahasa (numerik bulat, bukan IPK).
     */
    private function cleanSkor(mixed $val): ?float
    {
        if ($val === null || trim((string)$val) === '') return null;
        $str = str_replace(',', '.', trim((string)$val));
        return is_numeric($str) ? round((float)$str, 2) : null;
    }

    private function resolveJabatanAkademik(mixed $val): ?string
    {
        if (!$val) return null;
        $str = strtolower(trim((string)$val));
        if (str_contains($str, 'asisten'))  return 'asisten_ahli';
        if (str_contains($str, 'kepala'))   return 'lektor_kepala';
        if (str_contains($str, 'lektor'))   return 'lektor';
        if (str_contains($str, 'besar') || str_contains($str, 'prof')) return 'guru_besar';
        return null;
    }

    private function resolveDate(mixed $value): ?string
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        // Excel serial number
        if (is_numeric($value)) {
            try {
                $date = ExcelDate::excelToDateTimeObject((float) $value);
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        $str = trim((string) $value);

        foreach (['m/d/Y', 'd/m/Y', 'd-m-Y', 'Y-m-d'] as $fmt) {
            $dt = \DateTime::createFromFormat($fmt, $str);
            if ($dt && $dt->format($fmt) === $str) {
                return $dt->format('Y-m-d');
            }
        }

        $ts = strtotime($str);
        return $ts !== false ? date('Y-m-d', $ts) : $str;
    }
}