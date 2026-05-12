<?php

namespace App\Imports;

use App\Models\Pelamar;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PelamarImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Generate unique email for pelamar
        $baseEmail = strtolower(str_replace(' ', '.', $row['nama'])) . '@pelamar.telkomuniversity.ac.id';
        $email = $baseEmail;
        $counter = 1;
        
        while (User::where('email', $email)->exists()) {
            $email = strtolower(str_replace(' ', '.', $row['nama'])) . $counter . '@pelamar.telkomuniversity.ac.id';
            $counter++;
        }

        // Create user account for pelamar
        $user = User::create([
            'name'     => $row['nama'],
            'email'    => $email,
            'password' => Hash::make('pelamar123'),
            'password_plain' => 'pelamar123',
            'role'     => 'pelamar',
        ]);

        // Create pelamar record
        return new Pelamar([
            'user_id'           => $user->id,
            
            // Data Diri
            'nik'               => $row['nik'] ?? null,
            'nama'              => $row['nama'],
            'tempat_lahir'      => $row['tempat_lahir'] ?? null,
            'tanggal_lahir'     => $row['tanggal_lahir'] ?? null,
            'no_telepon'        => $row['no_telepon'] ?? null,
            'jenis_kelamin'     => $row['jenis_kelamin'] ?? null,
            'alamat'            => $row['alamat'] ?? null,

            // Riwayat Pendidikan Level 1
            'jenjang'           => $row['jenjang'] ?? null,
            'institusi'         => $row['institusi'] ?? null,
            'prodi_pendidikan'  => $row['prodi_pendidikan'] ?? null,
            'ipk'               => $row['ipk'] ?? null,

            // Riwayat Pendidikan Level 2
            'jenjang_2'         => $row['jenjang_2'] ?? null,
            'institusi_2'       => $row['institusi_2'] ?? null,
            'prodi_pendidikan_2' => $row['prodi_pendidikan_2'] ?? null,
            'ipk_2'             => $row['ipk_2'] ?? null,

            // Riwayat Pendidikan Level 3
            'jenjang_3'         => $row['jenjang_3'] ?? null,
            'institusi_3'       => $row['institusi_3'] ?? null,
            'prodi_pendidikan_3' => $row['prodi_pendidikan_3'] ?? null,
            'ipk_3'             => $row['ipk_3'] ?? null,

            // Dokumen Pendukung
            'kategori_sertifikat' => $row['kategori_sertifikat'] ?? null,
            'jenis_tes_bahasa'  => $row['jenis_tes_bahasa'] ?? null,
            'skor_bahasa'       => $row['skor_bahasa'] ?? null,
            'tanggal_tes_bahasa' => $row['tanggal_tes_bahasa'] ?? null,

            // Riwayat Akademik
            'nidn'              => $row['nidn'] ?? null,
            'homebase'          => $row['homebase'] ?? null,
            'jabatan_akademik'  => $row['jabatan_akademik'] ?? null,
            'minat_riset'       => $row['minat_riset'] ?? null,
            'h_index'           => $row['h_index'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nama'              => 'required|string|max:255',
            'nik'               => 'nullable|string|max:16|unique:pelamars,nik',
            'no_telepon'        => 'nullable|string|max:20',
            'jenis_kelamin'     => 'nullable|in:L,P',
            'jenjang'           => 'nullable|in:S1,S2,S3',
            'jenjang_2'         => 'nullable|in:S1,S2,S3',
            'jenjang_3'         => 'nullable|in:S1,S2,S3',
            'ipk'               => 'nullable|numeric|min:0|max:4',
            'ipk_2'             => 'nullable|numeric|min:0|max:4',
            'ipk_3'             => 'nullable|numeric|min:0|max:4',
            'skor_bahasa'       => 'nullable|numeric|min:0',
            'kategori_sertifikat' => 'nullable|in:kompetensi,keahlian_khusus',
            'jenis_tes_bahasa'  => 'nullable|in:PBT,TOEFL_ITP,EPrT,CBT,IBT,IELTS,AcEPT',
            'jabatan_akademik'  => 'nullable|in:asisten_ahli,lektor,lektor_kepala,profesor',
            'h_index'           => 'nullable|integer|min:0',
        ];
    }
}
