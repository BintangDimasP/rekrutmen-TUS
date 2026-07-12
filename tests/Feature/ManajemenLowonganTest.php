<?php

/**
 * WHITE BOX TESTING - MANAJEMEN LOWONGAN
 * Teknik     : Branch Coverage
 * Controller : Admin\LowonganController
 *
 * ============================================================
 * ANALISIS BRANCH:
 * ============================================================
 *
 * [store() — Buat Lowongan Baru]
 *
 *   if (is_string($prodiPrioritasRaw) && $prodiPrioritasRaw !== '')  // Branch 1
 *   if (is_string($skillRaw) && $skillRaw !== '')                    // Branch 2
 *
 * Branch 1 — Prodi prioritas diisi
 *   TRUE  : Ada nilai → parse dan simpan
 *   FALSE : Kosong → simpan null
 *
 * Branch 2 — Skill dibutuhkan diisi
 *   TRUE  : Ada nilai → parse dan simpan
 *   FALSE : Kosong → simpan null
 *
 * ------------------------------------------------------------
 *
 * [update() — Update Lowongan]
 *
 *   foreach ['prodi_prioritas', 'skill_dibutuhkan']                 // Branch 3
 *     if (is_string($raw) && $raw !== '') → parse                   // Branch 3
 *     else → null                                                   // Branch 3
 *
 * (Sama dengan store, tapi di loop foreach)
 *
 * ------------------------------------------------------------
 *
 * [toggleStatus() — Toggle Status Lowongan]
 *
 *   $newStatus = $rawStatus === 'aktif' ? 'ditutup' : 'aktif'       // Branch 4
 *   if ($newStatus === 'aktif')                                      // Branch 5
 *     if ($lowongan->tanggal_tutup && isPast())                     // Branch 6
 *     if ($lowongan->sisa_kuota <= 0)                               // Branch 7
 *
 * Branch 4 — Arah toggle
 *   TRUE  : Status aktif → jadi ditutup
 *   FALSE : Status ditutup/draft → jadi aktif
 *
 * Branch 5 — Validasi saat akan diaktifkan
 *   TRUE  : Akan diaktifkan → cek tanggal dan kuota
 *   FALSE : Akan ditutup → langsung update
 *
 * Branch 6 — Tanggal tutup sudah lewat
 *   TRUE  : Sudah lewat → return error
 *   FALSE : Belum lewat → lanjut cek kuota
 *
 * Branch 7 — Kuota habis
 *   TRUE  : Kuota habis → return error
 *   FALSE : Kuota cukup → update status
 *
 * ============================================================
 * PETA TEST CASE → BRANCH YANG DICAKUP:
 * ============================================================
 *
 * [Buat Lowongan]
 * TC-01 : B1=F, B2=F → buat lowongan tanpa prodi prioritas dan skill
 * TC-02 : B1=T, B2=T → buat lowongan dengan prodi prioritas dan skill
 * TC-03 : validasi   → tanggal tutup bukan setelah hari ini
 *
 * [Update Lowongan]
 * TC-04 : B3=F → update lowongan, field optional dikosongkan
 * TC-05 : B3=T → update lowongan dengan prodi prioritas dan skill
 *
 * [Toggle Status]
 * TC-06 : B4=T (aktif→ditutup) → toggle lowongan aktif menjadi ditutup
 * TC-07 : B4=F, B5=T, B6=F, B7=F → toggle lowongan ditutup menjadi aktif (valid)
 * TC-08 : B5=T, B6=T → aktifkan lowongan yang tanggal tutupnya sudah lewat
 * TC-09 : B5=T, B7=T → aktifkan lowongan yang kuotanya habis
 */

use App\Models\Lowongan;
use App\Models\Pelamar;
use App\Models\Lamaran;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->prodi = Prodi::factory()->create();
});

// ================================================================
// BUAT LOWONGAN BARU
// ================================================================

// ---------------------------------------------------------------
// TC-01 | Happy Path
// B1=F, B2=F : Buat lowongan tanpa prodi prioritas dan skill
// ---------------------------------------------------------------
test('TC-01: Admin membuat lowongan tanpa prodi prioritas dan skill, sistem berhasil menyimpan lowongan', function () {
    // Act
    $response = $this->actingAs($this->admin)->post(route('admin.lowongan.store'), [
        'nama_posisi'     => 'Dosen Teknik Informatika',
        'prodi_id'        => $this->prodi->id,
        'jenjang_minimal' => 'S2',
        'minimal_ipk'     => 3.0,
        'kuota'           => 5,
        'tanggal_tutup'   => now()->addDays(30)->format('Y-m-d'),
        'status'          => 'aktif',
    ]);

    // Assert
    $response->assertRedirect(route('admin.lowongan.index'));
    $response->assertSessionHas('success');

    $lowongan = Lowongan::where('nama_posisi', 'Dosen Teknik Informatika')->first();
    expect($lowongan)->not->toBeNull();
    expect($lowongan->prodi_prioritas)->toBeNull();
    expect($lowongan->skill_dibutuhkan)->toBeNull();
});

