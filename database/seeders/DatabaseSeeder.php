<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat akun Admin utama
        User::create([
            'name' => 'bintang',
            'email' => 'admin@telu.ac.id',
            'password' => bcrypt('12345678'), // set password default yang mudah diingat
            'role' => 'admin',
        ]);
    }
}
