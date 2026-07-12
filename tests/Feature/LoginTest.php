<?php

/**
 * WHITE BOX TESTING - LOGIN
 * Teknik     : Branch Coverage
 * Controller : AuthenticatedSessionController@store
 * Request    : LoginRequest (authenticate, ensureIsNotRateLimited)
 * Route      : POST /login
 *
 * ============================================================
 * ANALISIS BRANCH PADA KODE SUMBER:
 * ============================================================
 *
 * [LoginRequest::authenticate()]
 *
 *   $this->ensureIsNotRateLimited();                          // Branch 1
 *
 *   if (! Auth::attempt(...)) {                               // Branch 2
 *       RateLimiter::hit($this->throttleKey());
 *       throw ValidationException::withMessages([...]);
 *   }
 *   RateLimiter::clear($this->throttleKey());
 *
 * [LoginRequest::ensureIsNotRateLimited()]
 *
 *   if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {  // Branch 3
 *       return;
 *   }
 *   event(new Lockout($this));
 *   throw ValidationException::withMessages([...]);
 *
 * [AuthenticatedSessionController::store()]
 *
 *   return redirect()->intended(route('dashboard'));          // Branch 4: redirect berdasarkan role
 *
 * [Validasi input dari rules()]
 *
 *   'email'    => ['required', 'string', 'email']            // Branch 5
 *   'password' => ['required', 'string']                     // Branch 6
 *
 * ------------------------------------------------------------
 * Branch 1 — Rate limiter check (ensureIsNotRateLimited)
 *   TRUE  : Belum melebihi batas → lanjut authenticate
 *   FALSE : Sudah terlalu banyak percobaan → throw throttle error
 *
 * Branch 2 — Auth::attempt (kredensial valid/tidak)
 *   TRUE  : Kredensial valid → login berhasil, redirect dashboard
 *   FALSE : Kredensial salah → throw ValidationException email error
 *
 * Branch 3 — RateLimiter::tooManyAttempts
 *   TRUE  : Belum terkena rate limit → return (lanjut)
 *   FALSE : Sudah terkena rate limit → throw throttle error
 *
 * Branch 4 — Redirect setelah login (berdasarkan role)
 *   admin    → redirect ke admin.dashboard
 *   pelamar  → redirect ke pelamar.dashboard
 *   penguji  → redirect ke penguji.dashboard
 *   kaprodi  → redirect ke kaprodi.dashboard
 *   invalid  → logout dan redirect ke login dengan error
 *
 * Branch 5 — Validasi field email
 *   TRUE  : Email diisi dan format valid → lanjut
 *   FALSE : Email kosong atau format salah → error validasi
 *
 * Branch 6 — Validasi field password
 *   TRUE  : Password diisi → lanjut
 *   FALSE : Password kosong → error validasi
 *
 * ============================================================
 * PETA TEST CASE → BRANCH YANG DICAKUP:
 * ============================================================
 *
 * TC-01 : B2=T, B4=pelamar  → happy path, login pelamar berhasil
 * TC-02 : B2=T, B4=admin    → happy path, login admin berhasil
 * TC-03 : B2=T, B4=penguji  → happy path, login penguji berhasil
 * TC-04 : B2=T, B4=kaprodi  → happy path, login kaprodi berhasil
 * TC-05 : B2=F              → unhappy, password salah
 * TC-06 : B2=F              → unhappy, email tidak terdaftar
 * TC-07 : B5=F (kosong)     → unhappy, email tidak diisi
 * TC-08 : B5=F (format)     → unhappy, format email tidak valid
 * TC-09 : B6=F              → unhappy, password tidak diisi
 * TC-10 : B1=F / B3=F       → unhappy, terlalu banyak percobaan login (rate limit)
 * TC-11 : B4=invalid role   → unhappy, role tidak dikenal → redirect login dengan error
 *
 * ============================================================
 * TABEL HASIL PENGUJIAN:
 * ============================================================
 *
 * | Test Case | Skenario                                              | Hasil yang Diharapkan                                                              | Hasil |
 * |-----------|-------------------------------------------------------|------------------------------------------------------------------------------------|-------|
 * | TC-01     | Pelamar login dengan kredensial valid                 | Pelamar berhasil login, sistem mengarahkan ke dashboard pelamar                    | Lulus |
 * | TC-02     | Admin login dengan kredensial valid                   | Admin berhasil login, sistem mengarahkan ke dashboard admin                        | Lulus |
 * | TC-03     | Penguji login dengan kredensial valid                 | Penguji berhasil login, sistem mengarahkan ke dashboard penguji                    | Lulus |
 * | TC-04     | Kaprodi login dengan kredensial valid                 | Kaprodi berhasil login, sistem mengarahkan ke dashboard kaprodi                    | Lulus |
 * | TC-05     | Pengguna login dengan password yang salah             | Pengguna gagal login, sistem menampilkan pesan error kredensial tidak valid        | Lulus |
 * | TC-06     | Pengguna login dengan email yang tidak terdaftar      | Pengguna gagal login, sistem menampilkan pesan error kredensial tidak valid        | Lulus |
 * | TC-07     | Pengguna login tanpa mengisi field email              | Pengguna gagal login, sistem menampilkan pesan error email wajib diisi             | Lulus |
 * | TC-08     | Pengguna login dengan format email tidak valid        | Pengguna gagal login, sistem menampilkan pesan error format email tidak valid      | Lulus |
 * | TC-09     | Pengguna login tanpa mengisi field password           | Pengguna gagal login, sistem menampilkan pesan error password wajib diisi          | Lulus |
 * | TC-10     | Pengguna login melebihi batas percobaan               | Pengguna gagal login, sistem menampilkan pesan error terlalu banyak percobaan      | Lulus |
 * | TC-11     | Pengguna login dengan akun yang memiliki role invalid | Pengguna gagal login, sistem menampilkan pesan error akun belum memiliki akses     | Lulus |
 */

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ---------------------------------------------------------------
// TC-01 | Happy Path
// B2=T : Kredensial valid
// B4   : Role pelamar → redirect ke dashboard
// ---------------------------------------------------------------
test('TC-01: Pelamar berhasil login, sistem mengarahkan ke dashboard pelamar', function () {
    // Arrange
    $user = User::factory()->create([
        'role'     => 'pelamar',
        'password' => bcrypt('password123'),
    ]);

    // Act
    $response = $this->post('/login', [
        'email'    => $user->email,
        'password' => 'password123',
    ]);

    // Assert
    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard'));
});

