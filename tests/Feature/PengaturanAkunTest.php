<?php

/**
 * WHITE BOX TESTING - PENGATURAN AKUN
 * Teknik     : Branch Coverage
 * Controller : SettingController
 * 
 * ============================================================
 * ANALISIS BRANCH:
 * ============================================================
 * 
 * [update() — Ganti Password]
 * 
 *   if (!Hash::check($request->current_password, $user->password))  // Branch 1
 * 
 * Branch 1 — Validasi password lama
 *   TRUE  : Password lama tidak sesuai → return error
 *   FALSE : Password lama sesuai → update password
 * 
 * ------------------------------------------------------------
 * 
 * [updateFoto() — Update Foto Profil]
 * 
 *   if ($user->foto_profil && Storage::exists(...))                 // Branch 2
 *   if (!$path)                                                     // Branch 3
 *   try-catch                                                       // Branch 4
 * 
 * Branch 2 — User sudah memiliki foto profil lama
 *   TRUE  : Ada foto lama → hapus sebelum upload baru
 *   FALSE : Tidak ada foto lama → langsung upload
 * 
 * Branch 3 — Validasi hasil store
 *   TRUE  : Gagal menyimpan file → return error
 *   FALSE : Berhasil menyimpan → update database
 * 
 * Branch 4 — Exception handling
 *   Exception : Error saat upload → catch dan return error
 *   No Exception : Upload sukses → return success
 * 
 * ------------------------------------------------------------
 * 
 * [deleteFoto() — Hapus Foto Profil]
 * 
 *   if ($user->foto_profil && Storage::exists(...))                 // Branch 5
 * 
 * Branch 5 — User memiliki foto profil
 *   TRUE  : Ada foto → hapus dari storage
 *   FALSE : Tidak ada foto → skip
 * 
 * ============================================================
 * PETA TEST CASE → BRANCH YANG DICAKUP:
 * ============================================================
 * 
 * [Ganti Password]
 * TC-01 : B1=F → password lama benar, update berhasil
 * TC-02 : B1=T → password lama salah, return error
 * TC-03 : validasi → password baru < 8 karakter
 * TC-04 : validasi → konfirmasi password tidak cocok
 * 
 * [Update Foto Profil]
 * TC-05 : B2=F → user belum punya foto, upload foto baru
 * TC-06 : B2=T → user sudah punya foto, replace dengan foto baru
 * TC-07 : validasi → file bukan gambar
 * TC-08 : validasi → ukuran file melebihi batas
 * 
 * [Hapus Foto Profil]
 * TC-09 : B5=T → user punya foto, hapus berhasil
 */

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

// ================================================================
// GANTI PASSWORD
// ================================================================

// ---------------------------------------------------------------
// TC-01 | Happy Path
// B1=F : Password lama benar → update berhasil
// ---------------------------------------------------------------
test('TC-01: Pengguna menginput password lama yang benar, sistem berhasil memperbarui password', function () {
    // Arrange
    $user = User::factory()->create([
        'password' => Hash::make('OldPassword123'),
    ]);

    // Act
    $response = $this->actingAs($user)->put(route('settings.password.update'), [
        'current_password'      => 'OldPassword123',
        'password'              => 'NewPassword456',
        'password_confirmation' => 'NewPassword456',
    ]);

    // Assert
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Password berhasil diperbarui.');
    
    $user->refresh();
    expect(Hash::check('NewPassword456', $user->password))->toBeTrue();
});

// ---------------------------------------------------------------
// TC-02 | Unhappy Path
// B1=T : Password lama salah → return error
// ---------------------------------------------------------------
test('TC-02: Pengguna menginput password lama yang salah, sistem menampilkan pesan error', function () {
    // Arrange
    $user = User::factory()->create([
        'password' => Hash::make('OldPassword123'),
    ]);

    // Act
    $response = $this->actingAs($user)->put(route('settings.password.update'), [
        'current_password'      => 'WrongPassword',
        'password'              => 'NewPassword456',
        'password_confirmation' => 'NewPassword456',
    ]);

    // Assert
    $response->assertRedirect();
    $response->assertSessionHasErrors(['current_password' => 'Password lama tidak sesuai.']);
    
    $user->refresh();
    expect(Hash::check('OldPassword123', $user->password))->toBeTrue();
});

// ---------------------------------------------------------------
// TC-03 | Validasi
// Password baru kurang dari 8 karakter
// ---------------------------------------------------------------
test('TC-03: Pengguna menginput password baru kurang dari 8 karakter, sistem menampilkan pesan error', function () {
    // Arrange
    $user = User::factory()->create([
        'password' => Hash::make('OldPassword123'),
    ]);

    // Act
    $response = $this->actingAs($user)->put(route('settings.password.update'), [
        'current_password'      => 'OldPassword123',
        'password'              => 'Short1',
        'password_confirmation' => 'Short1',
    ]);

    // Assert
    $response->assertSessionHasErrors(['password']);
});

