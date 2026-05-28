<?php

namespace Tests\Feature\Auth;

use App\Mail\PasswordResetOtpMail;
use App\Models\Dosen;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Test: Forgot Password OTP flow
 *
 * Cakupan:
 *  - Step 1: Email — kirim OTP, validasi email terdaftar, exclude admin
 *  - Step 2: OTP — verifikasi OTP, expired, attempt limit
 *  - Step 3: Reset password — validasi token, ubah password, redirect login
 */
class ForgotPasswordOtpTest extends TestCase
{
    use RefreshDatabase;

    // ════════════════════════════════════════════════════════════
    // STEP 1: Email & Send OTP
    // ════════════════════════════════════════════════════════════

    public function test_forgot_password_email_page_loads(): void
    {
        $this->get(route('password.request'))->assertOk();
    }

    public function test_send_otp_succeeds_for_pelamar(): void
    {
        Mail::fake();
        $user = User::factory()->create([
            'role'  => 'pelamar',
            'email' => 'pelamar@example.com',
        ]);

        $this->post(route('password.otp.send'), ['email' => $user->email])
            ->assertRedirect(route('password.otp.form'));

        Mail::assertSent(PasswordResetOtpMail::class, fn($mail) => $mail->hasTo($user->email));
        $this->assertDatabaseHas('password_reset_otps', ['email' => $user->email]);
    }

    public function test_send_otp_succeeds_for_penguji(): void
    {
        Mail::fake();
        $user = User::factory()->create([
            'role'  => 'penguji',
            'email' => 'penguji@example.com',
        ]);

        $this->post(route('password.otp.send'), ['email' => $user->email])
            ->assertRedirect(route('password.otp.form'));

        Mail::assertSent(PasswordResetOtpMail::class);
    }

    public function test_send_otp_succeeds_for_kaprodi(): void
    {
        Mail::fake();
        $user = User::factory()->create([
            'role'  => 'kaprodi',
            'email' => 'kaprodi@example.com',
        ]);

        $this->post(route('password.otp.send'), ['email' => $user->email])
            ->assertRedirect(route('password.otp.form'));

        Mail::assertSent(PasswordResetOtpMail::class);
    }

    public function test_send_otp_blocked_for_admin(): void
    {
        Mail::fake();
        $admin = User::factory()->create([
            'role'  => 'admin',
            'email' => 'admin@example.com',
        ]);

        $this->post(route('password.otp.send'), ['email' => $admin->email])
            ->assertSessionHasErrors('email');

        Mail::assertNothingSent();
        $this->assertDatabaseMissing('password_reset_otps', ['email' => $admin->email]);
    }

    public function test_send_otp_fails_for_unregistered_email(): void
    {
        Mail::fake();

        $this->post(route('password.otp.send'), ['email' => 'tidak_ada@example.com'])
            ->assertSessionHasErrors('email');

        Mail::assertNothingSent();
    }

    public function test_send_otp_requires_valid_email_format(): void
    {
        $this->post(route('password.otp.send'), ['email' => 'bukan-email'])
            ->assertSessionHasErrors('email');

        $this->post(route('password.otp.send'), [])
            ->assertSessionHasErrors('email');
    }

    public function test_send_otp_blocked_during_cooldown(): void
    {
        Mail::fake();
        $user = User::factory()->create(['role' => 'pelamar', 'email' => 'a@example.com']);

        // Kirim pertama
        $this->post(route('password.otp.send'), ['email' => $user->email])->assertRedirect();

        // Coba kirim ulang segera → harus diblokir cooldown
        $this->post(route('password.otp.send'), ['email' => $user->email])
            ->assertSessionHasErrors('email');
    }

    // ════════════════════════════════════════════════════════════
    // STEP 2: Verify OTP
    // ════════════════════════════════════════════════════════════

    public function test_otp_form_redirects_if_no_session(): void
    {
        $this->get(route('password.otp.form'))
            ->assertRedirect(route('password.otp.email'));
    }

    public function test_otp_form_loads_with_session(): void
    {
        $this->withSession(['fp_email' => 'pelamar@example.com'])
            ->get(route('password.otp.form'))
            ->assertOk();
    }

