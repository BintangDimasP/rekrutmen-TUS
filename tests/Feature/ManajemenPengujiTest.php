<?php

/**
 * WHITE BOX TESTING - MANAJEMEN PENGUJI
 * Teknik     : Branch Coverage
 * Controller : Admin\PengujiController
 *
 * ============================================================
 * ANALISIS BRANCH:
 * ============================================================
 *
 * [store() — Tunjuk Penguji]
 *
 *   foreach dosen_ids → getOrCreateUser()                          // Branch 1
 *   if (empty($user->role)) → set role penguji                     // Branch 2
 *   else                    → biarkan role tetap (kaprodi rangkap) // Branch 2
 *
 * Branch 1 — Dosen sudah punya akun atau belum
 *   TRUE  : Sudah punya akun → gunakan akun existing
 *   FALSE : Belum punya akun → buat akun baru
 *
 * Branch 2 — Dosen sudah punya role aktif
 *   TRUE  : Belum punya role → set role penguji
 *   FALSE : Sudah kaprodi → role tetap kaprodi, tambah flag is_penguji
 *
 * ------------------------------------------------------------
 *
 * [destroy() — Cabut Penguji]
 *
 *   if (!$user) return                                              // Branch 3
 *   if ($user->is_kaprodi) → downgrade ke kaprodi                  // Branch 4
 *   else                   → hapus akun user                       // Branch 4
 *
 * Branch 3 — User tidak ada
 *   TRUE  : Tidak ada akun → skip
 *
 * Branch 4 — Penguji juga kaprodi
 *   TRUE  : Masih kaprodi → role kembali ke kaprodi
 *   FALSE : Bukan kaprodi → hapus akun user
 *
 * ============================================================
 * PETA TEST CASE → BRANCH YANG DICAKUP:
 * ============================================================
 *
 * [Tunjuk Penguji]
 * TC-01 : B1=F, B2=T → dosen belum punya akun, ditunjuk penguji
 * TC-02 : B1=T, B2=F → dosen sudah kaprodi, ditunjuk penguji (rangkap)
 * TC-03 : validasi   → dosen_ids kosong
 *
 * [Cabut Penguji]
 * TC-04 : B4=F → penguji biasa dicabut, akun dihapus
 * TC-05 : B4=T → penguji yang juga kaprodi dicabut, role kembali ke kaprodi
 */

use App\Models\Dosen;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->prodi = Prodi::factory()->create();
});

// ================================================================
// TUNJUK PENGUJI
// ================================================================

// ---------------------------------------------------------------
// TC-01 | Happy Path
// B1=F, B2=T : Dosen belum punya akun → buat akun dengan role penguji
// ---------------------------------------------------------------
test('TC-01: Admin menunjuk dosen biasa sebagai penguji, sistem membuat akun penguji', function () {
    // Arrange
    $dosen = Dosen::factory()->create([
        'prodi_id'   => $this->prodi->id,
        'is_penguji' => false,
        'is_kaprodi' => false,
    ]);

    // Pastikan belum ada akun user
    $this->assertDatabaseMissing('users', ['dosen_id' => $dosen->id]);

    // Act
    $response = $this->actingAs($this->admin)->post(route('admin.penguji.store'), [
        'dosen_ids' => [$dosen->id],
    ]);

    // Assert
    $response->assertRedirect();
    $response->assertSessionHas('success');

    $dosen->refresh();
    expect($dosen->is_penguji)->toBeTrue();

    $user = User::where('dosen_id', $dosen->id)->first();
    expect($user)->not->toBeNull();
    expect($user->role)->toBe('penguji');
    expect($user->is_penguji)->toBeTrue();
});

// ---------------------------------------------------------------
// TC-02 | Happy Path
// B1=T, B2=F : Dosen sudah kaprodi → rangkap penguji, role tetap kaprodi
// ---------------------------------------------------------------
test('TC-02: Admin menunjuk dosen yang sudah kaprodi sebagai penguji, sistem menambahkan flag penguji tanpa mengubah role', function () {
    // Arrange
    $dosen = Dosen::factory()->create([
        'prodi_id'   => $this->prodi->id,
        'is_penguji' => false,
        'is_kaprodi' => true,
    ]);
    $user = User::factory()->create([
        'role'       => 'kaprodi',
        'dosen_id'   => $dosen->id,
        'prodi_id'   => $this->prodi->id,
        'is_kaprodi' => true,
        'is_penguji' => false,
    ]);

    // Act
    $response = $this->actingAs($this->admin)->post(route('admin.penguji.store'), [
        'dosen_ids' => [$dosen->id],
    ]);

    // Assert
    $response->assertRedirect();
    $response->assertSessionHas('success');

    $dosen->refresh();
    expect($dosen->is_penguji)->toBeTrue();

    $user->refresh();
    expect($user->role)->toBe('kaprodi'); // Role tetap kaprodi
    expect($user->is_penguji)->toBeTrue();
});

// ---------------------------------------------------------------
// TC-03 | Validasi
// dosen_ids kosong
// ---------------------------------------------------------------
test('TC-03: Admin menunjuk penguji tanpa memilih dosen, sistem menampilkan pesan error', function () {
    // Act
    $response = $this->actingAs($this->admin)->post(route('admin.penguji.store'), [
        'dosen_ids' => [],
    ]);

    // Assert
    $response->assertSessionHasErrors(['dosen_ids']);
});

// ================================================================
// CABUT PENGUJI
// ================================================================

// ---------------------------------------------------------------
// TC-04 | Happy Path
// B4=F : Penguji biasa dicabut → akun user dihapus
// ---------------------------------------------------------------
test('TC-04: Admin mencabut status penguji dari dosen yang bukan kaprodi, sistem menghapus akun user', function () {
    // Arrange
    $dosen = Dosen::factory()->create([
        'prodi_id'   => $this->prodi->id,
        'is_penguji' => true,
        'is_kaprodi' => false,
    ]);
    $user = User::factory()->create([
        'role'       => 'penguji',
        'dosen_id'   => $dosen->id,
        'is_penguji' => true,
        'is_kaprodi' => false,
    ]);

    // Act
    $response = $this->actingAs($this->admin)->delete(route('admin.penguji.destroy', $dosen));

    // Assert
    $response->assertRedirect(route('admin.penguji.index'));
    $response->assertSessionHas('success');

    $dosen->refresh();
    expect($dosen->is_penguji)->toBeFalse();
    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});

// ---------------------------------------------------------------
// TC-05 | Happy Path
// B4=T : Penguji yang juga kaprodi dicabut → role kembali ke kaprodi
// ---------------------------------------------------------------
test('TC-05: Admin mencabut status penguji dari dosen yang juga kaprodi, sistem mengubah role kembali ke kaprodi', function () {
    // Arrange
    $dosen = Dosen::factory()->create([
        'prodi_id'   => $this->prodi->id,
        'is_penguji' => true,
        'is_kaprodi' => true,
    ]);
    $user = User::factory()->create([
        'role'       => 'kaprodi',
        'dosen_id'   => $dosen->id,
        'prodi_id'   => $this->prodi->id,
        'is_penguji' => true,
        'is_kaprodi' => true,
    ]);

    // Act
    $response = $this->actingAs($this->admin)->delete(route('admin.penguji.destroy', $dosen));

    // Assert
    $response->assertRedirect(route('admin.penguji.index'));
    $response->assertSessionHas('success');

    $dosen->refresh();
    expect($dosen->is_penguji)->toBeFalse();

    $user->refresh();
    expect($user->role)->toBe('kaprodi');
    expect($user->is_penguji)->toBeFalse();
});
