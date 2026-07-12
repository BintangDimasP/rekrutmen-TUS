<?php

/**
 * WHITE BOX TESTING - REGISTRASI
 * Teknik     : Branch Coverage
 * Controller : RegisteredUserController@store, @checkEmail, @checkIdentity
 * Route      : POST /register, POST /check-email, POST /check-identity
 *
 * ============================================================
 * ANALISIS BRANCH PADA KODE SUMBER:
 * ============================================================
 *
 * [store() — Validasi field wajib]
 *
 *   'email' => unique + NotDosenInternalDomain()             // Branch 1
 *   'nik'   => unique via PHP closure                        // Branch 2
 *   'no_telepon' => unique via PHP closure                   // Branch 3
 *
 * [store() — Upload file (foreach $fileFields)]
 *
 *   if ($request->hasFile($requestKey)) {                    // Branch 4
 *       $pelamarData[$dbKey] = $request->file(...)->store(...)
 *   }
 *   (diulang untuk 8 field: ijazah, transkrip, cv,
 *    pas_foto, ktp, sertifikat_kompetensi,
 *    sertifikat_bahasa, kartu_dosen)
 *
 * [checkEmail()]
 *
 *   try { $request->validate([...]) }                        // Branch 5
 *   catch (ValidationException $e) { ... }
 *
 * [checkIdentity() — Pengecekan NIK]
 *
 *   if ($nik) { ... }                                        // Branch 6
 *   if (!preg_match('/^\d{16}$/', $nik)) { ... }            // Branch 7
 *   if ($exists) { ... }                                     // Branch 8
 *
 * [checkIdentity() — Pengecekan No. Telepon]
 *
 *   if ($noTelepon) { ... }                                  // Branch 9
 *   if (!preg_match('/^08[0-9]{6,13}$/', $noTelepon)) {...} // Branch 10
 *   if ($exists) { ... }                                     // Branch 11
 *
 * ------------------------------------------------------------
 * Branch 1 — Validasi email (unique + bukan domain dosen internal)
 *   TRUE  : Email valid dan belum terdaftar → lanjut
 *   FALSE : Email sudah terdaftar atau domain internal → error
 *
 * Branch 2 — Validasi NIK unik via PHP
 *   TRUE  : NIK belum terdaftar → lanjut
 *   FALSE : NIK sudah terdaftar → error
 *
 * Branch 3 — Validasi no_telepon unik via PHP
 *   TRUE  : No. telepon belum terdaftar → lanjut
 *   FALSE : No. telepon sudah terdaftar → error
 *
 * Branch 4 — Upload file (hasFile)
 *   TRUE  : File dikirim → simpan ke storage
 *   FALSE : File tidak dikirim → field null
 *
 * Branch 5 — checkEmail: ValidationException
 *   TRUE  : Email valid → return valid:true
 *   FALSE : Email invalid → return valid:false + message
 *
 * Branch 6 — checkIdentity: NIK dikirim atau tidak
 *   TRUE  : NIK ada → validasi format dan keunikan
 *   FALSE : NIK tidak ada → lewati
 *
 * Branch 7 — checkIdentity: format NIK valid
 *   TRUE  : Format 16 digit → lanjut cek keunikan
 *   FALSE : Format salah → return error format
 *
 * Branch 8 — checkIdentity: NIK sudah terdaftar
 *   TRUE  : NIK ditemukan → return error duplikat
 *   FALSE : NIK belum ada → lanjut
 *
 * Branch 9 — checkIdentity: no_telepon dikirim atau tidak
 *   TRUE  : No. telepon ada → validasi format dan keunikan
 *   FALSE : No. telepon tidak ada → lewati
 *
 * Branch 10 — checkIdentity: format no_telepon valid
 *   TRUE  : Format 08xxx → lanjut cek keunikan
 *   FALSE : Format salah → return error format
 *
 * Branch 11 — checkIdentity: no_telepon sudah terdaftar
 *   TRUE  : No. telepon ditemukan → return error duplikat
 *   FALSE : No. telepon belum ada → lanjut
 *
 * ============================================================
 * PETA TEST CASE → BRANCH YANG DICAKUP:
 * ============================================================
 *
 * TC-01 : B1=T, B2=T, B3=T, B4=T (semua file)  → happy path lengkap
 * TC-02 : B1=T, B2=T, B3=T, B4=F (tanpa file)  → happy path minimal
 * TC-03 : B1=F (email duplikat)                 → unhappy, email sudah terdaftar
 * TC-04 : B1=F (domain dosen)                   → unhappy, email domain internal
 * TC-05 : B2=F                                  → unhappy, NIK sudah terdaftar
 * TC-06 : B3=F                                  → unhappy, no. telepon sudah terdaftar
 * TC-07 : B5=T                                  → happy, checkEmail valid
 * TC-08 : B5=F (email duplikat)                 → unhappy, checkEmail email sudah terdaftar
 * TC-09 : B5=F (domain dosen)                   → unhappy, checkEmail domain internal
 * TC-10 : B6=T, B7=T, B8=F                      → happy, checkIdentity NIK valid & tersedia
 * TC-11 : B6=T, B7=F                            → unhappy, checkIdentity NIK format salah
 * TC-12 : B6=T, B7=T, B8=T                      → unhappy, checkIdentity NIK sudah terdaftar
 * TC-13 : B9=T, B10=T, B11=F                    → happy, checkIdentity no. telepon valid & tersedia
 * TC-14 : B9=T, B10=F                           → unhappy, checkIdentity no. telepon format salah
 * TC-15 : B9=T, B10=T, B11=T                    → unhappy, checkIdentity no. telepon sudah terdaftar
 * TC-16 : B6=F, B9=F                            → happy, checkIdentity tanpa parameter
 *
 * ============================================================
 * TABEL HASIL PENGUJIAN:
 * ============================================================
 *
 * | Test Case | Skenario                                                       | Hasil yang Diharapkan                                                                 |
 * |-----------|----------------------------------------------------------------|---------------------------------------------------------------------------------------|
 * | TC-01     | Registrasi dengan data lengkap dan semua file diunggah         | Pelamar berhasil registrasi, sistem mengarahkan ke dashboard pelamar                  |
 * | TC-02     | Registrasi dengan data wajib saja tanpa mengunggah file        | Pelamar berhasil registrasi, sistem mengarahkan ke dashboard pelamar                  |
 * | TC-03     | Registrasi dengan email yang sudah terdaftar                   | Pelamar gagal registrasi, sistem menampilkan pesan error email sudah terdaftar        |
 * | TC-04     | Registrasi dengan email domain dosen internal                  | Pelamar gagal registrasi, sistem menampilkan pesan error email tidak diizinkan        |
 * | TC-05     | Registrasi dengan NIK yang sudah terdaftar                     | Pelamar gagal registrasi, sistem menampilkan pesan error NIK sudah terdaftar         |
 * | TC-06     | Registrasi dengan no. telepon yang sudah terdaftar             | Pelamar gagal registrasi, sistem menampilkan pesan error no. telepon sudah terdaftar |
 * | TC-07     | Pengecekan email valid dan belum terdaftar                     | Sistem mengembalikan respons valid                                                    |
 * | TC-08     | Pengecekan email yang sudah terdaftar                          | Sistem mengembalikan respons tidak valid dan pesan error email sudah terdaftar        |
 * | TC-09     | Pengecekan email dengan domain dosen internal                  | Sistem mengembalikan respons tidak valid dan pesan error email tidak diizinkan        |
 * | TC-10     | Pengecekan NIK dengan format valid dan belum terdaftar         | Sistem mengembalikan respons valid                                                    |
 * | TC-11     | Pengecekan NIK dengan format tidak valid                       | Sistem mengembalikan respons tidak valid dan pesan error format NIK                   |
 * | TC-12     | Pengecekan NIK yang sudah terdaftar                            | Sistem mengembalikan respons tidak valid dan pesan error NIK sudah terdaftar         |
 * | TC-13     | Pengecekan no. telepon dengan format valid dan belum terdaftar | Sistem mengembalikan respons valid                                                    |
 * | TC-14     | Pengecekan no. telepon dengan format tidak valid               | Sistem mengembalikan respons tidak valid dan pesan error format no. telepon           |
 * | TC-15     | Pengecekan no. telepon yang sudah terdaftar                    | Sistem mengembalikan respons tidak valid dan pesan error no. telepon sudah terdaftar |
 * | TC-16     | Pengecekan identitas tanpa mengirim parameter apapun           | Sistem mengembalikan respons valid                                                    |
 */

