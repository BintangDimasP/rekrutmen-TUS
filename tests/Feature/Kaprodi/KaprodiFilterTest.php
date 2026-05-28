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
 * Test: Kaprodi — Filter pelamar (AJAX), search, dan isolasi data prodi.
 */
class KaprodiFilterTest extends TestCase
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

    private function makeLamaranForProdi(int $prodiId, string $status = 'seleksi_tahap1'): array
    {
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodiId]);
        $pUser    = User::factory()->create(['role' => 'pelamar']);
        $pelamar  = Pelamar::factory()->create(['user_id' => $pUser->id, 'nama' => 'Pelamar ' . uniqid()]);
        $lamaran  = Lamaran::factory()->create([
            'pelamar_id'  => $pelamar->id,
            'lowongan_id' => $lowongan->id,
            'status'      => $status,
        ]);
        return [$pelamar, $lamaran, $lowongan];
    }

    // ══════════════════════════════════════════════════════════════
    // Filter AJAX
    // ══════════════════════════════════════════════════════════════

    public function test_kaprodi_filter_returns_json(): void
    {
        [$user, $prodi] = $this->makeKaprodi();
        $this->makeLamaranForProdi($prodi->id);

        $response = $this->actingAs($user)
            ->getJson(route('kaprodi.pelamar.filter'));

        $response->assertOk()
            ->assertJsonStructure(['lamarans']);
    }

    public function test_kaprodi_filter_by_status(): void
    {
        [$user, $prodi] = $this->makeKaprodi();
        $this->makeLamaranForProdi($prodi->id, 'diterima');
        $this->makeLamaranForProdi($prodi->id, 'ditolak');

        $response = $this->actingAs($user)
            ->getJson(route('kaprodi.pelamar.filter') . '?status=diterima');

        $response->assertOk();
        $lamarans = $response->json('lamarans');
        $this->assertCount(1, $lamarans);
        $this->assertEquals('diterima', $lamarans[0]['status']);
    }

    public function test_kaprodi_filter_by_lowongan(): void
    {
        [$user, $prodi] = $this->makeKaprodi();
        [, $lamaran1, $lowongan1] = $this->makeLamaranForProdi($prodi->id);
        $this->makeLamaranForProdi($prodi->id);

        $response = $this->actingAs($user)
            ->getJson(route('kaprodi.pelamar.filter') . '?lowongan_id=' . $lowongan1->id);

        $response->assertOk();
        $lamarans = $response->json('lamarans');
        $this->assertCount(1, $lamarans);
        $this->assertEquals($lowongan1->id, $lamarans[0]['lowongan_id']);
    }

    public function test_kaprodi_filter_only_returns_own_prodi_data(): void
    {
        [$user, $prodi1] = $this->makeKaprodi();
        [, $prodi2]      = $this->makeKaprodi();

        // 2 lamaran di prodi1
        $this->makeLamaranForProdi($prodi1->id);
        $this->makeLamaranForProdi($prodi1->id);

        // 1 lamaran di prodi2 (tidak boleh muncul)
        $this->makeLamaranForProdi($prodi2->id);

        $response = $this->actingAs($user)
            ->getJson(route('kaprodi.pelamar.filter'));

        $response->assertOk();
        $this->assertCount(2, $response->json('lamarans'));
    }

    public function test_non_kaprodi_cannot_access_filter(): void
    {
        $pelamar = User::factory()->create(['role' => 'pelamar']);

        $this->actingAs($pelamar)
            ->getJson(route('kaprodi.pelamar.filter'))
            ->assertRedirect(route('pelamar.dashboard'));
    }

    // ══════════════════════════════════════════════════════════════
    // Pelamar list (paginated)
    // ══════════════════════════════════════════════════════════════

    public function test_kaprodi_pelamar_list_loads_with_data(): void
    {
        [$user, $prodi] = $this->makeKaprodi();
        $this->makeLamaranForProdi($prodi->id);
        $this->makeLamaranForProdi($prodi->id);

        $this->actingAs($user)
            ->get(route('kaprodi.pelamar.index'))
            ->assertOk();
    }

    public function test_kaprodi_pelamar_list_search_by_nama(): void
    {
        [$user, $prodi] = $this->makeKaprodi();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
        $pUser    = User::factory()->create(['role' => 'pelamar']);
        $pelamar  = Pelamar::factory()->create(['user_id' => $pUser->id, 'nama' => 'Unik Sekali']);
        Lamaran::factory()->create(['pelamar_id' => $pelamar->id, 'lowongan_id' => $lowongan->id]);

        $this->actingAs($user)
            ->get(route('kaprodi.pelamar.index') . '?search=Unik+Sekali')
            ->assertOk();
    }

    // ══════════════════════════════════════════════════════════════
    // Role switch — kaprodi + penguji rangkap
    // ══════════════════════════════════════════════════════════════

    public function test_rangkap_dosen_can_switch_from_kaprodi_to_penguji(): void
    {
        $prodi = Prodi::factory()->create();
        $dosen = Dosen::factory()->create([
            'is_kaprodi' => true,
            'is_penguji' => true,
            'prodi_id'   => $prodi->id,
        ]);
        $user = User::factory()->create([
            'role'       => 'kaprodi',
            'is_kaprodi' => true,
            'is_penguji' => true,
            'dosen_id'   => $dosen->id,
            'prodi_id'   => $prodi->id,
        ]);

        $this->actingAs($user)
            ->post(route('role.switch'), ['role' => 'penguji'])
            ->assertRedirect(route('penguji.dashboard'));

        $this->assertEquals('penguji', $user->fresh()->role);
    }

    public function test_rangkap_dosen_can_switch_from_penguji_to_kaprodi(): void
    {
        $prodi = Prodi::factory()->create();
        $dosen = Dosen::factory()->create([
            'is_kaprodi' => true,
            'is_penguji' => true,
            'prodi_id'   => $prodi->id,
        ]);
        $user = User::factory()->create([
            'role'       => 'penguji',
            'is_kaprodi' => true,
            'is_penguji' => true,
            'dosen_id'   => $dosen->id,
            'prodi_id'   => $prodi->id,
        ]);

        $this->actingAs($user)
            ->post(route('role.switch'), ['role' => 'kaprodi'])
            ->assertRedirect(route('kaprodi.dashboard'));

        $this->assertEquals('kaprodi', $user->fresh()->role);
    }
}
