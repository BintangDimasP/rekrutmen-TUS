<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_forgot_password_dapat_ditampilkan(): void
    {
        $response = $this->get('/forgot-password');
        $response->assertStatus(200);
    }

    public function test_kirim_otp_berhasil_untuk_email_pelamar(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'pelamar@gmail.com',
            'role' => 'pelamar',
        ]);

        $response = $this->post('/forgot-password/send-otp', [
            'email' => 'pelamar@gmail.com',
        ]);

        $response->assertRedirect(route('password.otp.form'));
        $this->assertDatabaseHas('password_reset_otps', ['email' => 'pelamar@gmail.com']);
    }

    public function test_kirim_otp_gagal_untuk_email_tidak_terdaftar(): void
    {
        $response = $this->post('/forgot-password/send-otp', [
            'email' => 'tidakada@gmail.com',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_kirim_otp_diblokir_untuk_domain_internal(): void
    {
        $user = User::factory()->create([
            'email' => 'dosen@pengajar.telkomuniversity.ac.id',
            'role' => 'penguji',
        ]);

        $response = $this->post('/forgot-password/send-otp', [
            'email' => 'dosen@pengajar.telkomuniversity.ac.id',
        ]);

        $response->assertRedirect(route('password.otp.blocked'));
    }

    public function test_verifikasi_otp_gagal_dengan_kode_salah(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'pelamar@gmail.com',
            'role' => 'pelamar',
        ]);

        $this->post('/forgot-password/send-otp', ['email' => 'pelamar@gmail.com']);

        $response = $this->withSession(['fp_email' => 'pelamar@gmail.com'])
            ->post('/forgot-password/verify-otp', ['otp' => '000000']);

        $response->assertSessionHasErrors(['otp']);
    }

    public function test_reset_password_berhasil(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'pelamar2@gmail.com',
            'role' => 'pelamar',
            'password' => Hash::make('oldpassword'),
        ]);

        $resetToken = \Illuminate\Support\Str::random(64);
        \Illuminate\Support\Facades\DB::table('password_reset_otps')->insert([
            'email' => 'pelamar2@gmail.com',
            'otp_hash' => Hash::make('123456'),
            'expires_at' => now()->addMinutes(10),
            'verified_at' => now(),
            'reset_token' => $resetToken,
            'attempts' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->withSession([
            'fp_email' => 'pelamar2@gmail.com',
            'fp_reset_token' => $resetToken,
        ])->post('/forgot-password/reset', [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect('/login');
        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }
}
