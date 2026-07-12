<?php

use App\Models\User;
use App\Models\Pelamar;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * ==============================================================================
 * BRANCH COVERAGE UNTUK: Auth\RegisteredUserController
 * ==============================================================================
 * 
 * ANALISIS CABANG (BRANCH):
 * 
 * Method: store()
 * ----------------
 * Branch 1: Validasi email NotDosenInternalDomain
 *   - True: Email bukan domain internal dosen (diterima)
 *   - False: Email domain internal dosen (ditolak)
 * 
 * Branch 2: Validasi NIK unique (closure validation)
 *   - True: NIK belum terdaftar
 *   - False: NIK sudah terdaftar
 * 
 * Branch 3: Validasi no_telepon unique (closure validation)
 *   - True: No telepon belum terdaftar
 *   - False: No telepon sudah terdaftar
 * 
 * Branch 4-11: Upload file optional (hasFile untuk setiap field)
 *   - ijazah, transkrip, cv, pas_foto, ktp, sertifikat_kompetensi,
 *     sertifikat_bahasa, kartu_dosen
 *   - True: File di-upload, simpan
 *   - False: File tidak di-upload, skip
 * 
 * Method: checkEmail()
 * --------------------
 * Branch 12: Validasi email pada AJAX
 *   - True: Email valid dan belum terdaftar
 *   - False: Email invalid atau sudah terdaftar (ValidationException)
 * 
 * Method: checkIdentity()
 * -----------------------
 * Branch 13: Pengecekan NIK ada ($nik)
 *   - True: Validasi NIK format dan ketersediaan
 *   - False: Skip validasi NIK
 * 
 * Branch 14: Validasi format NIK (preg_match 16 digit)
 *   - True: Format valid
 *   - False: Format invalid
 * 
 * Branch 15: NIK sudah terdaftar
 *   - True: Return error
 *   - False: Lanjut
 * 
 * Branch 16: Pengecekan no_telepon ada ($noTelepon)
 *   - True: Validasi format dan ketersediaan
 *   - False: Skip
 * 
 * Branch 17: Validasi format no_telepon (preg_match 08...)
 *   - True: Format valid
 *   - False: Format invalid
 * 
 * Branch 18: No telepon sudah terdaftar
 *   - True: Return error
 *   - False: Valid
 */

beforeEach(function () {
    Storage::fake('public');
});

// ===================== TEST CASES UNTUK store() =====================

