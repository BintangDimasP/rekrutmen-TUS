<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'email'             => 'testpelamar@gmail.com',
            'password'          => 'password',
            'password_confirmation' => 'password',
            // Step 2 required fields
            'nik'               => '3201011501900099',
            'nama'              => 'Test Pelamar',
            'tempat_lahir'      => 'Jakarta',
            'tanggal_lahir'     => '1995-01-15',
            'no_telepon'        => '081234567890',
            'jenis_kelamin'     => 'L',
            'kewarganegaraan'   => 'WNI',
            'status_pernikahan' => 'Belum Menikah',
            'alamat_domisili'   => 'Jl. Test No. 1',
            'alamat_ktp'        => 'Jl. Test No. 1',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }
}
