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
 * Test: Admin — Jadwal Seleksi (index, create, destroy + conflict detection)
 */
class JadwalSeleksiTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function setupData(): array
    {
        $prodi   = Prodi::factory()->create();
        $dosen   = Dosen::factory()->create(['is_penguji' => true, 'prodi_id' => $prodi->id]);
        User::factory()->create(['role' => 'penguji', 'is_penguji' => true, 'dosen_id' => $dosen->id]);
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
        $pUser    = User::factory()->create(['role' => 'pelamar']);
        $pelamar  = Pelamar::factory()->create(['user_id' => $pUser->id]);
        Lamaran::factory()->create([
            'pelamar_id' => $pelamar->id, 'lowongan_id' => $lowongan->id, 'status' => 'seleksi_tahap1',
        ]);
        return [$dosen, $lowongan, $pelamar, $prodi];
    }

    // ── Index ──────────────────────────────────────────────────────────

    public function test_admin_can_view_jadwal_index(): void
    {
        $this->actingAs($this->admin())
             ->get(route('admin.jadwal.index'))
             ->assertOk();
    }

    public function test_admin_can_view_jadwal_create_form(): void
    {
        $this->actingAs($this->admin())
             ->get(route('admin.jadwal.create'))
             ->assertOk();
    }

    // ── Store ──────────────────────────────────────────────────────────

    public function test_admin_can_create_jadwal(): void
    {
        $admin = $this->admin();
        [$dosen, $lowongan, $pelamar] = $this->setupData();
        $tanggal = now()->addDays(10)->format('Y-m-d');

        $payload = [
            'tanggal'    => $tanggal,
            'lowongan_id'=> $lowongan->id,
            'schedule'   => [
                $pelamar->id => [
                    'sesi'               => 1,
                    'penguji_wawancara_ids' => [$dosen->id],
                    'penguji_micro_ids'    => [$dosen->id],
                ],
            ],
        ];

        $this->actingAs($admin)
             ->post(route('admin.jadwal.store'), $payload)
             ->assertRedirect(route('admin.jadwal.index'));

        $this->assertDatabaseHas('jadwal_seleksis', [
            'pelamar_id'  => $pelamar->id,
            'lowongan_id' => $lowongan->id,
            'penguji_id'  => $dosen->id,
        ]);
    }

    public function test_store_jadwal_requires_tanggal(): void
    {
        $admin = $this->admin();
        [$dosen, $lowongan, $pelamar] = $this->setupData();

        $this->actingAs($admin)
             ->post(route('admin.jadwal.store'), [
                 'lowongan_id' => $lowongan->id,
                 'schedule'    => [$pelamar->id => ['sesi' => 1]],
             ])
             ->assertSessionHasErrors('tanggal');
    }

    public function test_store_jadwal_requires_lowongan_id(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
             ->post(route('admin.jadwal.store'), [
                 'tanggal'  => now()->addDays(5)->format('Y-m-d'),
                 'schedule' => [],
             ])
             ->assertSessionHasErrors(['lowongan_id', 'schedule']);
    }

    // ── Conflict detection ─────────────────────────────────────────────

    public function test_duplicate_jadwal_is_skipped_with_warning(): void
    {
        $admin = $this->admin();
        [$dosen, $lowongan, $pelamar] = $this->setupData();
        $tanggal = now()->addDays(10)->format('Y-m-d');

        // Buat jadwal pertama
        JadwalSeleksi::factory()->create([
            'tanggal'      => $tanggal,
            'pelamar_id'   => $pelamar->id,
            'lowongan_id'  => $lowongan->id,
            'penguji_id'   => $dosen->id,
            'tipe_seleksi' => 'micro_teaching',
            'sesi'         => 1,
        ]);

        $payload = [
            'tanggal'    => $tanggal,
            'lowongan_id'=> $lowongan->id,
            'schedule'   => [
                $pelamar->id => [
                    'sesi'             => 1,
                    'penguji_micro_ids' => [$dosen->id],
                    'penguji_wawancara_ids' => [],
                ],
            ],
        ];

        // Tidak boleh membuat duplikat, tapi tidak crash
        $this->actingAs($admin)
             ->post(route('admin.jadwal.store'), $payload)
             ->assertRedirect(); // redirect somewhere (either index or back with warning)

        // Masih hanya 1 jadwal micro teaching untuk kombinasi ini
        $this->assertEquals(1, JadwalSeleksi::whereDate('tanggal', $tanggal)
            ->where('pelamar_id', $pelamar->id)
            ->where('penguji_id', $dosen->id)
            ->where('tipe_seleksi', 'micro_teaching')
            ->where('sesi', 1)
            ->count());
    }

    // ── Destroy ────────────────────────────────────────────────────────

    public function test_admin_can_delete_jadwal(): void
    {
        $admin = $this->admin();
        [$dosen, $lowongan, $pelamar] = $this->setupData();
        $jadwal = JadwalSeleksi::factory()->create([
            'penguji_id'  => $dosen->id,
            'pelamar_id'  => $pelamar->id,
            'lowongan_id' => $lowongan->id,
        ]);

        $this->actingAs($admin)
             ->delete(route('admin.jadwal.destroy', $jadwal))
             ->assertRedirect();

        $this->assertDatabaseMissing('jadwal_seleksis', ['id' => $jadwal->id]);
    }

    // ── API Endpoints ──────────────────────────────────────────────────

    public function test_api_lowongan_by_prodi_returns_json(): void
    {
        $admin = $this->admin();
        $prodi = Prodi::factory()->create();
        Lowongan::factory()->create(['prodi_id' => $prodi->id, 'status' => 'aktif']);

        $this->actingAs($admin)
             ->getJson(route('admin.api.lowongan', ['prodi_id' => $prodi->id]))
             ->assertOk()
             ->assertJsonStructure([['id', 'nama_posisi']]);
    }

    public function test_api_penguji_by_prodi_returns_json(): void
    {
        $admin = $this->admin();
        $prodi = Prodi::factory()->create();
        Dosen::factory()->create(['prodi_id' => $prodi->id, 'is_penguji' => true]);

        $this->actingAs($admin)
             ->getJson(route('admin.api.penguji', ['prodi_id' => $prodi->id]))
             ->assertOk()
             ->assertJsonStructure([['id', 'nama', 'kode']]);
    }
}
