<?php

use App\Models\User;
use App\Models\Prodi;
use App\Models\Lowongan;
use App\Models\Lamaran;
use App\Models\Pelamar;

// Path 1: Linear (N1→N2→...→N10)
test('Kaprodi berhasil mengakses dashboard dengan data lamaran', function () {
    // Arrange
    $prodi = Prodi::factory()->create();
    $user = User::factory()->create([
        'role'     => 'kaprodi',
        'prodi_id' => $prodi->id,
    ]);
    $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
    $pelamar1 = Pelamar::factory()->create();
    $pelamar2 = Pelamar::factory()->create();
    $pelamar3 = Pelamar::factory()->create();
    Lamaran::factory()->create(['lowongan_id' => $lowongan->id, 'pelamar_id' => $pelamar1->id]);
    Lamaran::factory()->create(['lowongan_id' => $lowongan->id, 'pelamar_id' => $pelamar2->id]);
    Lamaran::factory()->create(['lowongan_id' => $lowongan->id, 'pelamar_id' => $pelamar3->id]);

    // Act
    $response = $this->actingAs($user)->get('/kaprodi/dashboard');

    // Assert
    $response->assertStatus(200);
    $response->assertViewIs('kaprodi.dashboard');
    $response->assertViewHas('totalPelamar');
    $response->assertViewHas('lamaranTerbaru');
});

// Path 1 + Edge Case: Dashboard tanpa data lamaran
test('Kaprodi berhasil mengakses dashboard tanpa data lamaran', function () {
    // Arrange
    $prodi = Prodi::factory()->create();
    $user = User::factory()->create([
        'role'     => 'kaprodi',
        'prodi_id' => $prodi->id,
    ]);

    // Act
    $response = $this->actingAs($user)->get('/kaprodi/dashboard');

    // Assert
    $response->assertStatus(200);
    $response->assertViewHas('totalPelamar', 0);
    $response->assertViewHas('totalDiterima', 0);
});
