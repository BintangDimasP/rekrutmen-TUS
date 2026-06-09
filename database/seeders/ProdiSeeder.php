<?php

namespace Database\Seeders;

use App\Models\Prodi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProdiSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $prodis = [
            ['nama' => 'Teknik Informatika',             'kode' => 'IF'],
            ['nama' => 'Sistem Informasi',               'kode' => 'SI'],
            ['nama' => 'Teknik Elektro',                 'kode' => 'TE'],
            ['nama' => 'Teknik Telekomunikasi',          'kode' => 'TT'],
            ['nama' => 'Teknik Komputer',                'kode' => 'TK'],
            ['nama' => 'Ilmu Komunikasi',                'kode' => 'IK'],
            ['nama' => 'Manajemen Bisnis Telekomunikasi dan Informatika', 'kode' => 'MBTI'],
            ['nama' => 'Desain Komunikasi Visual',       'kode' => 'DKV'],
            ['nama' => 'Administrasi Bisnis',            'kode' => 'AB'],
            ['nama' => 'Teknik Industri',                'kode' => 'TI'],
        ];

        foreach ($prodis as $prodi) {
            Prodi::firstOrCreate(
                ['kode' => $prodi['kode']],
                ['nama' => $prodi['nama']]
            );
        }

        $this->command->info('✓ Prodi seeded: ' . count($prodis) . ' prodi.');
    }
}
