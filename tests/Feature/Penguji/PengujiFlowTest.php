<?php

namespace Tests\Feature\Penguji;

use App\Models\Dosen;
use App\Models\JadwalSeleksi;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Pelamar;
use App\Models\Penilaian;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test: Penguji — Dashboard, lihat jadwal, dan penilaian
 */
class PengujiFlowTest extends TestCase
{
    use RefreshDatabase;

    private function makePenguji(): array
    {
        $prodi = Prodi::factory()->create();
        $dosen = Dosen::factory()->create(['is_penguji' => true, 'prodi_id' => $prodi->id]);
        $user  = User::factory()->create([
            'role'       => 'penguji',
            'is_penguji' => true,
            'dosen_id'   => $dosen->id,
        ]);
        return [$user, $dosen, $prodi];
    }

    private function makeJadwal(int $dosenId, int $prodi_id): array
    {
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi_id]);
        $pUser    = User::factory()->create(['role' => 'pelamar']);
        $pelamar  = Pelamar::factory()->create(['user_id' => $pUser->id]);
        $lamaran  = Lamaran::factory()->create([
            'pelamar_id'  => $pelamar->id,
            'lowongan_id' => $lowongan->id,
            'status'      => 'seleksi_tahap2',
        ]);
        $jadwal = JadwalSeleksi::factory()->create([
            'penguji_id'   => $dosenId,
            'pelamar_id'   => $pelamar->id,
            'lowongan_id'  => $lowongan->id,
            'tipe_seleksi' => 'micro_teaching',
            'sesi'         => 1,
            'tanggal'      => now()->addDays(3)->format('Y-m-d'),
        ]);
        return [$jadwal, $pelamar, $lowongan];
    }

    // ── Dashboard ──────────────────────────────────────────────────────

    public function test_penguji_can_view_dashboard(): void
    {
        [$user] = $this->makePenguji();

        $this->actingAs($user)
             ->get(route('penguji.dashboard'))
             ->assertOk();
    }

    public function test_pelamar_cannot_access_penguji_dashboard(): void
    {
        $pelamar = User::factory()->create(['role' => 'pelamar']);

        $this->actingAs($pelamar)
             ->get(route('penguji.dashboard'))
             ->assertRedirect(route('pelamar.dashboard'));
    }

    // ── Pengujian list ─────────────────────────────────────────────────

    public function test_penguji_can_view_pengujian_list(): void
    {
        [$user, $dosen, $prodi] = $this->makePenguji();

        $this->actingAs($user)
             ->get(route('penguji.pengujian.index'))
             ->assertOk();
    }

    // ── Show jadwal ────────────────────────────────────────────────────

    public function test_penguji_can_view_own_jadwal(): void
    {
        [$user, $dosen, $prodi] = $this->makePenguji();
        [$jadwal] = $this->makeJadwal($dosen->id, $prodi->id);

        $this->actingAs($user)
             ->get(route('penguji.pengujian.show', $jadwal))
             ->assertOk();
    }

    public function test_penguji_cannot_view_other_penguji_jadwal(): void
    {
        [$user1, $dosen1, $prodi] = $this->makePenguji();
        [$user2, $dosen2]         = $this->makePenguji();
        [$jadwal]                  = $this->makeJadwal($dosen2->id, $prodi->id);

        $this->actingAs($user1)
             ->get(route('penguji.pengujian.show', $jadwal))
             ->assertStatus(403);
    }

    // ── Store nilai micro teaching ─────────────────────────────────────

    public function test_penguji_can_submit_micro_teaching_nilai(): void
    {
        [$user, $dosen, $prodi] = $this->makePenguji();
        [$jadwal] = $this->makeJadwal($dosen->id, $prodi->id);

        $payload = [
            'rekomendasi'      => 'direkomendasikan',
            'prodi_tujuan'     => 'Teknik Informatika',
            'kelompok_keahlian'=> 'scout',
            'bidang_keahlian'  => 'Artificial Intelligence',
            'catatan'          => 'Bagus',
            // k1: 2 items
            'k1_item_1' => 4, 'k1_item_2' => 4,
            // k2: 3 items
            'k2_item_1' => 5, 'k2_item_2' => 4, 'k2_item_3' => 4,
            // k3: 6 items
            'k3_item_1' => 4, 'k3_item_2' => 3, 'k3_item_3' => 4,
            'k3_item_4' => 4, 'k3_item_5' => 5, 'k3_item_6' => 4,
            // k4: 3 items
            'k4_item_1' => 4, 'k4_item_2' => 4, 'k4_item_3' => 4,
            // k5: 1 item
            'k5_item_1' => 5,
        ];

        $this->actingAs($user)
             ->post(route('penguji.pengujian.storeNilai', $jadwal), $payload)
             ->assertRedirect(route('penguji.pengujian.show', $jadwal->id));

        $this->assertDatabaseHas('penilaians', ['jadwal_seleksi_id' => $jadwal->id]);

        $penilaian = Penilaian::where('jadwal_seleksi_id', $jadwal->id)->first();
        $this->assertNotNull($penilaian);
        $this->assertGreaterThan(0, $penilaian->total_nilai);
        $this->assertLessThanOrEqual(5, $penilaian->total_nilai);
    }

    public function test_penguji_cannot_submit_nilai_twice(): void
    {
        [$user, $dosen, $prodi] = $this->makePenguji();
        [$jadwal] = $this->makeJadwal($dosen->id, $prodi->id);

        // First submission
        Penilaian::factory()->create(['jadwal_seleksi_id' => $jadwal->id]);

        $this->actingAs($user)
             ->post(route('penguji.pengujian.storeNilai', $jadwal), ['rekomendasi' => 'direkomendasikan'])
             ->assertRedirect(route('penguji.pengujian.show', $jadwal->id));

        // Still only 1 penilaian
        $this->assertEquals(1, Penilaian::where('jadwal_seleksi_id', $jadwal->id)->count());
    }

    public function test_penguji_cannot_submit_nilai_missing_required_fields(): void
    {
        [$user, $dosen, $prodi] = $this->makePenguji();
        [$jadwal] = $this->makeJadwal($dosen->id, $prodi->id);

        $this->actingAs($user)
             ->post(route('penguji.pengujian.storeNilai', $jadwal), [])
             ->assertSessionHasErrors();
    }
}
