<?php

namespace Database\Factories;

use App\Models\Dosen;
use App\Models\Lowongan;
use App\Models\Pelamar;
use Illuminate\Database\Eloquent\Factories\Factory;

class JadwalSeleksiFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tanggal'      => now()->addDays(7)->format('Y-m-d'),
            'lowongan_id'  => Lowongan::factory(),
            'pelamar_id'   => Pelamar::factory(),
            'penguji_id'   => Dosen::factory()->state(['is_penguji' => true]),
            'tipe_seleksi' => 'micro_teaching',
            'sesi'         => 1,
        ];
    }
}
