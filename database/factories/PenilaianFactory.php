<?php

namespace Database\Factories;

use App\Models\JadwalSeleksi;
use Illuminate\Database\Eloquent\Factories\Factory;

class PenilaianFactory extends Factory
{
    public function definition(): array
    {
        return [
            'jadwal_seleksi_id' => JadwalSeleksi::factory(),
            'kategori_1'        => 4.0,
            'kategori_2'        => 4.0,
            'kategori_3'        => 4.0,
            'kategori_4'        => 4.0,
            'kategori_5'        => 4.0,
            'total_nilai'       => 4.0,
            'rekomendasi'       => 'direkomendasikan',
            'prodi_tujuan'      => 'Teknik Informatika',
            'catatan'           => 'Baik',
            'detail_nilai'      => json_encode(['k1_item_1' => 4, 'k1_item_2' => 4]),
        ];
    }
}
