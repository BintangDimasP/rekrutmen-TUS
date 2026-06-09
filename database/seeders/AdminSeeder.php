<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $admins = [
            [
                'name'     => 'Administrator',
                'username' => 'admin',
                'password' => 'admin123456',
            ],
        ];

        foreach ($admins as $data) {
            $email = strtolower($data['username']) . '@admin.telkomuniversity.ac.id';

            User::firstOrCreate(
                ['email' => $email],
                [
                    'name'     => $data['name'],
                    'email'    => $email,
                    'password' => Hash::make($data['password']),
                    'role'     => 'admin',
                ]
            );
        }

        $this->command->info('✓ Admin seeded:');
        $this->command->table(
            ['Nama', 'Email', 'Password'],
            collect($admins)->map(fn($a) => [
                $a['name'],
                strtolower($a['username']) . '@admin.telkomuniversity.ac.id',
                $a['password'],
            ])->toArray()
        );
    }
}
