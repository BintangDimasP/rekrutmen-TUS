<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Pelamar;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DaftarPelamarKaprodiTest extends TestCase
{
    use RefreshDatabase;

    private User $kaprodiUser;
    private Prodi $prodi;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prodi = Prodi::factory()->create();
        $dosen = Dosen::factory()->create(['prodi_id' => $this->prodi->id, 'is_kaprodi' => true]);
        $this->kaprodiUser = User::factory()->create([
            'role' => 'kaprodi',
            'dosen_id' => $dosen->id,
            'is_kaprodi' => true,
            'prodi_id' => $this->prodi->id,
        ]);
    }

    public function test_kaprodi_dapat_melihat_daftar_pelamar(): void
    {
        $lowongan = Lowongan::factory()->create(['prodi_id' => $this->prodi->id]);
        $pelamar = Pelamar::factory()->create();
        Lamaran::factory()->create(['pelamar_id' => $pelamar->id, 'lowongan_id' => $lowongan->id]);

        $response = $this->actingAs($this->kaprodiUser)->get(route('kaprodi.pelamar.index'));

        $response->assertStatus(200);
    }

    public function test_kaprodi_dapat_melihat_detail_pelamar(): void
    {
        $lowongan = Lowongan::factory()->create(['prodi_id' => $this->prodi->id]);
        $pelamar = Pelamar::factory()->create();
        Lamaran::factory()->create(['pelamar_id' => $pelamar->id, 'lowongan_id' => $lowongan->id]);

        $response = $this->actingAs($this->kaprodiUser)->get(route('kaprodi.pelamar.show', $pelamar));

        $response->assertStatus(200);
    }
}
