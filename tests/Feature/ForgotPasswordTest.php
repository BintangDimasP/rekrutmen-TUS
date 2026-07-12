<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

// Path 1: Tidak ada session token/email → redirect ke form email
test('Pengguna gagal mereset password karena sesi tidak ditemukan', function () {
    // Arrange: Tidak ada session fp_token & fp_email

    // Act
    $response = $this->post('/forgot-password/reset', [
        'password'              => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    // Assert: Diarahkan kembali ke form email
    $response->assertRedirect(route('password.otp.email'));
});

// Path 2: Token sudah expired
test('Pengguna gagal mereset password karena sesi sudah kedaluwarsa', function () {
    // Arrange: Buat user & token yang sudah expired
    $user = User::factory()->create([
        'email' => 'pelamar@gmail.com',
        'role'  => 'pelamar',
    ]);

    $resetToken = Str::random(64);

    DB::table('password_reset_otps')->insert([
        'email'       => 'pelamar@gmail.com',
        'otp_hash'    => Hash::make('123456'),
        'expires_at'  => now()->subMinutes(5), // ← sudah expired
        'verified_at' => now()->subMinutes(10),
        'reset_token' => $resetToken,
        'attempts'    => 0,
        'created_at'  => now(),
        'updated_at'  => now(),
    ]);

    // Act
    $response = $this->withSession([
        'fp_email'       => 'pelamar@gmail.com',
        'fp_reset_token' => $resetToken,
    ])->post('/forgot-password/reset', [
        'password'              => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    // Assert: Diarahkan ke form email dengan error
    $response->assertRedirect(route('password.otp.email'));
    $response->assertSessionHasErrors(['email']);
});

// Path 3: User tidak ditemukan / role tidak valid
test('Pengguna gagal mereset password karena akun tidak ditemukan', function () {
    // Arrange: Token valid tapi user tidak ada di database
    $resetToken = Str::random(64);

    DB::table('password_reset_otps')->insert([
        'email'       => 'tidakada@gmail.com',
        'otp_hash'    => Hash::make('123456'),
        'expires_at'  => now()->addMinutes(10), // ← masih valid
        'verified_at' => now(),
        'reset_token' => $resetToken,
        'attempts'    => 0,
        'created_at'  => now(),
        'updated_at'  => now(),
    ]);

    // Act
    $response = $this->withSession([
        'fp_email'       => 'tidakada@gmail.com',
        'fp_reset_token' => $resetToken,
    ])->post('/forgot-password/reset', [
        'password'              => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    // Assert: Diarahkan ke form email dengan error
    $response->assertRedirect(route('password.otp.email'));
    $response->assertSessionHasErrors(['email']);
});

// Path 4: Sukses reset password
test('Pengguna berhasil mereset password dengan token yang valid', function () {
    // Arrange: Buat user & token yang valid
    $user = User::factory()->create([
        'email'    => 'pelamar2@gmail.com',
        'role'     => 'pelamar',
        'password' => Hash::make('oldpassword'),
    ]);

    $resetToken = Str::random(64);

    DB::table('password_reset_otps')->insert([
        'email'       => 'pelamar2@gmail.com',
        'otp_hash'    => Hash::make('123456'),
        'expires_at'  => now()->addMinutes(10), // ← masih valid
        'verified_at' => now(),
        'reset_token' => $resetToken,
        'attempts'    => 0,
        'created_at'  => now(),
        'updated_at'  => now(),
    ]);

    // Act
    $response = $this->withSession([
        'fp_email'       => 'pelamar2@gmail.com',
        'fp_reset_token' => $resetToken,
    ])->post('/forgot-password/reset', [
        'password'              => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    // Assert: Redirect ke login & password berhasil diubah
    $response->assertRedirect(route('login'));
    expect(Hash::check('newpassword123', $user->fresh()->password))->toBeTrue();
    $this->assertDatabaseMissing('password_reset_otps', ['email' => 'pelamar2@gmail.com']);
});
