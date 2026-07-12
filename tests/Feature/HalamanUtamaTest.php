<?php

/**
 * WHITE BOX TESTING - HALAMAN UTAMA (LANDING PAGE)
 * Teknik     : Branch Coverage
 * Controller : LandingController@index
 * Route      : GET /
 *
 * ============================================================
 * ANALISIS BRANCH PADA KODE SUMBER:
 * ============================================================
 *
 *   $lowongans = Lowongan::where('status', 'aktif')      // Branch 1
 *       ->where('tanggal_tutup', '>=', now())            // Branch 2
 *       ->with('prodi')
 *       ->orderBy('created_at', 'desc')
 *       ->take(6)                                        // Branch 3
 *       ->get();
 *
 *   $totalPendaftar = Pelamar::count();                  // Branch 4
 *
 * Branch 1 — Filter status (where status = 'aktif')
 *   TRUE  : Ada lowongan berstatus 'aktif' → masuk hasil query
 *   FALSE : Status bukan 'aktif' (ditutup/draft) → tidak masuk hasil
 *
 * Branch 2 — Filter tanggal tutup (where tanggal_tutup >= now())
 *   TRUE  : Tanggal tutup belum lewat → masuk hasil query
 *   FALSE : Tanggal tutup sudah lewat → tidak masuk hasil
 *
 * Branch 3 — Batas jumlah data (take 6)
 *   TRUE  : Data lolos filter < 6 → tampil semua
 *   FALSE : Data lolos filter >= 6 → hanya 6 terbaru yang tampil
 *
 * Branch 4 — Jumlah pelamar (Pelamar::count())
 *   TRUE  : Ada pelamar terdaftar → totalPendaftar > 0
 *   FALSE : Tidak ada pelamar → totalPendaftar = 0
 *
 * ============================================================
 * PETA TEST CASE → BRANCH YANG DICAKUP:
 * ============================================================
 *
 * TC-01 : B1=T, B2=T, B3=<6,  B4=T  → happy path, data < 6
 * TC-02 : B1=T, B2=T, B3=>=6, B4=T  → happy path, data >= 6 (batas take)
 * TC-03 : B1=T, B2=F,          B4=T  → unhappy, aktif tapi sudah tutup
 * TC-04 : B1=F, B2=T,          B4=T  → unhappy, belum tutup tapi bukan aktif
 * TC-05 : B1=F, B2=F,          B4=F  → unhappy, tidak ada data sama sekali
 * TC-06 : B1=Mixed, B2=Mixed,  B4=T  → happy, filter dari data campuran
 * TC-07 : B1=T, B2=T, B3=<6,  B4=F  → happy path, nol pelamar
 *
 * ============================================================
 * TABEL HASIL PENGUJIAN:
 * ============================================================
 *
 * | Test Case | Skenario                                              | Hasil yang Diharapkan                                                    | Hasil |
 * |-----------|-------------------------------------------------------|--------------------------------------------------------------------------|-------|
 * | TC-01     | Lowongan aktif tersedia, jumlah kurang dari 6         | Sistem menampilkan semua lowongan aktif yang tersedia                    | Lulus |
 * | TC-02     | Lowongan aktif tersedia, jumlah lebih dari 6          | Sistem menampilkan 6 lowongan terbaru                                    | Lulus |
 * | TC-03     | Lowongan aktif namun tanggal tutup sudah lewat        | Sistem tidak menampilkan lowongan apapun                                 | Lulus |
 * | TC-04     | Lowongan tersedia namun berstatus ditutup atau draft  | Sistem tidak menampilkan lowongan apapun                                 | Lulus |
 * | TC-05     | Tidak ada lowongan dan pelamar di sistem              | Sistem menampilkan halaman dengan daftar kosong dan total pendaftar 0    | Lulus |
 * | TC-06     | Terdapat campuran lowongan aktif, ditutup, dan lewat  | Sistem hanya menampilkan lowongan yang aktif dan belum lewat tanggal     | Lulus |
 * | TC-07     | Lowongan aktif tersedia namun belum ada pelamar       | Sistem menampilkan lowongan dengan total pendaftar 0                     | Lulus |
 */

