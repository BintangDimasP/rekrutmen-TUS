<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\JadwalSeleksi;
use App\Models\Lowongan;
use App\Models\Pelamar;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DaftarPengujianTest extends TestCase
{
    use RefreshDatabase;

    private User $pengujiUser;
    private Dosen $dosen;

    protected function setUp(): void
    {
        parent::setUp();
        $prodi = Prodi::factory()->create();
        $this->dosen = Dosen::factory()->create(['prodi_id' => $prodi->id, 'is_penguji' => true]);
        $this->pengujiUser = User::factory()->create(['role' => 'penguji', 'dosen_id' => $this->dosen->id, 'is_penguji' => true]);
    }

    public function test_penguji_dapat_melihat_daftar_pengujian(): void
    {
        $response = $this->actingAs($this->pengujiUser)->get(route('penguji.pengujian.index'));

        $response->assertStatus(200);
    }

    public function test_penguji_dapat_melihat_detail_pelamar(): void
    {
        $prodi = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
        $pelamar = Pelamar::factory()->create();

        $jadwal = JadwalSeleksi::create([
            'tanggal' => now()->addDays(1)->format('Y-m-d'),
            'lowongan_id' => $lowongan->id,
            'pelamar_id' => $pelamar->id,
            'penguji_id' => $this->dosen->id,
            'tipe_seleksi' => 'micro_teaching',
            'sesi' => 1,
        ]);

        $response = $this->actingAs($this->pengujiUser)->get(route('penguji.pengujian.show', $jadwal));

        $response->assertStatus(200);
    }
}
