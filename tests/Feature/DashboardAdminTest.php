<?php

use App\Models\User;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Pelamar;
use App\Models\Prodi;

// Path 1: Tidak ada lamaran → acceptanceRate = 0 (node 3 ambil cabang kiri)
test('Admin berhasil mengakses dashboard tanpa data lamaran', function () {
    // Arrange: tidak ada lamaran di database
    $user = User::factory()->create(['role' => 'admin']);

    // Act
    $response = $this->actingAs($user)->get('/admin/dashboard');

    // Assert: acceptanceRate = 0 karena tidak ada lamaran
    $response->assertStatus(200);
    $response->assertViewIs('admin.dashboard');
    $response->assertViewHas('totalLamaran', 0);
    $acceptanceRate = $response->viewData('acceptanceRate');
    expect($acceptanceRate)->toBe(0);
});

// Path 2: Ada lamaran → acceptanceRate dihitung (node 3 ambil cabang kanan)
test('Admin berhasil mengakses dashboard dengan data lamaran', function () {
    // Arrange: ada lamaran diterima
    $user = User::factory()->create(['role' => 'admin']);
    $prodi = Prodi::factory()->create();
    $pelamar = Pelamar::factory()->create();
    $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
    Lamaran::factory()->create([
        'lowongan_id' => $lowongan->id,
        'pelamar_id'  => $pelamar->id,
        'status'      => 'diterima',
    ]);

    // Act
    $response = $this->actingAs($user)->get('/admin/dashboard');

    // Assert: acceptanceRate > 0 karena ada lamaran diterima
    $response->assertStatus(200);
    $acceptanceRate = $response->viewData('acceptanceRate');
    expect($acceptanceRate)->toBeGreaterThan(0);
    $response->assertViewHas('totalLamaran', 1);
});

// Path 3: Loop jalan, jumlah lamaran per bulan tidak melebihi maxChartValue (node 8 = false)
test('Admin berhasil mengakses dashboard dengan jumlah lamaran normal', function () {
    // Arrange: ada 1 lamaran bulan ini (tidak melebihi maxChartValue awal = 1)
    // Catatan: maxChartValue awal = 1, jika lamaran = 1 maka TIDAK > 1 → tidak update
    $user = User::factory()->create(['role' => 'admin']);
    $prodi = Prodi::factory()->create();
    $pelamar = Pelamar::factory()->create();
    $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
    Lamaran::factory()->create([
        'lowongan_id' => $lowongan->id,
        'pelamar_id'  => $pelamar->id,
        'status'      => 'menunggu',
        'created_at'  => now(),
    ]);

    // Act
    $response = $this->actingAs($user)->get('/admin/dashboard');

    // Assert: chartData terbentuk 12 bulan, maxChartValue tetap = 10 (dari max(10, 1))
    $response->assertStatus(200);
    $chartData = $response->viewData('chartData');
    expect($chartData)->toHaveCount(12);
    $maxChartValue = $response->viewData('maxChartValue');
    expect($maxChartValue)->toBe(10); // max(10, 1) = 10
});

// Path 4: Loop jalan, jumlah lamaran per bulan melebihi maxChartValue (node 8 = true)
test('Admin berhasil mengakses dashboard dengan jumlah lamaran melebihi batas grafik', function () {
    // Arrange: buat >1 lamaran di bulan yang sama agar lamaranCount > maxChartValue awal
    $user = User::factory()->create(['role' => 'admin']);
    $prodi = Prodi::factory()->create();
    $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);

    // Buat 15 lamaran bulan ini (melebihi maxChartValue awal = 1, dan melebihi batas 10)
    for ($i = 0; $i < 15; $i++) {
        $pelamar = Pelamar::factory()->create();
        Lamaran::factory()->create([
            'lowongan_id' => $lowongan->id,
            'pelamar_id'  => $pelamar->id,
            'status'      => 'menunggu',
            'created_at'  => now(),
        ]);
    }

    // Act
    $response = $this->actingAs($user)->get('/admin/dashboard');

    // Assert: maxChartValue terupdate menjadi 15 (karena 15 > 10)
    $response->assertStatus(200);
    $maxChartValue = $response->viewData('maxChartValue');
    expect($maxChartValue)->toBe(15); // lamaranCount 15 > maxChartValue 1 → update ke 15
});
