<?php

use App\Models\Dosen;
use App\Models\JadwalSeleksi;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Pelamar;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->prodi = Prodi::factory()->create();
    $this->lowongan = Lowongan::factory()->create(['prodi_id' => $this->prodi->id]);

    $this->penguji = Dosen::factory()->create([
        'prodi_id' => $this->prodi->id,
        'is_penguji' => true,
    ]);
    $this->pengujiUser = User::factory()->create([
        'role' => 'penguji',
        'dosen_id' => $this->penguji->id,
    ]);
});

test('TC-01: Penguji mengakses halaman daftar pengujian, sistem menampilkan daftar jadwal pengujian', function () {
    $pelamar = Pelamar::factory()->create();
    Lamaran::factory()->create([
        'pelamar_id' => $pelamar->id,
        'lowongan_id' => $this->lowongan->id,
    ]);

    JadwalSeleksi::factory()->create([
        'pelamar_id' => $pelamar->id,
        'lowongan_id' => $this->lowongan->id,
        'penguji_id' => $this->penguji->id,
    ]);

    $response = $this->actingAs($this->pengujiUser)->get(route('penguji.pengujian.index'));

    $response->assertStatus(200);
    $response->assertViewIs('penguji.pengujian.index');
    $response->assertViewHas('jadwals');
});

test('TC-02: Penguji mengakses daftar pengujian tanpa jadwal yang ditugaskan, sistem menampilkan daftar kosong', function () {
    $response = $this->actingAs($this->pengujiUser)->get(route('penguji.pengujian.index'));

    $response->assertStatus(200);
    $response->assertViewIs('penguji.pengujian.index');
    $jadwals = $response->viewData('jadwals');
    expect($jadwals)->toHaveCount(0);
});

test('TC-03: Penguji mengakses detail pengujian miliknya, sistem menampilkan detail data pelamar', function () {
    $pelamar = Pelamar::factory()->create();
    Lamaran::factory()->create([
        'pelamar_id' => $pelamar->id,
        'lowongan_id' => $this->lowongan->id,
    ]);

    $jadwal = JadwalSeleksi::factory()->create([
        'pelamar_id' => $pelamar->id,
        'lowongan_id' => $this->lowongan->id,
        'penguji_id' => $this->penguji->id,
    ]);

    $response = $this->actingAs($this->pengujiUser)->get(route('penguji.pengujian.show', $jadwal));

    $response->assertStatus(200);
    $response->assertViewIs('penguji.pengujian.show');
    $response->assertViewHas('jadwal');
});

test('TC-04: Penguji mengakses detail pengujian milik penguji lain, sistem menampilkan error 403', function () {
    $pengujiLain = Dosen::factory()->create(['prodi_id' => $this->prodi->id]);

    $pelamar = Pelamar::factory()->create();
    Lamaran::factory()->create([
        'pelamar_id' => $pelamar->id,
        'lowongan_id' => $this->lowongan->id,
    ]);

    $jadwal = JadwalSeleksi::factory()->create([
        'pelamar_id' => $pelamar->id,
        'lowongan_id' => $this->lowongan->id,
        'penguji_id' => $pengujiLain->id,
    ]);

    $response = $this->actingAs($this->pengujiUser)->get(route('penguji.pengujian.show', $jadwal));

    $response->assertStatus(403);
});

test('TC-05: Pengguna tanpa data dosen mengakses daftar pengujian, sistem menampilkan error 403', function () {
    $userTanpaDosen = User::factory()->create(['role' => 'penguji']);

    $response = $this->actingAs($userTanpaDosen)->get(route('penguji.pengujian.index'));

    $response->assertStatus(403);
});
