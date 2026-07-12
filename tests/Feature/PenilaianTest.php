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
    $this->prodi   = Prodi::factory()->create();
    $this->dosen   = Dosen::factory()->create(['prodi_id' => $this->prodi->id]);
    $this->penguji = User::factory()->create([
        'role'     => 'penguji',
        'dosen_id' => $this->dosen->id,
    ]);

    $this->lowongan = Lowongan::factory()->create(['prodi_id' => $this->prodi->id]);
    $this->pelamar  = Pelamar::factory()->create();
});

function microPayload(): array
{
    return [
        'rekomendasi'       => 'direkomendasikan',
        'prodi_tujuan'      => 'Teknik Informatika',
        'kelompok_keahlian' => 'scout',
        'bidang_keahlian'   => 'Machine Learning',
        'k1_item_1' => 4, 'k1_item_2' => 5,
        'k2_item_1' => 4, 'k2_item_2' => 3, 'k2_item_3' => 5,
        'k3_item_1' => 4, 'k3_item_2' => 5, 'k3_item_3' => 4,
        'k3_item_4' => 3, 'k3_item_5' => 5, 'k3_item_6' => 4,
        'k4_item_1' => 5, 'k4_item_2' => 4, 'k4_item_3' => 4,
        'k5_item_1' => 5,
    ];
}

function wawancaraPayload(): array
{
    return [
        'rekomendasi'      => 'direkomendasikan',
        'prodi_tujuan'     => 'Teknik Informatika',
        'status_rekrutmen' => 'profesional_full_time',
        'k1_item_1' => 4, 'k1_item_2' => 5, 'k1_item_3' => 4,
        'k1_item_4' => 3, 'k1_item_5' => 5, 'k1_item_6' => 4,
        'k1_item_7' => 5, 'k1_item_8' => 4,
    ];
}

test('TC-01: Penguji menilai micro teaching, sistem berhasil menyimpan penilaian', function () {
    $jadwal = JadwalSeleksi::create([
        'lowongan_id'  => $this->lowongan->id,
        'pelamar_id'   => $this->pelamar->id,
        'penguji_id'   => $this->dosen->id,
        'tipe_seleksi' => 'micro_teaching',
        'tanggal'      => now()->addDays(1),
        'sesi'         => 1,
        'ruangan'      => 'Lab A',
        'link_meeting' => 'https://meet.example.com/test',
    ]);

    $response = $this->actingAs($this->penguji)
        ->post(route('penguji.pengujian.storeNilai', $jadwal), microPayload());

    $response->assertRedirect(route('penguji.pengujian.show', $jadwal->id));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('penilaians', ['jadwal_seleksi_id' => $jadwal->id]);

    $penilaian = Penilaian::where('jadwal_seleksi_id', $jadwal->id)->first();
    expect($penilaian->kategori_1)->not->toBeNull();
    expect($penilaian->kategori_5)->not->toBeNull();
    expect($penilaian->total_nilai)->toBeFloat();
});

test('TC-02: Penguji menilai wawancara setelah semua micro teaching selesai dinilai, sistem berhasil menyimpan penilaian', function () {
    $jadwalMicro = JadwalSeleksi::create([
        'lowongan_id'  => $this->lowongan->id,
        'pelamar_id'   => $this->pelamar->id,
        'penguji_id'   => $this->dosen->id,
        'tipe_seleksi' => 'micro_teaching',
        'tanggal'      => now()->addDays(1),
        'sesi'         => 1,
        'ruangan'      => 'Lab A',
        'link_meeting' => 'https://meet.example.com/micro',
    ]);

    Penilaian::create([
        'jadwal_seleksi_id' => $jadwalMicro->id,
        'kategori_1'        => 4.5,
        'total_nilai'       => 4.5,
        'detail_nilai'      => ['k1_item_1' => 5],
        'rekomendasi'       => 'direkomendasikan',
        'prodi_tujuan'      => 'Teknik Informatika',
        'kelompok_keahlian' => 'scout',
        'bidang_keahlian'   => 'ML',
    ]);

    $jadwalWawancara = JadwalSeleksi::create([
        'lowongan_id'  => $this->lowongan->id,
        'pelamar_id'   => $this->pelamar->id,
        'penguji_id'   => $this->dosen->id,
        'tipe_seleksi' => 'wawancara',
        'tanggal'      => now()->addDays(2),
        'sesi'         => 1,
        'ruangan'      => 'Ruang B',
        'link_meeting' => 'https://meet.example.com/wawancara',
    ]);

    $response = $this->actingAs($this->penguji)
        ->post(route('penguji.pengujian.storeNilai', $jadwalWawancara), wawancaraPayload());

    $response->assertRedirect(route('penguji.pengujian.show', $jadwalWawancara->id));
    $response->assertSessionHas('success');

    $penilaian = Penilaian::where('jadwal_seleksi_id', $jadwalWawancara->id)->first();
    expect($penilaian)->not->toBeNull();
    expect($penilaian->total_nilai)->toBeFloat();
});

