<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\JadwalSeleksi;
use App\Models\Lowongan;
use App\Models\Pelamar;
use App\Models\Penilaian;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PenilaianTest extends TestCase
{
    use RefreshDatabase;

    private User $pengujiUser;
    private Dosen $dosen;
    private Pelamar $pelamar;
    private Lowongan $lowongan;

    protected function setUp(): void
    {
        parent::setUp();
        $prodi = Prodi::factory()->create();
        $this->dosen = Dosen::factory()->create(['prodi_id' => $prodi->id, 'is_penguji' => true]);
        $this->pengujiUser = User::factory()->create(['role' => 'penguji', 'dosen_id' => $this->dosen->id, 'is_penguji' => true]);
        $this->pelamar = Pelamar::factory()->create();
        $this->lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
    }

    public function test_penguji_dapat_menilai_micro_teaching(): void
    {
        $jadwal = JadwalSeleksi::create([
            'tanggal' => now()->addDays(1)->format('Y-m-d'),
            'lowongan_id' => $this->lowongan->id,
            'pelamar_id' => $this->pelamar->id,
            'penguji_id' => $this->dosen->id,
            'tipe_seleksi' => 'micro_teaching',
            'sesi' => 1,
        ]);

        $data = [
            'k1_item_1' => 4, 'k1_item_2' => 4,
            'k2_item_1' => 3, 'k2_item_2' => 4, 'k2_item_3' => 3,
            'k3_item_1' => 4, 'k3_item_2' => 3, 'k3_item_3' => 4, 'k3_item_4' => 3, 'k3_item_5' => 4, 'k3_item_6' => 3,
            'k4_item_1' => 4, 'k4_item_2' => 3, 'k4_item_3' => 4,
            'k5_item_1' => 4,
            'rekomendasi' => 'direkomendasikan',
            'prodi_tujuan' => 'Teknik Informatika',
            'kelompok_keahlian' => 'scout',
            'bidang_keahlian' => 'Software Engineering',
        ];

        $response = $this->actingAs($this->pengujiUser)->post(route('penguji.pengujian.storeNilai', $jadwal), $data);

        $response->assertRedirect(route('penguji.pengujian.show', $jadwal));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('penilaians', ['jadwal_seleksi_id' => $jadwal->id]);
    }

    public function test_penguji_dapat_menilai_wawancara(): void
    {
        // Prasyarat: micro teaching harus sudah dinilai
        $jadwalMicro = JadwalSeleksi::create([
            'tanggal' => now()->addDays(1)->format('Y-m-d'),
            'lowongan_id' => $this->lowongan->id,
            'pelamar_id' => $this->pelamar->id,
            'penguji_id' => $this->dosen->id,
            'tipe_seleksi' => 'micro_teaching',
            'sesi' => 1,
        ]);
        Penilaian::create([
            'jadwal_seleksi_id' => $jadwalMicro->id,
            'kategori_1' => 4, 'kategori_2' => 3, 'kategori_3' => 4, 'kategori_4' => 3, 'kategori_5' => 4,
            'detail_nilai' => [],
            'total_nilai' => 3.6,
            'rekomendasi' => 'direkomendasikan',
            'prodi_tujuan' => 'TI',
            'kelompok_keahlian' => 'scout',
            'bidang_keahlian' => 'SE',
        ]);

        // Buat jadwal wawancara
        $jadwalWawancara = JadwalSeleksi::create([
            'tanggal' => now()->addDays(1)->format('Y-m-d'),
            'lowongan_id' => $this->lowongan->id,
            'pelamar_id' => $this->pelamar->id,
            'penguji_id' => $this->dosen->id,
            'tipe_seleksi' => 'wawancara',
            'sesi' => 1,
        ]);

        $data = [
            'k1_item_1' => 4, 'k1_item_2' => 3, 'k1_item_3' => 4, 'k1_item_4' => 3,
            'k1_item_5' => 4, 'k1_item_6' => 3, 'k1_item_7' => 4, 'k1_item_8' => 3,
            'rekomendasi' => 'direkomendasikan',
            'prodi_tujuan' => 'Teknik Informatika',
        ];

        $response = $this->actingAs($this->pengujiUser)->post(route('penguji.pengujian.storeNilai', $jadwalWawancara), $data);

        $response->assertRedirect(route('penguji.pengujian.show', $jadwalWawancara));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('penilaians', ['jadwal_seleksi_id' => $jadwalWawancara->id]);
    }

    public function test_penguji_tidak_bisa_menilai_wawancara_sebelum_micro_selesai(): void
    {
        // Micro teaching belum dinilai
        JadwalSeleksi::create([
            'tanggal' => now()->addDays(1)->format('Y-m-d'),
            'lowongan_id' => $this->lowongan->id,
            'pelamar_id' => $this->pelamar->id,
            'penguji_id' => $this->dosen->id,
            'tipe_seleksi' => 'micro_teaching',
            'sesi' => 1,
        ]);

        $jadwalWawancara = JadwalSeleksi::create([
            'tanggal' => now()->addDays(1)->format('Y-m-d'),
            'lowongan_id' => $this->lowongan->id,
            'pelamar_id' => $this->pelamar->id,
            'penguji_id' => $this->dosen->id,
            'tipe_seleksi' => 'wawancara',
            'sesi' => 1,
        ]);

        // Coba akses form uji wawancara
        $response = $this->actingAs($this->pengujiUser)->get(route('penguji.pengujian.uji', $jadwalWawancara));

        $response->assertStatus(403);
    }
}