use App\Models\Lowongan;
use App\Models\Pelamar;
use App\Models\Prodi;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->prodi = Prodi::factory()->create();
});

// ---------------------------------------------------------------
// TC-01 | Happy Path
// B1=T : Ada lowongan status 'aktif'
// B2=T : Tanggal tutup belum lewat
// B3=T : Jumlah data < 6 (semua tampil)
// B4=T : Ada pelamar terdaftar
// ---------------------------------------------------------------
test('TC-01: Sistem menampilkan semua lowongan aktif yang tersedia', function () {
    // Arrange
    Lowongan::factory()->count(3)->create([
        'prodi_id'      => $this->prodi->id,
        'status'        => 'aktif',
        'tanggal_tutup' => now()->addDays(7),
    ]);
    Pelamar::factory()->count(5)->create();

    // Act
    $response = $this->get('/');

    // Assert
    $response->assertStatus(200);
    $response->assertViewIs('landing');
    $response->assertViewHas('totalPendaftar', 5);

    $lowongans = $response->viewData('lowongans');
    expect($lowongans)->toHaveCount(3);

    foreach ($lowongans as $lowongan) {
        expect($lowongan->status)->toBe('aktif');
        expect($lowongan->tanggal_tutup->gte(now()))->toBeTrue();
    }
});

// ---------------------------------------------------------------
// TC-02 | Happy Path
// B1=T  : Ada lowongan status 'aktif'
// B2=T  : Tanggal tutup belum lewat
// B3=F  : Jumlah data >= 6 → dipotong menjadi 6
// B4=T  : Ada pelamar terdaftar
// ---------------------------------------------------------------
test('TC-02: Sistem menampilkan 6 lowongan terbaru', function () {
    // Arrange
    Lowongan::factory()->count(10)->create([
        'prodi_id'      => $this->prodi->id,
        'status'        => 'aktif',
        'tanggal_tutup' => now()->addDays(7),
    ]);
    Pelamar::factory()->count(15)->create();

    // Act
    $response = $this->get('/');

    // Assert
    $response->assertStatus(200);
    $response->assertViewHas('totalPendaftar', 15);

    $lowongans = $response->viewData('lowongans');
    expect($lowongans)->toHaveCount(6);

    // Verifikasi urutan terbaru ke terlama (orderBy created_at desc)
    $dates = $lowongans->pluck('created_at');
    for ($i = 0; $i < $dates->count() - 1; $i++) {
        expect($dates[$i]->gte($dates[$i + 1]))->toBeTrue();
    }
});

// ---------------------------------------------------------------
// TC-03 | Unhappy Path
// B1=T  : Status 'aktif'
// B2=F  : Tanggal tutup sudah lewat → tidak masuk query
// B4=T  : Ada pelamar terdaftar
// ---------------------------------------------------------------
test('TC-03: Sistem tidak menampilkan lowongan apapun ketika tanggal tutup sudah lewat', function () {
    // Arrange
    Lowongan::factory()->count(4)->create([
        'prodi_id'      => $this->prodi->id,
        'status'        => 'aktif',
        'tanggal_tutup' => now()->subDays(3),
    ]);
    Pelamar::factory()->count(10)->create();

    // Act
    $response = $this->get('/');

    // Assert
    $response->assertStatus(200);
    $response->assertViewHas('totalPendaftar', 10);

    $lowongans = $response->viewData('lowongans');
    expect($lowongans)->toHaveCount(0);
});

// ---------------------------------------------------------------
// TC-04 | Unhappy Path
// B1=F  : Status bukan 'aktif' (ditutup / draft)
// B2=T  : Tanggal tutup belum lewat
// B4=T  : Ada pelamar terdaftar
// ---------------------------------------------------------------
test('TC-04: Sistem tidak menampilkan lowongan apapun ketika status berstatus ditutup atau draft', function () {
    // Arrange
    Lowongan::factory()->count(3)->create([
        'prodi_id'      => $this->prodi->id,
        'status'        => 'ditutup',
        'tanggal_tutup' => now()->addDays(5),
    ]);
    Lowongan::factory()->count(2)->create([
        'prodi_id'      => $this->prodi->id,
        'status'        => 'draft',
        'tanggal_tutup' => now()->addDays(5),
    ]);
    Pelamar::factory()->count(8)->create();

    // Act
    $response = $this->get('/');

    // Assert
    $response->assertStatus(200);
    $response->assertViewHas('totalPendaftar', 8);

    $lowongans = $response->viewData('lowongans');
    expect($lowongans)->toHaveCount(0);
});

