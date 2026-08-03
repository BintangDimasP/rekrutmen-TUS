<?php

namespace App\Imports;

use App\Models\Pelamar;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class PelamarImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    /**
     * Password di-hash SEKALI dan di-reuse untuk semua baris.
     * Hash::make() (bcrypt) mahal — memanggil per baris akan timeout.
     */
    private string $hashedPassword;

    public function __construct()
    {
        $this->hashedPassword = \Illuminate\Support\Facades\Hash::make('pelamar123');
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
     * Ini menyatukan variasi nama kolom (mis. "email_address" → "email",
     * "nama_lengkap" → "nama", "nomor_ktp" → "nik", dll.)
     */
    public function prepareForValidation(array $data, int $index): array
    {
        // ── Normalisasi kolom wajib ──────────────────────────────────
        $data['email'] = strtolower(trim((string) $this->col($data, [
            'email', 'email_address', 'alamat_email',
        ])));

        $data['nama'] = trim((string) $this->col($data, [
            'nama', 'nama_lengkap', 'name', 'full_name',
        ]));

        // ── Normalisasi NIK (bisa "nik", "nomor_ktp", "nomor_kt", dll.) ─
        $nik = $this->col($data, ['nik', 'nomor_ktp', 'nomor_kt', 'no_ktp']);
        if ($nik !== null) {
            $data['nik'] = (string) $nik;
        }

        // ── Normalisasi field numerik lain ───────────────────────────
        foreach (['no_telepon', 'no_hp', 'no_ijazah', 'no_ijazah_2', 'no_ijazah_3', 'nidn'] as $f) {
            if (isset($data[$f]) && $data[$f] !== null) {
                $data[$f] = (string) $data[$f];
            }
        }

        // ── Normalisasi tanggal (handle Excel serial number) ─────────
        $data['tanggal_lahir'] = $this->resolveDate(
            $this->col($data, ['tanggal_lahir', 'tanggal_la'])
        );
        $data['tanggal_tes_bahasa'] = $this->resolveDate(
            $data['tanggal_tes_bahasa'] ?? null
        );

        // ── Normalisasi no_telepon dari variasi kolom ────────────────
        if (empty($data['no_telepon'])) {
            $data['no_telepon'] = $this->col($data, ['no_telepon', 'no_hp', 'nomor_telepon', 'phone']);
            if ($data['no_telepon'] !== null) {
                $data['no_telepon'] = (string) $data['no_telepon'];
            }
        }

        // ── Normalisasi alamat ───────────────────────────────────────
        if (empty($data['alamat_ktp'])) {
            $data['alamat_ktp'] = $this->col($data, ['alamat_ktp', 'alamat_di_ktp', 'alamat_sesuai_ktp']);
        }
        if (empty($data['alamat_domisili'])) {
            $data['alamat_domisili'] = $this->col($data, ['alamat_domisili', 'domisili']);
        }

        return $data;
    }

    public function model(array $row)
    {
        // Ambil nilai yang sudah dinormalisasi oleh prepareForValidation
        $email = strtolower(trim((string) ($row['email'] ?? '')));
        $nama  = trim((string) ($row['nama'] ?? ''));

        // Jika email sudah terdaftar, skip baris ini (jangan error)
        if (User::where('email', $email)->exists()) {
            return null;
        }

        // Buat akun user
        $user = User::create([
            'name'           => $nama,
            'email'          => $email,
            'password'       => $this->hashedPassword,
            'password_plain' => 'pelamar123',
            'role'           => 'pelamar',
        ]);

        // Resolve kolom opsional
        $nik       = $this->col($row, ['nik', 'nomor_ktp', 'nomor_kt', 'no_ktp']);
        $noTelepon = $this->col($row, ['no_telepon', 'no_hp', 'nomor_telepon', 'phone']);

        return new Pelamar([
            'user_id' => $user->id,

            // ── Data Diri ──────────────────────────────────────────
            'nama'              => $nama,
            'nik'               => $nik !== null ? (string) $nik : null,
            'tempat_lahir'      => $row['tempat_lahir']      ?? null,
            'tanggal_lahir'     => $row['tanggal_lahir']     ?? null,
            'no_telepon'        => $noTelepon !== null ? (string) $noTelepon : null,
            'jenis_kelamin'     => $row['jenis_kelamin']     ?? null,
            'kewarganegaraan'   => $row['kewarganegaraan']   ?? null,
            'status_pernikahan' => $row['status_pernikahan'] ?? null,
            'alamat_domisili'   => $row['alamat_domisili']   ?? null,
            'alamat_ktp'        => $row['alamat_ktp']        ?? null,

            // ── Riwayat Pendidikan Level 1 ─────────────────────────
            'jenjang'           => $row['jenjang']           ?? null,
            'institusi'         => $row['institusi']         ?? null,
            'prodi_pendidikan'  => $row['prodi_pendidikan']  ?? null,
            'akreditas'         => $row['akreditas']         ?? null,
            'no_ijazah'         => isset($row['no_ijazah'])  ? (string) $row['no_ijazah'] : null,
            'ipk'               => $row['ipk']               ?? null,

            // ── Riwayat Pendidikan Level 2 ─────────────────────────
            'jenjang_2'          => $row['jenjang_2']           ?? null,
            'institusi_2'        => $row['institusi_2']         ?? null,
            'prodi_pendidikan_2' => $row['prodi_pendidikan_2']  ?? null,
            'akreditas_2'        => $row['akreditas_2']         ?? null,
            'no_ijazah_2'        => isset($row['no_ijazah_2'])  ? (string) $row['no_ijazah_2'] : null,
            'ipk_2'              => $row['ipk_2']               ?? null,

            // ── Riwayat Pendidikan Level 3 ─────────────────────────
            'jenjang_3'          => $row['jenjang_3']           ?? null,
            'institusi_3'        => $row['institusi_3']         ?? null,
            'prodi_pendidikan_3' => $row['prodi_pendidikan_3']  ?? null,
            'akreditas_3'        => $row['akreditas_3']         ?? null,
            'no_ijazah_3'        => isset($row['no_ijazah_3'])  ? (string) $row['no_ijazah_3'] : null,
            'ipk_3'              => $row['ipk_3']               ?? null,

            // ── Dokumen Pendukung ──────────────────────────────────
            'kategori_sertifikat' => $row['kategori_sertifikat'] ?? null,
            'jenis_tes_bahasa'    => $row['jenis_tes_bahasa']    ?? null,
            'skor_bahasa'         => $row['skor_bahasa']         ?? null,
            'tanggal_tes_bahasa'  => $row['tanggal_tes_bahasa']  ?? null,

            // ── Riwayat Akademik ───────────────────────────────────
            'nidn'             => isset($row['nidn'])            ? (string) $row['nidn'] : null,
            'homebase'         => $row['homebase']               ?? null,
            'jabatan_akademik' => $row['jabatan_akademik']       ?? null,
            'minat_riset'      => $row['minat_riset']            ?? null,
            'h_index'          => $row['h_index']                ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            // ── Wajib: hanya email dan nama ───────────────────────
            'email' => 'required|email|max:255',
            'nama'  => 'required|string|max:255',

            // ── Opsional: semua bidang lain ───────────────────────
            'nik'               => 'nullable|max:16',
            'tempat_lahir'      => 'nullable|string|max:100',
            'tanggal_lahir'     => 'nullable|date',
            'no_telepon'        => 'nullable|max:20',
            'jenis_kelamin'     => 'nullable|in:L,P',
            'kewarganegaraan'   => 'nullable|string|max:50',
            'status_pernikahan' => 'nullable|string|max:50',
            'alamat_domisili'   => 'nullable|string',
            'alamat_ktp'        => 'nullable|string',

            'jenjang'           => 'nullable|in:S1,S2,S3',
            'akreditas'         => 'nullable|string|max:10',
            'no_ijazah'         => 'nullable|string|max:100',
            'ipk'               => 'nullable|numeric|min:0|max:4',

            'jenjang_2'         => 'nullable|in:S1,S2,S3',
            'akreditas_2'       => 'nullable|string|max:10',
            'no_ijazah_2'       => 'nullable|string|max:100',
            'ipk_2'             => 'nullable|numeric|min:0|max:4',

            'jenjang_3'         => 'nullable|in:S1,S2,S3',
            'akreditas_3'       => 'nullable|string|max:10',
            'no_ijazah_3'       => 'nullable|string|max:100',
            'ipk_3'             => 'nullable|numeric|min:0|max:4',

            'kategori_sertifikat' => 'nullable|in:kompetensi,keahlian_khusus',
            'jenis_tes_bahasa'    => 'nullable|in:PBT,TOEFL_ITP,EPrT,CBT,IBT,IELTS,AcEPT',
            'skor_bahasa'         => 'nullable|numeric|min:0',
            'tanggal_tes_bahasa'  => 'nullable|date',

            'jabatan_akademik'  => 'nullable|in:asisten_ahli,lektor,lektor_kepala,guru_besar,non_jabatan',
            'h_index'           => 'nullable|integer|min:0',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'email.required' => 'Kolom email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'nama.required'  => 'Kolom nama wajib diisi.',

            'tanggal_lahir.date'    => 'Format tanggal lahir tidak valid.',
            'tanggal_tes_bahasa.date' => 'Format tanggal tes bahasa tidak valid.',
            'jenis_kelamin.in'      => 'Jenis kelamin harus L atau P.',
            'jenjang.in'            => 'Jenjang harus S1, S2, atau S3.',
            'jenjang_2.in'          => 'Jenjang_2 harus S1, S2, atau S3.',
            'jenjang_3.in'          => 'Jenjang_3 harus S1, S2, atau S3.',
            'jabatan_akademik.in'   => 'Jabatan akademik tidak valid.',
            'jenis_tes_bahasa.in'   => 'Jenis tes bahasa tidak valid.',
            'kategori_sertifikat.in'=> 'Kategori sertifikat harus kompetensi atau keahlian_khusus.',
        ];
    }

    // ── Private Helpers ─────────────────────────────────────────────────

    /**
     * Cari nilai dari beberapa kemungkinan nama kolom.
     * Mengembalikan null jika semua kosong.
     */
    private function col(array $row, array $keys): mixed
    {
        foreach ($keys as $key) {
            $val = $row[$key] ?? null;
            if ($val !== null && trim((string) $val) !== '') {
                return $val;
            }
        }
        return null;
    }

    /**
     * Konversi Excel date serial atau string tanggal format apapun
     * menjadi string 'Y-m-d' yang siap divalidasi Laravel.
     */
    private function resolveDate(mixed $value): ?string
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        // Angka → Excel date serial
        if (is_numeric($value)) {
            try {
                $date = ExcelDate::excelToDateTimeObject((float) $value);
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        $str = trim((string) $value);

        // Format Indonesia: d/m/Y atau d-m-Y
        foreach (['d/m/Y', 'd-m-Y'] as $fmt) {
            $dt = \DateTime::createFromFormat($fmt, $str);
            if ($dt && $dt->format($fmt) === $str) {
                return $dt->format('Y-m-d');
            }
        }

        // Fallback: strtotime (Y-m-d, m/d/Y, dll.)
        $ts = strtotime($str);
        return $ts !== false ? date('Y-m-d', $ts) : $str;
    }
}