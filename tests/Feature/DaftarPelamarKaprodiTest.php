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

    $this->kaprodiUser = User::factory()->create([
        'role' => 'kaprodi',
        'prodi_id' => $this->prodi->id,
    ]);

    $this->lowongan = Lowongan::factory()->create(['prodi_id' => $this->prodi->id]);
});

test('TC-01: Kaprodi mengakses halaman daftar pelamar, sistem menampilkan daftar pelamar di prodi', function () {
    $pelamar = Pelamar::factory()->create();
    Lamaran::factory()->create([
        'pelamar_id' => $pelamar->id,
        'lowongan_id' => $this->lowongan->id,
    ]);

    $response = $this->actingAs($this->kaprodiUser)->get(route('kaprodi.pelamar.index'));

    $response->assertStatus(200);
    $response->assertViewIs('kaprodi.pelamar');
    $response->assertViewHas('lamarans');
});

test('TC-02: Kaprodi mengakses daftar pelamar tanpa pelamar di prodi, sistem menampilkan daftar kosong', function () {
    $response = $this->actingAs($this->kaprodiUser)->get(route('kaprodi.pelamar.index'));

    $response->assertStatus(200);
    $response->assertViewIs('kaprodi.pelamar');
    $lamarans = $response->viewData('lamarans');
    expect($lamarans->count())->toBe(0);
});

test('TC-03: Kaprodi mengakses detail pelamar yang melamar ke prodi, sistem menampilkan detail pelamar dan jadwal seleksi', function () {
    $pelamar = Pelamar::factory()->create();
    $lamaran = Lamaran::factory()->create([
        'pelamar_id' => $pelamar->id,
        'lowongan_id' => $this->lowongan->id,
    ]);

    $penguji = Dosen::factory()->create(['prodi_id' => $this->prodi->id]);
    JadwalSeleksi::factory()->create([
        'pelamar_id' => $pelamar->id,
        'lowongan_id' => $this->lowongan->id,
        'penguji_id' => $penguji->id,
        'tipe_seleksi' => 'micro_teaching',
    ]);

    $response = $this->actingAs($this->kaprodiUser)->get(route('kaprodi.pelamar.show', $pelamar));

    $response->assertStatus(200);
    $response->assertViewIs('kaprodi.pelamar-show');
    $response->assertViewHas('pelamar');
    $response->assertViewHas('micro');
    $response->assertViewHas('wawancara');
});

test('TC-04: Kaprodi mengakses detail pelamar yang tidak melamar ke prodi, sistem menampilkan error 403', function () {
    $prodiLain = Prodi::factory()->create();
    $lowonganLain = Lowongan::factory()->create(['prodi_id' => $prodiLain->id]);

    $pelamar = Pelamar::factory()->create();
    Lamaran::factory()->create([
        'pelamar_id' => $pelamar->id,
        'lowongan_id' => $lowonganLain->id,
    ]);

    $response = $this->actingAs($this->kaprodiUser)->get(route('kaprodi.pelamar.show', $pelamar));

    $response->assertStatus(403);
});

test('TC-05: Kaprodi melakukan pencarian pelamar, sistem menampilkan hasil pencarian', function () {
    $pelamar1 = Pelamar::factory()->create(['nama' => 'Budi Santoso']);
    $pelamar2 = Pelamar::factory()->create(['nama' => 'Andi Wijaya']);

    Lamaran::factory()->create([
        'pelamar_id' => $pelamar1->id,
        'lowongan_id' => $this->lowongan->id,
    ]);
    Lamaran::factory()->create([
        'pelamar_id' => $pelamar2->id,
        'lowongan_id' => $this->lowongan->id,
    ]);

    $response = $this->actingAs($this->kaprodiUser)->get(route('kaprodi.pelamar.index', ['search' => 'Budi']));

    $response->assertStatus(200);
    $response->assertViewIs('kaprodi.pelamar');
    $lamarans = $response->viewData('lamarans');
    expect($lamarans->count())->toBe(1);
    expect($lamarans->first()->pelamar->nama)->toBe('Budi Santoso');
});