test('TC-03: Penguji mencoba menilai jadwal yang sudah dinilai, sistem menampilkan pesan error', function () {
    $jadwal = JadwalSeleksi::create([
        'lowongan_id'  => $this->lowongan->id,
        'pelamar_id'   => $this->pelamar->id,
        'penguji_id'   => $this->dosen->id,
        'tipe_seleksi' => 'micro_teaching',
        'tanggal'      => now()->addDays(1),
        'sesi'         => 1,
        'ruangan'      => 'Lab A',
        'link_meeting' => 'https://meet.example.com/test',
    ]);

    Penilaian::create([
        'jadwal_seleksi_id' => $jadwal->id,
        'kategori_1'        => 4.0,
        'total_nilai'       => 4.0,
        'detail_nilai'      => ['k1_item_1' => 4],
        'rekomendasi'       => 'direkomendasikan',
        'prodi_tujuan'      => 'Teknik Informatika',
    ]);

    $response = $this->actingAs($this->penguji)
        ->post(route('penguji.pengujian.storeNilai', $jadwal), microPayload());

    $response->assertRedirect(route('penguji.pengujian.show', $jadwal->id));
    $response->assertSessionHas('success', 'Penilaian sudah dilakukan sebelumnya. Tidak dapat mengubah nilai.');
});

test('TC-04: Penguji mencoba menilai wawancara sebelum micro teaching selesai, sistem menampilkan error 403', function () {
    $jadwalMicro = JadwalSeleksi::create([
        'lowongan_id'  => $this->lowongan->id,
        'pelamar_id'   => $this->pelamar->id,
        'penguji_id'   => $this->dosen->id,
        'tipe_seleksi' => 'micro_teaching',
        'tanggal'      => now()->addDays(1),
        'sesi'         => 1,
        'ruangan'      => 'Lab A',
        'link_meeting' => 'https://meet.example.com/micro',
    ]);

    $jadwalWawancara = JadwalSeleksi::create([
        'lowongan_id'  => $this->lowongan->id,
        'pelamar_id'   => $this->pelamar->id,
        'penguji_id'   => $this->dosen->id,
        'tipe_seleksi' => 'wawancara',
        'tanggal'      => now()->addDays(2),
        'sesi'         => 1,
        'ruangan'      => 'Ruang B',
        'link_meeting' => 'https://meet.example.com/wawancara',
    ]);

    $response = $this->actingAs($this->penguji)
        ->get(route('penguji.pengujian.uji', $jadwalWawancara));

    $response->assertStatus(403);
});

test('TC-05: Penguji mengakses halaman penilaian micro teaching, sistem menampilkan form penilaian', function () {
    $jadwal = JadwalSeleksi::create([
        'lowongan_id'  => $this->lowongan->id,
        'pelamar_id'   => $this->pelamar->id,
        'penguji_id'   => $this->dosen->id,
        'tipe_seleksi' => 'micro_teaching',
        'tanggal'      => now()->addDays(1),
        'sesi'         => 1,
        'ruangan'      => 'Lab A',
        'link_meeting' => 'https://meet.example.com/test',
    ]);

    $response = $this->actingAs($this->penguji)
        ->get(route('penguji.pengujian.uji', $jadwal));

    $response->assertStatus(200);
    $response->assertViewIs('penguji.pengujian.uji_micro');
});

test('TC-06: Penguji mengakses halaman penilaian yang sudah dinilai, sistem mengarahkan ke halaman detail', function () {
    $jadwal = JadwalSeleksi::create([
        'lowongan_id'  => $this->lowongan->id,
        'pelamar_id'   => $this->pelamar->id,
        'penguji_id'   => $this->dosen->id,
        'tipe_seleksi' => 'micro_teaching',
        'tanggal'      => now()->addDays(1),
        'sesi'         => 1,
        'ruangan'      => 'Lab A',
        'link_meeting' => 'https://meet.example.com/test',
    ]);

    Penilaian::create([
        'jadwal_seleksi_id' => $jadwal->id,
        'kategori_1'        => 4.0,
        'total_nilai'       => 4.0,
        'detail_nilai'      => ['k1_item_1' => 4],
        'rekomendasi'       => 'direkomendasikan',
        'prodi_tujuan'      => 'Teknik Informatika',
    ]);

    $response = $this->actingAs($this->penguji)
        ->get(route('penguji.pengujian.uji', $jadwal));

    $response->assertRedirect(route('penguji.pengujian.show', $jadwal->id));
});
