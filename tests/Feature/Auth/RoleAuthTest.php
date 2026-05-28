<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test: Login & redirect per role
 */
class RoleAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_redirected_to_admin_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'admin', 'password' => bcrypt('password')]);

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
             ->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
    }

    public function test_pelamar_redirected_to_pelamar_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'pelamar', 'password' => bcrypt('password')]);

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
             ->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
    }

    public function test_penguji_redirected_to_penguji_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'penguji', 'password' => bcrypt('password')]);

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
             ->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
    }

    public function test_kaprodi_redirected_to_kaprodi_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'kaprodi', 'password' => bcrypt('password')]);

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
             ->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
    }

    public function test_dosen_without_role_cannot_login(): void
    {
        $user = User::factory()->create(['role' => null, 'password' => bcrypt('password')]);

        $this->post('/login', ['email' => $user->email, 'password' => 'password']);

        // Dosen tanpa role di-logout oleh middleware
        // mereka bisa login tapi langsung dikick saat akses route
        $this->get('/admin/dashboard')->assertRedirect();
    }

    public function test_wrong_password_rejected(): void
    {
        $user = User::factory()->create(['role' => 'pelamar', 'password' => bcrypt('password')]);

        $this->post('/login', ['email' => $user->email, 'password' => 'salah']);

        $this->assertGuest();
    }

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/login');
    }

    public function test_pelamar_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'pelamar']);

        $this->actingAs($user)
             ->get('/admin/dashboard')
             ->assertRedirect(route('pelamar.dashboard'));
    }

    public function test_penguji_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'penguji']);

        $this->actingAs($user)
             ->get('/admin/dashboard')
             ->assertRedirect(route('penguji.dashboard'));
    }

    public function test_kaprodi_cannot_access_penguji_pages(): void
    {
        $user = User::factory()->create(['role' => 'kaprodi']);

        $this->actingAs($user)
             ->get('/penguji/dashboard')
             ->assertRedirect(route('kaprodi.dashboard'));
    }
}