// ---------------------------------------------------------------
// TC-02 | Happy Path
// B1=T, B2=T : Buat lowongan dengan prodi prioritas dan skill
// ---------------------------------------------------------------
test('TC-02: Admin membuat lowongan dengan prodi prioritas dan skill, sistem berhasil menyimpan dengan data lengkap', function () {
    // Act
    $response = $this->actingAs($this->admin)->post(route('admin.lowongan.store'), [
        'nama_posisi'      => 'Dosen Sistem Informasi',
        'prodi_id'         => $this->prodi->id,
        'jenjang_minimal'  => 'S2',
        'minimal_ipk'      => 3.0,
        'kuota'            => 3,
        'tanggal_tutup'    => now()->addDays(30)->format('Y-m-d'),
        'status'           => 'aktif',
        'prodi_prioritas'  => 'Sistem Informasi||Teknik Informatika',
        'skill_dibutuhkan' => 'Machine Learning||Data Science',
    ]);

    // Assert
    $response->assertRedirect(route('admin.lowongan.index'));
    $response->assertSessionHas('success');

    $lowongan = Lowongan::where('nama_posisi', 'Dosen Sistem Informasi')->first();
    expect($lowongan)->not->toBeNull();
    expect($lowongan->prodi_prioritas)->toBe('Sistem Informasi, Teknik Informatika');
    expect($lowongan->skill_dibutuhkan)->toBe('Machine Learning, Data Science');
});

// ---------------------------------------------------------------
// TC-03 | Validasi
// Tanggal tutup bukan setelah hari ini
// ---------------------------------------------------------------
test('TC-03: Admin membuat lowongan dengan tanggal tutup yang sudah lewat, sistem menampilkan pesan error', function () {
    // Act
    $response = $this->actingAs($this->admin)->post(route('admin.lowongan.store'), [
        'nama_posisi'     => 'Dosen Test',
        'prodi_id'        => $this->prodi->id,
        'jenjang_minimal' => 'S1',
        'minimal_ipk'     => 3.0,
        'kuota'           => 1,
        'tanggal_tutup'   => now()->subDays(1)->format('Y-m-d'), // kemarin
        'status'          => 'aktif',
    ]);

    // Assert
    $response->assertSessionHasErrors(['tanggal_tutup']);
});

// ================================================================
// UPDATE LOWONGAN
// ================================================================

// ---------------------------------------------------------------
// TC-04 | Happy Path
// B3=F : Update lowongan, field optional dikosongkan
// ---------------------------------------------------------------
test('TC-04: Admin mengubah data lowongan tanpa prodi prioritas dan skill, sistem berhasil memperbarui', function () {
    // Arrange
    $lowongan = Lowongan::factory()->create([
        'prodi_id'         => $this->prodi->id,
        'nama_posisi'      => 'Dosen Lama',
        'prodi_prioritas'  => 'Teknik Informatika',
        'skill_dibutuhkan' => 'Python',
        'tanggal_tutup'    => now()->addDays(30),
        'status'           => 'draft',
    ]);

    // Act
    $response = $this->actingAs($this->admin)->put(route('admin.lowongan.update', $lowongan), [
        'nama_posisi'     => 'Dosen Baru',
        'prodi_id'        => $this->prodi->id,
        'jenjang_minimal' => 'S2',
        'minimal_ipk'     => 3.0,
        'kuota'           => 5,
        'tanggal_tutup'   => now()->addDays(30)->format('Y-m-d'),
        'status'          => 'draft',
        // prodi_prioritas dan skill_dibutuhkan tidak dikirim → null
    ]);

    // Assert
    $response->assertRedirect(route('admin.lowongan.index'));
    $response->assertSessionHas('success');

    $lowongan->refresh();
    expect($lowongan->nama_posisi)->toBe('Dosen Baru');
    expect($lowongan->prodi_prioritas)->toBeNull();
    expect($lowongan->skill_dibutuhkan)->toBeNull();
});

// ---------------------------------------------------------------
// TC-05 | Happy Path
// B3=T : Update lowongan dengan prodi prioritas dan skill
// ---------------------------------------------------------------
test('TC-05: Admin mengubah lowongan dengan menambahkan prodi prioritas dan skill, sistem berhasil memperbarui', function () {
    // Arrange
    $lowongan = Lowongan::factory()->create([
        'prodi_id'        => $this->prodi->id,
        'tanggal_tutup'   => now()->addDays(30),
        'status'          => 'draft',
    ]);

    // Act
    $response = $this->actingAs($this->admin)->put(route('admin.lowongan.update', $lowongan), [
        'nama_posisi'      => $lowongan->nama_posisi,
        'prodi_id'         => $this->prodi->id,
        'jenjang_minimal'  => 'S2',
        'minimal_ipk'      => 3.0,
        'kuota'            => 5,
        'tanggal_tutup'    => now()->addDays(30)->format('Y-m-d'),
        'status'           => 'draft',
        'prodi_prioritas'  => 'Teknik Informatika||Sistem Informasi',
        'skill_dibutuhkan' => 'Web Development||Cloud Computing',
    ]);

    // Assert
    $response->assertRedirect(route('admin.lowongan.index'));

    $lowongan->refresh();
    expect($lowongan->prodi_prioritas)->toBe('Teknik Informatika, Sistem Informasi');
    expect($lowongan->skill_dibutuhkan)->toBe('Web Development, Cloud Computing');
});

