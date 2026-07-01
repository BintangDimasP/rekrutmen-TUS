<?php

namespace Tests\Feature;

use App\Models\Pelamar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class VerifikasiOtpTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Pelamar $pelamar;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'pelamar', 'email_verified_at' => null]);
        $this->pelamar = Pelamar::factory()->create(['user_id' => $this->user->id, 'phone_verified_at' => null]);
    }

    // ── Verifikasi Email ─────────────────────────────────────────

    public function test_kirim_otp_email_berhasil(): void
    {
        $response = $this->actingAs($this->user)->postJson(route('email.otp.send'));

        $response->assertStatus(200);
        $response->assertJson(['message' => 'OTP berhasil dikirim ke email Anda.']);
        $this->assertDatabaseHas('email_verification_otps', ['email' => $this->user->email]);
    }

    public function test_verifikasi_email_otp_valid(): void
    {
        $otp = '123456';
        DB::table('email_verification_otps')->insert([
            'email' => $this->user->email,
            'otp_hash' => Hash::make($otp),
            'expires_at' => now()->addMinutes(5),
            'attempts' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->postJson(route('email.otp.verify'), ['otp' => $otp]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Email berhasil diverifikasi!']);
        $this->user->refresh();
        $this->assertNotNull($this->user->email_verified_at);
    }

    public function test_verifikasi_email_otp_salah(): void
    {
        DB::table('email_verification_otps')->insert([
            'email' => $this->user->email,
            'otp_hash' => Hash::make('123456'),
            'expires_at' => now()->addMinutes(5),
            'attempts' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->postJson(route('email.otp.verify'), ['otp' => '999999']);

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Kode OTP tidak valid.']);
    }

    public function test_verifikasi_email_otp_kedaluwarsa(): void
    {
        DB::table('email_verification_otps')->insert([
            'email' => $this->user->email,
            'otp_hash' => Hash::make('123456'),
            'expires_at' => now()->subMinutes(1),
            'attempts' => 0,
            'created_at' => now()->subMinutes(6),
            'updated_at' => now()->subMinutes(6),
        ]);

        $response = $this->actingAs($this->user)->postJson(route('email.otp.verify'), ['otp' => '123456']);

        $response->assertStatus(400);
        $response->assertJsonFragment(['message' => 'Kode OTP sudah kadaluarsa. Silakan minta ulang.']);
    }

    // ── Verifikasi WhatsApp ──────────────────────────────────────

    public function test_verifikasi_whatsapp_otp_valid(): void
    {
        $phone = $this->pelamar->no_telepon;
        // Normalize: replace leading 0 with 62
        $normalized = '62' . substr($phone, 1);

        $otp = '654321';
        DB::table('phone_verification_otps')->insert([
            'phone' => $normalized,
            'otp_hash' => Hash::make($otp),
            'expires_at' => now()->addMinutes(5),
            'attempts' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->postJson(route('phone.otp.verify'), ['otp' => $otp]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Nomor WhatsApp berhasil diverifikasi!']);
        $this->pelamar->refresh();
        $this->assertNotNull($this->pelamar->phone_verified_at);
    }

    public function test_verifikasi_whatsapp_otp_salah(): void
    {
        $phone = $this->pelamar->no_telepon;
        $normalized = '62' . substr($phone, 1);

        DB::table('phone_verification_otps')->insert([
            'phone' => $normalized,
            'otp_hash' => Hash::make('654321'),
            'expires_at' => now()->addMinutes(5),
            'attempts' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->postJson(route('phone.otp.verify'), ['otp' => '111111']);

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Kode OTP tidak valid.']);
    }
}
