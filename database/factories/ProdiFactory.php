<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProdiFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama' => fake()->randomElement(['Teknik Informatika', 'Sistem Informasi', 'Teknik Elektro', 'Manajemen']) . ' ' . fake()->unique()->numerify('##'),
            'kode' => strtoupper(fake()->unique()->lexify('????')),
        ];
    }
}
