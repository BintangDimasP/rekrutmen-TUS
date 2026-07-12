<?php

/**
 * WHITE BOX TESTING - MANAJEMEN PELAMAR
 * Teknik     : Branch Coverage
 * Controller : Admin\PelamarController, Admin\LamaranController
 *
 * ============================================================
 * ANALISIS BRANCH:
 * ============================================================
 *
 * [PelamarController@show — Detail Pelamar]
 *
 *   if ($activeLamaranId) → cari lamaran by id                     // Branch 1
 *   if (!$activeLamaran)  → fallback ke lamaran pertama            // Branch 2
 *   if ($activeLamaran)   → load snapshot & jadwal                 // Branch 3
 *
 * Branch 1 — lamaran_id dikirim via query string
 *   TRUE  : Ada lamaran_id → cari spesifik
 *   FALSE : Tidak ada → skip
 *
 * Branch 2 — Lamaran spesifik tidak ditemukan / tidak ada query
 *   TRUE  : Null → fallback ke lamaran pertama
 *
 * Branch 3 — Pelamar memiliki lamaran
 *   TRUE  : Ada lamaran → load snapshot dan jadwal
 *   FALSE : Tidak ada lamaran → semua null
 *
 * ------------------------------------------------------------
 *
 * [PelamarController@import — Import Pelamar]
 *
 *   try-catch ValidationException                                   // Branch 4
 *   try-catch Exception                                             // Branch 5
 *
 * Branch 4 — File Excel valid tapi data bermasalah
 *   ValidationException → return error baris per baris
 *
 * Branch 5 — File valid format
 *   No exception → success
 *
 * ------------------------------------------------------------
 *
 * [LamaranController@update — Update Status Lamaran]
 *
 *   if ($statusLama !== $validated['status'])                       // Branch 6
 *   if ($userId)                                                    // Branch 7
 *   if (in_array($validated['status'], ['diterima', 'ditolak']))   // Branch 8
 *   if ($prodiId)                                                   // Branch 9
 *
 * Branch 6 — Status berubah
 *   TRUE  : Status berubah → kirim notifikasi
 *   FALSE : Status sama → tidak ada notifikasi
 *
 * Branch 7 — Pelamar memiliki akun user
 *   TRUE  : Ada user → kirim notifikasi ke pelamar
 *   FALSE : Tidak ada user → skip
 *
 * Branch 8 — Status akhir diterima/ditolak
 *   TRUE  : diterima/ditolak → notifikasi kaprodi
 *   FALSE : status lain → skip
 *
 * ============================================================
 * PETA TEST CASE → BRANCH YANG DICAKUP:
 * ============================================================
 *
 * [Detail Pelamar]
 * TC-01 : B1=F, B2=T, B3=T → akses detail pelamar tanpa lamaran_id
 * TC-02 : B1=T, B3=T       → akses detail pelamar dengan lamaran_id spesifik
 * TC-03 : B3=F             → akses detail pelamar yang belum punya lamaran
 *
 * [Import Pelamar]
 * TC-04 : B5 (no exception) → import file Excel valid
 * TC-05 : validasi          → import file bukan Excel
 *
 * [Update Status Lamaran]
 * TC-06 : B6=F → update catatan tanpa ubah status
 * TC-07 : B6=T, B7=T, B8=F → ubah status ke seleksi_tahap1
 * TC-08 : B6=T, B7=T, B8=T → ubah status ke diterima, notif kaprodi
 * TC-09 : B6=T, B7=T, B8=T → ubah status ke ditolak, notif kaprodi
 */

use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Pelamar;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->prodi = Prodi::factory()->create();
});

// ================================================================
// DETAIL PELAMAR
// ================================================================

// ---------------------------------------------------------------
// TC-01 | Happy Path
// B1=F, B2=T, B3=T : Akses detail pelamar tanpa lamaran_id → fallback ke lamaran pertama
// ---------------------------------------------------------------
test('TC-01: Admin mengakses detail pelamar, sistem menampilkan data pelamar beserta lamaran', function () {
    // Arrange
    $pelamarUser = User::factory()->create(['role' => 'pelamar']);
    $pelamar     = Pelamar::factory()->create(['user_id' => $pelamarUser->id]);
    $lowongan    = Lowongan::factory()->create(['prodi_id' => $this->prodi->id]);

    Lamaran::factory()->create([
        'pelamar_id'  => $pelamar->id,
        'lowongan_id' => $lowongan->id,
        'status'      => 'menunggu',
    ]);

    // Act
    $response = $this->actingAs($this->admin)->get(route('admin.pelamar.show', $pelamar));

    // Assert
    $response->assertStatus(200)->assertViewIs('admin.pelamar.show');

    $activeLamaran = $response->viewData('activeLamaran');
    expect($activeLamaran)->not->toBeNull();
});