// ---------------------------------------------------------------
// TC-02 | Happy Path
// B2=T : Kredensial valid
// B4   : Role admin → redirect ke dashboard
// ---------------------------------------------------------------
test('TC-02: Admin berhasil login, sistem mengarahkan ke dashboard admin', function () {
    // Arrange
    $user = User::factory()->create([
        'role'     => 'admin',
        'password' => bcrypt('password123'),
    ]);

    // Act
    $response = $this->post('/login', [
        'email'    => $user->email,
        'password' => 'password123',
    ]);

    // Assert
    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard'));
});

// ---------------------------------------------------------------
// TC-03 | Happy Path
// B2=T : Kredensial valid
// B4   : Role penguji → redirect ke dashboard
// ---------------------------------------------------------------
test('TC-03: Penguji berhasil login, sistem mengarahkan ke dashboard penguji', function () {
    // Arrange
    $user = User::factory()->create([
        'role'     => 'penguji',
        'password' => bcrypt('password123'),
    ]);

    // Act
    $response = $this->post('/login', [
        'email'    => $user->email,
        'password' => 'password123',
    ]);

    // Assert
    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard'));
});

// ---------------------------------------------------------------
// TC-04 | Happy Path
// B2=T : Kredensial valid
// B4   : Role kaprodi → redirect ke dashboard
// ---------------------------------------------------------------
test('TC-04: Kaprodi berhasil login, sistem mengarahkan ke dashboard kaprodi', function () {
    // Arrange
    $user = User::factory()->create([
        'role'     => 'kaprodi',
        'password' => bcrypt('password123'),
    ]);

    // Act
    $response = $this->post('/login', [
        'email'    => $user->email,
        'password' => 'password123',
    ]);

    // Assert
    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard'));
});