use App\Models\Pelamar;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

// Data registrasi minimal yang valid (tanpa file)
function validRegistrasiData(array $override = []): array
{
    return array_merge([
        'email'             => 'pelamar@gmail.com',
        'password'          => 'Password123!',
        'password_confirmation' => 'Password123!',
        'nik'               => '3201234567890001',
        'nama'              => 'Budi Santoso',
        'tempat_lahir'      => 'Bandung',
        'tanggal_lahir'     => '1995-06-15',
        'no_telepon'        => '081234567890',
        'jenis_kelamin'     => 'L',
        'kewarganegaraan'   => 'WNI',
        'status_pernikahan' => 'Belum Menikah',
        'alamat_domisili'   => 'Jl. Sudirman No. 1',
        'alamat_ktp'        => 'Jl. Sudirman No. 1',
    ], $override);
}

// ---------------------------------------------------------------
// TC-01 | Happy Path
// B1=T, B2=T, B3=T : Semua validasi lolos
// B4=T             : Semua file diunggah
// ---------------------------------------------------------------
test('TC-01: Pelamar berhasil registrasi, sistem mengarahkan ke dashboard pelamar', function () {
    // Arrange
    Storage::fake('public');

    $data = validRegistrasiData([
        'ijazah'               => UploadedFile::fake()->create('ijazah.pdf', 1024, 'application/pdf'),
        'transkrip'            => UploadedFile::fake()->create('transkrip.pdf', 1024, 'application/pdf'),
        'cv'                   => UploadedFile::fake()->create('cv.pdf', 1024, 'application/pdf'),
        'pas_foto'             => UploadedFile::fake()->image('foto.jpg'),
        'ktp'                  => UploadedFile::fake()->image('ktp.jpg'),
        'sertifikat_kompetensi' => UploadedFile::fake()->create('sertif.pdf', 1024, 'application/pdf'),
        'sertifikat_bahasa'    => UploadedFile::fake()->create('toefl.pdf', 1024, 'application/pdf'),
        'kartu_dosen'          => UploadedFile::fake()->image('kartu.jpg'),
    ]);

    // Act
    $response = $this->post('/register', $data);

    // Assert
    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard'));
    $this->assertDatabaseHas('users', ['email' => 'pelamar@gmail.com', 'role' => 'pelamar']);
    $this->assertDatabaseHas('pelamars', ['nama' => 'Budi Santoso']);
});

