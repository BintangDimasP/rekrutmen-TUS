<?php

namespace Tests\Feature\Pelamar;

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
 * Test: Pelamar — Riwayat lamaran detail (show), isolasi data,
 *              dan tampilan jadwal seleksi di history.
 */
class HistoryDetailTest extends TestCase
{
    use RefreshDatabase;

    private function makePelamar(): array
    {
        $user    = User::factory()->create(['role' => 'pelamar']);
        $pelamar = Pelamar::factory()->create(['user_id' => $user->id]);
        return [$user, $pelamar];
    }

    private function makeLamaran(int $pelamarId): array
    {
        $prodi    = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
        $lamaran  = Lamaran::factory()->create([
            'pelamar_id'  => $pelamarId,
            'lowongan_id' => $lowongan->id,
            'status'      => 'seleksi_tahap2',
        ]);
        return [$lamaran, $lowongan, $prodi];
    }

    // ══════════════════════════════════════════════════════════════
    // History show — akses normal
    // ══════════════════════════════════════════════════════════════

    public function test_pelamar_can_view_own_lamaran_detail(): void
    {
        [$user, $pelamar] = $this->makePelamar();
        [$lamaran]        = $this->makeLamaran($pelamar->id);

        $this->actingAs($user)
            ->get(route('pelamar.history.show', $lamaran))
            ->assertOk();
    }

    public function test_pelamar_cannot_view_other_pelamar_lamaran(): void
    {
        [$user1, $pelamar1] = $this->makePelamar();
        [$user2, $pelamar2] = $this->makePelamar();
        [$lamaran2]         = $this->makeLamaran($pelamar2->id);

        $this->actingAs($user1)
            ->get(route('pelamar.history.show', $lamaran2))
            ->assertStatus(403);
    }

    public function test_guest_cannot_view_history_detail(): void
    {
        [$user, $pelamar] = $this->makePelamar();
        [$lamaran]        = $this->makeLamaran($pelamar->id);

        $this->get(route('pelamar.history.show', $lamaran))
            ->assertRedirect('/login');
    }

    // ══════════════════════════════════════════════════════════════
    // History show — dengan jadwal seleksi
    // ══════════════════════════════════════════════════════════════

    public function test_history_show_displays_jadwal_seleksi(): void
    {
        [$user, $pelamar] = $this->makePelamar();
        [$lamaran, $lowongan] = $this->makeLamaran($pelamar->id);

        $prodi = Prodi::factory()->create();
        $dosen = Dosen::factory()->create(['is_penguji' => true, 'prodi_id' => $prodi->id]);

        JadwalSeleksi::factory()->create([
            'pelamar_id'   => $pelamar->id,
            'lowongan_id'  => $lowongan->id,
            'penguji_id'   => $dosen->id,
            'tipe_seleksi' => 'micro_teaching',
            'sesi'         => 1,
        ]);

        $this->actingAs($user)
            ->get(route('pelamar.history.show', $lamaran))
            ->assertOk();
    }

    // ══════════════════════════════════════════════════════════════
    // History index — pagination & filter
    // ══════════════════════════════════════════════════════════════

    public function test_history_index_shows_all_own_lamarans(): void
    {
        [$user, $pelamar] = $this->makePelamar();

        // Buat 3 lamaran
        for ($i = 0; $i < 3; $i++) {
            $this->makeLamaran($pelamar->id);
        }

        $this->actingAs($user)
            ->get(route('pelamar.history.index'))
            ->assertOk();
    }

    public function test_history_index_empty_for_new_pelamar(): void
    {
        [$user] = $this->makePelamar();

        $this->actingAs($user)
            ->get(route('pelamar.history.index'))
            ->assertOk();
    }

    // ══════════════════════════════════════════════════════════════
    // Lowongan show — detail sebelum apply
    // ══════════════════════════════════════════════════════════════

    public function test_pelamar_can_view_lowongan_show_with_existing_lamaran(): void
    {
        [$user, $pelamar] = $this->makePelamar();
        $prodi    = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id, 'status' => 'aktif']);

        // Sudah pernah melamar
        Lamaran::factory()->create([
            'pelamar_id'  => $pelamar->id,
            'lowongan_id' => $lowongan->id,
        ]);

        $this->actingAs($user)
            ->get(route('pelamar.lowongan.show', $lowongan))
            ->assertOk();
    }

    public function test_pelamar_apply_page_redirects_if_already_applied(): void
    {
        [$user, $pelamar] = $this->makePelamar();
        $prodi    = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id, 'status' => 'aktif']);

        Lamaran::factory()->create([
            'pelamar_id'  => $pelamar->id,
            'lowongan_id' => $lowongan->id,
        ]);

        $this->actingAs($user)
            ->get(route('pelamar.lowongan.apply', $lowongan))
            ->assertRedirect(route('pelamar.history.index'));
    }

    public function test_pelamar_apply_page_redirects_if_lowongan_full(): void
    {
        [$user, $pelamar] = $this->makePelamar();
        $prodi    = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id, 'kuota' => 1]);

        // Isi kuota
        $otherUser    = User::factory()->create(['role' => 'pelamar']);
        $otherPelamar = Pelamar::factory()->create(['user_id' => $otherUser->id]);
        Lamaran::factory()->create([
            'pelamar_id'  => $otherPelamar->id,
            'lowongan_id' => $lowongan->id,
            'status'      => 'menunggu',
        ]);

        $this->actingAs($user)
            ->get(route('pelamar.lowongan.apply', $lowongan))
            ->assertRedirect();
    }

    // ══════════════════════════════════════════════════════════════
    // Toggle save lowongan
    // ══════════════════════════════════════════════════════════════

    public function test_pelamar_can_toggle_save_lowongan(): void
    {
        [$user, $pelamar] = $this->makePelamar();
        $prodi    = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);

        $response = $this->actingAs($user)
            ->postJson(route('pelamar.lowongan.save', $lowongan));

        $response->assertOk()
            ->assertJsonStructure(['saved']);
    }

    public function test_toggle_save_returns_true_when_saved(): void
    {
        [$user, $pelamar] = $this->makePelamar();
        $prodi    = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);

        // Toggle pertama → saved = true
        $response = $this->actingAs($user)
            ->postJson(route('pelamar.lowongan.save', $lowongan));

        $response->assertJson(['saved' => true]);
    }

    public function test_toggle_save_returns_false_when_unsaved(): void
    {
        [$user, $pelamar] = $this->makePelamar();
        $prodi    = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);

        // Toggle pertama → saved
        $this->actingAs($user)->postJson(route('pelamar.lowongan.save', $lowongan));

        // Toggle kedua → unsaved
        $response = $this->actingAs($user)
            ->postJson(route('pelamar.lowongan.save', $lowongan));

        $response->assertJson(['saved' => false]);
    }
}
