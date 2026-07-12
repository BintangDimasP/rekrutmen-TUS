<?php

/**
 * WHITE BOX TESTING - LUPA PASSWORD
 * Teknik     : Branch Coverage
 * Controller : ForgotPasswordOtpController
 * Routes     :
 *   POST /forgot-password/send-otp   → sendOtp()
 *   POST /forgot-password/verify-otp → verifyOtp()
 *   POST /forgot-password/reset      → resetPassword()
 *
 * ============================================================
 * ANALISIS BRANCH PADA KODE SUMBER:
 * ============================================================
 *
 * [sendOtp()]
 *   foreach BLOCKED_DOMAINS → if domain cocok redirect blocked      // Branch 1
 *   if (!$user || !in_array(role, ALLOWED_ROLES))                    // Branch 2
 *   if ($lastOtp) → if ($elapsedSec < COOLDOWN)                      // Branch 3 & 4
 *   try Mail::send / catch Throwable                                 // Branch 5
 *
 * [verifyOtp()]
 *   if (!$record)                                                    // Branch 6
 *   if (now() > expires_at)                                          // Branch 7
 *   if (attempts >= MAX_ATTEMPTS)                                    // Branch 8
 *   if (!Hash::check(otp))                                           // Branch 9
 *
 * [resetPassword()]
 *   if (!$record || now() > expires_at)                              // Branch 10
 *   validasi password (required, confirmed, min:8)                   // Branch 11
 *
 * ============================================================
 * PETA TEST CASE → BRANCH YANG DICAKUP:
 * ============================================================
 *
 * TC-01 : B1=F, B2=F, B3=F, B5=T  → happy path, OTP berhasil dikirim
 * TC-02 : B1=T                     → unhappy, domain internal diblokir
 * TC-03 : B2=T                     → unhappy, email tidak terdaftar
 * TC-04 : B3=T, B4=T               → unhappy, cooldown masih aktif
 * TC-05 : B3=T, B4=F               → happy, cooldown sudah selesai
 * TC-06 : B7=T                     → unhappy, OTP kedaluwarsa
 * TC-07 : B8=T                     → unhappy, percobaan melebihi batas
 * TC-08 : B9=T                     → unhappy, OTP tidak sesuai
 * TC-09 : B9=F                     → happy, OTP sesuai
 * TC-10 : B10=F, B11=T (valid)     → happy, reset password berhasil
 * TC-11 : B11=F                    → unhappy, password tidak memenuhi syarat
 */

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

// Helper: buat record OTP
function buatOtp(string $email, string $otp, array $override = []): void
{
    DB::table('password_reset_otps')->insert(array_merge([
        'email'      => $email,
        'otp_hash'   => Hash::make($otp),
        'expires_at' => now()->addMinutes(1),
        'attempts'   => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ], $override));
}

// Helper: buat user pelamar
function buatPelamar(string $email = 'pelamar@gmail.com'): User
{
    return User::factory()->create(['email' => $email, 'role' => 'pelamar']);
}

// ---------------------------------------------------------------
// TC-01 | Happy Path
// B1=F, B2=F, B3=F, B5=T : Email valid, user pelamar, kirim OTP berhasil
// ---------------------------------------------------------------
test('TC-01: Pelamar berhasil meminta reset password, sistem mengarahkan ke halaman verifikasi OTP', function () {
    Mail::fake();
    buatPelamar();

    $response = $this->post('/forgot-password/send-otp', [
        'email' => 'pelamar@gmail.com',
    ]);

    $response->assertRedirect(route('password.otp.form'));
    $this->assertDatabaseHas('password_reset_otps', ['email' => 'pelamar@gmail.com']);
});

// ---------------------------------------------------------------
// TC-02 | Unhappy Path
// B1=T : Domain cocok dengan BLOCKED_DOMAINS
// ---------------------------------------------------------------
test('TC-02: Pelamar gagal meminta reset password, sistem mengarahkan ke halaman blocked', function () {
    $response = $this->post('/forgot-password/send-otp', [
        'email' => 'user@pengajar.telkomuniversity.ac.id',
    ]);

    $response->assertRedirect(route('password.otp.blocked'));
});

// ---------------------------------------------------------------
// TC-03 | Unhappy Path
// B2=T : User tidak ditemukan di database
// ---------------------------------------------------------------
test('TC-03: Pelamar gagal meminta reset password, sistem menampilkan pesan error email tidak terdaftar', function () {
    $response = $this->post('/forgot-password/send-otp', [
        'email' => 'tidakada@gmail.com',
    ]);

    $response->assertSessionHasErrors('email');
});

// ---------------------------------------------------------------
// TC-04 | Unhappy Path
// B3=T, B4=T : Ada OTP sebelumnya, cooldown masih aktif (10 detik)
// ---------------------------------------------------------------
test('TC-04: Pelamar gagal meminta kirim ulang OTP, sistem mengarahkan ke halaman verifikasi OTP', function () {
    Mail::fake();
    $user = buatPelamar();
    buatOtp($user->email, '123456', ['created_at' => now()->subSeconds(10)]);

    $response = $this->post('/forgot-password/send-otp', [
        'email' => $user->email,
    ]);

    $response->assertRedirect(route('password.otp.form'));
});

