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

class Iterasi4Test extends TestCase
{
    use RefreshDatabase;

    // ═══════════════════════════════════════════════════════════
    // PENJADWALAN SELEKSI OLEH ADMIN
    // ═══════════════════════════════════════════════════════════

    public function test_admin_jadwal_index_loads(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get(route('admin.jadwal.index'))->assertOk();
    }

    public function test_admin_jadwal_create_loads(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get(route('admin.jadwal.create'))->assertOk();
    }

    // ═══════════════════════════════════════════════════════════
    // DAFTAR JADWAL PENGUJIAN OLEH PENGUJI
    // ═══════════════════════════════════════════════════════════

    public function test_penguji_dashboard_loads(): void
    {
        $prodi = Prodi::factory()->create();
        $dosen = Dosen::factory()->create(['is_penguji' => true, 'prodi_id' => $prodi->id]);
        $user = User::factory()->create(['role' => 'penguji', 'dosen_id' => $dosen->id]);
        $this->actingAs($user)->get(route('penguji.dashboard'))->assertOk();
    }

    public function test_penguji_pengujian_index_loads(): void
    {
        $prodi = Prodi::factory()->create();
        $dosen = Dosen::factory()->create(['is_penguji' => true, 'prodi_id' => $prodi->id]);
        $user = User::factory()->create(['role' => 'penguji', 'dosen_id' => $dosen->id]);
        $this->actingAs($user)->get(route('penguji.pengujian.index'))->assertOk();
    }

    public function test_penguji_can_view_own_jadwal(): void
    {
        $prodi = Prodi::factory()->create();
        $dosen = Dosen::factory()->create(['is_penguji' => true, 'prodi_id' => $prodi->id]);
        $user = User::factory()->create(['role' => 'penguji', 'dosen_id' => $dosen->id]);
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
        $pUser = User::factory()->create(['role' => 'pelamar']);
        $pelamar = Pelamar::factory()->create(['user_id' => $pUser->id]);
        $jadwal = JadwalSeleksi::factory()->create([
            'penguji_id' => $dosen->id, 'pelamar_id' => $pelamar->id, 'lowongan_id' => $lowongan->id,
        ]);

        $this->actingAs($user)->get(route('penguji.pengujian.show', $jadwal))->assertOk();
    }

    public function test_penguji_cannot_access_other_penguji_jadwal(): void
    {
        $prodi = Prodi::factory()->create();
        $dosen1 = Dosen::factory()->create(['is_penguji' => true, 'prodi_id' => $prodi->id]);
        $dosen2 = Dosen::factory()->create(['is_penguji' => true, 'prodi_id' => $prodi->id]);
        $user1 = User::factory()->create(['role' => 'penguji', 'dosen_id' => $dosen1->id]);
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
        $pUser = User::factory()->create(['role' => 'pelamar']);
        $pelamar = Pelamar::factory()->create(['user_id' => $pUser->id]);
        $jadwal = JadwalSeleksi::factory()->create([
            'penguji_id' => $dosen2->id, 'pelamar_id' => $pelamar->id, 'lowongan_id' => $lowongan->id,
        ]);

        $this->actingAs($user1)->get(route('penguji.pengujian.show', $jadwal))->assertStatus(403);
    }

    // ═══════════════════════════════════════════════════════════
    // PENILAIAN MICRO TEACHING DAN WAWANCARA
    // ═══════════════════════════════════════════════════════════

    public function test_penguji_can_access_uji_page(): void
    {
        $prodi = Prodi::factory()->create();
        $dosen = Dosen::factory()->create(['is_penguji' => true, 'prodi_id' => $prodi->id]);
        $user = User::factory()->create(['role' => 'penguji', 'dosen_id' => $dosen->id]);
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
        $pUser = User::factory()->create(['role' => 'pelamar']);
        $pelamar = Pelamar::factory()->create(['user_id' => $pUser->id]);
        $jadwal = JadwalSeleksi::factory()->create([
            'penguji_id' => $dosen->id, 'pelamar_id' => $pelamar->id,
            'lowongan_id' => $lowongan->id, 'tipe_seleksi' => 'micro_teaching',
        ]);

        $this->actingAs($user)->get(route('penguji.pengujian.uji', $jadwal))->assertOk();
    }

