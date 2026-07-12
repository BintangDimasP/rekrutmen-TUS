<?php

use App\Models\User;
use App\Models\Pelamar;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Prodi;

// Path 1: Pelamar ada, tidak ada show_profile_reminder
test('Pelamar berhasil mengakses dashboard dengan data lamaran', function () {
    // Arrange
    $user = User::factory()->create(['role' => 'pelamar']);
    $pelamar = Pelamar::factory()->create(['user_id' => $user->id]);
    $prodi = Prodi::factory()->create();
    $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
    Lamaran::factory()->create([
        'pelamar_id'  => $pelamar->id,
        'lowongan_id' => $lowongan->id,
        'status'      => 'menunggu',
    ]);

    // Act
    $response = $this->actingAs($user)->get('/pelamar/dashboard');

    // Assert
    $response->assertStatus(200);
    $response->assertViewIs('pelamar.dashboard');
    $response->assertViewHas('totalLamaran', 1);
    $response->assertViewHas('lamaranAktif', 1);
    $response->assertViewHas('showProfileModal', false);
});

// Path 2: Pelamar ada, show_profile_reminder aktif, profil belum lengkap
test('Pelamar melihat modal pengingat profil saat pertama kali login', function () {
    // Arrange
    $user = User::factory()->create(['role' => 'pelamar']);
    $pelamar = Pelamar::factory()->create([
        'user_id'    => $user->id,
        'file_cv'    => null, // profil belum lengkap
        'file_ktp'   => null,
        'file_pas_foto' => null,
    ]);

    // Act: dengan session show_profile_reminder = true
    $response = $this->actingAs($user)
        ->withSession(['show_profile_reminder' => true])
        ->get('/pelamar/dashboard');

    // Assert: modal profil muncul
    $response->assertStatus(200);
    $response->assertViewHas('showProfileModal', true);
    $incompleteSections = $response->viewData('incompleteSections');
    expect($incompleteSections)->not->toBeEmpty();
});

// Path 3: Pelamar ada, show_profile_reminder aktif, profil sudah lengkap
test('Pelamar tidak melihat modal pengingat profil jika profil sudah lengkap', function () {
    // Arrange: buat user dengan profil lengkap
    $user = User::factory()->create([
        'role'              => 'pelamar',
        'email_verified_at' => now(),
    ]);
    $pelamar = Pelamar::factory()->create([
        'user_id'          => $user->id,
        'nik'              => '3201010101010001',
        'nama'             => 'Test Pelamar',
        'tempat_lahir'     => 'Bandung',
        'tanggal_lahir'    => '1995-01-01',
        'no_telepon'       => '081234567890',
        'jenis_kelamin'    => 'L',
        'alamat_domisili'  => 'Jl. Test',
        'jenjang'          => 'S2',
        'institusi'        => 'ITB',
        'file_ijazah'      => 'ijazah.pdf',
        'file_transkrip'   => 'transkrip.pdf',
        'file_cv'          => 'cv.pdf',
        'file_pas_foto'    => 'foto.jpg',
        'file_ktp'         => 'ktp.pdf',
        'nidn'             => '0101010101',
        'homebase'         => 'Teknik Informatika',
        'jabatan_akademik' => 'lektor',
    ]);

    // Act
    $response = $this->actingAs($user)
        ->withSession(['show_profile_reminder' => true])
        ->get('/pelamar/dashboard');

    // Assert: modal tidak muncul karena profil lengkap
    $response->assertStatus(200);
    $response->assertViewHas('showProfileModal', false);
});

// Path 4: Pelamar belum memiliki data lamaran
test('Pelamar berhasil mengakses dashboard tanpa data lamaran', function () {
    // Arrange: user pelamar sudah ada record Pelamar tapi belum ada lamaran
    $user = User::factory()->create(['role' => 'pelamar']);
    Pelamar::factory()->create(['user_id' => $user->id]);

    // Act
    $response = $this->actingAs($user)->get('/pelamar/dashboard');

    // Assert: tetap bisa akses, data default 0
    $response->assertStatus(200);
    $response->assertViewHas('totalLamaran', 0);
    $response->assertViewHas('showProfileModal', false);
});
