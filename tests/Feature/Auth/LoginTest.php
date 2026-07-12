<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

/**
 * WHITEBOX TESTING: Login (store method)
 * 
 * FLOWGRAPH:
 *   N1 (START) → N2 (Check Rate Limit) → [decision]
 *      ↓ (limited) → EXIT
 *      ↓ (ok) → N3 (Auth::attempt) → [decision]
 *         ↓ (failed) → EXIT
 *         ↓ (success) → N4 (Regenerate) → N5 (Set flag) → N6 (Redirect) → END
 * 
 * CYCLOMATIC COMPLEXITY: V(G) = 3
 *   - Total Nodes: 6
 *   - Decision Points: 2
 *     1. Rate limit check (ensureIsNotRateLimited)
 *     2. Auth::attempt (berhasil/gagal)
 *   - V(G) = 2 + 1 = 3
 * 
 * BASIS PATH: 3 paths
 *   Path 1: N1 → N2 (rate limited) → EXIT
 *   Path 2: N1 → N2 → N3 (auth failed) → EXIT
 *   Path 3: N1 → N2 → N3 (success) → N4 → N5 → N6 (Happy path)
 */

describe('Login Authentication', function () {
    
    /**
     * PATH 3: N1 → N2 → N3 (success) → N4 → N5 → N6
     * Happy Path: Login berhasil dengan kredensial valid
     */
    it('dapat login dengan kredensial valid (pelamar)', function () {
        // Arrange: Buat user pelamar
        $user = User::factory()->create([
            'email' => 'pelamar@test.com',
            'password' => Hash::make('password123'),
            'role' => 'pelamar',
        ]);

        // Act: Submit login form
        $response = $this->post(route('login'), [
            'email' => 'pelamar@test.com',
            'password' => 'password123',
        ]);

        // Assert: Redirect ke dashboard
        $response->assertRedirect(route('dashboard'));
        
        // Assert: User ter-autentikasi
        $this->assertAuthenticatedAs($user);
        
        // Assert: Session flag untuk profile reminder dibuat
        expect(session('show_profile_reminder'))->toBeTrue();
    });

    /**
     * PATH 3: Login berhasil dengan kredensial valid (dosen/admin)
     */
    it('dapat login dengan kredensial valid (admin)', function () {
        // Arrange: Buat user admin
        $user = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // Act
        $response = $this->post(route('login'), [
            'email' => 'admin@test.com',
            'password' => 'password123',
        ]);

        // Assert
        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    });

    /**
     * PATH 2: N1 → N2 → N3 (auth failed) → EXIT
     * Login gagal: Password salah
     */
    it('tidak dapat login dengan password salah', function () {
        // Arrange
        User::factory()->create([
            'email' => 'pelamar@test.com',
            'password' => Hash::make('correct-password'),
            'role' => 'pelamar',
        ]);

        // Act: Submit dengan password salah
        $response = $this->post(route('login'), [
            'email' => 'pelamar@test.com',
            'password' => 'wrong-password',
        ]);

        // Assert: Tidak redirect, ada error
        $response->assertSessionHasErrors('email');
        
        // Assert: User tidak ter-autentikasi
        $this->assertGuest();
    });

    /**
     * PATH 2: Login gagal: Email tidak terdaftar
     */
    it('tidak dapat login dengan email yang tidak terdaftar', function () {
        // Act
        $response = $this->post(route('login'), [
            'email' => 'tidakada@test.com',
            'password' => 'password123',
        ]);

        // Assert
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    });

    /**
     * PATH 1: N1 → N2 (rate limited) → EXIT
     * Login ditolak karena rate limit (terlalu banyak percobaan)
     */
    it('diblokir setelah 5 kali percobaan login gagal', function () {
        // Arrange: Buat user
        $pelamar = Pelamar::factory()->create([
            'email' => 'pelamar@test.com',
            'password' => Hash::make('correct-password'),
        ]);

        // Act: Lakukan 5 kali login gagal
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login'), [
                'email' => 'pelamar@test.com',
                'password' => 'wrong-password',
            ]);
        }

        // Act: Percobaan ke-6
        $response = $this->post(route('login'), [
            'email' => 'pelamar@test.com',
            'password' => 'wrong-password',
        ]);

        // Assert: Error throttle muncul
        $response->assertSessionHasErrors('email');
        
        // Assert: Error message berisi 'Too many'
        expect($response->getSession()->get('errors')->first('email'))
            ->toContain('Too many');
        
        // Assert: Tetap tidak ter-autentikasi
        $this->assertGuest();
    });

    /**
     * PATH 1 (Edge Case): Rate limit dibersihkan setelah login berhasil
     */
    it('rate limit dibersihkan setelah login berhasil', function () {
        // Arrange
        $pelamar = Pelamar::factory()->create([
            'email' => 'pelamar@test.com',
            'password' => Hash::make('correct-password'),
        ]);

        // Act: Lakukan 3 kali login gagal
        for ($i = 0; $i < 3; $i++) {
            $this->post(route('login'), [
                'email' => 'pelamar@test.com',
                'password' => 'wrong-password',
            ]);
        }

        // Act: Login berhasil
        $response = $this->post(route('login'), [
            'email' => 'pelamar@test.com',
            'password' => 'correct-password',
        ]);

        // Assert: Login berhasil
        $this->assertAuthenticatedAs($pelamar);

        // Logout dulu
        $this->post(route('logout'));

        // Act: Coba login lagi (seharusnya tidak rate limited)
        $response = $this->post(route('login'), [
            'email' => 'pelamar@test.com',
            'password' => 'correct-password',
        ]);

        // Assert: Tetap bisa login
        $this->assertAuthenticatedAs($pelamar);
    });

    /**
     * PATH 3 (Edge Case): Redirect ke intended URL
     */
    it('redirect ke intended URL setelah login berhasil', function () {
        // Arrange: Buat user
        $pelamar = Pelamar::factory()->create([
            'email' => 'pelamar@test.com',
            'password' => Hash::make('password123'),
        ]);

        // Act: Akses halaman protected dulu (belum login)
        $this->get(route('dashboard'));

        // Act: Login
        $response = $this->post(route('login'), [
            'email' => 'pelamar@test.com',
            'password' => 'password123',
        ]);

        // Assert: Redirect ke dashboard (intended URL)
        $response->assertRedirect(route('dashboard'));
    });

    /**
     * PATH 3 (Edge Case): Session regeneration
     */
    it('meregenerasi session setelah login berhasil', function () {
        // Arrange
        $pelamar = Pelamar::factory()->create([
            'email' => 'pelamar@test.com',
            'password' => Hash::make('password123'),
        ]);

        // Ambil session ID sebelum login
        $oldSessionId = session()->getId();

        // Act: Login
        $response = $this->post(route('login'), [
            'email' => 'pelamar@test.com',
            'password' => 'password123',
        ]);

        // Assert: Session ID berubah (regenerated)
        expect(session()->getId())->not->toBe($oldSessionId);
    });

    /**
     * Edge Case: Validasi input kosong
     */
    it('menolak login dengan email kosong', function () {
        $response = $this->post(route('login'), [
            'email' => '',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    });

    it('menolak login dengan password kosong', function () {
        $response = $this->post(route('login'), [
            'email' => 'pelamar@test.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password');
    });

    it('menolak login dengan format email tidak valid', function () {
        $response = $this->post(route('login'), [
            'email' => 'bukan-email',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    });

    /**
     * PATH 3: Test remember me functionality
     */
    it('dapat mengaktifkan remember me saat login', function () {
        // Arrange
        $pelamar = Pelamar::factory()->create([
            'email' => 'pelamar@test.com',
            'password' => Hash::make('password123'),
        ]);

        // Act: Login dengan remember = true
        $response = $this->post(route('login'), [
            'email' => 'pelamar@test.com',
            'password' => 'password123',
            'remember' => true,
        ]);

        // Assert: Login berhasil
        $this->assertAuthenticatedAs($pelamar);
        
        // Assert: Remember cookie ada
        $response->assertCookie(Auth::getRecallerName());
    });
});

/**
 * WHITEBOX TESTING: Logout (destroy method)
 * 
 * Logout memiliki path linier tanpa decision point
 * V(G) = 1 (no branches)
 */
describe('Logout', function () {
    
    it('dapat logout dan menghapus session', function () {
        // Arrange: Login dulu
        $pelamar = Pelamar::factory()->create([
            'email' => 'pelamar@test.com',
            'password' => Hash::make('password123'),
        ]);
        
        $this->actingAs($pelamar);

        // Act: Logout
        $response = $this->post(route('logout'));

        // Assert: Redirect ke homepage
        $response->assertRedirect('/');
        
        // Assert: User tidak ter-autentikasi
        $this->assertGuest();
    });
});