// Test Case 1: Registrasi berhasil dengan data lengkap termasuk semua file
test('TC-REG-01: Pelamar berhasil registrasi dengan data lengkap dan semua file dokumen', function () {
    // Arrange: Data lengkap dengan semua file (Branch 4-11 semua TRUE)
    $data = [
        'email' => 'pelamar.baru@gmail.com', // Branch 1 TRUE: bukan domain internal
        'password' => 'Password123!',
        
        // Step 2: Data pribadi
        'nik' => '3201234567890123', // Branch 2 TRUE: NIK baru
        'nama' => 'Ahmad Rizki',
        'tempat_lahir' => 'Bandung',
        'tanggal_lahir' => '1995-05-15',
        'no_telepon' => '081234567890', // Branch 3 TRUE: No telepon baru
        'jenis_kelamin' => 'L',
        'kewarganegaraan' => 'Indonesia',
        'status_pernikahan' => 'Belum Menikah',
        'alamat_domisili' => 'Jl. Telekomunikasi No. 1',
        'alamat_ktp' => 'Jl. Telekomunikasi No. 1',
        
        // Step 3: Pendidikan
        'jenjang' => 'S2',
        'institusi' => 'Institut Teknologi Bandung',
        'prodi_pendidikan' => 'Teknik Informatika',
        'akreditas' => 'A',
        'no_ijazah' => 'ITB-2020-12345',
        'ipk' => 3.75,
        'ijazah' => UploadedFile::fake()->create('ijazah.pdf', 1000),
        'transkrip' => UploadedFile::fake()->create('transkrip.pdf', 1000),
        
        // Step 4: Dokumen tambahan
        'cv' => UploadedFile::fake()->create('cv.pdf', 1000),
        'pas_foto' => UploadedFile::fake()->image('foto.jpg', 300, 400),
        'ktp' => UploadedFile::fake()->create('ktp.pdf', 500),
        'kategori_sertifikat' => 'kompetensi',
        'sertifikat_kompetensi' => UploadedFile::fake()->create('sertifikat.pdf', 1000),
        'jenis_tes_bahasa' => 'TOEFL_ITP',
        'skor_bahasa' => 550,
        'tanggal_tes_bahasa' => '2023-06-01',
        'sertifikat_bahasa' => UploadedFile::fake()->create('toefl.pdf', 1000),
        
        // Step 5: Khusus dosen
        'nidn' => '0123456789',
        'homebase' => 'Fakultas Informatika',
        'jabatan_akademik' => 'lektor',
        'minat_riset' => 'Machine Learning, AI',
        'h_index' => 5,
        'kartu_dosen' => UploadedFile::fake()->create('kartu_dosen.pdf', 500),
    ];

    // Act: Submit registrasi
    $response = $this->post(route('register'), $data);

    // Assert: Verifikasi registrasi berhasil
    $response->assertRedirect(route('dashboard'));
    $response->assertSessionHas('success');
    
    // Verifikasi user dibuat
    $this->assertDatabaseHas('users', [
        'email' => 'pelamar.baru@gmail.com',
        'name' => 'Ahmad Rizki',
        'role' => 'pelamar',
    ]);
    
    // Verifikasi password ter-hash dengan benar
    $user = User::where('email', 'pelamar.baru@gmail.com')->first();
    expect(Hash::check('Password123!', $user->password))->toBeTrue();
    
    // Verifikasi pelamar dibuat dengan data enkripsi
    $pelamar = Pelamar::where('user_id', $user->id)->first();
    expect($pelamar)->not->toBeNull();
    expect($pelamar->nik)->toBe('3201234567890123'); // Akan di-decrypt otomatis
    expect($pelamar->nama)->toBe('Ahmad Rizki');
    expect($pelamar->no_telepon)->toBe('081234567890');
    
    // Verifikasi semua file ter-upload (Branch 4-11 TRUE)
    expect($pelamar->file_ijazah)->not->toBeNull();
    expect($pelamar->file_transkrip)->not->toBeNull();
    expect($pelamar->file_cv)->not->toBeNull();
    expect($pelamar->file_pas_foto)->not->toBeNull();
    expect($pelamar->file_ktp)->not->toBeNull();
    expect($pelamar->file_sertifikat)->not->toBeNull();
    expect($pelamar->file_sertifikat_bahasa)->not->toBeNull();
    expect($pelamar->file_kartu_dosen)->not->toBeNull();
    
    Storage::disk('public')->assertExists($pelamar->file_ijazah);
    Storage::disk('public')->assertExists($pelamar->file_cv);
    
    // Verifikasi user langsung login setelah registrasi
    $this->assertAuthenticated();
});

// Test Case 2: Registrasi dengan data minimal tanpa file optional
test('TC-REG-02: Pelamar berhasil registrasi dengan data wajib saja tanpa file optional', function () {
    // Arrange: Hanya data wajib, semua optional kosong (Branch 4-11 semua FALSE)
    $data = [
        'email' => 'pelamar.minimal@yahoo.com',
        'password' => 'SecurePass456!',
        
        'nik' => '3301234567890123',
        'nama' => 'Siti Nurhaliza',
        'tempat_lahir' => 'Jakarta',
        'tanggal_lahir' => '1998-03-20',
        'no_telepon' => '082345678901',
        'jenis_kelamin' => 'P',
        'kewarganegaraan' => 'Indonesia',
        'status_pernikahan' => 'Menikah',
        'alamat_domisili' => 'Jl. Sudirman No. 10',
        'alamat_ktp' => 'Jl. Sudirman No. 10',
        
        // Semua field optional dikosongkan
    ];

    // Act
    $response = $this->post(route('register'), $data);

    // Assert
    $response->assertRedirect(route('dashboard'));
    
    $user = User::where('email', 'pelamar.minimal@yahoo.com')->first();
    expect($user)->not->toBeNull();
    
    $pelamar = Pelamar::where('user_id', $user->id)->first();
    expect($pelamar)->not->toBeNull();
    
    // Verifikasi semua file optional null (Branch 4-11 FALSE)
    expect($pelamar->file_ijazah)->toBeNull();
    expect($pelamar->file_transkrip)->toBeNull();
    expect($pelamar->file_cv)->toBeNull();
    expect($pelamar->file_pas_foto)->toBeNull();
    expect($pelamar->file_ktp)->toBeNull();
    expect($pelamar->file_sertifikat)->toBeNull();
    expect($pelamar->file_sertifikat_bahasa)->toBeNull();
    expect($pelamar->file_kartu_dosen)->toBeNull();
});

