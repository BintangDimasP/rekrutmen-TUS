<?php

namespace Tests\Feature;

use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Pelamar;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EksporDataTest extends TestCase
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
        $pelamar = Pelamar::factory()->create();
        Lamaran::factory()->create(['pelamar_id' => $pelamar->id, 'lowongan_id' => $this->lowongan->id]);
    }

    public function test_admin_dapat_ekspor_rekap_pelamar_ke_excel(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.lamaran.export', $this->lowongan));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_admin_dapat_ekspor_rekap_penilaian_ke_excel(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.lamaran.exportNilai', $this->lowongan));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
}
