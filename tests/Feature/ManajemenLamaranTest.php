<?php

namespace Tests\Feature;

use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Pelamar;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManajemenLamaranTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Lowongan $lowongan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $prodi = Prodi::factory()->create();
        $this->lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
    }

    public function test_admin_dapat_melihat_daftar_lamaran(): void
    {
        Lamaran::factory()->count(3)->create(['lowongan_id' => $this->lowongan->id]);

        $response = $this->actingAs($this->admin)->get(route('admin.lamaran.index', $this->lowongan));

        $response->assertStatus(200);
    }

    public function test_admin_dapat_melihat_detail_lamaran(): void
    {
        $lamaran = Lamaran::factory()->create(['lowongan_id' => $this->lowongan->id]);

        $response = $this->actingAs($this->admin)->get(route('admin.lamaran.show', $lamaran));

        $response->assertStatus(200);
    }

    public function test_admin_dapat_update_status_lamaran(): void
    {
        $lamaran = Lamaran::factory()->create([
            'lowongan_id' => $this->lowongan->id,
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.lamaran.update', $lamaran), [
            'status' => 'seleksi_tahap1',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('lamarans', ['id' => $lamaran->id, 'status' => 'seleksi_tahap1']);
    }

    public function test_admin_dapat_filter_lamaran(): void
    {
        $pelamar = Pelamar::factory()->create(['nama' => 'Budi Santoso']);
        Lamaran::factory()->create([
            'lowongan_id' => $this->lowongan->id,
            'pelamar_id' => $pelamar->id,
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($this->admin)->getJson(route('admin.lamaran.filter', $this->lowongan) . '?search=Budi&status=menunggu');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'lamarans');
    }

    public function test_admin_dapat_hapus_lamaran(): void
    {
        $lamaran = Lamaran::factory()->create(['lowongan_id' => $this->lowongan->id]);

        $response = $this->actingAs($this->admin)->delete(route('admin.lamaran.destroy', $lamaran));

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('lamarans', ['id' => $lamaran->id]);
    }
}
