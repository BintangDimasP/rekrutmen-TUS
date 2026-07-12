<?php

/**
 * WHITE BOX TESTING - MANAJEMEN DOSEN
 * Teknik     : Branch Coverage
 * Controller : Admin\DosenController
 *
 * ============================================================
 * ANALISIS BRANCH:
 * ============================================================
 *
 * [store() — Tambah Dosen Baru]
 *
 *   $isKaprodi = $request->boolean('is_kaprodi')                    // Branch 1
 *   if ($isKaprodi) → demote kaprodi lama + buat akun               // Branch 1
 *   else            → dosen biasa, tidak ada akun                   // Branch 1
 *
 * Branch 1 — Dosen ditunjuk sebagai kaprodi saat dibuat
 *   TRUE  : is_kaprodi=true → demote kaprodi lama, buat akun user
 *   FALSE : is_kaprodi=false → simpan sebagai dosen biasa
 *
 * ------------------------------------------------------------
 *
 * [update() — Update Dosen]
 *
 *   if ($isKaprodi && !$wasKaprodi)  → baru jadi kaprodi            // Branch 2
 *   elseif (!$isKaprodi && $wasKaprodi) → kaprodi dicabut           // Branch 3
 *   elseif ($isKaprodi && $wasKaprodi)  → tetap kaprodi             // Branch 4
 *   else                                → dosen biasa               // Branch 5
 *
 *   // dalam Branch 3 (cabut kaprodi):
 *   if ($user->is_penguji) → role jadi penguji                      // Branch 3a
 *   else                   → hapus akun user                        // Branch 3b
 *
 * Branch 2 — Dosen baru ditunjuk jadi kaprodi
 *   TRUE  : Buat/ambil akun user, aktifkan role kaprodi
 *
 * Branch 3 — Status kaprodi dicabut
 *   3a: Masih penguji → downgrade ke role penguji
 *   3b: Bukan penguji → hapus akun user
 *
 * Branch 4 — Tetap kaprodi
 *   Update nama di akun user
 *
 * Branch 5 — Dosen biasa, tidak ada perubahan user
 *
 * ------------------------------------------------------------
 *
 * [destroy() — Hapus Dosen]
 *
 *   Tidak ada branch logic khusus
 *
 * ============================================================
 * PETA TEST CASE → BRANCH YANG DICAKUP:
 * ============================================================
 *
 * [Tambah Dosen]
 * TC-01 : B1=F → tambah dosen biasa
 * TC-02 : B1=T → tambah dosen langsung sebagai kaprodi
 * TC-03 : validasi → kode dosen duplikat
 *
 * [Update Dosen]
 * TC-04 : B5   → update data dosen biasa
 * TC-05 : B2   → dosen biasa ditunjuk jadi kaprodi
 * TC-06 : B3a  → kaprodi dicabut, dosen masih penguji → role jadi penguji
 * TC-07 : B3b  → kaprodi dicabut, bukan penguji → akun dihapus
 * TC-08 : B4   → dosen tetap kaprodi, nama diperbarui
 *
 * [Hapus Dosen]
 * TC-09 : Happy path → hapus dosen biasa
 * TC-10 : Happy path → hapus dosen yang memiliki akun user
 */

use App\Models\Dosen;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->prodi = Prodi::factory()->create();
});

// ================================================================
// TAMBAH DOSEN BARU
// ================================================================

// ---------------------------------------------------------------
// TC-01 | Happy Path
// B1=F : Tambah dosen biasa tanpa kaprodi
// ---------------------------------------------------------------
test('TC-01: Admin menambahkan dosen biasa, sistem berhasil menyimpan data dosen tanpa akun', function () {
    // Act
    $response = $this->actingAs($this->admin)->post(route('admin.dosen.store', $this->prodi), [
        'nama' => 'Dr. Budi Santoso',
        'kode' => 'BDS',
        'nip'  => '198501012010011001',
    ]);

    // Assert
    $response->assertRedirect();
    $response->assertSessionHas('success');

    $dosen = Dosen::where('kode', 'BDS')->first();
    expect($dosen)->not->toBeNull();
    expect($dosen->is_kaprodi)->toBeFalse();

    // Dosen biasa tidak punya akun user
    $this->assertDatabaseMissing('users', ['dosen_id' => $dosen->id]);
});

