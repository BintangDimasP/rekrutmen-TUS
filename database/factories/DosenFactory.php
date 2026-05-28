<?php

namespace Database\Factories;

use App\Models\Prodi;
use Illuminate\Database\Eloquent\Factories\Factory;

class DosenFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama'        => fake()->name(),
            'kode'        => strtoupper(fake()->unique()->lexify('???')),
            'nip'         => fake()->unique()->numerify('##############'),
            'nidn'        => fake()->unique()->numerify('##########'),
            'email'       => '-',
            'prodi_id'    => Prodi::factory(),
            'is_penguji'  => false,
            'is_kaprodi'  => false,
        ];
    }
}
