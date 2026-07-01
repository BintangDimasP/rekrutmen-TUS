<?php

namespace Tests\Feature;

use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Pelamar;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RiwayatLamaranTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Pelamar $pelamar;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'pelamar']);
        $this->pelamar = Pelamar::factory()->create(['user_id' => $this->user->id]);
    }

    public function test_pelamar_dapat_melihat_riwayat_lamaran(): void
    {
        $response = $this->actingAs($this->user)->get(route('pelamar.history.index'));

        $response->assertStatus(200);
    }

    public function test_pelamar_dapat_melihat_detail_riwayat(): void
    {
        $prodi = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
        $lamaran = Lamaran::factory()->create([
            'pelamar_id' => $this->pelamar->id,
            'lowongan_id' => $lowongan->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('pelamar.history.show', $lamaran));

        $response->assertStatus(200);
    }

    public function test_pelamar_dapat_mengundurkan_diri(): void
    {
        $prodi = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
        $lamaran = Lamaran::factory()->create([
            'pelamar_id' => $this->pelamar->id,
            'lowongan_id' => $lowongan->id,
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($this->user)->put(route('pelamar.history.withdraw', $lamaran));

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('lamarans', ['id' => $lamaran->id, 'status' => 'mengundurkan_diri']);
    }

    public function test_pelamar_tidak_bisa_mengundurkan_diri_dari_lamaran_yang_sudah_final(): void
    {
        $prodi = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
        $lamaran = Lamaran::factory()->create([
            'pelamar_id' => $this->pelamar->id,
            'lowongan_id' => $lowongan->id,
            'status' => 'diterima',
        ]);

        $response = $this->actingAs($this->user)->put(route('pelamar.history.withdraw', $lamaran));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('lamarans', ['id' => $lamaran->id, 'status' => 'diterima']);
    }
}
