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

test('TC-01: Admin mencetak berita acara general dengan pelamar diterima, sistem menampilkan data kandidat', function () {
    $pelamar = Pelamar::factory()->create();
    $lamaran = Lamaran::factory()->create([
        'pelamar_id' => $pelamar->id,
        'lowongan_id' => $this->lowongan->id,
        'status' => 'diterima',
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

    $response = $this->actingAs($this->admin)->get(route('admin.lowongan.beritaAcara', $this->lowongan));

    $response->assertStatus(200);
    $response->assertViewIs('admin.lowongan.berita_acara');
    $kandidats = $response->viewData('kandidats');
    expect($kandidats)->toHaveCount(1);
});

test('TC-02: Admin mencetak berita acara general dengan pelamar ditolak, sistem menampilkan data kandidat', function () {
    $pelamar = Pelamar::factory()->create();
    $lamaran = Lamaran::factory()->create([
        'pelamar_id' => $pelamar->id,
        'lowongan_id' => $this->lowongan->id,
        'status' => 'ditolak',
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
        'total_nilai' => 2.5,
        'rekomendasi' => 'tidak_direkomendasikan',
    ]);

    Penilaian::factory()->create([
        'jadwal_seleksi_id' => $jadwalWawancara->id,
        'total_nilai' => 2.0,
        'rekomendasi' => 'tidak_direkomendasikan',
    ]);

    $response = $this->actingAs($this->admin)->get(route('admin.lowongan.beritaAcara', $this->lowongan));

    $response->assertStatus(200);
    $response->assertViewIs('admin.lowongan.berita_acara');
    $kandidats = $response->viewData('kandidats');
    expect($kandidats)->toHaveCount(1);
});

test('TC-03: Admin mencetak berita acara general tanpa pelamar final, sistem menampilkan daftar kosong', function () {
    $pelamar = Pelamar::factory()->create();
    Lamaran::factory()->create([
        'pelamar_id' => $pelamar->id,
        'lowongan_id' => $this->lowongan->id,
        'status' => 'menunggu',
    ]);

    $response = $this->actingAs($this->admin)->get(route('admin.lowongan.beritaAcara', $this->lowongan));

    $response->assertStatus(200);
    $response->assertViewIs('admin.lowongan.berita_acara');
    $kandidats = $response->viewData('kandidats');
    expect($kandidats)->toHaveCount(0);
});

test('TC-04: Admin mencetak berita acara personal pelamar, sistem menampilkan halaman cetak', function () {
    $pelamar = Pelamar::factory()->create();
    $lamaran = Lamaran::factory()->create([
        'pelamar_id' => $pelamar->id,
        'lowongan_id' => $this->lowongan->id,
        'status' => 'diterima',
    ]);

    $penguji = Dosen::factory()->create(['prodi_id' => $this->prodi->id]);

    JadwalSeleksi::factory()->create([
        'pelamar_id' => $pelamar->id,
        'lowongan_id' => $this->lowongan->id,
        'penguji_id' => $penguji->id,
        'tipe_seleksi' => 'micro_teaching',
    ]);

    $response = $this->actingAs($this->admin)->get(route('admin.lamaran.cetak', $lamaran));

    $response->assertStatus(200);
    $response->assertViewIs('admin.lamaran.cetak');
    $response->assertViewHas('lamaran');
    $response->assertViewHas('micro');
    $response->assertViewHas('wawancara');
});
