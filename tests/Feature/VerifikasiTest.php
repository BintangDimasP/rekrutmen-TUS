<?php

use App\Models\Pelamar;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->unverified()->create([
        'role'  => 'pelamar',
        'email' => 'pelamar@example.com',
    ]);

    $this->pelamar = Pelamar::factory()->create([
        'user_id'            => $this->user->id,
        'no_telepon'         => '081234567890',
        'phone_verified_at'  => null,
    ]);
});

test('TC-01: Pelamar meminta OTP verifikasi email, sistem mengirimkan OTP ke email', function () {
    Mail::fake();

    $response = $this->actingAs($this->user)->post(route('email.otp.send'));

    $response->assertStatus(200);
    $response->assertJson(['message' => 'OTP berhasil dikirim ke email Anda.']);

    $this->assertDatabaseHas('email_verification_otps', ['email' => $this->user->email]);
});

test('TC-02: Pelamar meminta OTP saat email sudah terverifikasi, sistem menampilkan pesan error', function () {
    $verifiedUser = User::factory()->create([
        'role'              => 'pelamar',
        'email'             => 'verified@example.com',
        'email_verified_at' => now(),
    ]);
    Pelamar::factory()->create(['user_id' => $verifiedUser->id]);

    $response = $this->actingAs($verifiedUser)->post(route('email.otp.send'));

    $response->assertStatus(400);
    $response->assertJson(['message' => 'Email sudah terverifikasi.']);
});

