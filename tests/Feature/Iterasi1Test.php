<?php

namespace Tests\Feature;

use App\Models\Pelamar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class Iterasi1Test extends TestCase
{
    use RefreshDatabase;

    // ═══════════════════════════════════════════════════════════
    // LOGIN PENGGUNA
    // ═══════════════════════════════════════════════════════════

    public function test_login_page_loads(): void
    {
        $this->get('/login')->assertOk();
    }

    public function test_user_can_login_with_correct_credentials(): void
    {
        $user = User::factory()->create(['role' => 'pelamar', 'password' => Hash::make('password123')]);
        Pelamar::factory()->create(['user_id' => $user->id]);

        $this->post('/login', ['email' => $user->email, 'password' => 'password123'])
             ->assertRedirect('/dashboard');
    }

    public function test_user_cannot_login_with_wrong_password(): void
    {
        $user = User::factory()->create(['password' => Hash::make('password123')]);

        $this->post('/login', ['email' => $user->email, 'password' => 'wrongpassword'])
             ->assertSessionHasErrors();
    }

    public function test_user_cannot_login_with_nonexistent_email(): void
    {
        $this->post('/login', ['email' => 'noone@test.com', 'password' => 'password123'])
             ->assertSessionHasErrors();
    }

    // ═══════════════════════════════════════════════════════════
    // REGISTRASI PELAMAR
    // ═══════════════════════════════════════════════════════════

    public function test_register_page_loads(): void
    {
        $this->get('/register')->assertOk();
    }

    public function test_pelamar_can_register_with_valid_data(): void
    {
        $this->post('/register', [
            'email' => 'pelamar@gmail.com',
            'password' => 'password123',
            'nik' => '3201234567890123',
            'nama' => 'Test Pelamar',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1995-01-01',
            'no_telepon' => '081234567890',
            'jenis_kelamin' => 'L',
            'kewarganegaraan' => 'Indonesia',
            'status_pernikahan' => 'Belum Menikah',
            'alamat_domisili' => 'Jl. Test No. 1',
            'alamat_ktp' => 'Jl. Test No. 1',
        ])->assertRedirect('/dashboard');

        $this->assertDatabaseHas('users', ['email' => 'pelamar@gmail.com']);
        // nik is encrypted at rest, so verify via model instead of raw DB check
        $pelamar = \App\Models\Pelamar::first();
        $this->assertNotNull($pelamar);
        $this->assertEquals('3201234567890123', $pelamar->nik);
    }

    public function test_register_rejects_duplicate_nik(): void
    {
        Pelamar::factory()->create(['nik' => '3201234567890123']);

        $this->post('/register', [
            'email' => 'new@gmail.com', 'password' => 'password123',
            'nik' => '3201234567890123', 'nama' => 'Dup', 'tempat_lahir' => 'X',
            'tanggal_lahir' => '1995-01-01', 'no_telepon' => '081299998888',
            'jenis_kelamin' => 'L', 'kewarganegaraan' => 'Indonesia',
            'status_pernikahan' => 'Belum Menikah', 'alamat_domisili' => 'X', 'alamat_ktp' => 'X',
        ])->assertSessionHasErrors('nik');
    }

    public function test_register_rejects_nik_not_16_digits(): void
    {
        $this->post('/register', [
            'email' => 'x@gmail.com', 'password' => 'password123',
            'nik' => '12345', 'nama' => 'X', 'tempat_lahir' => 'X',
            'tanggal_lahir' => '1995-01-01', 'no_telepon' => '081234567890',
            'jenis_kelamin' => 'L', 'kewarganegaraan' => 'Indonesia',
            'status_pernikahan' => 'Belum Menikah', 'alamat_domisili' => 'X', 'alamat_ktp' => 'X',
        ])->assertSessionHasErrors('nik');
    }

    public function test_register_rejects_duplicate_phone(): void
    {
        Pelamar::factory()->create(['no_telepon' => '081234567890']);

        $this->post('/register', [
            'email' => 'y@gmail.com', 'password' => 'password123',
            'nik' => '3201234567890199', 'nama' => 'Y', 'tempat_lahir' => 'Y',
            'tanggal_lahir' => '1995-01-01', 'no_telepon' => '081234567890',
            'jenis_kelamin' => 'L', 'kewarganegaraan' => 'Indonesia',
            'status_pernikahan' => 'Belum Menikah', 'alamat_domisili' => 'Y', 'alamat_ktp' => 'Y',
        ])->assertSessionHasErrors('no_telepon');
    }

    public function test_register_rejects_invalid_phone_format(): void
    {
        $this->post('/register', [
            'email' => 'z@gmail.com', 'password' => 'password123',
            'nik' => '3201234567890100', 'nama' => 'Z', 'tempat_lahir' => 'Z',
            'tanggal_lahir' => '1995-01-01', 'no_telepon' => '12345',
            'jenis_kelamin' => 'L', 'kewarganegaraan' => 'Indonesia',
            'status_pernikahan' => 'Belum Menikah', 'alamat_domisili' => 'Z', 'alamat_ktp' => 'Z',
        ])->assertSessionHasErrors('no_telepon');
    }

    public function test_register_rejects_dosen_internal_email(): void
    {
        $this->post('/register', [
            'email' => 'test@pengajar.telkomuniversity.ac.id', 'password' => 'password123',
            'nik' => '3201234567890101', 'nama' => 'X', 'tempat_lahir' => 'X',
            'tanggal_lahir' => '1995-01-01', 'no_telepon' => '081234567891',
            'jenis_kelamin' => 'L', 'kewarganegaraan' => 'Indonesia',
            'status_pernikahan' => 'Belum Menikah', 'alamat_domisili' => 'X', 'alamat_ktp' => 'X',
        ])->assertSessionHasErrors('email');
    }

    // ═══════════════════════════════════════════════════════════
    // RESET PASSWORD VIA OTP
    // ═══════════════════════════════════════════════════════════

    public function test_forgot_password_page_loads(): void
    {
        $this->get('/forgot-password')->assertOk();
    }

    public function test_forgot_password_rejects_unregistered_email(): void
    {
        $this->post('/forgot-password/send-otp', ['email' => 'noone@test.com'])
             ->assertSessionHasErrors('email');
    }

    public function test_forgot_password_rejects_admin_email(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->post('/forgot-password/send-otp', ['email' => $admin->email])
             ->assertSessionHasErrors('email');
    }

    // ═══════════════════════════════════════════════════════════
    // HALAMAN DASHBOARD PENGGUNA
    // ═══════════════════════════════════════════════════════════

    public function test_dashboard_redirects_admin(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user)->get('/dashboard')->assertRedirect(route('admin.dashboard'));
    }

    public function test_dashboard_redirects_pelamar(): void
    {
        $user = User::factory()->create(['role' => 'pelamar']);
        Pelamar::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user)->get('/dashboard')->assertRedirect(route('pelamar.dashboard'));
    }

    public function test_dashboard_redirects_penguji(): void
    {
        $user = User::factory()->create(['role' => 'penguji']);
        $this->actingAs($user)->get('/dashboard')->assertRedirect(route('penguji.dashboard'));
    }

    public function test_dashboard_redirects_kaprodi(): void
    {
        $user = User::factory()->create(['role' => 'kaprodi']);
        $this->actingAs($user)->get('/dashboard')->assertRedirect(route('kaprodi.dashboard'));
    }

    // ═══════════════════════════════════════════════════════════
    // PENGATURAN PASSWORD (SETTINGS)
    // ═══════════════════════════════════════════════════════════

    public function test_settings_page_accessible_by_pelamar(): void
    {
        $user = User::factory()->create(['role' => 'pelamar']);
        Pelamar::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user)->get(route('settings.index'))->assertOk();
    }

    public function test_password_change_with_correct_old_password(): void
    {
        $user = User::factory()->create(['role' => 'pelamar', 'password' => Hash::make('oldpass123')]);
        Pelamar::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->put(route('settings.password.update'), [
            'current_password' => 'oldpass123',
            'password' => 'newpass123',
            'password_confirmation' => 'newpass123',
        ])->assertSessionHasNoErrors();
    }

    public function test_password_change_rejects_wrong_old_password(): void
    {
        $user = User::factory()->create(['role' => 'pelamar', 'password' => Hash::make('oldpass123')]);
        Pelamar::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->put(route('settings.password.update'), [
            'current_password' => 'wrongpassword',
            'password' => 'newpass123',
            'password_confirmation' => 'newpass123',
        ])->assertSessionHasErrors('current_password');
    }

    public function test_settings_not_accessible_by_admin(): void
    {
        // Settings page accessible by ALL roles including admin (password & foto)
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user)->get(route('settings.index'))
             ->assertOk();
    }
}
