<?php

namespace Tests\Feature;

use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Pelamar;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CetakBeritaAcaraTest extends TestCase
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

    public function test_admin_dapat_cetak_berita_acara_personal(): void
    {
        $pelamar = Pelamar::factory()->create();
        $lamaran = Lamaran::factory()->create([
            'pelamar_id' => $pelamar->id,
            'lowongan_id' => $this->lowongan->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.lamaran.cetak', $lamaran));

        $response->assertStatus(200);
    }

    public function test_admin_dapat_cetak_berita_acara_general(): void
    {
        Lamaran::factory()->count(3)->create(['lowongan_id' => $this->lowongan->id]);

        $response = $this->actingAs($this->admin)->get(route('admin.lamaran.index', $this->lowongan));

        $response->assertStatus(200);
    }
}