// Test Case 3: Registrasi gagal - Email domain internal dosen (Branch 1 FALSE)
test('TC-REG-03: Registrasi gagal karena menggunakan email domain internal dosen', function () {
    // Arrange: Email dengan domain internal (misal @telkomuniversity.ac.id)
    // Asumsi NotDosenInternalDomain menolak domain tertentu
    $data = [
        'email' => 'dosen@telkomuniversity.ac.id', // Branch 1 FALSE
        'password' => 'Password123!',
        'nik' => '3201234567890123',
        'nama' => 'Ahmad Rizki',
        'tempat_lahir' => 'Bandung',
        'tanggal_lahir' => '1995-05-15',
        'no_telepon' => '081234567890',
        'jenis_kelamin' => 'L',
        'kewarganegaraan' => 'Indonesia',
        'status_pernikahan' => 'Belum Menikah',
        'alamat_domisili' => 'Jl. Telekomunikasi No. 1',
        'alamat_ktp' => 'Jl. Telekomunikasi No. 1',
    ];

    // Act
    $response = $this->post(route('register'), $data);

    // Assert: Validasi gagal
    $response->assertSessionHasErrors('email');
    $this->assertDatabaseMissing('users', [
        'email' => 'dosen@telkomuniversity.ac.id',
    ]);
});

// Test Case 4: Registrasi gagal - NIK sudah terdaftar (Branch 2 FALSE)
test('TC-REG-04: Registrasi gagal karena NIK sudah terdaftar', function () {
    // Arrange: Buat pelamar dengan NIK tertentu
    $existingUser = User::factory()->create();
    $existingPelamar = Pelamar::factory()->create([
        'user_id' => $existingUser->id,
        'nik' => '3201234567890123', // NIK yang sudah ada
    ]);
    
    $data = [
        'email' => 'pelamar.baru@gmail.com',
        'password' => 'Password123!',
        'nik' => '3201234567890123', // Branch 2 FALSE: NIK duplikat
        'nama' => 'Ahmad Rizki',
        'tempat_lahir' => 'Bandung',
        'tanggal_lahir' => '1995-05-15',
        'no_telepon' => '081234567890',
        'jenis_kelamin' => 'L',
        'kewarganegaraan' => 'Indonesia',
        'status_pernikahan' => 'Belum Menikah',
        'alamat_domisili' => 'Jl. Telekomunikasi No. 1',
        'alamat_ktp' => 'Jl. Telekomunikasi No. 1',
    ];

    // Act
    $response = $this->post(route('register'), $data);

    // Assert
    $response->assertSessionHasErrors('nik');
    $response->assertSessionHasErrorsIn('default', [
        'nik' => 'NIK sudah terdaftar'
    ], true);
});

