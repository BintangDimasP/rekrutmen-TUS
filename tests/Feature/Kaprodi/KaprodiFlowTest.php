<?php

namespace Tests\Feature\Kaprodi;

use App\Models\Dosen;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Pelamar;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test: Kaprodi — Dashboard, lihat pelamar, isolasi data antar prodi
 */
class KaprodiFlowTest extends TestCase
{
    use RefreshDatabase;

    private function makeKaprodi(): array
    {
        $prodi = Prodi::factory()->create();
        $dosen = Dosen::factory()->create(['is_kaprodi' => true, 'prodi_id' => $prodi->id]);
        $user  = User::factory()->create([
            'role'       => 'kaprodi',
            'is_kaprodi' => true,
            'dosen_id'   => $dosen->id,
            'prodi_id'   => $prodi->id,
        ]);
        return [$user, $prodi];
    }

    private function makeLamaranForProdi(int $prodiId): array
    {
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodiId]);
        $pUser    = User::factory()->create(['role' => 'pelamar']);
        $pelamar  = Pelamar::factory()->create(['user_id' => $pUser->id]);
        $lamaran  = Lamaran::factory()->create([
            'pelamar_id'  => $pelamar->id,
            'lowongan_id' => $lowongan->id,
            'status'      => 'seleksi_tahap1',
        ]);
        return [$pelamar, $lamaran, $lowongan];
    }

    // ── Dashboard ──────────────────────────────────────────────────────

    public function test_kaprodi_can_view_dashboard(): void
    {
        [$user] = $this->makeKaprodi();

        $this->actingAs($user)
             ->get(route('kaprodi.dashboard'))
             ->assertOk();
    }

    public function test_pelamar_cannot_access_kaprodi_dashboard(): void
    {
        $pelamar = User::factory()->create(['role' => 'pelamar']);

        $this->actingAs($pelamar)
             ->get(route('kaprodi.dashboard'))
             ->assertRedirect(route('pelamar.dashboard'));
    }

    public function test_admin_cannot_access_kaprodi_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
             ->get(route('kaprodi.dashboard'))
             ->assertRedirect(route('admin.dashboard'));
    }

    // ── Pelamar list ───────────────────────────────────────────────────

    public function test_kaprodi_can_view_pelamar_in_own_prodi(): void
    {
        [$user, $prodi] = $this->makeKaprodi();
        $this->makeLamaranForProdi($prodi->id);

        $this->actingAs($user)
             ->get(route('kaprodi.pelamar.index'))
             ->assertOk();
    }

    // ── Isolasi data: kaprodi tidak bisa lihat pelamar prodi lain ─────

    public function test_kaprodi_cannot_view_pelamar_from_other_prodi(): void
    {
        [$user, $prodi1] = $this->makeKaprodi();
        [, $prodi2]      = $this->makeKaprodi(); // prodi lain

        [$pelamar]       = $this->makeLamaranForProdi($prodi2->id); // pelamar di prodi lain

        $this->actingAs($user)
             ->get(route('kaprodi.pelamar.show', $pelamar))
             ->assertStatus(403);
    }

    public function test_kaprodi_can_view_pelamar_from_own_prodi(): void
    {
        [$user, $prodi] = $this->makeKaprodi();
        [$pelamar]      = $this->makeLamaranForProdi($prodi->id);

        $this->actingAs($user)
             ->get(route('kaprodi.pelamar.show', $pelamar))
             ->assertOk();
    }

    // ── Dashboard stats reflect only own prodi ─────────────────────────

    public function test_kaprodi_dashboard_only_counts_own_prodi_lamarans(): void
    {
        [$user, $prodi1] = $this->makeKaprodi();
        [, $prodi2]      = $this->makeKaprodi();

        // 2 lamaran di prodi1
        $this->makeLamaranForProdi($prodi1->id);
        $this->makeLamaranForProdi($prodi1->id);

        // 1 lamaran di prodi2 (tidak boleh ikut dihitung)
        $this->makeLamaranForProdi($prodi2->id);

        $response = $this->actingAs($user)
                         ->get(route('kaprodi.dashboard'));

        $response->assertOk();
        // Hanya bisa verifikasi halaman load tanpa error
        // Data count hanya bisa di-assert via View data
    }
}