// ---------------------------------------------------------------
// TC-05 | Unhappy Path
// B1=F  : Tidak ada lowongan sama sekali
// B2=F  : Tidak ada lowongan sama sekali
// B4=F  : Tidak ada pelamar → totalPendaftar = 0
// ---------------------------------------------------------------
test('TC-05: Sistem menampilkan halaman dengan daftar kosong dan total pendaftar 0', function () {
    // Arrange: database kosong

    // Act
    $response = $this->get('/');

    // Assert
    $response->assertStatus(200);
    $response->assertViewIs('landing');
    $response->assertViewHas('totalPendaftar', 0);

    $lowongans = $response->viewData('lowongans');
    expect($lowongans)->toHaveCount(0);
});

// ---------------------------------------------------------------
// TC-06 | Happy Path
// B1=Mixed : Sebagian aktif, sebagian tidak
// B2=Mixed : Sebagian belum tutup, sebagian sudah
// B4=T     : Ada pelamar terdaftar
// ---------------------------------------------------------------
test('TC-06: Sistem hanya menampilkan lowongan yang aktif dan belum lewat tanggal tutup', function () {
    // Arrange
    // (1) Aktif + belum tutup → TAMPIL (2 data)
    Lowongan::factory()->count(2)->create([
        'prodi_id'      => $this->prodi->id,
        'status'        => 'aktif',
        'tanggal_tutup' => now()->addDays(7),
    ]);
    // (2) Aktif + sudah tutup → TIDAK TAMPIL
    Lowongan::factory()->count(3)->create([
        'prodi_id'      => $this->prodi->id,
        'status'        => 'aktif',
        'tanggal_tutup' => now()->subDays(2),
    ]);
    // (3) Ditutup + belum tutup → TIDAK TAMPIL
    Lowongan::factory()->count(2)->create([
        'prodi_id'      => $this->prodi->id,
        'status'        => 'ditutup',
        'tanggal_tutup' => now()->addDays(5),
    ]);
    // (4) Draft + sudah tutup → TIDAK TAMPIL
    Lowongan::factory()->create([
        'prodi_id'      => $this->prodi->id,
        'status'        => 'draft',
        'tanggal_tutup' => now()->subDays(1),
    ]);

    Pelamar::factory()->count(12)->create();

    // Act
    $response = $this->get('/');

    // Assert
    $response->assertStatus(200);
    $response->assertViewHas('totalPendaftar', 12);

    $lowongans = $response->viewData('lowongans');
    expect($lowongans)->toHaveCount(2);

    foreach ($lowongans as $lowongan) {
        expect($lowongan->status)->toBe('aktif');
        expect($lowongan->tanggal_tutup->gte(now()))->toBeTrue();
    }
});

// ---------------------------------------------------------------
// TC-07 | Happy Path
// B1=T  : Ada lowongan status 'aktif'
// B2=T  : Tanggal tutup belum lewat
// B3=T  : Jumlah data < 6
// B4=F  : Tidak ada pelamar → totalPendaftar = 0
// ---------------------------------------------------------------
test('TC-07: Sistem menampilkan lowongan dengan total pendaftar 0', function () {
    // Arrange
    Lowongan::factory()->count(4)->create([
        'prodi_id'      => $this->prodi->id,
        'status'        => 'aktif',
        'tanggal_tutup' => now()->addDays(10),
    ]);

    // Act
    $response = $this->get('/');

    // Assert
    $response->assertStatus(200);
    $response->assertViewHas('totalPendaftar', 0);

    $lowongans = $response->viewData('lowongans');
    expect($lowongans)->toHaveCount(4);
});
