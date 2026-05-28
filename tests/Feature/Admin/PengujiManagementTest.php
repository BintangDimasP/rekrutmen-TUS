<?php

namespace Tests\Feature\Admin;

use App\Models\Dosen;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test: Admin — Penunjukan & pencabutan penguji
 */
class PengujiManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    // ── Index ─────────────────────────────────────────────────────────

    public function test_admin_can_view_penguji_list(): void
    {
        $this->actingAs($this->admin())
             ->get(route('admin.penguji.index'))
             ->assertOk();
    }

    public function test_non_admin_cannot_view_penguji_list(): void
    {
        $pelamar = User::factory()->create(['role' => 'pelamar']);

        $this->actingAs($pelamar)
             ->get(route('admin.penguji.index'))
             ->assertRedirect(route('pelamar.dashboard'));
    }

    // ── Store: tunjuk penguji ──────────────────────────────────────────

    public function test_admin_can_appoint_penguji(): void
    {
        $admin = $this->admin();
        $dosen = Dosen::factory()->create(['is_penguji' => false]);

        $this->actingAs($admin)
             ->post(route('admin.penguji.store'), ['dosen_ids' => [$dosen->id]])
             ->assertRedirect();

        // Dosen sekarang is_penguji
        $this->assertDatabaseHas('dosens', ['id' => $dosen->id, 'is_penguji' => true]);

        // User account ter-buat
        $this->assertDatabaseHas('users', ['dosen_id' => $dosen->id, 'role' => 'penguji']);
    }

    public function test_appointing_kaprodi_as_penguji_makes_rangkap(): void
    {
        $admin = $this->admin();
        $prodi = Prodi::factory()->create();
        $dosen = Dosen::factory()->create(['is_kaprodi' => true, 'prodi_id' => $prodi->id]);

        // Create existing kaprodi user
        $kapUser = User::factory()->create([
            'role'       => 'kaprodi',
            'is_kaprodi' => true,
            'is_penguji' => false,
            'dosen_id'   => $dosen->id,
            'prodi_id'   => $prodi->id,
        ]);

        $this->actingAs($admin)
             ->post(route('admin.penguji.store'), ['dosen_ids' => [$dosen->id]])
             ->assertRedirect();

        // User lama harus punya is_penguji = true, role tetap kaprodi
        $kapUser->refresh();
        $this->assertTrue((bool) $kapUser->is_penguji);
        $this->assertEquals('kaprodi', $kapUser->role); // role tidak berubah
    }

    public function test_store_penguji_requires_dosen_ids(): void
    {
        $this->actingAs($this->admin())
             ->post(route('admin.penguji.store'), [])
             ->assertSessionHasErrors('dosen_ids');
    }

    // ── Destroy: cabut penguji ─────────────────────────────────────────

    public function test_admin_can_revoke_penguji(): void
    {
        $admin = $this->admin();
        $dosen = Dosen::factory()->create(['is_penguji' => true]);
        User::factory()->create([
            'role'       => 'penguji',
            'is_penguji' => true,
            'dosen_id'   => $dosen->id,
        ]);

        $this->actingAs($admin)
             ->delete(route('admin.penguji.destroy', $dosen))
             ->assertRedirect(route('admin.penguji.index'));

        $this->assertDatabaseHas('dosens', ['id' => $dosen->id, 'is_penguji' => false]);
        // User account dihapus
        $this->assertDatabaseMissing('users', ['dosen_id' => $dosen->id]);
    }

    public function test_revoking_rangkap_penguji_keeps_kaprodi_role(): void
    {
        $admin = $this->admin();
        $prodi = Prodi::factory()->create();
        $dosen = Dosen::factory()->create(['is_penguji' => true, 'is_kaprodi' => true, 'prodi_id' => $prodi->id]);
        $user  = User::factory()->create([
            'role'       => 'kaprodi',
            'is_penguji' => true,
            'is_kaprodi' => true,
            'dosen_id'   => $dosen->id,
            'prodi_id'   => $prodi->id,
        ]);

        $this->actingAs($admin)
             ->delete(route('admin.penguji.destroy', $dosen))
             ->assertRedirect(route('admin.penguji.index'));

        $user->refresh();
        // User tetap ada karena masih kaprodi
        $this->assertDatabaseHas('users', ['id' => $user->id]);
        $this->assertEquals('kaprodi', $user->role);
        $this->assertFalse((bool) $user->is_penguji);
    }
}
