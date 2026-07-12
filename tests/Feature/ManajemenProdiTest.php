<?php


use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
});

// ================================================================
// TAMBAH PRODI BARU
// ================================================================

// ---------------------------------------------------------------
// TC-01 | Happy Path
// Tambah prodi dengan data lengkap
// ---------------------------------------------------------------
test('TC-01: Admin menambahkan prodi dengan data lengkap, sistem berhasil menyimpan prodi', function () {
    // Act
    $response = $this->actingAs($this->admin)->post(route('admin.prodi.store'), [
        'nama' => 'Teknik Informatika',
        'kode' => 'TIF',
    ]);

    // Assert
    $response->assertRedirect(route('admin.prodi.index'));
    $response->assertSessionHas('success');
    
    $prodi = Prodi::where('kode', 'TIF')->first();
    expect($prodi)->not->toBeNull();
    expect($prodi->nama)->toBe('Teknik Informatika');
    expect($prodi->kode)->toBe('TIF');
});

// ---------------------------------------------------------------
// TC-02 | Validasi
// Kode prodi sudah ada (duplikat)
// ---------------------------------------------------------------
test('TC-02: Admin menambahkan prodi dengan kode yang sudah ada, sistem menampilkan pesan error', function () {
    // Arrange
    Prodi::factory()->create(['kode' => 'TIF']);

    // Act
    $response = $this->actingAs($this->admin)->post(route('admin.prodi.store'), [
        'nama' => 'Teknik Informatika Baru',
        'kode' => 'TIF',
    ]);

    // Assert
    $response->assertSessionHasErrors(['kode']);
});

// ---------------------------------------------------------------
// TC-03 | Happy Path
// Tambah prodi dengan logo
// ---------------------------------------------------------------
test('TC-03: Admin menambahkan prodi dengan logo, sistem berhasil menyimpan prodi dan logo', function () {
    // Arrange
    Storage::fake('public');

    // Act
    $response = $this->actingAs($this->admin)->post(route('admin.prodi.store'), [
        'nama' => 'Teknik Elektro',
        'kode' => 'TE',
        'logo' => UploadedFile::fake()->image('logo-te.png'),
    ]);

    // Assert
    $response->assertRedirect(route('admin.prodi.index'));
    $response->assertSessionHas('success');
    
    $prodi = Prodi::where('kode', 'TE')->first();
    expect($prodi)->not->toBeNull();
    expect($prodi->logo)->not->toBeNull();
    Storage::disk('public')->assertExists($prodi->logo);
});

// ================================================================
// UPDATE PRODI
// ================================================================

// ---------------------------------------------------------------
// TC-04 | Happy Path
// Update nama prodi
// ---------------------------------------------------------------
test('TC-04: Admin mengubah nama prodi, sistem berhasil memperbarui data', function () {
    // Arrange
    $prodi = Prodi::factory()->create([
        'nama' => 'Teknik Informatika',
        'kode' => 'TIF',
    ]);

    // Act
    $response = $this->actingAs($this->admin)->put(route('admin.prodi.update', $prodi), [
        'nama' => 'Teknik Informatika dan Komputer',
        'kode' => 'TIF',
    ]);

    // Assert
    $response->assertRedirect(route('admin.prodi.index'));
    $response->assertSessionHas('success');
    
    $prodi->refresh();
    expect($prodi->nama)->toBe('Teknik Informatika dan Komputer');
    expect($prodi->kode)->toBe('TIF');
});

// ---------------------------------------------------------------
// TC-05 | Validasi
// Update kode prodi menjadi kode yang sudah ada
// ---------------------------------------------------------------
test('TC-05: Admin mengubah kode prodi menjadi kode yang sudah ada, sistem menampilkan pesan error', function () {
    // Arrange
    Prodi::factory()->create(['kode' => 'SIF']);
    $prodi = Prodi::factory()->create(['kode' => 'TIF']);

    // Act
    $response = $this->actingAs($this->admin)->put(route('admin.prodi.update', $prodi), [
        'nama' => 'Teknik Informatika',
        'kode' => 'SIF', // Kode sudah dipakai prodi lain
    ]);

    // Assert
    $response->assertSessionHasErrors(['kode']);
});

// ---------------------------------------------------------------
// TC-06 | Happy Path
// Update logo prodi
// ---------------------------------------------------------------
test('TC-06: Admin mengubah logo prodi, sistem berhasil memperbarui logo', function () {
    // Arrange
    Storage::fake('public');
    $prodi = Prodi::factory()->create([
        'nama' => 'Teknik Informatika',
        'kode' => 'TIF',
        'logo' => 'prodi_logos/old-logo.png',
    ]);
    Storage::disk('public')->put('prodi_logos/old-logo.png', 'fake-content');

    // Act
    $response = $this->actingAs($this->admin)->put(route('admin.prodi.update', $prodi), [
        'nama' => 'Teknik Informatika',
        'kode' => 'TIF',
        'logo' => UploadedFile::fake()->image('new-logo.png'),
    ]);

    // Assert
    $response->assertRedirect(route('admin.prodi.index'));
    $response->assertSessionHas('success');
    
    $prodi->refresh();
    expect($prodi->logo)->not->toBe('prodi_logos/old-logo.png');
    Storage::disk('public')->assertMissing('prodi_logos/old-logo.png');
    Storage::disk('public')->assertExists($prodi->logo);
});

// ================================================================
// HAPUS PRODI
// ================================================================

// ---------------------------------------------------------------
// TC-07 | Happy Path
// Hapus prodi tanpa logo
// ---------------------------------------------------------------
test('TC-07: Admin menghapus prodi, sistem berhasil menghapus prodi', function () {
    // Arrange
    $prodi = Prodi::factory()->create([
        'nama' => 'Sistem Informasi',
        'kode' => 'SIF',
        'logo' => null,
    ]);

    // Act
    $response = $this->actingAs($this->admin)->delete(route('admin.prodi.destroy', $prodi));

    // Assert
    $response->assertRedirect(route('admin.prodi.index'));
    $response->assertSessionHas('success');
    
    $this->assertDatabaseMissing('prodis', ['id' => $prodi->id]);
});