// ---------------------------------------------------------------
// TC-02 | Happy Path
// B1=T, B2=T, B3=T : Semua validasi lolos
// B4=F             : Tidak ada file yang diunggah
// ---------------------------------------------------------------
test('TC-02: Pelamar berhasil registrasi tanpa mengunggah file, sistem mengarahkan ke dashboard pelamar', function () {
    // Arrange
    $data = validRegistrasiData();

    // Act
    $response = $this->post('/register', $data);

    // Assert
    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard'));
    $this->assertDatabaseHas('users', ['email' => 'pelamar@gmail.com', 'role' => 'pelamar']);
});

// ---------------------------------------------------------------
// TC-03 | Unhappy Path
// B1=F : Email sudah terdaftar (unique:users gagal)
// ---------------------------------------------------------------
test('TC-03: Pelamar gagal registrasi, sistem menampilkan pesan error email sudah terdaftar', function () {
    // Arrange: buat user dengan email yang sama
    User::factory()->create(['email' => 'pelamar@gmail.com']);

    $data = validRegistrasiData(['email' => 'pelamar@gmail.com']);

    // Act
    $response = $this->post('/register', $data);

    // Assert
    $this->assertGuest();
    $response->assertSessionHasErrors('email');
});

// ---------------------------------------------------------------
// TC-04 | Unhappy Path
// B1=F : Email menggunakan domain dosen internal (NotDosenInternalDomain gagal)
// ---------------------------------------------------------------
test('TC-04: Pelamar gagal registrasi, sistem menampilkan pesan error email tidak diizinkan', function () {
    // Arrange: email dengan domain internal dosen (telkomuniversity.ac.id)
    $data = validRegistrasiData(['email' => 'dosen@pengajar.telkomuniversity.ac.id']);

    // Act
    $response = $this->post('/register', $data);

    // Assert
    $this->assertGuest();
    $response->assertSessionHasErrors('email');
});