// Test Case 5: Registrasi gagal - No telepon sudah terdaftar (Branch 3 FALSE)
test('TC-REG-05: Registrasi gagal karena nomor telepon sudah terdaftar', function () {
    // Arrange
    $existingUser = User::factory()->create();
    $existingPelamar = Pelamar::factory()->create([
        'user_id' => $existingUser->id,
        'no_telepon' => '081234567890', // No telepon yang sudah ada
    ]);
    
    $data = [
        'email' => 'pelamar.baru@gmail.com',
        'password' => 'Password123!',
        'nik' => '3201234567890999',
        'nama' => 'Ahmad Rizki',
        'tempat_lahir' => 'Bandung',
        'tanggal_lahir' => '1995-05-15',
        'no_telepon' => '081234567890', // Branch 3 FALSE: No telepon duplikat
        'jenis_kelamin' => 'L',
        'kewarganegaraan' => 'Indonesia',
        'status_pernikahan' => 'Belum Menikah',
        'alamat_domisili' => 'Jl. Telekomunikasi No. 1',
        'alamat_ktp' => 'Jl. Telekomunikasi No. 1',
    ];

    // Act
    $response = $this->post(route('register'), $data);

    // Assert
    $response->assertSessionHasErrors('no_telepon');
    $response->assertSessionHasErrorsIn('default', [
        'no_telepon' => 'sudah terdaftar'
    ], true);
});

// Test Case 6: Registrasi dengan sebagian file saja (mixed upload)
test('TC-REG-06: Pelamar registrasi dengan sebagian file di-upload dan sebagian tidak', function () {
    // Arrange: Hanya upload beberapa file (test kombinasi TRUE/FALSE untuk Branch 4-11)
    $data = [
        'email' => 'pelamar.partial@gmail.com',
        'password' => 'Password123!',
        
        'nik' => '3401234567890123',
        'nama' => 'Budi Santoso',
        'tempat_lahir' => 'Surabaya',
        'tanggal_lahir' => '1997-08-10',
        'no_telepon' => '083456789012',
        'jenis_kelamin' => 'L',
        'kewarganegaraan' => 'Indonesia',
        'status_pernikahan' => 'Belum Menikah',
        'alamat_domisili' => 'Jl. Raya No. 5',
        'alamat_ktp' => 'Jl. Raya No. 5',
        
        // Hanya upload cv dan pas_foto (Branch 6 & 7 TRUE, yang lain FALSE)
        'cv' => UploadedFile::fake()->create('cv.pdf', 1000),
        'pas_foto' => UploadedFile::fake()->image('foto.jpg', 300, 400),
    ];

    // Act
    $response = $this->post(route('register'), $data);

    // Assert
    $response->assertRedirect(route('dashboard'));
    
    $pelamar = Pelamar::where('user_id', User::where('email', 'pelamar.partial@gmail.com')->first()->id)->first();
    
    // File yang di-upload harus ada
    expect($pelamar->file_cv)->not->toBeNull();
    expect($pelamar->file_pas_foto)->not->toBeNull();
    
    // File yang tidak di-upload harus null
    expect($pelamar->file_ijazah)->toBeNull();
    expect($pelamar->file_transkrip)->toBeNull();
    expect($pelamar->file_ktp)->toBeNull();
    expect($pelamar->file_sertifikat)->toBeNull();
});

// ===================== TEST CASES UNTUK checkEmail() =====================

// Test Case 7: AJAX check email - email valid dan tersedia (Branch 12 TRUE)
test('TC-REG-07: AJAX check email berhasil untuk email yang valid dan belum terdaftar', function () {
    // Arrange
    $data = ['email' => 'newemail@gmail.com'];

    // Act
    $response = $this->postJson('/register/check-email', $data);

    // Assert: Branch 12 TRUE - email valid
    $response->assertStatus(200);
    $response->assertJson(['valid' => true]);
});

// Test Case 8: AJAX check email - email sudah terdaftar (Branch 12 FALSE)
test('TC-REG-08: AJAX check email gagal karena email sudah terdaftar', function () {
    // Arrange: Buat user dengan email tertentu
    User::factory()->create(['email' => 'existing@gmail.com']);
    
    $data = ['email' => 'existing@gmail.com'];

    // Act
    $response = $this->postJson('/register/check-email', $data);

    // Assert: Branch 12 FALSE - ValidationException
    $response->assertStatus(422);
    $response->assertJson(['valid' => false]);
    $response->assertJsonStructure(['message']);
});

// Test Case 9: AJAX check email - format email invalid
test('TC-REG-09: AJAX check email gagal karena format email tidak valid', function () {
    // Arrange
    $data = ['email' => 'bukan-email-valid'];

    // Act
    $response = $this->postJson('/register/check-email', $data);

    // Assert
    $response->assertStatus(422);
    $response->assertJson(['valid' => false]);
});

