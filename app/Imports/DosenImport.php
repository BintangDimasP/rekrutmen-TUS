<?php

namespace App\Imports;

use App\Models\Dosen;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class DosenImport implements ToModel, WithHeadingRow, WithValidation
{
    protected $prodi_id;

    public function __construct($prodi_id)
    {
        $this->prodi_id = $prodi_id;
    }

    public function model(array $row)
    {
        $nama = $row['nama'];
        $kode = strtoupper($row['kode']);

        // Generate email: ambil 2 kata pertama, gabungkan, tambah @telu.ac.id
        $parts = preg_split('/\s+/', trim($nama));
        $emailPrefix = strtolower(implode('', array_slice($parts, 0, 2)));
        $email = $emailPrefix . '@telu.ac.id';

        // Handle unique email jika sudah ada di tabel dosens
        $counter = 1;
        while (Dosen::where('email', $email)->exists()) {
            $email = $emailPrefix . $counter . '@telu.ac.id';
            $counter++;
        }

        // Hanya simpan ke tabel dosens.
        // Akun login (User) akan dibuat otomatis nanti saat dosen ditunjuk sebagai Kaprodi atau Penguji.
        return new Dosen([
            'nama'       => $nama,
            'kode'       => $kode,
            'nip'        => $row['nip'] ?? null,
            'nidn'       => $row['nidn'] ?? null,
            'email'      => $email,
            'prodi_id'   => $this->prodi_id,
            'is_kaprodi' => false,
            'is_penguji' => false,
        ]);
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:50|unique:dosens,kode',
            'nip'  => 'nullable|string|max:50',
            'nidn' => 'nullable|string|max:50',
        ];
    }
}