// ---------------------------------------------------------------
// TC-02 | Happy Path
// B1=T : Tambah dosen langsung sebagai kaprodi → buat akun
// ---------------------------------------------------------------
test('TC-02: Admin menambahkan dosen langsung sebagai kaprodi, sistem membuat dosen dan akun kaprodi', function () {
    // Act
    $response = $this->actingAs($this->admin)->post(route('admin.dosen.store', $this->prodi), [
        'nama'       => 'Prof. Ahmad Kaprodi',
        'kode'       => 'AKP',
        'is_kaprodi' => true,
    ]);

    // Assert
    $response->assertRedirect();
    $response->assertSessionHas('success');

    $dosen = Dosen::where('kode', 'AKP')->first();
    expect($dosen)->not->toBeNull();
    expect($dosen->is_kaprodi)->toBeTrue();

    // Akun user dibuat otomatis
    $user = User::where('dosen_id', $dosen->id)->first();
    expect($user)->not->toBeNull();
    expect($user->role)->toBe('kaprodi');
    expect($user->prodi_id)->toBe($this->prodi->id);
});

// ---------------------------------------------------------------
// TC-03 | Validasi
// Kode dosen sudah ada (duplikat)
// ---------------------------------------------------------------
test('TC-03: Admin menambahkan dosen dengan kode yang sudah ada, sistem menampilkan pesan error', function () {
    // Arrange
    Dosen::factory()->create(['prodi_id' => $this->prodi->id, 'kode' => 'BDS']);

    // Act
    $response = $this->actingAs($this->admin)->post(route('admin.dosen.store', $this->prodi), [
        'nama' => 'Dosen Duplikat',
        'kode' => 'BDS',
    ]);

    // Assert
    $response->assertSessionHasErrors(['kode']);
});

// ================================================================
// UPDATE DOSEN
// ================================================================

// ---------------------------------------------------------------
// TC-04 | Happy Path
// B5 : Update data dosen biasa
// ---------------------------------------------------------------
test('TC-04: Admin mengubah data dosen biasa, sistem berhasil memperbarui data', function () {
    // Arrange
    $dosen = Dosen::factory()->create([
        'prodi_id'   => $this->prodi->id,
        'nama'       => 'Dosen Lama',
        'kode'       => 'DL',
        'is_kaprodi' => false,
        'is_penguji' => false,
    ]);

    // Act
    $response = $this->actingAs($this->admin)->put(route('admin.dosen.update', $dosen), [
        'nama'       => 'Dosen Baru',
        'kode'       => 'DB',
        'is_kaprodi' => false,
    ]);

    // Assert
    $response->assertRedirect();
    $response->assertSessionHas('success');

    $dosen->refresh();
    expect($dosen->nama)->toBe('Dosen Baru');
    expect($dosen->kode)->toBe('DB');
});

// ---------------------------------------------------------------
// TC-05 | Happy Path
// B2 : Dosen biasa baru ditunjuk jadi kaprodi
// ---------------------------------------------------------------
test('TC-05: Admin menunjuk dosen biasa sebagai kaprodi, sistem membuat akun kaprodi', function () {
    // Arrange
    $dosen = Dosen::factory()->create([
        'prodi_id'   => $this->prodi->id,
        'nama'       => 'Dr. Calon Kaprodi',
        'kode'       => 'CK',
        'is_kaprodi' => false,
        'is_penguji' => false,
    ]);

    // Act
    $response = $this->actingAs($this->admin)->put(route('admin.dosen.update', $dosen), [
        'nama'       => 'Dr. Calon Kaprodi',
        'kode'       => 'CK',
        'is_kaprodi' => true,
    ]);

    // Assert
    $response->assertRedirect();
    $response->assertSessionHas('success');

    $dosen->refresh();
    expect($dosen->is_kaprodi)->toBeTrue();

    $user = User::where('dosen_id', $dosen->id)->first();
    expect($user)->not->toBeNull();
    expect($user->role)->toBe('kaprodi');
});