// ---------------------------------------------------------------
// TC-04 | Validasi
// Konfirmasi password tidak cocok
// ---------------------------------------------------------------
test('TC-04: Pengguna menginput konfirmasi password yang tidak cocok, sistem menampilkan pesan error', function () {
    // Arrange
    $user = User::factory()->create([
        'password' => Hash::make('OldPassword123'),
    ]);

    // Act
    $response = $this->actingAs($user)->put(route('settings.password.update'), [
        'current_password'      => 'OldPassword123',
        'password'              => 'NewPassword456',
        'password_confirmation' => 'DifferentPassword',
    ]);

    // Assert
    $response->assertSessionHasErrors(['password']);
});

// ================================================================
// UPDATE FOTO PROFIL
// ================================================================

// ---------------------------------------------------------------
// TC-05 | Happy Path
// B2=F : User belum punya foto → upload foto baru
// ---------------------------------------------------------------
test('TC-05: Pengguna mengunggah foto profil pertama kali, sistem berhasil menyimpan foto', function () {
    // Arrange
    Storage::fake('public');
    $user = User::factory()->create(['foto_profil' => null]);

    // Act
    $response = $this->actingAs($user)->post(route('settings.foto.update'), [
        'foto_profil' => UploadedFile::fake()->image('avatar.jpg'),
    ]);

    // Assert
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Foto profil berhasil diperbarui.');
    
    $user->refresh();
    expect($user->foto_profil)->not->toBeNull();
    Storage::disk('public')->assertExists($user->foto_profil);
});

// ---------------------------------------------------------------
// TC-06 | Happy Path
// B2=T : User sudah punya foto → replace dengan foto baru
// ---------------------------------------------------------------
test('TC-06: Pengguna mengganti foto profil yang sudah ada, sistem menghapus foto lama dan menyimpan foto baru', function () {
    // Arrange
    Storage::fake('public');
    $user = User::factory()->create();
    
    // Buat foto lama
    $oldPhoto = UploadedFile::fake()->image('old-avatar.jpg');
    $oldPath = $oldPhoto->store('foto_profil/'.$user->id, 'public');
    $user->update(['foto_profil' => $oldPath]);

    // Act
    $response = $this->actingAs($user)->post(route('settings.foto.update'), [
        'foto_profil' => UploadedFile::fake()->image('new-avatar.jpg'),
    ]);

    // Assert
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Foto profil berhasil diperbarui.');
    
    $user->refresh();
    expect($user->foto_profil)->not->toBe($oldPath);
    Storage::disk('public')->assertMissing($oldPath);
    Storage::disk('public')->assertExists($user->foto_profil);
});

// ---------------------------------------------------------------
// TC-07 | Validasi
// File bukan gambar
// ---------------------------------------------------------------
test('TC-07: Pengguna mengunggah file bukan gambar, sistem menampilkan pesan error', function () {
    // Arrange
    Storage::fake('public');
    $user = User::factory()->create(['foto_profil' => null]);

    // Act
    $response = $this->actingAs($user)->post(route('settings.foto.update'), [
        'foto_profil' => UploadedFile::fake()->create('document.pdf', 100),
    ]);

    // Assert
    $response->assertSessionHasErrors(['foto_profil']);
});

// ---------------------------------------------------------------
// TC-08 | Validasi
// Ukuran file melebihi batas (8 MB)
// ---------------------------------------------------------------
test('TC-08: Pengguna mengunggah foto melebihi ukuran maksimal, sistem menampilkan pesan error', function () {
    // Arrange
    Storage::fake('public');
    $user = User::factory()->create(['foto_profil' => null]);

    // Act
    $response = $this->actingAs($user)->post(route('settings.foto.update'), [
        'foto_profil' => UploadedFile::fake()->image('large-avatar.jpg')->size(9000), // 9 MB
    ]);

    // Assert
    $response->assertSessionHasErrors(['foto_profil']);
});

// ================================================================
// HAPUS FOTO PROFIL
// ================================================================

// ---------------------------------------------------------------
// TC-09 | Happy Path
// B5=T : User punya foto → hapus berhasil
// ---------------------------------------------------------------
test('TC-09: Pengguna menghapus foto profil yang ada, sistem berhasil menghapus foto', function () {
    // Arrange
    Storage::fake('public');
    $user = User::factory()->create();
    
    // Buat foto profil
    $photo = UploadedFile::fake()->image('avatar.jpg');
    $path = $photo->store('foto_profil/'.$user->id, 'public');
    $user->update(['foto_profil' => $path]);

    // Act
    $response = $this->actingAs($user)->delete(route('settings.foto.delete'));

    // Assert
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Foto profil berhasil dihapus.');
    
    $user->refresh();
    expect($user->foto_profil)->toBeNull();
    Storage::disk('public')->assertMissing($path);
});