// ===================== TEST CASES UNTUK checkIdentity() =====================

// Test Case 10: AJAX check identity - NIK valid dan tersedia (Branch 13, 14, 15)
test('TC-REG-10: AJAX check identity berhasil untuk NIK valid yang belum terdaftar', function () {
    // Arrange: NIK valid 16 digit
    $data = ['nik' => '3201234567890123'];

    // Act
    $response = $this->postJson('/register/check-identity', $data);

    // Assert: Branch 13 TRUE, Branch 14 TRUE, Branch 15 FALSE (tidak ada duplikat)
    $response->assertStatus(200);
    $response->assertJson(['valid' => true]);
});

// Test Case 11: AJAX check identity - NIK format invalid (Branch 14 FALSE)
test('TC-REG-11: AJAX check identity gagal karena format NIK tidak 16 digit', function () {
    // Arrange: NIK tidak 16 digit
    $data = ['nik' => '12345']; // Hanya 5 digit

    // Act
    $response = $this->postJson('/register/check-identity', $data);

    // Assert: Branch 14 FALSE
    $response->assertStatus(422);
    $response->assertJson([
        'valid' => false,
        'field' => 'nik',
        'message' => 'NIK harus terdiri dari 16 digit angka.'
    ]);
});

// Test Case 12: AJAX check identity - NIK sudah terdaftar (Branch 15 TRUE)
test('TC-REG-12: AJAX check identity gagal karena NIK sudah terdaftar', function () {
    // Arrange: Buat pelamar dengan NIK tertentu
    $user = User::factory()->create();
    Pelamar::factory()->create([
        'user_id' => $user->id,
        'nik' => '3201234567890123',
    ]);
    
    $data = ['nik' => '3201234567890123'];

    // Act
    $response = $this->postJson('/register/check-identity', $data);

    // Assert: Branch 15 TRUE
    $response->assertStatus(422);
    $response->assertJson([
        'valid' => false,
        'field' => 'nik',
        'message' => 'NIK sudah terdaftar. Pastikan Anda belum pernah mendaftar sebelumnya.'
    ]);
});

// Test Case 13: AJAX check identity - No telepon valid dan tersedia (Branch 16, 17, 18)
test('TC-REG-13: AJAX check identity berhasil untuk nomor telepon valid yang belum terdaftar', function () {
    // Arrange: No telepon valid format 08...
    $data = ['no_telepon' => '081234567890'];

    // Act
    $response = $this->postJson('/register/check-identity', $data);

    // Assert: Branch 16 TRUE, Branch 17 TRUE, Branch 18 FALSE
    $response->assertStatus(200);
    $response->assertJson(['valid' => true]);
});

// Test Case 14: AJAX check identity - No telepon format invalid (Branch 17 FALSE)
test('TC-REG-14: AJAX check identity gagal karena format nomor telepon invalid', function () {
    // Arrange: No telepon tidak diawali 08
    $data = ['no_telepon' => '0212345678'];

    // Act
    $response = $this->postJson('/register/check-identity', $data);

    // Assert: Branch 17 FALSE
    $response->assertStatus(422);
    $response->assertJson([
        'valid' => false,
        'field' => 'no_telepon',
    ]);
    $response->assertJsonFragment(['message']);
});

// Test Case 15: AJAX check identity - No telepon sudah terdaftar (Branch 18 TRUE)
test('TC-REG-15: AJAX check identity gagal karena nomor telepon sudah terdaftar', function () {
    // Arrange
    $user = User::factory()->create();
    Pelamar::factory()->create([
        'user_id' => $user->id,
        'no_telepon' => '081234567890',
    ]);
    
    $data = ['no_telepon' => '081234567890'];

    // Act
    $response = $this->postJson('/register/check-identity', $data);

    // Assert: Branch 18 TRUE
    $response->assertStatus(422);
    $response->assertJson([
        'valid' => false,
        'field' => 'no_telepon',
        'message' => 'No. telepon sudah terdaftar. Gunakan nomor telepon yang berbeda.'
    ]);
});

