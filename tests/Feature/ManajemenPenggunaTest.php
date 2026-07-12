<?php

use App\Models\Dosen;
use App\Models\Pelamar;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
});

test('TC-01: Admin menambahkan admin baru, sistem berhasil membuat akun admin', function () {
    // Act
    $response = $this->actingAs($this->admin)->post(route('admin.user.store'), [
        'name'     => 'Admin Baru',
        'username' => 'adminbaru',
        'password' => 'Password123',
    ]);

    // Assert
    $response->assertRedirect();
    $response->assertSessionHas('success');
    
    $this->assertDatabaseHas('users', [
        'name'  => 'Admin Baru',
        'email' => 'adminbaru@admin.telkomuniversity.ac.id',
        'role'  => 'admin',
    ]);
});

// ---------------------------------------------------------------
// TC-02 | Unhappy Path
// B1=T : Username sudah ada → return error
// ---------------------------------------------------------------
test('TC-02: Admin menambahkan admin dengan username yang sudah ada, sistem menampilkan pesan error', function () {
    // Arrange
    User::factory()->create([
        'email' => 'existing@admin.telkomuniversity.ac.id',
        'role'  => 'admin',
    ]);

    // Act
    $response = $this->actingAs($this->admin)->post(route('admin.user.store'), [
        'name'     => 'Admin Duplikat',
        'username' => 'existing',
        'password' => 'Password123',
    ]);

    // Assert
    $response->assertRedirect();
    $response->assertSessionHasErrors(['username']);
});

// ---------------------------------------------------------------
// TC-03 | Validasi
// Username dengan format salah (huruf besar, spasi, dll)
// ---------------------------------------------------------------
test('TC-03: Admin menambahkan admin dengan username format salah, sistem menampilkan pesan error', function () {
    // Act
    $response = $this->actingAs($this->admin)->post(route('admin.user.store'), [
        'name'     => 'Admin Test',
        'username' => 'Admin User', // spasi tidak diizinkan
        'password' => 'Password123',
    ]);

    // Assert
    $response->assertSessionHasErrors(['username']);
});

// ================================================================
// HAPUS USER
// ================================================================

// ---------------------------------------------------------------
// TC-04 | Unhappy Path
// B2=T : Admin mencoba hapus diri sendiri → return error
// ---------------------------------------------------------------
test('TC-04: Admin mencoba menghapus akun diri sendiri, sistem menampilkan pesan error', function () {
    // Act
    $response = $this->actingAs($this->admin)->delete(route('admin.user.destroy', $this->admin));

    // Assert
    $response->assertRedirect();
    $response->assertSessionHasErrors(['delete']);
    
    $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
});

// ---------------------------------------------------------------
// TC-05 | Happy Path
// B2=F, B3=T : Admin menghapus admin lain → delete sepenuhnya
// ---------------------------------------------------------------
test('TC-05: Admin menghapus akun admin lain, sistem berhasil menghapus akun', function () {
    // Arrange
    $otherAdmin = User::factory()->create(['role' => 'admin']);

    // Act
    $response = $this->actingAs($this->admin)->delete(route('admin.user.destroy', $otherAdmin));

    // Assert
    $response->assertRedirect();
    $response->assertSessionHas('success');
    
    $this->assertDatabaseMissing('users', ['id' => $otherAdmin->id]);
});

// ---------------------------------------------------------------
// TC-06 | Happy Path
// B2=F, B4=T, B5=T : Admin menghapus pelamar → delete pelamar + user
// ---------------------------------------------------------------
test('TC-06: Admin menghapus akun pelamar, sistem berhasil menghapus data pelamar dan akun', function () {
    // Arrange
    $pelamarUser = User::factory()->create(['role' => 'pelamar']);
    $pelamar = Pelamar::factory()->create(['user_id' => $pelamarUser->id]);

    // Act
    $response = $this->actingAs($this->admin)->delete(route('admin.user.destroy', $pelamarUser));

    // Assert
    $response->assertRedirect();
    $response->assertSessionHas('success');
    
    $this->assertDatabaseMissing('users', ['id' => $pelamarUser->id]);
    $this->assertDatabaseMissing('pelamars', ['id' => $pelamar->id]);
});

