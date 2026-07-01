<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_login_dapat_ditampilkan(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_login_berhasil_dengan_kredensial_valid(): void
    {
        $user = User::factory()->create([
            'role' => 'pelamar',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));
    }

    public function test_login_gagal_dengan_password_salah(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password123')]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'passwordsalah',
        ]);

        $this->assertGuest();
    }

    public function test_login_gagal_dengan_email_tidak_terdaftar(): void
    {
        $this->post('/login', [
            'email' => 'tidakada@example.com',
            'password' => 'password123',
        ]);

        $this->assertGuest();
    }

    public function test_login_gagal_dengan_field_kosong(): void
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }
}