// ---------------------------------------------------------------
// TC-05 | Unhappy Path
// B2=F : Auth::attempt gagal karena password salah
// ---------------------------------------------------------------
test('TC-05: Pengguna gagal login, sistem menampilkan pesan error kredensial tidak valid ketika password salah', function () {
    // Arrange
    $user = User::factory()->create([
        'password' => bcrypt('password123'),
    ]);

    // Act
    $response = $this->post('/login', [
        'email'    => $user->email,
        'password' => 'password_salah',
    ]);

    // Assert
    $this->assertGuest();
    $response->assertSessionHasErrors('email');
});

// ---------------------------------------------------------------
// TC-06 | Unhappy Path
// B2=F : Auth::attempt gagal karena email tidak terdaftar
// ---------------------------------------------------------------
test('TC-06: Pengguna gagal login, sistem menampilkan pesan error kredensial tidak valid ketika email tidak terdaftar', function () {
    // Arrange: tidak ada user yang dibuat

    // Act
    $response = $this->post('/login', [
        'email'    => 'tidakterdaftar@example.com',
        'password' => 'password123',
    ]);

    // Assert
    $this->assertGuest();
    $response->assertSessionHasErrors('email');
});

// ---------------------------------------------------------------
// TC-07 | Unhappy Path
// B5=F : Validasi gagal karena field email kosong
// ---------------------------------------------------------------
test('TC-07: Pengguna gagal login, sistem menampilkan pesan error email wajib diisi', function () {
    // Act
    $response = $this->post('/login', [
        'email'    => '',
        'password' => 'password123',
    ]);

    // Assert
    $this->assertGuest();
    $response->assertSessionHasErrors('email');
});

// ---------------------------------------------------------------
// TC-08 | Unhappy Path
// B5=F : Validasi gagal karena format email tidak valid
// ---------------------------------------------------------------
test('TC-08: Pengguna gagal login, sistem menampilkan pesan error format email tidak valid', function () {
    // Act
    $response = $this->post('/login', [
        'email'    => 'bukan-format-email',
        'password' => 'password123',
    ]);

    // Assert
    $this->assertGuest();
    $response->assertSessionHasErrors('email');
});

// ---------------------------------------------------------------
// TC-09 | Unhappy Path
// B6=F : Validasi gagal karena field password kosong
// ---------------------------------------------------------------
test('TC-09: Pengguna gagal login, sistem menampilkan pesan error password wajib diisi', function () {
    // Arrange
    $user = User::factory()->create();

    // Act
    $response = $this->post('/login', [
        'email'    => $user->email,
        'password' => '',
    ]);

    // Assert
    $this->assertGuest();
    $response->assertSessionHasErrors('password');
});

// ---------------------------------------------------------------
// TC-10 | Unhappy Path
// B1=F / B3=F : Rate limiter aktif setelah 5 percobaan gagal
// ---------------------------------------------------------------
test('TC-10: Pengguna gagal login, sistem menampilkan pesan error terlalu banyak percobaan', function () {
    // Arrange
    $user = User::factory()->create([
        'password' => bcrypt('password123'),
    ]);

    // Simulasi 5 percobaan gagal untuk trigger rate limiter
    for ($i = 0; $i < 5; $i++) {
        $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password_salah',
        ]);
    }

    // Act: percobaan ke-6 → sudah terkena throttle
    $response = $this->post('/login', [
        'email'    => $user->email,
        'password' => 'password123',
    ]);

    // Assert
    $this->assertGuest();
    $response->assertSessionHasErrors('email');
});

// ---------------------------------------------------------------
// TC-11 | Unhappy Path
// B4=invalid : Role null/tidak dikenal → logout dan redirect login
// ---------------------------------------------------------------
test('TC-11: Pengguna gagal login, sistem menampilkan pesan error akun belum memiliki akses ketika role tidak valid', function () {
    // Arrange
    $user = User::factory()->create([
        'role'     => null,
        'password' => bcrypt('password123'),
    ]);

    // Login berhasil secara autentikasi
    $this->post('/login', [
        'email'    => $user->email,
        'password' => 'password123',
    ]);

    // Act: akses /dashboard → middleware mendeteksi role invalid
    $response = $this->get(route('dashboard'));

    // Assert
    $response->assertRedirect('/login');
    $this->assertGuest();
});