// ---------------------------------------------------------------
// TC-05 | Unhappy Path
// B2=F : NIK sudah terdaftar (unique via PHP closure gagal)
// ---------------------------------------------------------------
test('TC-05: Pelamar gagal registrasi, sistem menampilkan pesan error NIK sudah terdaftar', function () {
    // Arrange: buat pelamar dengan NIK yang sama
    $existingUser = User::factory()->create();
    Pelamar::factory()->create([
        'user_id'    => $existingUser->id,
        'nik'        => '3201234567890001',
        'no_telepon' => '089999999999',
    ]);

    $data = validRegistrasiData([
        'email'      => 'baru@gmail.com',
        'nik'        => '3201234567890001', // NIK duplikat
        'no_telepon' => '081111111111',
    ]);

    // Act
    $response = $this->post('/register', $data);

    // Assert
    $this->assertGuest();
    $response->assertSessionHasErrors('nik');
});

// ---------------------------------------------------------------
// TC-06 | Unhappy Path
// B3=F : No. telepon sudah terdaftar (unique via PHP closure gagal)
// ---------------------------------------------------------------
test('TC-06: Pelamar gagal registrasi, sistem menampilkan pesan error no. telepon sudah terdaftar', function () {
    // Arrange: buat pelamar dengan no. telepon yang sama
    $existingUser = User::factory()->create();
    Pelamar::factory()->create([
        'user_id'    => $existingUser->id,
        'nik'        => '3201234567890099',
        'no_telepon' => '081234567890',
    ]);

    $data = validRegistrasiData([
        'email'      => 'baru@gmail.com',
        'nik'        => '3201234567890002', // NIK berbeda
        'no_telepon' => '081234567890',     // No. telepon duplikat
    ]);

    // Act
    $response = $this->post('/register', $data);

    // Assert
    $this->assertGuest();
    $response->assertSessionHasErrors('no_telepon');
});

// ---------------------------------------------------------------
// TC-07 | Happy Path
// B5=T : checkEmail — email valid dan belum terdaftar
// ---------------------------------------------------------------
test('TC-07: Sistem mengembalikan respons valid ketika email belum terdaftar', function () {
    // Act
    $response = $this->postJson('/register/check-email', [
        'email' => 'baru@gmail.com',
    ]);

    // Assert
    $response->assertStatus(200);
    $response->assertJson(['valid' => true]);
});

// ---------------------------------------------------------------
// TC-08 | Unhappy Path
// B5=F : checkEmail — email sudah terdaftar
// ---------------------------------------------------------------
test('TC-08: Sistem mengembalikan respons tidak valid dan pesan error email sudah terdaftar', function () {
    // Arrange
    User::factory()->create(['email' => 'sudahada@gmail.com']);

    // Act
    $response = $this->postJson('/register/check-email', [
        'email' => 'sudahada@gmail.com',
    ]);

    // Assert
    $response->assertStatus(422);
    $response->assertJson(['valid' => false]);
    $response->assertJsonStructure(['valid', 'message']);
});

// ---------------------------------------------------------------
// TC-09 | Unhappy Path
// B5=F : checkEmail — email domain dosen internal
// ---------------------------------------------------------------
test('TC-09: Sistem mengembalikan respons tidak valid dan pesan error email tidak diizinkan', function () {
    // Act
    $response = $this->postJson('/register/check-email', [
        'email' => 'dosen@pengajar.telkomuniversity.ac.id',
    ]);

    // Assert
    $response->assertStatus(422);
    $response->assertJson(['valid' => false]);
});