    public function test_verify_otp_succeeds_with_correct_code(): void
    {
        $email = 'pelamar@example.com';
        User::factory()->create(['role' => 'pelamar', 'email' => $email]);

        $otp = '123456';
        DB::table('password_reset_otps')->insert([
            'email'      => $email,
            'otp_hash'   => Hash::make($otp),
            'expires_at' => now()->addMinutes(10),
            'attempts'   => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->withSession(['fp_email' => $email])
            ->post(route('password.otp.verify'), ['otp' => $otp])
            ->assertRedirect(route('password.otp.reset.form'));

        $this->assertDatabaseHas('password_reset_otps', [
            'email' => $email,
        ]);

        $record = DB::table('password_reset_otps')->where('email', $email)->first();
        $this->assertNotNull($record->verified_at);
        $this->assertNotNull($record->reset_token);
    }

    public function test_verify_otp_fails_with_wrong_code(): void
    {
        $email = 'pelamar@example.com';
        User::factory()->create(['role' => 'pelamar', 'email' => $email]);

        DB::table('password_reset_otps')->insert([
            'email'      => $email,
            'otp_hash'   => Hash::make('123456'),
            'expires_at' => now()->addMinutes(10),
            'attempts'   => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->withSession(['fp_email' => $email])
            ->post(route('password.otp.verify'), ['otp' => '999999'])
            ->assertSessionHasErrors('otp');

        $record = DB::table('password_reset_otps')->where('email', $email)->first();
        $this->assertEquals(1, $record->attempts);
        $this->assertNull($record->verified_at);
    }

    public function test_verify_otp_fails_when_expired(): void
    {
        $email = 'pelamar@example.com';
        User::factory()->create(['role' => 'pelamar', 'email' => $email]);

        $otp = '123456';
        DB::table('password_reset_otps')->insert([
            'email'      => $email,
            'otp_hash'   => Hash::make($otp),
            'expires_at' => now()->subMinute(), // sudah expired
            'attempts'   => 0,
            'created_at' => now()->subMinutes(11),
            'updated_at' => now()->subMinutes(11),
        ]);

        $this->withSession(['fp_email' => $email])
            ->post(route('password.otp.verify'), ['otp' => $otp])
            ->assertSessionHasErrors('otp');

        $this->assertDatabaseMissing('password_reset_otps', ['email' => $email]);
    }

    public function test_verify_otp_locks_after_max_attempts(): void
    {
        $email = 'pelamar@example.com';
        User::factory()->create(['role' => 'pelamar', 'email' => $email]);

        DB::table('password_reset_otps')->insert([
            'email'      => $email,
            'otp_hash'   => Hash::make('123456'),
            'expires_at' => now()->addMinutes(10),
            'attempts'   => 5, // sudah max
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->withSession(['fp_email' => $email])
            ->post(route('password.otp.verify'), ['otp' => '999999'])
            ->assertRedirect(route('password.otp.email'));

        $this->assertDatabaseMissing('password_reset_otps', ['email' => $email]);
    }

    public function test_verify_otp_requires_6_digits(): void
    {
        $this->withSession(['fp_email' => 'pelamar@example.com'])
            ->post(route('password.otp.verify'), ['otp' => '123'])
            ->assertSessionHasErrors('otp');
    }

    // ════════════════════════════════════════════════════════════
    // STEP 3: Reset Password
    // ════════════════════════════════════════════════════════════

    public function test_reset_form_redirects_without_token(): void
    {
        $this->get(route('password.otp.reset.form'))
            ->assertRedirect(route('password.otp.email'));
    }

    public function test_reset_password_succeeds_with_valid_token(): void
    {
        $email = 'pelamar@example.com';
        $user  = User::factory()->create([
            'role'     => 'pelamar',
            'email'    => $email,
            'password' => Hash::make('oldpassword'),
        ]);

        $token = 'valid-token-' . str_repeat('a', 50);

        DB::table('password_reset_otps')->insert([
            'email'       => $email,
            'otp_hash'    => Hash::make('123456'),
            'verified_at' => now(),
            'reset_token' => $token,
            'expires_at'  => now()->addMinutes(10),
            'attempts'    => 0,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $this->withSession(['fp_email' => $email, 'fp_reset_token' => $token])
            ->post(route('password.otp.reset'), [
                'password'              => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHas('success');

        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
        $this->assertDatabaseMissing('password_reset_otps', ['email' => $email]);
    }

    public function test_reset_password_requires_min_8_chars(): void
    {
        $email = 'pelamar@example.com';
        User::factory()->create(['role' => 'pelamar', 'email' => $email]);

        $token = str_repeat('a', 64);
        DB::table('password_reset_otps')->insert([
            'email'       => $email,
            'otp_hash'    => Hash::make('123456'),
            'verified_at' => now(),
            'reset_token' => $token,
            'expires_at'  => now()->addMinutes(10),
            'attempts'    => 0,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $this->withSession(['fp_email' => $email, 'fp_reset_token' => $token])
            ->post(route('password.otp.reset'), [
                'password'              => '123',
                'password_confirmation' => '123',
            ])
            ->assertSessionHasErrors('password');
    }

    public function test_reset_password_requires_confirmation_match(): void
    {
        $email = 'pelamar@example.com';
        User::factory()->create(['role' => 'pelamar', 'email' => $email]);

        $token = str_repeat('a', 64);
        DB::table('password_reset_otps')->insert([
            'email'       => $email,
            'otp_hash'    => Hash::make('123456'),
            'verified_at' => now(),
            'reset_token' => $token,
            'expires_at'  => now()->addMinutes(10),
            'attempts'    => 0,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $this->withSession(['fp_email' => $email, 'fp_reset_token' => $token])
            ->post(route('password.otp.reset'), [
                'password'              => 'newpassword123',
                'password_confirmation' => 'lain',
            ])
            ->assertSessionHasErrors('password');
    }

    public function test_reset_password_fails_with_expired_token(): void
    {
        $email = 'pelamar@example.com';
        $user  = User::factory()->create([
            'role'     => 'pelamar',
            'email'    => $email,
            'password' => Hash::make('oldpassword'),
        ]);

        $token = str_repeat('a', 64);
        DB::table('password_reset_otps')->insert([
            'email'       => $email,
            'otp_hash'    => Hash::make('123456'),
            'verified_at' => now()->subMinutes(11),
            'reset_token' => $token,
            'expires_at'  => now()->subMinute(), // expired
            'attempts'    => 0,
            'created_at'  => now()->subMinutes(11),
            'updated_at'  => now()->subMinutes(11),
        ]);

        $this->withSession(['fp_email' => $email, 'fp_reset_token' => $token])
            ->post(route('password.otp.reset'), [
                'password'              => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ])
            ->assertRedirect(route('password.otp.email'));

        // Password tetap sama (tidak ter-reset)
        $this->assertTrue(Hash::check('oldpassword', $user->fresh()->password));
    }

    // ════════════════════════════════════════════════════════════
    // Full integration flow
    // ════════════════════════════════════════════════════════════

    public function test_complete_flow_pelamar_can_reset_password(): void
    {
        Mail::fake();

        $email = 'flow@example.com';
        $user  = User::factory()->create([
            'role'     => 'pelamar',
            'email'    => $email,
            'password' => Hash::make('oldpass'),
        ]);

        // Step 1: kirim OTP
        $this->post(route('password.otp.send'), ['email' => $email])
            ->assertRedirect(route('password.otp.form'));

        // Capture OTP dari Mail::fake — kita tahu OTP-nya 6 digit, tapi tidak tahu nilainya
        // Jadi kita ambil hash dari DB dan pakai trick: simpan OTP yang kita inject sendiri
        DB::table('password_reset_otps')->where('email', $email)->update([
            'otp_hash' => Hash::make('246810'),
        ]);

        // Step 2: verifikasi OTP
        $this->withSession(['fp_email' => $email])
            ->post(route('password.otp.verify'), ['otp' => '246810'])
            ->assertRedirect(route('password.otp.reset.form'));

        $token = DB::table('password_reset_otps')->where('email', $email)->value('reset_token');
        $this->assertNotNull($token);

        // Step 3: reset password
        $this->withSession(['fp_email' => $email, 'fp_reset_token' => $token])
            ->post(route('password.otp.reset'), [
                'password'              => 'brandnewpass123',
                'password_confirmation' => 'brandnewpass123',
            ])
            ->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('brandnewpass123', $user->fresh()->password));
        $this->assertDatabaseMissing('password_reset_otps', ['email' => $email]);
    }
}
