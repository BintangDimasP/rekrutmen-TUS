<?php

namespace Tests\Feature\Admin;

use App\Models\Lowongan;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test: Admin — CRUD Lowongan
 */
class LowonganTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function lowonganData(int $prodiId): array
    {
        return [
            'prodi_id'       => $prodiId,
            'nama_posisi'    => 'Dosen Teknik Informatika',
            'deskripsi'      => 'Membuka lowongan dosen tetap.',
            'kuota'          => 3,
            'jenjang_minimal'=> 'S2',
            'minimal_ipk'    => 3.00,
            'tanggal_buka'   => now()->format('Y-m-d'),
            'tanggal_tutup'  => now()->addDays(30)->format('Y-m-d'),
            'status'         => 'aktif',
        ];
    }

    public function test_admin_can_view_lowongan_list(): void
    {
        $this->actingAs($this->admin())
             ->get(route('admin.lowongan.index'))
             ->assertOk();
    }

    public function test_admin_can_create_lowongan(): void
    {
        $admin = $this->admin();
        $prodi = Prodi::factory()->create();

        $this->actingAs($admin)
             ->post(route('admin.lowongan.store'), $this->lowonganData($prodi->id))
             ->assertRedirect();

        $this->assertDatabaseHas('lowongans', [
            'nama_posisi' => 'Dosen Teknik Informatika',
            'prodi_id'    => $prodi->id,
        ]);
    }

    public function test_admin_can_update_lowongan(): void
    {
        $admin   = $this->admin();
        $prodi   = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);

        $data = $this->lowonganData($prodi->id);
        $data['nama_posisi'] = 'Dosen Sistem Informasi';

        $this->actingAs($admin)
             ->put(route('admin.lowongan.update', $lowongan), $data)
             ->assertRedirect();

        $this->assertDatabaseHas('lowongans', ['id' => $lowongan->id, 'nama_posisi' => 'Dosen Sistem Informasi']);
    }

    public function test_admin_can_delete_lowongan(): void
    {
        $admin    = $this->admin();
        $prodi    = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);

        $this->actingAs($admin)
             ->delete(route('admin.lowongan.destroy', $lowongan))
             ->assertRedirect();

        $this->assertDatabaseMissing('lowongans', ['id' => $lowongan->id]);
    }

    public function test_non_admin_cannot_create_lowongan(): void
    {
        $pelamar = User::factory()->create(['role' => 'pelamar']);
        $prodi   = Prodi::factory()->create();

        $this->actingAs($pelamar)
             ->post(route('admin.lowongan.store'), $this->lowonganData($prodi->id))
             ->assertRedirect(route('pelamar.dashboard'));
    }
}