// ---------------------------------------------------------------
// TC-06 | Happy Path
// B3a : Kaprodi dicabut, dosen masih penguji → role jadi penguji
// ---------------------------------------------------------------
test('TC-06: Admin mencabut status kaprodi dari dosen yang juga penguji, sistem mengubah role menjadi penguji', function () {
    // Arrange
    $dosen = Dosen::factory()->create([
        'prodi_id'   => $this->prodi->id,
        'nama'       => 'Dr. Kaprodi Penguji',
        'kode'       => 'KPG',
        'is_kaprodi' => true,
        'is_penguji' => true,
    ]);
    $user = User::factory()->create([
        'role'       => 'kaprodi',
        'dosen_id'   => $dosen->id,
        'prodi_id'   => $this->prodi->id,
        'is_kaprodi' => true,
        'is_penguji' => true,
    ]);

    // Act
    $response = $this->actingAs($this->admin)->put(route('admin.dosen.update', $dosen), [
        'nama'       => 'Dr. Kaprodi Penguji',
        'kode'       => 'KPG',
        'is_kaprodi' => false,
    ]);

    // Assert
    $response->assertRedirect();
    $response->assertSessionHas('success');

    $user->refresh();
    expect($user->role)->toBe('penguji');
    expect($user->is_kaprodi)->toBeFalse();
});

// ---------------------------------------------------------------
// TC-07 | Happy Path
// B3b : Kaprodi dicabut, bukan penguji → akun user dihapus
// ---------------------------------------------------------------
test('TC-07: Admin mencabut status kaprodi dari dosen yang bukan penguji, sistem menghapus akun user', function () {
    // Arrange
    $dosen = Dosen::factory()->create([
        'prodi_id'   => $this->prodi->id,
        'nama'       => 'Dr. Kaprodi Saja',
        'kode'       => 'KPS',
        'is_kaprodi' => true,
        'is_penguji' => false,
    ]);
    $user = User::factory()->create([
        'role'       => 'kaprodi',
        'dosen_id'   => $dosen->id,
        'prodi_id'   => $this->prodi->id,
        'is_kaprodi' => true,
        'is_penguji' => false,
    ]);

    // Act
    $response = $this->actingAs($this->admin)->put(route('admin.dosen.update', $dosen), [
        'nama'       => 'Dr. Kaprodi Saja',
        'kode'       => 'KPS',
        'is_kaprodi' => false,
    ]);

    // Assert
    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});

// ---------------------------------------------------------------
// TC-08 | Happy Path
// B4 : Tetap kaprodi, nama diperbarui
// ---------------------------------------------------------------
test('TC-08: Admin memperbarui nama dosen yang masih berstatus kaprodi, sistem memperbarui nama di akun', function () {
    // Arrange
    $dosen = Dosen::factory()->create([
        'prodi_id'   => $this->prodi->id,
        'nama'       => 'Dr. Kaprodi Lama',
        'kode'       => 'KL',
        'is_kaprodi' => true,
        'is_penguji' => false,
    ]);
    $user = User::factory()->create([
        'name'       => 'Dr. Kaprodi Lama',
        'role'       => 'kaprodi',
        'dosen_id'   => $dosen->id,
        'prodi_id'   => $this->prodi->id,
        'is_kaprodi' => true,
        'is_penguji' => false,
    ]);

    // Act
    $response = $this->actingAs($this->admin)->put(route('admin.dosen.update', $dosen), [
        'nama'       => 'Dr. Kaprodi Baru',
        'kode'       => 'KL',
        'is_kaprodi' => true,
    ]);

    // Assert
    $response->assertRedirect();
    $response->assertSessionHas('success');

    $dosen->refresh();
    expect($dosen->nama)->toBe('Dr. Kaprodi Baru');

    $user->refresh();
    expect($user->name)->toBe('Dr. Kaprodi Baru');
});