// ---------------------------------------------------------------
// TC-02 | Happy Path
// B1=T, B3=T : Akses detail pelamar dengan lamaran_id spesifik
// ---------------------------------------------------------------
test('TC-02: Admin mengakses detail pelamar dengan memilih lamaran tertentu, sistem menampilkan lamaran yang dipilih', function () {
    // Arrange
    $pelamarUser = User::factory()->create(['role' => 'pelamar']);
    $pelamar     = Pelamar::factory()->create(['user_id' => $pelamarUser->id]);
    $lowongan1   = Lowongan::factory()->create(['prodi_id' => $this->prodi->id]);
    $lowongan2   = Lowongan::factory()->create(['prodi_id' => $this->prodi->id]);

    $lamaran1 = Lamaran::factory()->create([
        'pelamar_id'  => $pelamar->id,
        'lowongan_id' => $lowongan1->id,
        'status'      => 'menunggu',
    ]);
    Lamaran::factory()->create([
        'pelamar_id'  => $pelamar->id,
        'lowongan_id' => $lowongan2->id,
        'status'      => 'diterima',
    ]);

    // Act: request dengan lamaran_id spesifik
    $response = $this->actingAs($this->admin)->get(route('admin.pelamar.show', $pelamar) . '?lamaran_id=' . $lamaran1->id);

    // Assert
    $response->assertStatus(200);

    $activeLamaran = $response->viewData('activeLamaran');
    expect($activeLamaran->id)->toBe($lamaran1->id);
});

// ---------------------------------------------------------------
// TC-03 | Edge Case
// B3=F : Pelamar belum punya lamaran → activeLamaran null
// ---------------------------------------------------------------
test('TC-03: Admin mengakses detail pelamar yang belum punya lamaran, sistem menampilkan data pelamar', function () {
    // Arrange
    $pelamarUser = User::factory()->create(['role' => 'pelamar']);
    $pelamar     = Pelamar::factory()->create(['user_id' => $pelamarUser->id]);

    // Act
    $response = $this->actingAs($this->admin)->get(route('admin.pelamar.show', $pelamar));

    // Assert
    $response->assertStatus(200);

    $activeLamaran = $response->viewData('activeLamaran');
    expect($activeLamaran)->toBeNull();
});

// ================================================================
// IMPORT PELAMAR
// ================================================================

// ---------------------------------------------------------------
// TC-04 | Happy Path
// Import file Excel valid
// ---------------------------------------------------------------
test('TC-04: Admin mengimpor data pelamar dengan file Excel valid, sistem berhasil mengimpor data', function () {
    // Arrange
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Header sesuai PelamarImport
    $header = ['email', 'nama', 'nik', 'no_telepon', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'alamat_domisili', 'jenjang', 'institusi'];
    $row = ['pelamar.import@example.com', 'Pelamar Import', '3201234567890001', '081234567890', 'L', 'Bandung', '1995-01-01', 'Jl. Test No. 1', 'S2', 'ITB'];
    $sheet->fromArray([$header, $row], null, 'A1');

    $tmpPath = tempnam(sys_get_temp_dir(), 'pelamar_import') . '.xlsx';
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save($tmpPath);

    $file = new \Illuminate\Http\UploadedFile(
        $tmpPath,
        'pelamar_import.xlsx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        null,
        true
    );

    // Act
    $response = $this->actingAs($this->admin)->post(route('admin.pelamar.import'), [
        'file' => $file,
    ]);

    // Assert
    $response->assertRedirect();
    $response->assertSessionHas('success');
});

// ---------------------------------------------------------------
// TC-05 | Validasi
// Import file bukan Excel
// ---------------------------------------------------------------
test('TC-05: Admin mengimpor data pelamar dengan file bukan Excel, sistem menampilkan pesan error', function () {
    // Arrange
    $file = \Illuminate\Http\UploadedFile::fake()->create('pelamar.pdf', 100, 'application/pdf');

    // Act
    $response = $this->actingAs($this->admin)->post(route('admin.pelamar.import'), [
        'file' => $file,
    ]);

    // Assert
    $response->assertSessionHasErrors(['file']);
});
