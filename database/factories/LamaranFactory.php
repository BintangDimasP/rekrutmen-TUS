<?php

namespace Database\Factories;

use App\Models\Lowongan;
use App\Models\Pelamar;
use Illuminate\Database\Eloquent\Factories\Factory;

class LamaranFactory extends Factory
{
    public function definition(): array
    {
        return [
            'pelamar_id'  => Pelamar::factory(),
            'lowongan_id' => Lowongan::factory(),
            'status'      => 'menunggu',
        ];
    }
}
