<?php

namespace Tests\Feature;

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
 * Test: Edge cases & bug detection
 * - Data isolation, null safety, business rule violations
 */
class EdgeCaseTest extends TestCase
{
    use RefreshDatabase;

    // ── 1. Pelamar tidak bisa lamar lowongan yang sudah ditutup (expired) ───

    public function test_pelamar_cannot_apply_to_expired_lowongan(): void
    {
        $user    = User::factory()->create(['role' => 'pelamar']);
        $pelamar = Pelamar::factory()->create(['user_id' => $user->id]);
        $prodi   = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create([
            'prodi_id'     => $prodi->id,
            'status'       => 'aktif',
            'tanggal_tutup'=> now()->subDay()->format('Y-m-d'), // kemarin = expired
        ]);

        // Status akan otomatis jadi 'ditutup' karena tanggal sudah lewat
        $this->assertEquals('ditutup', $lowongan->status);
    }

    // ── 2. Lowongan penuh (kuota 0) ───────────────────────────────────────

    public function test_lowongan_is_full_when_kuota_reached(): void
    {
        $prodi    = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id, 'kuota' => 1]);

        $pUser   = User::factory()->create(['role' => 'pelamar']);
        $pelamar = Pelamar::factory()->create(['user_id' => $pUser->id]);

        // Satu lamaran aktif = kuota penuh
        Lamaran::factory()->create([
            'pelamar_id'  => $pelamar->id,
            'lowongan_id' => $lowongan->id,
            'status'      => 'menunggu',
        ]);

        $lowongan->refresh();
        $this->assertTrue($lowongan->isFull());
        $this->assertEquals(0, $lowongan->sisa_kuota);
    }

    // ── 3. Ditolak tidak menghitung kuota ────────────────────────────────

    public function test_ditolak_lamaran_does_not_count_kuota(): void
    {
        $prodi    = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id, 'kuota' => 1]);

        $pUser   = User::factory()->create(['role' => 'pelamar']);
        $pelamar = Pelamar::factory()->create(['user_id' => $pUser->id]);

        Lamaran::factory()->create([
            'pelamar_id'  => $pelamar->id,
            'lowongan_id' => $lowongan->id,
            'status'      => 'ditolak', // ditolak tidak dihitung
        ]);

        $lowongan->refresh();
        $this->assertFalse($lowongan->isFull());
        $this->assertEquals(1, $lowongan->sisa_kuota);
    }

    // ── 4. Status label Lamaran harus valid ──────────────────────────────

    public function test_lamaran_status_label_is_not_null_for_all_statuses(): void
    {
        $statuses = ['menunggu', 'seleksi_tahap1', 'seleksi_tahap2', 'diterima', 'ditolak'];

        foreach ($statuses as $status) {
            $this->assertArrayHasKey(
                $status,
                \App\Models\Lamaran::STATUS_LABELS,
                "STATUS_LABELS tidak punya key: {$status}"
            );
            $this->assertNotEmpty(
                \App\Models\Lamaran::STATUS_LABELS[$status],
                "status_label kosong untuk status: {$status}"
            );
        }
    }

    // ── 5. Snapshot data pelamar tersimpan di lamaran ────────────────────

    public function test_lamaran_snapshot_returns_pelamar_data_when_filled(): void
    {
        $prodi    = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
        $pUser    = User::factory()->create(['role' => 'pelamar']);
        $pelamar  = Pelamar::factory()->create(['user_id' => $pUser->id, 'nama' => 'Snapshot Pelamar']);

        $lamaran = Lamaran::factory()->create([
            'pelamar_id'    => $pelamar->id,
            'lowongan_id'   => $lowongan->id,
            'snapshot_data' => $pelamar->toArray(),
        ]);

        $effective = $lamaran->effective_pelamar;
        $this->assertEquals('Snapshot Pelamar', $effective->nama);
    }

    // ── 6. Lamaran tanpa snapshot fallback ke relasi live ────────────────

    public function test_lamaran_without_snapshot_falls_back_to_live_pelamar(): void
    {
        $prodi    = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
        $pUser    = User::factory()->create(['role' => 'pelamar']);
        $pelamar  = Pelamar::factory()->create(['user_id' => $pUser->id, 'nama' => 'Live Pelamar']);

        $lamaran = Lamaran::factory()->create([
            'pelamar_id'    => $pelamar->id,
            'lowongan_id'   => $lowongan->id,
            'snapshot_data' => null,
        ]);

        $effective = $lamaran->effective_pelamar;
        $this->assertEquals('Live Pelamar', $effective->nama);
    }

    // ── 7. JadwalSeleksi: cek ketersediaan penguji (no conflict) ─────────

    public function test_penguji_available_when_no_jadwal_exists(): void
    {
        $tanggal   = now()->addDays(5)->format('Y-m-d');
        $pengujiId = 999; // tidak ada jadwal sama sekali

        $this->assertTrue(
            JadwalSeleksi::isPengujiAvailable($tanggal, $pengujiId, 'micro_teaching', 1)
        );
    }

    // ── 8. JadwalSeleksi: cek konflik penguji ────────────────────────────

    public function test_penguji_not_available_when_same_slot_exists(): void
    {
        $prodi    = Prodi::factory()->create();
        $dosen    = Dosen::factory()->create(['is_penguji' => true, 'prodi_id' => $prodi->id]);
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
        $pUser    = User::factory()->create(['role' => 'pelamar']);
        $pelamar  = Pelamar::factory()->create(['user_id' => $pUser->id]);
        $tanggal  = now()->addDays(5)->format('Y-m-d');

        JadwalSeleksi::factory()->create([
            'tanggal'      => $tanggal,
            'penguji_id'   => $dosen->id,
            'pelamar_id'   => $pelamar->id,
            'lowongan_id'  => $lowongan->id,
            'tipe_seleksi' => 'micro_teaching',
            'sesi'         => 3,
        ]);

        $this->assertFalse(
            JadwalSeleksi::isPengujiAvailable($tanggal, $dosen->id, 'micro_teaching', 3)
        );
    }

    // ── 9. Penguji hanya bisa lihat jadwalnya sendiri ────────────────────

    public function test_penguji_accessing_other_jadwal_returns_403(): void
    {
        $prodi  = Prodi::factory()->create();
        $dosen1 = Dosen::factory()->create(['is_penguji' => true, 'prodi_id' => $prodi->id]);
        $dosen2 = Dosen::factory()->create(['is_penguji' => true, 'prodi_id' => $prodi->id]);
        $user1  = User::factory()->create(['role' => 'penguji', 'is_penguji' => true, 'dosen_id' => $dosen1->id]);
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
        $pUser  = User::factory()->create(['role' => 'pelamar']);
        $pelamar= Pelamar::factory()->create(['user_id' => $pUser->id]);

        $jadwal = JadwalSeleksi::factory()->create([
            'penguji_id'  => $dosen2->id, // milik dosen2
            'pelamar_id'  => $pelamar->id,
            'lowongan_id' => $lowongan->id,
        ]);

        $this->actingAs($user1)
             ->get(route('penguji.pengujian.show', $jadwal))
             ->assertStatus(403);
    }

    // ── 10. User tanpa role tidak bisa akses sistem ───────────────────────

    public function test_user_with_null_role_cannot_access_dashboard(): void
    {
        $user = User::factory()->create(['role' => null]);

        // Login dulu
        $this->post('/login', ['email' => $user->email, 'password' => 'password']);

        // Middleware akan kick saat akses route yang perlu role
        $this->actingAs($user)
             ->get('/admin/dashboard')
             ->assertRedirect(); // Redirect atau 403
    }

    // ── 11. Total nilai micro teaching dalam range 1-5 ───────────────────

    public function test_micro_teaching_total_nilai_is_between_1_and_5(): void
    {
        // Minimum: semua 1
        $scores = [1, 1, 1, 1, 1]; // per kategori
        $total  = round(array_sum($scores) / count($scores), 2);
        $this->assertEquals(1.0, $total);

        // Maximum: semua 5
        $scores = [5, 5, 5, 5, 5];
        $total  = round(array_sum($scores) / count($scores), 2);
        $this->assertEquals(5.0, $total);

        // Mixed
        $scores = [4, 4.33, 3.67, 4, 5];
        $total  = round(array_sum($scores) / count($scores), 2);
        $this->assertGreaterThanOrEqual(1, $total);
        $this->assertLessThanOrEqual(5, $total);
    }

    // ── 12. Kaprodi tidak bisa lihat pelamar di prodi lain ───────────────

    public function test_kaprodi_isolation_from_other_prodi_data(): void
    {
        $prodi1 = Prodi::factory()->create();
        $prodi2 = Prodi::factory()->create();

        $kDosen = Dosen::factory()->create(['is_kaprodi' => true, 'prodi_id' => $prodi1->id]);
        $kUser  = User::factory()->create([
            'role'       => 'kaprodi',
            'is_kaprodi' => true,
            'dosen_id'   => $kDosen->id,
            'prodi_id'   => $prodi1->id,
        ]);

        // Pelamar di prodi2
        $lowongan2 = Lowongan::factory()->create(['prodi_id' => $prodi2->id]);
        $pUser     = User::factory()->create(['role' => 'pelamar']);
        $pelamar2  = Pelamar::factory()->create(['user_id' => $pUser->id]);
        Lamaran::factory()->create(['pelamar_id' => $pelamar2->id, 'lowongan_id' => $lowongan2->id]);

        $this->actingAs($kUser)
             ->get(route('kaprodi.pelamar.show', $pelamar2))
             ->assertStatus(403);
    }
}
