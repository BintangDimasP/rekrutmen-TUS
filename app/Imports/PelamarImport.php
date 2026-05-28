<?php

namespace App\Imports;

use App\Models\Pelamar;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class PelamarImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    /**
     * FUNGSI KUNCI: Override bawaan SkipsEmptyRows.
     * Jika 'nama' kosong, sistem akan otomatis membuang baris ini 
     * sebelum masuk ke rules() dan database.
     */
    public function isEmptyWhen(array $row): bool
    {
        return !isset($row['nama']) || trim((string)$row['nama']) === '';
    }

    public function model(array $row)
    {
        // Cast numeric fields yang sering dibaca Excel sebagai number
        $row['nik']        = isset($row['nik']) ? (string) $row['nik'] : null;
        $row['no_telepon'] = isset($row['no_telepon']) ? (string) $row['no_telepon'] : null;
        $row['no_ijazah']  = isset($row['no_ijazah']) ? (string) $row['no_ijazah'] : null;
        $row['no_ijazah_2'] = isset($row['no_ijazah_2']) ? (string) $row['no_ijazah_2'] : null;
        $row['no_ijazah_3'] = isset($row['no_ijazah_3']) ? (string) $row['no_ijazah_3'] : null;
        $row['nidn']       = isset($row['nidn']) ? (string) $row['nidn'] : null;

        // Generate unique email for pelamar
        $baseEmail = strtolower(str_replace(' ', '.', $row['nama'])) . '@gmail.com';
        $email = $baseEmail;
        $counter = 1;

        while (User::where('email', $email)->exists()) {
            $email = strtolower(str_replace(' ', '.', $row['nama'])) . $counter . '@gmail.com';
            $counter++;
        }

        // Create user account for pelamar
        $user = User::create([
            'name'           => $row['nama'],
            'email'          => $email,
            'password'       => Hash::make('pelamar123'),
            'password_plain' => 'pelamar123',
            'role'           => 'pelamar',
        ]);

        // Create pelamar record
        return new Pelamar([
            'user_id' => $user->id,

            // ── Data Diri ──────────────────────────────────────────
            'nik'               => $row['nik']              ?? null,
            'nama'              => $row['nama'],
            'tempat_lahir'      => $row['tempat_lahir']     ?? null,
            'tanggal_lahir'     => $row['tanggal_lahir']    ?? null,
            'no_telepon'        => $row['no_telepon']       ?? null,
            'jenis_kelamin'     => $row['jenis_kelamin']    ?? null,
            'kewarganegaraan'   => $row['kewarganegaraan']  ?? null,
            'status_pernikahan' => $row['status_pernikahan'] ?? null,
            'alamat_domisili'   => $row['alamat_domisili']  ?? null,
            'alamat_ktp'        => $row['alamat_ktp']       ?? null,

            // ── Riwayat Pendidikan Level 1 ─────────────────────────
            'jenjang'           => $row['jenjang']          ?? null,
            'institusi'         => $row['institusi']        ?? null,
            'prodi_pendidikan'  => $row['prodi_pendidikan'] ?? null,
            'akreditas'         => $row['akreditas']        ?? null,
            'no_ijazah'         => $row['no_ijazah']        ?? null,
            'ipk'               => $row['ipk']              ?? null,

            // ── Riwayat Pendidikan Level 2 ─────────────────────────
            'jenjang_2'          => $row['jenjang_2']           ?? null,
            'institusi_2'        => $row['institusi_2']         ?? null,
            'prodi_pendidikan_2' => $row['prodi_pendidikan_2']  ?? null,
            'akreditas_2'        => $row['akreditas_2']         ?? null,
            'no_ijazah_2'        => $row['no_ijazah_2']         ?? null,
            'ipk_2'              => $row['ipk_2']               ?? null,

            // ── Riwayat Pendidikan Level 3 ─────────────────────────
            'jenjang_3'          => $row['jenjang_3']           ?? null,
            'institusi_3'        => $row['institusi_3']         ?? null,
            'prodi_pendidikan_3' => $row['prodi_pendidikan_3']  ?? null,
            'akreditas_3'        => $row['akreditas_3']         ?? null,
            'no_ijazah_3'        => $row['no_ijazah_3']         ?? null,
            'ipk_3'              => $row['ipk_3']               ?? null,

            // ── Dokumen Pendukung (file diabaikan saat import) ─────
            'kategori_sertifikat'  => $row['kategori_sertifikat']   ?? null,
            'jenis_tes_bahasa'     => $row['jenis_tes_bahasa']      ?? null,
            'skor_bahasa'          => $row['skor_bahasa']           ?? null,
            'tanggal_tes_bahasa'   => $row['tanggal_tes_bahasa']    ?? null,

            // ── Riwayat Akademik ───────────────────────────────────
            'nidn'              => $row['nidn']              ?? null,
            'homebase'          => $row['homebase']          ?? null,
            'jabatan_akademik'  => $row['jabatan_akademik']  ?? null,
            'minat_riset'       => $row['minat_riset']       ?? null,
            'h_index'           => $row['h_index']           ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            // Aturan kembali normal (bersih tanpa exclude_if)
            'nama'              => 'required|string|max:255',
            'nik'               => 'required|max:16|unique:pelamars,nik',
            'tempat_lahir'      => 'required|string|max:100',
            'tanggal_lahir'     => 'required|date',
            'no_telepon'        => 'required|max:20',
            'jenis_kelamin'     => 'required|in:L,P',

            // Opsional — Data Diri
            'kewarganegaraan'   => 'nullable|string|max:50',
            'status_pernikahan' => 'nullable|string|max:50',
            'alamat_domisili'   => 'nullable|string',
            'alamat_ktp'        => 'nullable|string',

            // Opsional — Pendidikan
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

            // Opsional — Dokumen & Bahasa
            'kategori_sertifikat' => 'nullable|in:kompetensi,keahlian_khusus',
            'jenis_tes_bahasa'    => 'nullable|in:PBT,TOEFL_ITP,EPrT,CBT,IBT,IELTS,AcEPT',
            'skor_bahasa'         => 'nullable|numeric|min:0',
            'tanggal_tes_bahasa'  => 'nullable|date',

            // Opsional — Akademik
            'jabatan_akademik'  => 'nullable|in:asisten_ahli,lektor,lektor_kepala,guru_besar,non_jabatan',
            'h_index'           => 'nullable|integer|min:0',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'nama.required'         => 'Kolom nama wajib diisi.',
            'nik.required'          => 'Kolom NIK wajib diisi.',
            'nik.unique'            => 'NIK sudah terdaftar di sistem.',
            'tempat_lahir.required' => 'Kolom tempat lahir wajib diisi.',
            'tanggal_lahir.required'=> 'Kolom tanggal lahir wajib diisi.',
            'no_telepon.required'   => 'Kolom no_telepon wajib diisi.',
            'jenis_kelamin.required'=> 'Kolom jenis_kelamin wajib diisi (L atau P).',
            'jenis_kelamin.in'      => 'Jenis kelamin harus L atau P.',
            'jenjang.in'            => 'Jenjang harus S1, S2, atau S3.',
            'jenjang_2.in'          => 'Jenjang_2 harus S1, S2, atau S3.',
            'jenjang_3.in'          => 'Jenjang_3 harus S1, S2, atau S3.',
            'jabatan_akademik.in'   => 'Jabatan akademik tidak valid.',
            'jenis_tes_bahasa.in'   => 'Jenis tes bahasa tidak valid.',
            'kategori_sertifikat.in'=> 'Kategori sertifikat harus kompetensi atau keahlian_khusus.',
        ];
    }
}