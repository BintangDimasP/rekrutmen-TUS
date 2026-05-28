<?php

namespace Tests\Feature\Admin;

use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Pelamar;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test: Admin — Manajemen Lamaran (lihat, update status, hapus)
 */
class LamaranManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function makeLamaran(): array
    {
        $prodi    = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
        $user     = User::factory()->create(['role' => 'pelamar']);
        $pelamar  = Pelamar::factory()->create(['user_id' => $user->id]);
        $lamaran  = Lamaran::factory()->create([
            'pelamar_id'  => $pelamar->id,
            'lowongan_id' => $lowongan->id,
            'status'      => 'menunggu',
        ]);
        return [$lamaran, $pelamar, $lowongan, $prodi];
    }

    // ── Index ──────────────────────────────────────────────────────────

    public function test_admin_can_view_lamaran_list(): void
    {
        [$lamaran, , $lowongan] = $this->makeLamaran();

        $this->actingAs($this->admin())
             ->get(route('admin.lamaran.index', $lowongan))
             ->assertOk();
    }

    // ── Show ───────────────────────────────────────────────────────────

    public function test_admin_can_view_lamaran_detail(): void
    {
        [$lamaran] = $this->makeLamaran();

        $this->actingAs($this->admin())
             ->get(route('admin.lamaran.show', $lamaran))
             ->assertOk();
    }

    // ── Update status ──────────────────────────────────────────────────

    public function test_admin_can_update_lamaran_status_to_seleksi_tahap1(): void
    {
        [$lamaran] = $this->makeLamaran();

        $this->actingAs($this->admin())
             ->put(route('admin.lamaran.update', $lamaran), ['status' => 'seleksi_tahap1'])
             ->assertRedirect();

        $this->assertDatabaseHas('lamarans', ['id' => $lamaran->id, 'status' => 'seleksi_tahap1']);
    }

    public function test_admin_can_update_lamaran_status_to_diterima(): void
    {
        [$lamaran] = $this->makeLamaran();

        $this->actingAs($this->admin())
             ->put(route('admin.lamaran.update', $lamaran), ['status' => 'diterima'])
             ->assertRedirect();

        $this->assertDatabaseHas('lamarans', ['id' => $lamaran->id, 'status' => 'diterima']);
    }

    public function test_admin_can_update_lamaran_status_to_ditolak(): void
    {
        [$lamaran] = $this->makeLamaran();

        $this->actingAs($this->admin())
             ->put(route('admin.lamaran.update', $lamaran), ['status' => 'ditolak'])
             ->assertRedirect();

        $this->assertDatabaseHas('lamarans', ['id' => $lamaran->id, 'status' => 'ditolak']);
    }

    public function test_admin_cannot_set_invalid_lamaran_status(): void
    {
        [$lamaran] = $this->makeLamaran();

        $this->actingAs($this->admin())
             ->put(route('admin.lamaran.update', $lamaran), ['status' => 'status_tidak_valid'])
             ->assertSessionHasErrors('status');
    }

    // ── Destroy ────────────────────────────────────────────────────────

    public function test_admin_can_delete_lamaran(): void
    {
        [$lamaran] = $this->makeLamaran();

        $this->actingAs($this->admin())
             ->delete(route('admin.lamaran.destroy', $lamaran))
             ->assertRedirect();

        $this->assertDatabaseMissing('lamarans', ['id' => $lamaran->id]);
    }

    // ── Access control ─────────────────────────────────────────────────

    public function test_pelamar_cannot_manage_lamarans(): void
    {
        [$lamaran] = $this->makeLamaran();
        $pelamar = User::factory()->create(['role' => 'pelamar']);

        $this->actingAs($pelamar)
             ->put(route('admin.lamaran.update', $lamaran), ['status' => 'diterima'])
             ->assertRedirect(route('pelamar.dashboard'));
    }

    public function test_penguji_cannot_manage_lamarans(): void
    {
        [$lamaran] = $this->makeLamaran();
        $penguji = User::factory()->create(['role' => 'penguji']);

        $this->actingAs($penguji)
             ->get(route('admin.lamaran.show', $lamaran))
             ->assertRedirect(route('penguji.dashboard'));
    }
}
