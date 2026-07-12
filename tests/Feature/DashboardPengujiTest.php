<?php

use App\Models\User;
use App\Models\Prodi;
use App\Models\Dosen;

// Path 1: Sukses — dosen ditemukan, ada jadwal pengujian
test('Penguji berhasil mengakses dashboard dengan data pengujian', function () {
    // Arrange
    $prodi = Prodi::factory()->create();
    $dosen = Dosen::factory()->create([
        'prodi_id'   => $prodi->id,
        'is_penguji' => true,
    ]);
    $user = User::factory()->create([
        'role'     => 'penguji',
        'dosen_id' => $dosen->id,
        'prodi_id' => $prodi->id,
    ]);

    // Act
    $response = $this->actingAs($user)->get('/penguji/dashboard');

    // Assert
    $response->assertStatus(200);
    $response->assertViewIs('penguji.dashboard');
    $response->assertViewHas('totalDiuji');
    $response->assertViewHas('totalDinilai');
    $response->assertViewHas('totalBelumDinilai');
});

// Path 2: Gagal — dosen tidak ditemukan (abort 403)
test('Penguji gagal mengakses dashboard karena tidak terdaftar sebagai dosen', function () {
    // Arrange: user penguji tapi tanpa dosen_id
    $user = User::factory()->create([
        'role'     => 'penguji',
        'dosen_id' => null,
    ]);

    // Act
    $response = $this->actingAs($user)->get('/penguji/dashboard');

    // Assert
    $response->assertStatus(403);
});
