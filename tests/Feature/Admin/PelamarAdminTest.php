<?php

namespace Tests\Feature\Admin;

use App\Models\Dosen;
use App\Models\JadwalSeleksi;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Pelamar;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test: Admin — Halaman detail pelamar, filter lamaran, toggle status lowongan,
 *              jadwal update, dan notifikasi saat status lamaran berubah.
 */
class PelamarAdminTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function makeData(): array
    {
        $prodi    = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
        $pUser    = User::factory()->create(['role' => 'pelamar']);
        $pelamar  = Pelamar::factory()->create(['user_id' => $pUser->id]);
        $lamaran  = Lamaran::factory()->create([
            'pelamar_id'  => $pelamar->id,
            'lowongan_id' => $lowongan->id,
            'status'      => 'menunggu',
        ]);
        return [$prodi, $lowongan, $pelamar, $lamaran, $pUser];
    }

    // ══════════════════════════════════════════════════════════════
    // Admin Pelamar — show
    // ══════════════════════════════════════════════════════════════

    public function test_admin_can_view_pelamar_detail(): void
    {
        [, , $pelamar] = $this->makeData();

        $this->actingAs($this->admin())
            ->get(route('admin.pelamar.show', $pelamar))
            ->assertOk();
    }

    public function test_non_admin_cannot_view_pelamar_detail(): void
    {
        [, , $pelamar] = $this->makeData();
        $penguji = User::factory()->create(['role' => 'penguji']);

        $this->actingAs($penguji)
            ->get(route('admin.pelamar.show', $pelamar))
            ->assertRedirect(route('penguji.dashboard'));
    }

    // ══════════════════════════════════════════════════════════════
    // Lamaran filter (AJAX JSON)
    // ══════════════════════════════════════════════════════════════

    public function test_admin_can_filter_lamaran_by_search(): void
    {
        [$prodi, $lowongan, $pelamar] = $this->makeData();

        $response = $this->actingAs($this->admin())
            ->getJson(route('admin.lamaran.filter', $lowongan) . '?search=' . urlencode($pelamar->nama));

        $response->assertOk()
            ->assertJsonStructure(['lamarans']);
    }

    public function test_admin_can_filter_lamaran_by_status(): void
    {
        [$prodi, $lowongan] = $this->makeData();

        $response = $this->actingAs($this->admin())
            ->getJson(route('admin.lamaran.filter', $lowongan) . '?status=menunggu');

        $response->assertOk()
            ->assertJsonStructure(['lamarans']);
    }

    public function test_filter_returns_empty_for_no_match(): void
    {
        [$prodi, $lowongan] = $this->makeData();

        $response = $this->actingAs($this->admin())
            ->getJson(route('admin.lamaran.filter', $lowongan) . '?search=TIDAKADANAMASEPERTI_XYZ_999');

        $response->assertOk();
        $this->assertCount(0, $response->json('lamarans'));
    }

    // ══════════════════════════════════════════════════════════════
    // Lowongan — toggle status (aktif ↔ ditutup)
    // ══════════════════════════════════════════════════════════════

    public function test_admin_can_toggle_lowongan_status_to_ditutup(): void
    {
        $prodi    = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create([
            'prodi_id'      => $prodi->id,
            'status'        => 'aktif',
            'tanggal_tutup' => now()->addDays(30)->format('Y-m-d'),
        ]);

        $this->actingAs($this->admin())
            ->patch(route('admin.lowongan.toggleStatus', $lowongan))
            ->assertRedirect();

        // getRawOriginal karena model accessor bisa override status
        $this->assertEquals('ditutup', $lowongan->fresh()->getRawOriginal('status'));
    }

    public function test_admin_can_toggle_lowongan_status_back_to_aktif(): void
    {
        $prodi    = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create([
            'prodi_id'      => $prodi->id,
            'status'        => 'ditutup',
            'tanggal_tutup' => now()->addDays(30)->format('Y-m-d'),
            'kuota'         => 5,
        ]);

        $this->actingAs($this->admin())
            ->patch(route('admin.lowongan.toggleStatus', $lowongan))
            ->assertRedirect();

        $this->assertEquals('aktif', $lowongan->fresh()->getRawOriginal('status'));
    }

    public function test_toggle_to_aktif_fails_if_tanggal_tutup_expired(): void
    {
        $prodi    = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create([
            'prodi_id'      => $prodi->id,
            'status'        => 'ditutup',
            'tanggal_tutup' => now()->subDays(5)->format('Y-m-d'), // sudah lewat
            'kuota'         => 5,
        ]);

        $this->actingAs($this->admin())
            ->patch(route('admin.lowongan.toggleStatus', $lowongan))
            ->assertRedirect();

        // Status harus tetap ditutup karena tanggal sudah lewat
        $this->assertEquals('ditutup', $lowongan->fresh()->getRawOriginal('status'));
    }

    // ══════════════════════════════════════════════════════════════
    // Jadwal — update individual
    // ══════════════════════════════════════════════════════════════

    public function test_admin_can_update_jadwal_tanggal_and_sesi(): void
    {
        $prodi    = Prodi::factory()->create();
        $dosen    = Dosen::factory()->create(['is_penguji' => true, 'prodi_id' => $prodi->id]);
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
        $pUser    = User::factory()->create(['role' => 'pelamar']);
        $pelamar  = Pelamar::factory()->create(['user_id' => $pUser->id]);

        $jadwal = JadwalSeleksi::factory()->create([
            'penguji_id'   => $dosen->id,
            'pelamar_id'   => $pelamar->id,
            'lowongan_id'  => $lowongan->id,
            'tipe_seleksi' => 'micro_teaching',
            'sesi'         => 1,
            'tanggal'      => now()->addDays(5)->format('Y-m-d'),
        ]);

        $newTanggal = now()->addDays(10)->format('Y-m-d');

        $this->actingAs($this->admin())
            ->put(route('admin.jadwal.update', $jadwal), [
                'tanggal' => $newTanggal,
                'sesi'    => 2,
            ])
            ->assertRedirect();

        $jadwal->refresh();
        // Bandingkan sebagai string Y-m-d karena tanggal adalah Carbon object
        $this->assertEquals($newTanggal, $jadwal->tanggal->format('Y-m-d'));
        $this->assertEquals(2, $jadwal->sesi);
    }

    public function test_jadwal_update_requires_tanggal_and_sesi(): void
    {
        $prodi    = Prodi::factory()->create();
        $dosen    = Dosen::factory()->create(['is_penguji' => true, 'prodi_id' => $prodi->id]);
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
        $pUser    = User::factory()->create(['role' => 'pelamar']);
        $pelamar  = Pelamar::factory()->create(['user_id' => $pUser->id]);

        $jadwal = JadwalSeleksi::factory()->create([
            'penguji_id'  => $dosen->id,
            'pelamar_id'  => $pelamar->id,
            'lowongan_id' => $lowongan->id,
        ]);

        $this->actingAs($this->admin())
            ->put(route('admin.jadwal.update', $jadwal), [])
            ->assertSessionHasErrors(['tanggal', 'sesi']);
    }

    // ══════════════════════════════════════════════════════════════
    // Lamaran — update dengan notifikasi
    // ══════════════════════════════════════════════════════════════

    public function test_status_change_sends_notification_to_pelamar(): void
    {
        [$prodi, $lowongan, $pelamar, $lamaran, $pUser] = $this->makeData();

        $this->actingAs($this->admin())
            ->put(route('admin.lamaran.update', $lamaran), ['status' => 'seleksi_tahap1'])
            ->assertRedirect();

        // Notifikasi harus terkirim ke pelamar
        $this->assertDatabaseHas('notifikasis', [
            'user_id' => $pUser->id,
        ]);
    }

    public function test_no_notification_when_status_unchanged(): void
    {
        [$prodi, $lowongan, $pelamar, $lamaran, $pUser] = $this->makeData();

        // Update dengan status yang sama (menunggu → menunggu)
        $this->actingAs($this->admin())
            ->put(route('admin.lamaran.update', $lamaran), ['status' => 'menunggu'])
            ->assertRedirect();

        // Tidak ada notifikasi karena status tidak berubah
        $this->assertDatabaseMissing('notifikasis', ['user_id' => $pUser->id]);
    }
}
