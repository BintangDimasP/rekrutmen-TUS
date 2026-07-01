<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_pengaturan_dapat_ditampilkan(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/settings');
        $response->assertStatus(200);
    }

    public function test_ubah_password_berhasil(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('passwordlama'),
        ]);

        $response = $this->actingAs($user)->put('/settings/password', [
            'current_password' => 'passwordlama',
            'password' => 'passwordbaru123',
            'password_confirmation' => 'passwordbaru123',
        ]);

        $response->assertSessionHas('success');
        $this->assertTrue(Hash::check('passwordbaru123', $user->fresh()->password));
    }

    public function test_ubah_password_gagal_password_lama_salah(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('passwordlama'),
        ]);

        $response = $this->actingAs($user)->put('/settings/password', [
            'current_password' => 'passwordsalah',
            'password' => 'passwordbaru123',
            'password_confirmation' => 'passwordbaru123',
        ]);

        $response->assertSessionHasErrors(['current_password']);
    }

    public function test_ubah_password_gagal_kurang_dari_8_karakter(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('passwordlama'),
        ]);

        $response = $this->actingAs($user)->put('/settings/password', [
            'current_password' => 'passwordlama',
            'password' => '1234',
            'password_confirmation' => '1234',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_ubah_password_gagal_konfirmasi_tidak_cocok(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('passwordlama'),
        ]);

        $response = $this->actingAs($user)->put('/settings/password', [
            'current_password' => 'passwordlama',
            'password' => 'passwordbaru123',
            'password_confirmation' => 'berbeda123',
        ]);

        $response->assertSessionHasErrors(['password']);
    }
}
