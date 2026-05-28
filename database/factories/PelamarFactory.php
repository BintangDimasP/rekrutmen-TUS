<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PelamarFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'       => User::factory()->state(['role' => 'pelamar']),
            'nik'           => fake()->unique()->numerify('################'),
            'nama'          => fake()->name(),
            'tempat_lahir'  => fake()->city(),
            'tanggal_lahir' => fake()->date('Y-m-d', '-25 years'),
            'no_telepon'    => '08' . fake()->numerify('#########'),
            'jenis_kelamin' => fake()->randomElement(['L', 'P']),
        ];
    }
}