// ---------------------------------------------------------------
// TC-07 | Happy Path
// B2=F, B6=T, B7=T : Admin menghapus dosen → reset role + delete user
// ---------------------------------------------------------------
test('TC-07: Admin menghapus akun dosen, sistem berhasil mereset role dosen dan menghapus akun', function () {
    // Arrange
    $prodi = Prodi::factory()->create();
    $dosen = Dosen::factory()->create([
        'prodi_id'   => $prodi->id,
        'is_penguji' => true,
        'is_kaprodi' => false,
    ]);
    $dosenUser = User::factory()->create([
        'role'     => 'penguji',
        'dosen_id' => $dosen->id,
    ]);

    // Act
    $response = $this->actingAs($this->admin)->delete(route('admin.user.destroy', $dosenUser));

    // Assert
    $response->assertRedirect();
    $response->assertSessionHas('success');
    
    $this->assertDatabaseMissing('users', ['id' => $dosenUser->id]);
    
    $dosen->refresh();
    expect($dosen->is_penguji)->toBeFalse();
    expect($dosen->is_kaprodi)->toBeFalse();
});

// ================================================================
// UPDATE KREDENSIAL
// ================================================================

// ---------------------------------------------------------------
// TC-08 | Happy Path
// B8=T, B9=T, B10=F : Update email pelamar saja
// ---------------------------------------------------------------
test('TC-08: Admin mengubah email pelamar, sistem berhasil memperbarui email', function () {
    // Arrange
    $pelamarUser = User::factory()->create([
        'role'  => 'pelamar',
        'email' => 'old@example.com',
    ]);

    // Act
    $response = $this->actingAs($this->admin)->put(route('admin.user.update', $pelamarUser), [
        'email' => 'new@example.com',
    ]);

    // Assert
    $response->assertRedirect();
    $response->assertSessionHas('success');
    
    $pelamarUser->refresh();
    expect($pelamarUser->email)->toBe('new@example.com');
});

// ---------------------------------------------------------------
// TC-09 | Happy Path
// B8=T, B10=T : Update password pelamar saja
// ---------------------------------------------------------------
test('TC-09: Admin mengubah password pelamar, sistem berhasil memperbarui password', function () {
    // Arrange
    $pelamarUser = User::factory()->create([
        'role'     => 'pelamar',
        'email'    => 'pelamar@example.com',
        'password' => Hash::make('OldPassword123'),
    ]);

    // Act
    $response = $this->actingAs($this->admin)->put(route('admin.user.update', $pelamarUser), [
        'email'    => 'pelamar@example.com',
        'password' => 'NewPassword456',
    ]);

    // Assert
    $response->assertRedirect();
    $response->assertSessionHas('success');
    
    $pelamarUser->refresh();
    expect(Hash::check('NewPassword456', $pelamarUser->password))->toBeTrue();
});

// ---------------------------------------------------------------
// TC-10 | Happy Path
// B8=F, B10=T : Update password admin/dosen
// ---------------------------------------------------------------
test('TC-10: Admin mengubah password admin lain, sistem berhasil memperbarui password', function () {
    // Arrange
    $otherAdmin = User::factory()->create([
        'role'     => 'admin',
        'password' => Hash::make('OldPassword123'),
    ]);

    // Act
    $response = $this->actingAs($this->admin)->put(route('admin.user.update', $otherAdmin), [
        'password' => 'NewPassword456',
    ]);

    // Assert
    $response->assertRedirect();
    $response->assertSessionHas('success');
    
    $otherAdmin->refresh();
    expect(Hash::check('NewPassword456', $otherAdmin->password))->toBeTrue();
});

// ---------------------------------------------------------------
// TC-11 | Validasi
// Pelamar update email dengan domain internal
// ---------------------------------------------------------------
test('TC-11: Admin mengubah email pelamar dengan domain internal, sistem menampilkan pesan error', function () {
    // Arrange
    $pelamarUser = User::factory()->create([
        'role'  => 'pelamar',
        'email' => 'pelamar@example.com',
    ]);

    // Act
    $response = $this->actingAs($this->admin)->put(route('admin.user.update', $pelamarUser), [
        'email' => 'test@pengajar.telkomuniversity.ac.id',
    ]);

    // Assert
    $response->assertSessionHasErrors(['email']);
});
