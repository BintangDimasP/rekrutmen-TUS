<?php

namespace App\Imports;

use App\Models\Dosen;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Row;

class DosenImport implements OnEachRow, WithHeadingRow, WithValidation
{
    protected int $prodi_id;

    public function __construct(int $prodi_id)
    {
        $this->prodi_id = $prodi_id;
    }

    /**
     * Dosen biasa TIDAK mendapat akun user.
     * Akun user hanya dibuat saat dosen ditunjuk sebagai penguji atau kaprodi.
     */
    public function onRow(Row $row): void
    {
        $data = $row->toArray();

        Dosen::create([
            'nama'       => $data['nama'],
            'kode'       => strtoupper($data['kode']),
            'nip'        => $data['nip'] ?? null,
            'nidn'       => $data['nidn'] ?? null,
            'email'      => '-',
            'prodi_id'   => $this->prodi_id,
            'is_kaprodi' => false,
            'is_penguji' => false,
        ]);
        // Sengaja tidak memanggil getOrCreateUser() — dosen biasa bukan user sistem
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