// ---------------------------------------------------------------
// TC-05 | Happy Path
// B3=T, B4=F : Ada OTP sebelumnya, cooldown sudah selesai (90 detik lalu)
// ---------------------------------------------------------------
test('TC-05: Pelamar berhasil meminta kirim ulang OTP, sistem mengarahkan ke halaman verifikasi OTP', function () {
    Mail::fake();
    $user = buatPelamar();
    buatOtp($user->email, '123456', ['created_at' => now()->subSeconds(90)]);

    $response = $this->post('/forgot-password/send-otp', [
        'email' => $user->email,
    ]);

    $response->assertRedirect(route('password.otp.form'));
});

// ---------------------------------------------------------------
// TC-06 | Unhappy Path
// B7=T : OTP sudah melewati expires_at
// ---------------------------------------------------------------
test('TC-06: Pelamar gagal memverifikasi OTP, sistem menampilkan pesan error OTP sudah kedaluwarsa', function () {
    $user = buatPelamar();
    buatOtp($user->email, '123456', ['expires_at' => now()->subMinutes(5)]);

    $this->withSession(['fp_email' => $user->email]);

    $response = $this->post('/forgot-password/verify-otp', ['otp' => '123456']);

    $response->assertSessionHasErrors('otp');
});

// ---------------------------------------------------------------
// TC-07 | Unhappy Path
// B8=T : attempts >= MAX_ATTEMPTS (5)
// ---------------------------------------------------------------
test('TC-07: Pelamar gagal memverifikasi OTP, sistem mengarahkan ke halaman input email', function () {
    $user = buatPelamar();
    buatOtp($user->email, '123456', ['attempts' => 5]);

    $this->withSession(['fp_email' => $user->email]);

    $response = $this->post('/forgot-password/verify-otp', ['otp' => '999999']);

    $response->assertRedirect(route('password.otp.email'));
});

// ---------------------------------------------------------------
// TC-08 | Unhappy Path
// B9=T : OTP tidak cocok dengan hash
// ---------------------------------------------------------------
test('TC-08: Pelamar gagal memverifikasi OTP, sistem menampilkan pesan error OTP tidak sesuai', function () {
    $user = buatPelamar();
    buatOtp($user->email, '123456');

    $this->withSession(['fp_email' => $user->email]);

    $response = $this->post('/forgot-password/verify-otp', ['otp' => '000000']);

    $response->assertSessionHasErrors('otp');

    $record = DB::table('password_reset_otps')->where('email', $user->email)->first();
    expect($record->attempts)->toBe(1);
});

// ---------------------------------------------------------------
// TC-09 | Happy Path
// B9=F : OTP cocok → reset_token digenerate
// ---------------------------------------------------------------
test('TC-09: Pelamar berhasil memverifikasi OTP, sistem mengarahkan ke halaman reset password', function () {
    $user = buatPelamar();
    buatOtp($user->email, '123456');

    $this->withSession(['fp_email' => $user->email]);

    $response = $this->post('/forgot-password/verify-otp', ['otp' => '123456']);

    $response->assertRedirect(route('password.otp.reset.form'));

    $record = DB::table('password_reset_otps')->where('email', $user->email)->first();
    expect($record->verified_at)->not->toBeNull();
    expect($record->reset_token)->not->toBeNull();
});

// ---------------------------------------------------------------
// TC-10 | Happy Path
// B10=F, B11=valid : Token valid, password memenuhi syarat
// ---------------------------------------------------------------
test('TC-10: Pelamar berhasil memperbarui password, sistem mengarahkan ke halaman login', function () {
    $user       = buatPelamar();
    $resetToken = Str::random(64);

    DB::table('password_reset_otps')->insert([
        'email'       => $user->email,
        'otp_hash'    => Hash::make('123456'),
        'expires_at'  => now()->addMinutes(10),
        'verified_at' => now(),
        'reset_token' => $resetToken,
        'attempts'    => 0,
        'created_at'  => now(),
        'updated_at'  => now(),
    ]);

    $this->withSession([
        'fp_email'       => $user->email,
        'fp_reset_token' => $resetToken,
    ]);

    $response = $this->post('/forgot-password/reset', [
        'password'              => 'NewPassword123!',
        'password_confirmation' => 'NewPassword123!',
    ]);

    $response->assertRedirect(route('login'));
    $this->assertDatabaseMissing('password_reset_otps', ['email' => $user->email]);
    expect(Hash::check('NewPassword123!', $user->fresh()->password))->toBeTrue();
});

// ---------------------------------------------------------------
// TC-11 | Unhappy Path
// B11=F : Password terlalu pendek, tidak memenuhi syarat
// ---------------------------------------------------------------
test('TC-11: Pelamar gagal memperbarui password, sistem menampilkan pesan error validasi password', function () {
    $user       = buatPelamar();
    $resetToken = Str::random(64);

    DB::table('password_reset_otps')->insert([
        'email'       => $user->email,
        'otp_hash'    => Hash::make('123456'),
        'expires_at'  => now()->addMinutes(10),
        'verified_at' => now(),
        'reset_token' => $resetToken,
        'attempts'    => 0,
        'created_at'  => now(),
        'updated_at'  => now(),
    ]);

    $this->withSession([
        'fp_email'       => $user->email,
        'fp_reset_token' => $resetToken,
    ]);

    $response = $this->post('/forgot-password/reset', [
        'password'              => '123',
        'password_confirmation' => '123',
    ]);

    $response->assertSessionHasErrors('password');
});
