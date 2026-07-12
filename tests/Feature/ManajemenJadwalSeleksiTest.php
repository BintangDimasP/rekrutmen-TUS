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
    $this->admin   = User::factory()->create(['role' => 'admin']);
    $this->prodi   = Prodi::factory()->create();
    $this->dosen   = Dosen::factory()->create(['prodi_id' => $this->prodi->id, 'is_penguji' => true]);
    $this->lowongan = Lowongan::factory()->create(['prodi_id' => $this->prodi->id]);
    $this->pelamar  = Pelamar::factory()->create();
    Lamaran::factory()->create([
        'pelamar_id'  => $this->pelamar->id,
        'lowongan_id' => $this->lowongan->id,
        'status'      => 'seleksi_tahap1',
    ]);
});

test('TC-01: Admin membuat jadwal seleksi baru, sistem berhasil menyimpan jadwal', function () {
    $response = $this->actingAs($this->admin)->post(route('admin.jadwal.store'), [
        'tanggal'     => now()->addDays(7)->format('Y-m-d'),
        'lowongan_id' => $this->lowongan->id,
        'schedule'    => [
            $this->pelamar->id => [
                'sesi'                  => 1,
                'link'                  => 'https://meet.example.com/test',
                'penguji_wawancara_ids' => [$this->dosen->id],
                'penguji_micro_ids'     => [$this->dosen->id],
            ],
        ],
    ]);

    $response->assertRedirect(route('admin.jadwal.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('jadwal_seleksis', [
        'pelamar_id'  => $this->pelamar->id,
        'lowongan_id' => $this->lowongan->id,
        'penguji_id'  => $this->dosen->id,
        'sesi'        => 1,
    ]);
});

test('TC-02: Admin membuat jadwal dengan penguji yang sudah terjadwal di sesi yang sama, sistem menampilkan pesan error', function () {
    $tanggal = now()->addDays(7)->format('Y-m-d');

    $pelamar2 = Pelamar::factory()->create();
    JadwalSeleksi::create([
        'tanggal'      => $tanggal,
        'lowongan_id'  => $this->lowongan->id,
        'pelamar_id'   => $pelamar2->id,
        'penguji_id'   => $this->dosen->id,
        'tipe_seleksi' => 'wawancara',
        'sesi'         => 1,
    ]);

    $response = $this->actingAs($this->admin)->post(route('admin.jadwal.store'), [
        'tanggal'     => $tanggal,
        'lowongan_id' => $this->lowongan->id,
        'schedule'    => [
            $this->pelamar->id => [
                'sesi'                  => 1,
                'penguji_wawancara_ids' => [$this->dosen->id],
                'penguji_micro_ids'     => [],
            ],
        ],
    ]);

    $response->assertRedirect();
    $response->assertSessionHasErrors(['jadwal']);
});

test('TC-03: Admin memperbarui jadwal seleksi, sistem berhasil memperbarui', function () {
    $jadwal = JadwalSeleksi::create([
        'tanggal'      => now()->addDays(5)->format('Y-m-d'),
        'lowongan_id'  => $this->lowongan->id,
        'pelamar_id'   => $this->pelamar->id,
        'penguji_id'   => $this->dosen->id,
        'tipe_seleksi' => 'micro_teaching',
        'sesi'         => 1,
    ]);

    $tanggalBaru = now()->addDays(10)->format('Y-m-d');

    $response = $this->actingAs($this->admin)->put(route('admin.jadwal.update', $jadwal), [
        'tanggal' => $tanggalBaru,
        'sesi'    => 2,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $jadwal->refresh();
    expect($jadwal->sesi)->toBe(2);
    expect($jadwal->tanggal->format('Y-m-d'))->toBe($tanggalBaru);
});

test('TC-04: Admin memperbarui jadwal pada sesi yang telah terjadwal, sistem menampilkan pesan error', function () {
    $tanggal = now()->addDays(5)->format('Y-m-d');

    $pelamar2 = Pelamar::factory()->create();
    JadwalSeleksi::create([
        'tanggal'      => $tanggal,
        'lowongan_id'  => $this->lowongan->id,
        'pelamar_id'   => $pelamar2->id,
        'penguji_id'   => $this->dosen->id,
        'tipe_seleksi' => 'micro_teaching',
        'sesi'         => 2,
    ]);

    $jadwal = JadwalSeleksi::create([
        'tanggal'      => $tanggal,
        'lowongan_id'  => $this->lowongan->id,
        'pelamar_id'   => $this->pelamar->id,
        'penguji_id'   => $this->dosen->id,
        'tipe_seleksi' => 'micro_teaching',
        'sesi'         => 1,
    ]);

    $response = $this->actingAs($this->admin)->put(route('admin.jadwal.update', $jadwal), [
        'tanggal' => $tanggal,
        'sesi'    => 2,
    ]);

    $response->assertSessionHasErrors(['edit']);
});

test('TC-05: Admin memperbarui jadwal yang sudah dilakukan penilaian, sistem menampilkan pesan error', function () {
    $jadwalMicro = JadwalSeleksi::create([
        'tanggal'      => now()->addDays(3)->format('Y-m-d'),
        'lowongan_id'  => $this->lowongan->id,
        'pelamar_id'   => $this->pelamar->id,
        'penguji_id'   => $this->dosen->id,
        'tipe_seleksi' => 'micro_teaching',
        'sesi'         => 1,
    ]);
    Penilaian::create([
        'jadwal_seleksi_id' => $jadwalMicro->id,
        'kategori_1'        => 4.0,
        'total_nilai'       => 4.0,
        'detail_nilai'      => ['k1_item_1' => 4],
        'rekomendasi'       => 'direkomendasikan',
        'prodi_tujuan'      => 'TI',
    ]);

    $jadwalWawancara = JadwalSeleksi::create([
        'tanggal'      => now()->addDays(3)->format('Y-m-d'),
        'lowongan_id'  => $this->lowongan->id,
        'pelamar_id'   => $this->pelamar->id,
        'penguji_id'   => $this->dosen->id,
        'tipe_seleksi' => 'wawancara',
        'sesi'         => 1,
    ]);
    Penilaian::create([
        'jadwal_seleksi_id' => $jadwalWawancara->id,
        'kategori_1'        => 4.0,
        'total_nilai'       => 4.0,
        'detail_nilai'      => ['k1_item_1' => 4],
        'rekomendasi'       => 'direkomendasikan',
        'prodi_tujuan'      => 'TI',
    ]);

    $response = $this->actingAs($this->admin)->put(route('admin.jadwal.updateGroup'), [
        'pelamar_id'  => $this->pelamar->id,
        'lowongan_id' => $this->lowongan->id,
        'tanggal'     => now()->addDays(5)->format('Y-m-d'),
        'sesi'        => 2,
    ]);

    $response->assertSessionHasErrors(['edit']);
});
