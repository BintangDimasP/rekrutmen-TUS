<?php

use App\Models\Dosen;
use App\Models\JadwalSeleksi;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Pelamar;
use App\Models\Penilaian;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->prodi = Prodi::factory()->create();
    $this->lowongan = Lowongan::factory()->create(['prodi_id' => $this->prodi->id]);
});

test('TC-01: Admin mengekspor data lamaran dengan data ada, sistem mengunduh file Excel', function () {
    $pelamar = Pelamar::factory()->create();
    Lamaran::factory()->create([
        'pelamar_id' => $pelamar->id,
        'lowongan_id' => $this->lowongan->id,
    ]);

    $response = $this->actingAs($this->admin)->get(route('admin.lamaran.export', $this->lowongan));

    $response->assertStatus(200);
    $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
});

test('TC-02: Admin mengekspor data lamaran tanpa data, sistem mengunduh file Excel kosong', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.lamaran.export', $this->lowongan));

    $response->assertStatus(200);
    $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
});

test('TC-03: Admin mengekspor rekap nilai dengan data ada, sistem mengunduh file Excel', function () {
    $pelamar = Pelamar::factory()->create();
    $lamaran = Lamaran::factory()->create([
        'pelamar_id' => $pelamar->id,
        'lowongan_id' => $this->lowongan->id,
    ]);

    $penguji = Dosen::factory()->create(['prodi_id' => $this->prodi->id]);

    $jadwalMicro = JadwalSeleksi::factory()->create([
        'pelamar_id' => $pelamar->id,
        'lowongan_id' => $this->lowongan->id,
        'penguji_id' => $penguji->id,
        'tipe_seleksi' => 'micro_teaching',
    ]);

    $jadwalWawancara = JadwalSeleksi::factory()->create([
        'pelamar_id' => $pelamar->id,
        'lowongan_id' => $this->lowongan->id,
        'penguji_id' => $penguji->id,
        'tipe_seleksi' => 'wawancara',
    ]);

    Penilaian::factory()->create([
        'jadwal_seleksi_id' => $jadwalMicro->id,
        'total_nilai' => 4.5,
        'rekomendasi' => 'direkomendasikan',
    ]);

    Penilaian::factory()->create([
        'jadwal_seleksi_id' => $jadwalWawancara->id,
        'total_nilai' => 4.0,
        'rekomendasi' => 'direkomendasikan',
    ]);

    $response = $this->actingAs($this->admin)->get(route('admin.lamaran.exportNilai', $this->lowongan));

    $response->assertStatus(200);
    $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
});

test('TC-04: Admin mengekspor rekap nilai tanpa data, sistem mengunduh file Excel kosong', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.lamaran.exportNilai', $this->lowongan));

    $response->assertStatus(200);
    $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
});