    public function test_penguji_cannot_resubmit_penilaian(): void
    {
        $prodi = Prodi::factory()->create();
        $dosen = Dosen::factory()->create(['is_penguji' => true, 'prodi_id' => $prodi->id]);
        $user = User::factory()->create(['role' => 'penguji', 'dosen_id' => $dosen->id]);
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
        $pUser = User::factory()->create(['role' => 'pelamar']);
        $pelamar = Pelamar::factory()->create(['user_id' => $pUser->id]);
        $jadwal = JadwalSeleksi::factory()->create([
            'penguji_id' => $dosen->id, 'pelamar_id' => $pelamar->id, 'lowongan_id' => $lowongan->id,
        ]);
        Penilaian::factory()->create(['jadwal_seleksi_id' => $jadwal->id]);

        $this->actingAs($user)->get(route('penguji.pengujian.uji', $jadwal))
            ->assertRedirect(route('penguji.pengujian.show', $jadwal->id));
    }

    // ═══════════════════════════════════════════════════════════
    // MONITORING PELAMAR OLEH KAPRODI
    // ═══════════════════════════════════════════════════════════

    public function test_kaprodi_dashboard_loads(): void
    {
        $prodi = Prodi::factory()->create();
        $dosen = Dosen::factory()->create(['is_kaprodi' => true, 'prodi_id' => $prodi->id]);
        $user = User::factory()->create(['role' => 'kaprodi', 'dosen_id' => $dosen->id, 'prodi_id' => $prodi->id]);
        $this->actingAs($user)->get(route('kaprodi.dashboard'))->assertOk();
    }

    public function test_kaprodi_pelamar_index_loads(): void
    {
        $prodi = Prodi::factory()->create();
        $dosen = Dosen::factory()->create(['is_kaprodi' => true, 'prodi_id' => $prodi->id]);
        $user = User::factory()->create(['role' => 'kaprodi', 'dosen_id' => $dosen->id, 'prodi_id' => $prodi->id]);
        $this->actingAs($user)->get(route('kaprodi.pelamar.index'))->assertOk();
    }

    public function test_kaprodi_can_view_pelamar_in_own_prodi(): void
    {
        $prodi = Prodi::factory()->create();
        $dosen = Dosen::factory()->create(['is_kaprodi' => true, 'prodi_id' => $prodi->id]);
        $kUser = User::factory()->create(['role' => 'kaprodi', 'dosen_id' => $dosen->id, 'prodi_id' => $prodi->id]);
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
        $pUser = User::factory()->create(['role' => 'pelamar']);
        $pelamar = Pelamar::factory()->create(['user_id' => $pUser->id]);
        Lamaran::factory()->create(['pelamar_id' => $pelamar->id, 'lowongan_id' => $lowongan->id]);

        $this->actingAs($kUser)->get(route('kaprodi.pelamar.show', $pelamar))->assertOk();
    }

    public function test_kaprodi_cannot_view_pelamar_from_other_prodi(): void
    {
        $prodi1 = Prodi::factory()->create();
        $prodi2 = Prodi::factory()->create();
        $dosen = Dosen::factory()->create(['is_kaprodi' => true, 'prodi_id' => $prodi1->id]);
        $kUser = User::factory()->create(['role' => 'kaprodi', 'dosen_id' => $dosen->id, 'prodi_id' => $prodi1->id]);
        $lowongan2 = Lowongan::factory()->create(['prodi_id' => $prodi2->id]);
        $pUser = User::factory()->create(['role' => 'pelamar']);
        $pelamar = Pelamar::factory()->create(['user_id' => $pUser->id]);
        Lamaran::factory()->create(['pelamar_id' => $pelamar->id, 'lowongan_id' => $lowongan2->id]);

        $this->actingAs($kUser)->get(route('kaprodi.pelamar.show', $pelamar))->assertStatus(403);
    }
}
