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
        // Email dosen biasa selalu '-' (tidak punya akses login)
        // Email akan di-generate otomatis saat ditunjuk sebagai penguji/kaprodi
        return new Dosen([
            'nama'       => $row['nama'],
            'kode'       => strtoupper($row['kode']),
            'nip'        => $row['nip'] ?? null,
            'nidn'       => $row['nidn'] ?? null,
            'email'      => '-',
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