// ================================================================
// HAPUS DOSEN
// ================================================================

// ---------------------------------------------------------------
// TC-09 | Happy Path
// Hapus dosen biasa (tanpa akun user)
// ---------------------------------------------------------------
test('TC-09: Admin menghapus dosen biasa, sistem berhasil menghapus data dosen', function () {
    // Arrange
    $dosen = Dosen::factory()->create([
        'prodi_id'   => $this->prodi->id,
        'is_kaprodi' => false,
        'is_penguji' => false,
    ]);

    // Act
    $response = $this->actingAs($this->admin)->delete(route('admin.dosen.destroy', $dosen));

    // Assert
    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('dosens', ['id' => $dosen->id]);
});

// ---------------------------------------------------------------
// TC-10 | Happy Path
// Hapus dosen yang memiliki akun user (kaprodi/penguji)
// ---------------------------------------------------------------
test('TC-10: Admin menghapus dosen yang memiliki akun, sistem menghapus dosen dan akun user', function () {
    // Arrange
    $dosen = Dosen::factory()->create([
        'prodi_id'   => $this->prodi->id,
        'is_kaprodi' => true,
        'is_penguji' => false,
    ]);
    $user = User::factory()->create([
        'role'     => 'kaprodi',
        'dosen_id' => $dosen->id,
        'prodi_id' => $this->prodi->id,
    ]);

    // Act
    $response = $this->actingAs($this->admin)->delete(route('admin.dosen.destroy', $dosen));

    // Assert
    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('dosens', ['id' => $dosen->id]);
    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});

// ================================================================
// IMPORT DOSEN
// ================================================================

// ---------------------------------------------------------------
// TC-11 | Happy Path
// Import file Excel valid
// ---------------------------------------------------------------
test('TC-11: Admin mengimpor data dosen dengan file Excel valid, sistem berhasil mengimpor data', function () {
    // Arrange
    $header = ['nama', 'kode', 'nip', 'nidn', 'no_telepon'];
    $rows = [
        ['Dr. Import Satu', 'IMP1', '198501012010011001', '0012345678', '081234567890'],
        ['Dr. Import Dua',  'IMP2', '198601022011012002', '0087654321', '081298765432'],
    ];

    // Buat file Excel sementara
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->fromArray([$header, ...$rows], null, 'A1');

    // Paksa kolom NIP, NIDN, no_telepon jadi string agar tidak truncated
    foreach ([3, 4, 5] as $col) {
        foreach ([2, 3] as $row) {
            $sheet->getCellByColumnAndRow($col, $row)
                ->getStyle()
                ->getNumberFormat()
                ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
            $sheet->getCellByColumnAndRow($col, $row)->setValueExplicit(
                $sheet->getCellByColumnAndRow($col, $row)->getValue(),
                \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
            );
        }
    }

    $tmpPath = tempnam(sys_get_temp_dir(), 'dosen_import') . '.xlsx';
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save($tmpPath);

    $file = new \Illuminate\Http\UploadedFile($tmpPath, 'dosen_import.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

    // Act
    $response = $this->actingAs($this->admin)->post(route('admin.dosen.import', $this->prodi), [
        'file' => $file,
    ]);

    // Assert
    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('dosens', ['kode' => 'IMP1', 'prodi_id' => $this->prodi->id]);
    $this->assertDatabaseHas('dosens', ['kode' => 'IMP2', 'prodi_id' => $this->prodi->id]);
});

// ---------------------------------------------------------------
// TC-12 | Validasi
// Import file bukan Excel
// ---------------------------------------------------------------
test('TC-12: Admin mengimpor data dosen dengan file bukan Excel, sistem menampilkan pesan error', function () {
    // Arrange
    \Illuminate\Support\Facades\Storage::fake('local');
    $file = \Illuminate\Http\UploadedFile::fake()->create('dosen.pdf', 100, 'application/pdf');

    // Act
    $response = $this->actingAs($this->admin)->post(route('admin.dosen.import', $this->prodi), [
        'file' => $file,
    ]);

    // Assert
    $response->assertSessionHasErrors(['file']);
});
