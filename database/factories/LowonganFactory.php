<?php

namespace Database\Factories;

use App\Models\Prodi;
use Illuminate\Database\Eloquent\Factories\Factory;

class LowonganFactory extends Factory
{
    public function definition(): array
    {
        return [
            'prodi_id'        => Prodi::factory(),
            'nama_posisi'     => 'Dosen ' . fake()->randomElement(['Teknik Informatika', 'Sistem Informasi', 'Matematika']),
            'deskripsi'       => fake()->paragraph(),
            'kuota'           => fake()->numberBetween(1, 10),
            'jenjang_minimal' => fake()->randomElement(['S2', 'S3']),
            'minimal_ipk'     => fake()->randomFloat(2, 2.75, 3.50),
            'tanggal_tutup'   => now()->addDays(30)->format('Y-m-d'),
            'status'          => 'aktif',
        ];
    }
}
