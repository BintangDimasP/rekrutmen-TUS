<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\JadwalSeleksi;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Pelamar;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JadwalSeleksiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Prodi $prodi;
    private Lowongan $lowongan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->prodi = Prodi::factory()->create();
        $this->lowongan = Lowongan::factory()->create(['prodi_id' => $this->prodi->id]);
    }

    public function test_admin_dapat_melihat_halaman_jadwal_seleksi(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.jadwal.index'));

        $response->assertStatus(200);
    }

    public function test_admin_dapat_melihat_form_buat_jadwal(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.jadwal.create'));

        $response->assertStatus(200);
    }

    public function test_admin_gagal_buat_jadwal_tanggal_sudah_lewat(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.jadwal.store'), [
            'tanggal' => '2020-01-01',
            'lowongan_id' => $this->lowongan->id,
            'schedule' => [],
        ]);

        $response->assertSessionHasErrors('tanggal');
    }

    public function test_admin_gagal_buat_jadwal_schedule_kosong(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.jadwal.store'), [
            'tanggal' => now()->addDays(1)->format('Y-m-d'),
            'lowongan_id' => $this->lowongan->id,
            'schedule' => [],
        ]);

        $response->assertSessionHasErrors('schedule');
    }

    public function test_admin_dapat_hapus_jadwal(): void
    {
        $pelamar = Pelamar::factory()->create();
        $penguji = Dosen::factory()->create(['prodi_id' => $this->prodi->id, 'is_penguji' => true]);

        $jadwal = JadwalSeleksi::create([
            'tanggal' => now()->addDays(1)->format('Y-m-d'),
            'lowongan_id' => $this->lowongan->id,
            'pelamar_id' => $pelamar->id,
            'penguji_id' => $penguji->id,
            'tipe_seleksi' => 'wawancara',
            'sesi' => 1,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.jadwal.destroy', $jadwal));

        $response->assertRedirect();
        $this->assertDatabaseMissing('jadwal_seleksis', ['id' => $jadwal->id]);
    }
}