test('TC-03: Pelamar meminta OTP sebelum cooldown selesai, sistem menampilkan pesan tunggu', function () {
    DB::table('email_verification_otps')->insert([
        'email'      => $this->user->email,
        'otp_hash'   => Hash::make('123456'),
        'expires_at' => now()->addMinutes(5),
        'attempts'   => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $response = $this->actingAs($this->user)->post(route('email.otp.send'));

    $response->assertStatus(429);
});

test('TC-04: Pelamar menginput OTP email yang benar, sistem berhasil memverifikasi email', function () {
    $otp = '123456';

    DB::table('email_verification_otps')->insert([
        'email'      => $this->user->email,
        'otp_hash'   => Hash::make($otp),
        'expires_at' => now()->addMinutes(5),
        'attempts'   => 0,
        'created_at' => now()->subSeconds(10),
        'updated_at' => now()->subSeconds(10),
    ]);

    $response = $this->actingAs($this->user)->post(route('email.otp.verify'), ['otp' => $otp]);

    $response->assertStatus(200);
    $response->assertJson(['message' => 'Email berhasil diverifikasi!']);

    $this->user->refresh();
    expect($this->user->email_verified_at)->not->toBeNull();
});

test('TC-05: Pelamar menginput OTP email yang salah, sistem menambah hitungan percobaan', function () {
    DB::table('email_verification_otps')->insert([
        'email'      => $this->user->email,
        'otp_hash'   => Hash::make('999999'),
        'expires_at' => now()->addMinutes(5),
        'attempts'   => 0,
        'created_at' => now()->subSeconds(10),
        'updated_at' => now()->subSeconds(10),
    ]);

    $response = $this->actingAs($this->user)->post(route('email.otp.verify'), ['otp' => '000000']);

    $response->assertStatus(400);
    $response->assertJson(['message' => 'Kode OTP tidak valid.']);

    $record = DB::table('email_verification_otps')->where('email', $this->user->email)->first();
    expect($record->attempts)->toBe(1);
});

test('TC-06: Pelamar menginput OTP email yang sudah kadaluarsa, sistem menampilkan pesan error', function () {
    DB::table('email_verification_otps')->insert([
        'email'      => $this->user->email,
        'otp_hash'   => Hash::make('123456'),
        'expires_at' => now()->subMinutes(1),
        'attempts'   => 0,
        'created_at' => now()->subMinutes(6),
        'updated_at' => now()->subMinutes(6),
    ]);

    $response = $this->actingAs($this->user)->post(route('email.otp.verify'), ['otp' => '123456']);

    $response->assertStatus(400);
    $response->assertJson(['message' => 'Kode OTP sudah kadaluarsa. Silakan minta ulang.']);
});

test('TC-07: Pelamar menginput OTP email lebih dari 5 kali salah, sistem menampilkan pesan error', function () {
    DB::table('email_verification_otps')->insert([
        'email'      => $this->user->email,
        'otp_hash'   => Hash::make('999999'),
        'expires_at' => now()->addMinutes(5),
        'attempts'   => 5,
        'created_at' => now()->subSeconds(10),
        'updated_at' => now()->subSeconds(10),
    ]);

    $response = $this->actingAs($this->user)->post(route('email.otp.verify'), ['otp' => '123456']);

    $response->assertStatus(400);
    $response->assertJson(['message' => 'Terlalu banyak percobaan salah. Silakan minta kode OTP baru.']);
});

test('TC-08: Pelamar meminta OTP verifikasi nomor telepon, sistem berhasil mengirimkan OTP', function () {
    Config::set('services.wappin.enabled', true);

    $this->partialMock(\App\Http\Controllers\Auth\PhoneVerificationOtpController::class, function ($mock) {
        $mock->shouldAllowMockingProtectedMethods();
        $mock->shouldReceive('sendWhatsapp')->andReturn(['status' => true]);
    });

    $response = $this->actingAs($this->user)->post(route('phone.otp.send'));

    $response->assertStatus(200);
    $response->assertJson(['message' => 'OTP berhasil dikirim ke WhatsApp Anda.']);

    $this->assertDatabaseHas('phone_verification_otps', ['phone' => '6281234567890']);
});

test('TC-09: Pelamar menginput OTP nomor telepon yang sudah kadaluarsa, sistem menampilkan pesan error', function () {
    Config::set('services.wappin.enabled', true);

    DB::table('phone_verification_otps')->insert([
        'phone'      => '6281234567890',
        'otp_hash'   => Hash::make('123456'),
        'expires_at' => now()->subMinutes(1),
        'attempts'   => 0,
        'created_at' => now()->subMinutes(6),
        'updated_at' => now()->subMinutes(6),
    ]);

    $response = $this->actingAs($this->user)->post(route('phone.otp.verify'), ['otp' => '123456']);

    $response->assertStatus(400);
    $response->assertJson(['message' => 'Kode OTP sudah kadaluarsa. Silakan minta ulang.']);
});

test('TC-10: Pelamar menginput OTP nomor telepon yang salah, sistem menambah hitungan percobaan', function () {
    Config::set('services.wappin.enabled', true);

    DB::table('phone_verification_otps')->insert([
        'phone'      => '6281234567890',
        'otp_hash'   => Hash::make('999999'),
        'expires_at' => now()->addMinutes(5),
        'attempts'   => 0,
        'created_at' => now()->subSeconds(10),
        'updated_at' => now()->subSeconds(10),
    ]);

    $response = $this->actingAs($this->user)->post(route('phone.otp.verify'), ['otp' => '000000']);

    $response->assertStatus(400);
    $response->assertJson(['message' => 'Kode OTP tidak valid.']);

    $record = DB::table('phone_verification_otps')->where('phone', '6281234567890')->first();
    expect($record->attempts)->toBe(1);
});

test('TC-11: Pelamar menginput OTP nomor telepon lebih dari 5 kali salah, sistem menampilkan pesan error', function () {
    Config::set('services.wappin.enabled', true);

    DB::table('phone_verification_otps')->insert([
        'phone'      => '6281234567890',
        'otp_hash'   => Hash::make('999999'),
        'expires_at' => now()->addMinutes(5),
        'attempts'   => 5,
        'created_at' => now()->subSeconds(10),
        'updated_at' => now()->subSeconds(10),
    ]);

    $response = $this->actingAs($this->user)->post(route('phone.otp.verify'), ['otp' => '123456']);

    $response->assertStatus(400);
    $response->assertJson(['message' => 'Terlalu banyak percobaan salah. Silakan minta kode OTP baru.']);
});
