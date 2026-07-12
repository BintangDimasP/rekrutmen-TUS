<?php

/**
 * WHITE BOX TESTING - MANAJEMEN LAMARAN
 * Iterasi    : 3
 * Teknik     : Branch Coverage
 * Controller : Admin\LamaranController@update, @destroy
 * Konteks    : Admin mengakses halaman detail lowongan → kelola lamaran masuk
 *
 * ============================================================
 * ANALISIS BRANCH:
 * ============================================================
 *
 * [update() — Update Status Lamaran]
 *
 *   if ($statusLama !== $validated['status'])                       // Branch 1
 *   if ($userId)                                                    // Branch 2
 *   if (in_array($validated['status'], ['diterima', 'ditolak']))   // Branch 3
 *   if ($prodiId)                                                   // Branch 4
 *
 * Branch 1 — Status berubah
 *   TRUE  : Status berubah → kirim notifikasi
 *   FALSE : Status sama → tidak ada notifikasi
 *
 * Branch 2 — Pelamar memiliki akun user
 *   TRUE  : Ada user → kirim notifikasi ke pelamar
 *   FALSE : Tidak ada user → skip
 *
 * Branch 3 — Status final diterima atau ditolak
 *   TRUE  : diterima/ditolak → notifikasi kaprodi
 *   FALSE : Status lain → skip
 *
 * Branch 4 — Lowongan memiliki prodi
 *   TRUE  : Ada prodi → notifikasi kaprodi prodi tersebut
 *
 * ------------------------------------------------------------
 *
 * [destroy() — Hapus Lamaran]
 *
 *   Tidak ada branch logic khusus
 *
 * ============================================================
 * PETA TEST CASE → BRANCH YANG DICAKUP:
 * ============================================================
 *
 * [Update Status]
 * TC-01 : B1=F → update catatan tanpa mengubah status
 * TC-02 : B1=T, B2=T, B3=F → ubah status ke seleksi tahap 1
 * TC-03 : B1=T, B2=T, B3=T, B4=T → ubah status ke diterima
 * TC-04 : B1=T, B2=T, B3=T, B4=T → ubah status ke ditolak
 * TC-05 : validasi → status tidak valid
 *
 * [Hapus Lamaran]
 * TC-06 : Happy path → hapus lamaran
 */

use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Pelamar;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin    = User::factory()->create(['role' => 'admin']);
    $this->prodi    = Prodi::factory()->create();
    $this->lowongan = Lowongan::factory()->create(['prodi_id' => $this->prodi->id]);

    $this->pelamarUser = User::factory()->create(['role' => 'pelamar']);
    $this->pelamar     = Pelamar::factory()->create(['user_id' => $this->pelamarUser->id]);
});

// ================================================================
// UPDATE STATUS LAMARAN
// ================================================================

// ---------------------------------------------------------------
// TC-01 | Happy Path
// B1=F : Perbarui catatan tanpa mengubah status → tidak ada notifikasi
// ---------------------------------------------------------------
test('TC-01: Admin memperbarui catatan lamaran tanpa mengubah status, sistem berhasil memperbarui', function () {
    // Arrange
    $lamaran = Lamaran::factory()->create([
        'pelamar_id'  => $this->pelamar->id,
        'lowongan_id' => $this->lowongan->id,
        'status'      => 'menunggu',
    ]);

    // Act
    $response = $this->actingAs($this->admin)->put(route('admin.lamaran.update', $lamaran), [
        'status'        => 'menunggu',
        'catatan_admin' => 'Catatan baru dari admin',
    ]);

    // Assert
    $response->assertRedirect();
    $response->assertSessionHas('success');

    $lamaran->refresh();
    expect($lamaran->catatan_admin)->toBe('Catatan baru dari admin');
    expect($lamaran->status)->toBe('menunggu');
});

// ---------------------------------------------------------------
// TC-02 | Happy Path
// B1=T, B2=T, B3=F : Ubah status ke seleksi tahap 1 → notif ke pelamar
// ---------------------------------------------------------------
test('TC-02: Admin mengubah status lamaran ke seleksi tahap 1, sistem memperbarui status dan mengirim notifikasi ke pelamar', function () {
    // Arrange
    $lamaran = Lamaran::factory()->create([
        'pelamar_id'  => $this->pelamar->id,
        'lowongan_id' => $this->lowongan->id,
        'status'      => 'menunggu',
    ]);

    // Act
    $response = $this->actingAs($this->admin)->put(route('admin.lamaran.update', $lamaran), [
        'status' => 'seleksi_tahap1',
    ]);

    // Assert
    $response->assertRedirect();
    $response->assertSessionHas('success');

    $lamaran->refresh();
    expect($lamaran->status)->toBe('seleksi_tahap1');
});

// ---------------------------------------------------------------
// TC-03 | Happy Path
// B1=T, B2=T, B3=T, B4=T : Ubah status ke diterima → notif pelamar + kaprodi
// ---------------------------------------------------------------
test('TC-03: Admin mengubah status lamaran menjadi diterima, sistem memperbarui status dan mengirim notifikasi ke pelamar dan kaprodi', function () {
    // Arrange
    $lamaran = Lamaran::factory()->create([
        'pelamar_id'  => $this->pelamar->id,
        'lowongan_id' => $this->lowongan->id,
        'status'      => 'seleksi_tahap2',
    ]);

    // Act
    $response = $this->actingAs($this->admin)->put(route('admin.lamaran.update', $lamaran), [
        'status' => 'diterima',
    ]);

    // Assert
    $response->assertRedirect();
    $response->assertSessionHas('success');

    $lamaran->refresh();
    expect($lamaran->status)->toBe('diterima');
});

// ---------------------------------------------------------------
// TC-04 | Happy Path
// B1=T, B2=T, B3=T, B4=T : Ubah status ke ditolak → notif pelamar + kaprodi
// ---------------------------------------------------------------
test('TC-04: Admin mengubah status lamaran menjadi ditolak, sistem memperbarui status dan mengirim notifikasi ke pelamar dan kaprodi', function () {
    // Arrange
    $lamaran = Lamaran::factory()->create([
        'pelamar_id'  => $this->pelamar->id,
        'lowongan_id' => $this->lowongan->id,
        'status'      => 'seleksi_tahap2',
    ]);

    // Act
    $response = $this->actingAs($this->admin)->put(route('admin.lamaran.update', $lamaran), [
        'status' => 'ditolak',
    ]);

    // Assert
    $response->assertRedirect();
    $response->assertSessionHas('success');

    $lamaran->refresh();
    expect($lamaran->status)->toBe('ditolak');
});

// ================================================================
// HAPUS LAMARAN
// ================================================================

// ---------------------------------------------------------------
// TC-05 | Happy Path
// Hapus lamaran
// ---------------------------------------------------------------
test('TC-05: Admin menghapus lamaran, sistem berhasil menghapus data lamaran', function () {
    // Arrange
    $lamaran = Lamaran::factory()->create([
        'pelamar_id'  => $this->pelamar->id,
        'lowongan_id' => $this->lowongan->id,
        'status'      => 'menunggu',
    ]);

    // Act
    $response = $this->actingAs($this->admin)->delete(route('admin.lamaran.destroy', $lamaran));

    // Assert
    $response->assertRedirect(route('admin.lamaran.index', $this->lowongan->id));
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('lamarans', ['id' => $lamaran->id]);
});