// Test Case 16: AJAX check identity - Tidak ada parameter (Branch 13 & 16 FALSE)
test('TC-REG-16: AJAX check identity valid ketika tidak ada NIK atau no_telepon yang dikirim', function () {
    // Arrange: Tidak ada parameter
    $data = [];

    // Act
    $response = $this->postJson('/register/check-identity', $data);

    // Assert: Branch 13 FALSE, Branch 16 FALSE - skip semua validasi
    $response->assertStatus(200);
    $response->assertJson(['valid' => true]);
});

// Test Case 17: AJAX check identity - Check NIK dan no_telepon sekaligus
test('TC-REG-17: AJAX check identity dengan NIK dan no_telepon yang keduanya valid', function () {
    // Arrange: Kirim kedua parameter sekaligus
    $data = [
        'nik' => '3201234567890123',
        'no_telepon' => '081234567890',
    ];

    // Act
    $response = $this->postJson('/register/check-identity', $data);

    // Assert: Kedua validasi harus lulus
    $response->assertStatus(200);
    $response->assertJson(['valid' => true]);
});

// Test Case 18: Registrasi dengan validasi password lemah
test('TC-REG-18: Registrasi gagal karena password tidak memenuhi kriteria keamanan', function () {
    // Arrange: Password terlalu lemah
    $data = [
        'email' => 'pelamar@gmail.com',
        'password' => '123', // Password lemah
        'nik' => '3201234567890123',
        'nama' => 'Ahmad Rizki',
        'tempat_lahir' => 'Bandung',
        'tanggal_lahir' => '1995-05-15',
        'no_telepon' => '081234567890',
        'jenis_kelamin' => 'L',
        'kewarganegaraan' => 'Indonesia',
        'status_pernikahan' => 'Belum Menikah',
        'alamat_domisili' => 'Jl. Telekomunikasi No. 1',
        'alamat_ktp' => 'Jl. Telekomunikasi No. 1',
    ];

    // Act
    $response = $this->post(route('register'), $data);

    // Assert
    $response->assertSessionHasErrors('password');
});

// Test Case 19: Registrasi dengan tanggal lahir invalid
test('TC-REG-19: Registrasi gagal karena format tanggal lahir tidak valid', function () {
    // Arrange
    $data = [
        'email' => 'pelamar@gmail.com',
        'password' => 'Password123!',
        'nik' => '3201234567890123',
        'nama' => 'Ahmad Rizki',
        'tempat_lahir' => 'Bandung',
        'tanggal_lahir' => '32-13-2000', // Format invalid
        'no_telepon' => '081234567890',
        'jenis_kelamin' => 'L',
        'kewarganegaraan' => 'Indonesia',
        'status_pernikahan' => 'Belum Menikah',
        'alamat_domisili' => 'Jl. Telekomunikasi No. 1',
        'alamat_ktp' => 'Jl. Telekomunikasi No. 1',
    ];

    // Act
    $response = $this->post(route('register'), $data);

    // Assert
    $response->assertSessionHasErrors('tanggal_lahir');
});

// Test Case 20: Registrasi dengan IPK di luar range 0-4
test('TC-REG-20: Registrasi gagal karena IPK melebihi nilai maksimum 4.0', function () {
    // Arrange
    $data = [
        'email' => 'pelamar@gmail.com',
        'password' => 'Password123!',
        'nik' => '3201234567890123',
        'nama' => 'Ahmad Rizki',
        'tempat_lahir' => 'Bandung',
        'tanggal_lahir' => '1995-05-15',
        'no_telepon' => '081234567890',
        'jenis_kelamin' => 'L',
        'kewarganegaraan' => 'Indonesia',
        'status_pernikahan' => 'Belum Menikah',
        'alamat_domisili' => 'Jl. Telekomunikasi No. 1',
        'alamat_ktp' => 'Jl. Telekomunikasi No. 1',
        'ipk' => 5.0, // IPK invalid > 4.0
    ];

    // Act
    $response = $this->post(route('register'), $data);

    // Assert
    $response->assertSessionHasErrors('ipk');
});