// ---------------------------------------------------------------
// TC-10 | Happy Path
// B6=T, B7=T, B8=F : checkIdentity — NIK format valid dan belum terdaftar
// ---------------------------------------------------------------
test('TC-10: Sistem mengembalikan respons valid ketika NIK valid dan belum terdaftar', function () {
    // Act
    $response = $this->postJson('/register/check-identity', [
        'nik' => '3201234567890001',
    ]);

    // Assert
    $response->assertStatus(200);
    $response->assertJson(['valid' => true]);
});

// ---------------------------------------------------------------
// TC-11 | Unhappy Path
// B6=T, B7=F : checkIdentity — NIK format tidak valid (bukan 16 digit)
// ---------------------------------------------------------------
test('TC-11: Sistem mengembalikan respons tidak valid dan pesan error format NIK', function () {
    // Act
    $response = $this->postJson('/register/check-identity', [
        'nik' => '12345', // Bukan 16 digit
    ]);

    // Assert
    $response->assertStatus(422);
    $response->assertJson(['valid' => false, 'field' => 'nik']);
});

// ---------------------------------------------------------------
// TC-12 | Unhappy Path
// B6=T, B7=T, B8=T : checkIdentity — NIK sudah terdaftar
// ---------------------------------------------------------------
test('TC-12: Sistem mengembalikan respons tidak valid dan pesan error NIK sudah terdaftar', function () {
    // Arrange
    $user = User::factory()->create();
    Pelamar::factory()->create([
        'user_id' => $user->id,
        'nik'     => '3201234567890001',
    ]);

    // Act
    $response = $this->postJson('/register/check-identity', [
        'nik' => '3201234567890001',
    ]);

    // Assert
    $response->assertStatus(422);
    $response->assertJson(['valid' => false, 'field' => 'nik']);
});

// ---------------------------------------------------------------
// TC-13 | Happy Path
// B9=T, B10=T, B11=F : checkIdentity — no. telepon valid dan belum terdaftar
// ---------------------------------------------------------------
test('TC-13: Sistem mengembalikan respons valid ketika no. telepon valid dan belum terdaftar', function () {
    // Act
    $response = $this->postJson('/register/check-identity', [
        'no_telepon' => '081234567890',
    ]);

    // Assert
    $response->assertStatus(200);
    $response->assertJson(['valid' => true]);
});

// ---------------------------------------------------------------
// TC-14 | Unhappy Path
// B9=T, B10=F : checkIdentity — format no. telepon tidak valid
// ---------------------------------------------------------------
test('TC-14: Sistem mengembalikan respons tidak valid dan pesan error format no. telepon', function () {
    // Act
    $response = $this->postJson('/register/check-identity', [
        'no_telepon' => '021-12345', // Bukan format 08xxx
    ]);

    // Assert
    $response->assertStatus(422);
    $response->assertJson(['valid' => false, 'field' => 'no_telepon']);
});

// ---------------------------------------------------------------
// TC-15 | Unhappy Path
// B9=T, B10=T, B11=T : checkIdentity — no. telepon sudah terdaftar
// ---------------------------------------------------------------
test('TC-15: Sistem mengembalikan respons tidak valid dan pesan error no. telepon sudah terdaftar', function () {
    // Arrange
    $user = User::factory()->create();
    Pelamar::factory()->create([
        'user_id'    => $user->id,
        'no_telepon' => '081234567890',
    ]);

    // Act
    $response = $this->postJson('/register/check-identity', [
        'no_telepon' => '081234567890',
    ]);

    // Assert
    $response->assertStatus(422);
    $response->assertJson(['valid' => false, 'field' => 'no_telepon']);
});

// ---------------------------------------------------------------
// TC-16 | Happy Path
// B6=F, B9=F : checkIdentity — tidak ada parameter yang dikirim
// ---------------------------------------------------------------
test('TC-16: Sistem mengembalikan respons valid ketika tidak ada parameter identitas yang dikirim', function () {
    // Act
    $response = $this->postJson('/register/check-identity', []);

    // Assert
    $response->assertStatus(200);
    $response->assertJson(['valid' => true]);
});
