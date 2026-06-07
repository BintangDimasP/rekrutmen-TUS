<?php

namespace Tests\Feature\Admin;

use App\Models\Dosen;
use App\Models\Pelamar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test: Admin — Manajemen User (edit & destroy)
 */
class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    // ── INDEX ────────────────────────────────────────────────────

    public function test_admin_can_view_user_list(): void
    {
        $this->actingAs($this->adminUser())
             ->get(route('admin.user.index'))
             ->assertOk();
    }

    public function test_non_admin_cannot_view_user_list(): void
    {
        $pelamar = User::factory()->create(['role' => 'pelamar']);

        $this->actingAs($pelamar)
             ->get(route('admin.user.index'))
             ->assertRedirect(route('pelamar.dashboard'));
    }

    // ── UPDATE password ──────────────────────────────────────────

    public function test_admin_can_reset_user_password(): void
    {
        $admin  = $this->adminUser();
        // Use penguji (non-pelamar) — pelamar update also requires email field
        $target = User::factory()->create(['role' => 'penguji', 'password' => bcrypt('lama123')]);

        $this->actingAs($admin)
             ->put(route('admin.user.update', $target), ['password' => 'baru1234'])
             ->assertRedirect();

        $target->refresh();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('baru1234', $target->password));
    }

    // ── DESTROY pelamar ──────────────────────────────────────────

    public function test_admin_can_delete_pelamar_account(): void
    {
        $admin   = $this->adminUser();
        $pelamar = User::factory()->create(['role' => 'pelamar']);

        Pelamar::factory()->create(['user_id' => $pelamar->id]);

        $this->actingAs($admin)
             ->delete(route('admin.user.destroy', $pelamar))
             ->assertRedirect();

        $this->assertDatabaseMissing('users', ['id' => $pelamar->id]);
        $this->assertDatabaseMissing('pelamars', ['user_id' => $pelamar->id]);
    }

    // ── DESTROY dosen (penguji) ──────────────────────────────────

    public function test_admin_can_revoke_penguji_role_and_deletes_user(): void
    {
        $admin = $this->adminUser();
        $dosen = Dosen::factory()->create(['is_penguji' => true]);
        $penguji = User::factory()->create([
            'role'       => 'penguji',
            'is_penguji' => true,
            'dosen_id'   => $dosen->id,
        ]);

        $this->actingAs($admin)
             ->delete(route('admin.user.destroy', $penguji))
             ->assertRedirect();

        // User account should be deleted
        $this->assertDatabaseMissing('users', ['id' => $penguji->id]);

        // Dosen data should still exist with flags reset
        $this->assertDatabaseHas('dosens', ['id' => $dosen->id, 'is_penguji' => false]);
    }

    public function test_admin_can_delete_other_admin_account(): void
    {
        $admin  = $this->adminUser();
        $admin2 = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
             ->delete(route('admin.user.destroy', $admin2))
             ->assertRedirect();

        // Admin2 should be deleted
        $this->assertDatabaseMissing('users', ['id' => $admin2->id]);
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $admin = $this->adminUser();

        $this->actingAs($admin)
             ->delete(route('admin.user.destroy', $admin))
             ->assertRedirect();

        // Admin should NOT be deleted (self-deletion blocked)
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }
}
