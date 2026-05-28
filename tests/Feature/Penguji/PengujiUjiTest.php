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
 * Test: Penguji — Halaman uji (form penilaian), wawancara,
 *              blokir wawancara sebelum micro selesai, dan submit wawancara.
 */
class PengujiUjiTest extends TestCase
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

    private function makePelamar(): array
    {
        $pUser   = User::factory()->create(['role' => 'pelamar']);
        $pelamar = Pelamar::factory()->create(['user_id' => $pUser->id]);
        return [$pelamar, $pUser];
    }

    // ══════════════════════════════════════════════════════════════
    // Halaman uji micro teaching
    // ══════════════════════════════════════════════════════════════

    public function test_penguji_can_access_uji_micro_page(): void
    {
        [$user, $dosen, $prodi] = $this->makePenguji();
        [$pelamar]              = $this->makePelamar();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);

        $jadwal = JadwalSeleksi::factory()->create([
            'penguji_id'   => $dosen->id,
            'pelamar_id'   => $pelamar->id,
            'lowongan_id'  => $lowongan->id,
            'tipe_seleksi' => 'micro_teaching',
        ]);

        $this->actingAs($user)
            ->get(route('penguji.pengujian.uji', $jadwal))
            ->assertOk();
    }

    public function test_penguji_cannot_access_uji_if_already_scored(): void
    {
        [$user, $dosen, $prodi] = $this->makePenguji();
        [$pelamar]              = $this->makePelamar();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);

        $jadwal = JadwalSeleksi::factory()->create([
            'penguji_id'   => $dosen->id,
            'pelamar_id'   => $pelamar->id,
            'lowongan_id'  => $lowongan->id,
            'tipe_seleksi' => 'micro_teaching',
        ]);

        // Sudah ada penilaian
        Penilaian::factory()->create(['jadwal_seleksi_id' => $jadwal->id]);

        $this->actingAs($user)
            ->get(route('penguji.pengujian.uji', $jadwal))
            ->assertRedirect(route('penguji.pengujian.show', $jadwal->id));
    }

    public function test_penguji_cannot_access_other_penguji_uji_page(): void
    {
        [$user1, $dosen1, $prodi] = $this->makePenguji();
        [$user2, $dosen2]         = $this->makePenguji();
        [$pelamar]                = $this->makePelamar();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);

        $jadwal = JadwalSeleksi::factory()->create([
            'penguji_id'   => $dosen2->id,
            'pelamar_id'   => $pelamar->id,
            'lowongan_id'  => $lowongan->id,
            'tipe_seleksi' => 'micro_teaching',
        ]);

        $this->actingAs($user1)
            ->get(route('penguji.pengujian.uji', $jadwal))
            ->assertStatus(403);
    }

    // ══════════════════════════════════════════════════════════════
    // Wawancara diblokir jika micro belum selesai
    // ══════════════════════════════════════════════════════════════

    public function test_wawancara_blocked_if_micro_not_fully_scored(): void
    {
        [$user, $dosen, $prodi] = $this->makePenguji();
        [$pelamar]              = $this->makePelamar();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);

        // Jadwal micro tanpa penilaian
        JadwalSeleksi::factory()->create([
            'penguji_id'   => $dosen->id,
            'pelamar_id'   => $pelamar->id,
            'lowongan_id'  => $lowongan->id,
            'tipe_seleksi' => 'micro_teaching',
        ]);

        // Jadwal wawancara
        $wawancara = JadwalSeleksi::factory()->create([
            'penguji_id'   => $dosen->id,
            'pelamar_id'   => $pelamar->id,
            'lowongan_id'  => $lowongan->id,
            'tipe_seleksi' => 'wawancara',
        ]);

        $this->actingAs($user)
            ->get(route('penguji.pengujian.uji', $wawancara))
            ->assertStatus(403);
    }

    public function test_wawancara_accessible_if_all_micro_scored(): void
    {
        [$user, $dosen, $prodi] = $this->makePenguji();
        [$pelamar]              = $this->makePelamar();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);

        // Jadwal micro dengan penilaian sudah ada
        $micro = JadwalSeleksi::factory()->create([
            'penguji_id'   => $dosen->id,
            'pelamar_id'   => $pelamar->id,
            'lowongan_id'  => $lowongan->id,
            'tipe_seleksi' => 'micro_teaching',
        ]);
        Penilaian::factory()->create(['jadwal_seleksi_id' => $micro->id]);

        // Jadwal wawancara
        $wawancara = JadwalSeleksi::factory()->create([
            'penguji_id'   => $dosen->id,
            'pelamar_id'   => $pelamar->id,
            'lowongan_id'  => $lowongan->id,
            'tipe_seleksi' => 'wawancara',
        ]);

        $this->actingAs($user)
            ->get(route('penguji.pengujian.uji', $wawancara))
            ->assertOk();
    }

    // ══════════════════════════════════════════════════════════════
    // Submit nilai wawancara
    // ══════════════════════════════════════════════════════════════

    public function test_penguji_can_submit_wawancara_nilai(): void
    {
        [$user, $dosen, $prodi] = $this->makePenguji();
        [$pelamar]              = $this->makePelamar();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);

        // Micro sudah dinilai
        $micro = JadwalSeleksi::factory()->create([
            'penguji_id'   => $dosen->id,
            'pelamar_id'   => $pelamar->id,
            'lowongan_id'  => $lowongan->id,
            'tipe_seleksi' => 'micro_teaching',
        ]);
        Penilaian::factory()->create(['jadwal_seleksi_id' => $micro->id]);

        $wawancara = JadwalSeleksi::factory()->create([
            'penguji_id'   => $dosen->id,
            'pelamar_id'   => $pelamar->id,
            'lowongan_id'  => $lowongan->id,
            'tipe_seleksi' => 'wawancara',
        ]);

        // 8 item wawancara (k1_item_1 s/d k1_item_8)
        $payload = [
            'rekomendasi'      => 'direkomendasikan',
            'prodi_tujuan'     => 'Teknik Informatika',
            'status_rekrutmen' => 'on_going',
            'catatan'          => 'Kandidat sangat baik',
        ];
        for ($i = 1; $i <= 8; $i++) {
            $payload["k1_item_{$i}"] = 4;
        }

        $this->actingAs($user)
            ->post(route('penguji.pengujian.storeNilai', $wawancara), $payload)
            ->assertRedirect(route('penguji.pengujian.show', $wawancara->id));

        $this->assertDatabaseHas('penilaians', ['jadwal_seleksi_id' => $wawancara->id]);

        $penilaian = Penilaian::where('jadwal_seleksi_id', $wawancara->id)->first();
        $this->assertEquals(4.0, $penilaian->total_nilai);
        $this->assertEquals('direkomendasikan', $penilaian->rekomendasi);
    }

    public function test_wawancara_nilai_requires_rekomendasi(): void
    {
        [$user, $dosen, $prodi] = $this->makePenguji();
        [$pelamar]              = $this->makePelamar();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);

        $micro = JadwalSeleksi::factory()->create([
            'penguji_id'   => $dosen->id,
            'pelamar_id'   => $pelamar->id,
            'lowongan_id'  => $lowongan->id,
            'tipe_seleksi' => 'micro_teaching',
        ]);
        Penilaian::factory()->create(['jadwal_seleksi_id' => $micro->id]);

        $wawancara = JadwalSeleksi::factory()->create([
            'penguji_id'   => $dosen->id,
            'pelamar_id'   => $pelamar->id,
            'lowongan_id'  => $lowongan->id,
            'tipe_seleksi' => 'wawancara',
        ]);

        $this->actingAs($user)
            ->post(route('penguji.pengujian.storeNilai', $wawancara), [
                'prodi_tujuan' => 'TI',
                // rekomendasi missing
            ])
            ->assertSessionHasErrors('rekomendasi');
    }

    // ══════════════════════════════════════════════════════════════
    // Penguji tanpa dosen_id → 403
    // ══════════════════════════════════════════════════════════════

    public function test_penguji_without_dosen_record_gets_403_on_dashboard(): void
    {
        // User penguji tapi tidak punya dosen_id
        $user = User::factory()->create(['role' => 'penguji', 'dosen_id' => null]);

        $this->actingAs($user)
            ->get(route('penguji.dashboard'))
            ->assertStatus(403);
    }
}