// ================================================================
// TOGGLE STATUS
// ================================================================

// ---------------------------------------------------------------
// TC-06 | Happy Path
// B4=T : Toggle lowongan aktif → menjadi ditutup
// ---------------------------------------------------------------
test('TC-06: Admin menonaktifkan lowongan yang sedang aktif, sistem mengubah status menjadi ditutup', function () {
    // Arrange: status 'aktif' di DB dengan tanggal belum lewat
    $lowongan = Lowongan::factory()->create([
        'prodi_id'      => $this->prodi->id,
        'status'        => 'aktif',
        'tanggal_tutup' => now()->addDays(10),
        'kuota'         => 5,
    ]);

    // Act
    $response = $this->actingAs($this->admin)->patch(route('admin.lowongan.toggleStatus', $lowongan));

    // Assert
    $response->assertRedirect(route('admin.lowongan.index'));
    $response->assertSessionHas('success');

    expect($lowongan->fresh()->getRawOriginal('status'))->toBe('ditutup');
});

// ---------------------------------------------------------------
// TC-07 | Happy Path
// B4=F, B5=T, B6=F, B7=F : Toggle lowongan ditutup → menjadi aktif (valid)
// ---------------------------------------------------------------
test('TC-07: Admin mengaktifkan lowongan yang sedang ditutup dengan data valid, sistem mengubah status menjadi aktif', function () {
    // Arrange
    $lowongan = Lowongan::factory()->create([
        'prodi_id'      => $this->prodi->id,
        'status'        => 'ditutup',
        'tanggal_tutup' => now()->addDays(10), // belum lewat
        'kuota'         => 5,                  // kuota cukup
    ]);

    // Act
    $response = $this->actingAs($this->admin)->patch(route('admin.lowongan.toggleStatus', $lowongan));

    // Assert
    $response->assertRedirect(route('admin.lowongan.index'));
    $response->assertSessionHas('success');

    expect($lowongan->fresh()->getRawOriginal('status'))->toBe('aktif');
});

// ---------------------------------------------------------------
// TC-08 | Unhappy Path
// B5=T, B6=T : Aktifkan lowongan yang tanggal tutupnya sudah lewat
// ---------------------------------------------------------------
test('TC-08: Admin mengaktifkan lowongan yang tanggal tutupnya sudah lewat, sistem menampilkan pesan error', function () {
    // Arrange
    $lowongan = Lowongan::factory()->create([
        'prodi_id'      => $this->prodi->id,
        'status'        => 'ditutup',
        'tanggal_tutup' => now()->subDays(1), // sudah lewat
        'kuota'         => 5,
    ]);

    // Act
    $response = $this->actingAs($this->admin)->patch(route('admin.lowongan.toggleStatus', $lowongan));

    // Assert
    $response->assertRedirect(route('admin.lowongan.index'));
    $response->assertSessionHas('error');

    expect($lowongan->fresh()->getRawOriginal('status'))->toBe('ditutup'); // tidak berubah
});

// ---------------------------------------------------------------
// TC-09 | Unhappy Path
// B5=T, B7=T : Aktifkan lowongan yang kuotanya habis
// ---------------------------------------------------------------
test('TC-09: Admin mengaktifkan lowongan yang kuotanya habis, sistem menampilkan pesan error', function () {
    // Arrange
    $lowongan = Lowongan::factory()->create([
        'prodi_id'      => $this->prodi->id,
        'status'        => 'ditutup',
        'tanggal_tutup' => now()->addDays(10),
        'kuota'         => 2,
    ]);

    // Isi kuota dengan lamaran aktif
    $pelamar1 = Pelamar::factory()->create();
    $pelamar2 = Pelamar::factory()->create();
    Lamaran::factory()->create([
        'lowongan_id' => $lowongan->id,
        'pelamar_id'  => $pelamar1->id,
        'status'      => 'menunggu',
    ]);
    Lamaran::factory()->create([
        'lowongan_id' => $lowongan->id,
        'pelamar_id'  => $pelamar2->id,
        'status'      => 'menunggu',
    ]);

    // Act
    $response = $this->actingAs($this->admin)->patch(route('admin.lowongan.toggleStatus', $lowongan));

    // Assert
    $response->assertRedirect(route('admin.lowongan.index'));
    $response->assertSessionHas('error');

    expect($lowongan->fresh()->getRawOriginal('status'))->toBe('ditutup'); // tidak berubah
});
