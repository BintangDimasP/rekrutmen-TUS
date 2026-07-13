<?php

use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Pelamar;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->prodi = Prodi::factory()->create();

    $this->user = User::factory()->create([
        'role'              => 'pelamar',
        'email_verified_at' => now(),
    ]);

    $this->pelamar = Pelamar::factory()->create([
        'user_id'    => $this->user->id,
        'no_telepon' => '081234567890',
    ]);

    $this->lowongan = Lowongan::factory()->create([
        'prodi_id'      => $this->prodi->id,
        'status'        => 'aktif',
        'tanggal_tutup' => now()->addDays(30),
        'kuota'         => 5,
    ]);
});

test('TC-01: Pelamar mengakses riwayat lamaran, sistem menampilkan daftar lamaran', function () {
    Lamaran::factory()->create([
        'pelamar_id'  => $this->pelamar->id,
        'lowongan_id' => $this->lowongan->id,
        'status'      => 'menunggu',
    ]);

    $response = $this->actingAs($this->user)->get(route('pelamar.history.index'));

    $response->assertStatus(200)->assertViewIs('pelamar.history.index');

    $lamarans = $response->viewData('lamarans');
    expect($lamarans->total())->toBe(1);
});

test('TC-02: Pelamar mengakses riwayat lamaran sebelum melengkapi profil, sistem mengarahkan ke halaman profil dengan pesan peringatan', function () {
    $userTanpaPelamar = User::factory()->create(['role' => 'pelamar']);

    $response = $this->actingAs($userTanpaPelamar)->get(route('pelamar.history.index'));

    $response->assertRedirect(route('pelamar.profil.index'));
    $response->assertSessionHas('warning');
});

test('TC-03: Pelamar mengakses detail riwayat lamaran miliknya, sistem menampilkan detail lamaran', function () {
    $lamaran = Lamaran::factory()->create([
        'pelamar_id'  => $this->pelamar->id,
        'lowongan_id' => $this->lowongan->id,
        'status'      => 'menunggu',
    ]);

    $response = $this->actingAs($this->user)->get(route('pelamar.history.show', $lamaran));

    $response->assertStatus(200)->assertViewIs('pelamar.history.show');
});

test('TC-04: Pelamar mengakses detail riwayat lamaran yang bukan miliknya, sistem menampilkan error 403', function () {
    $userLain    = User::factory()->create(['role' => 'pelamar']);
    $pelamarLain = Pelamar::factory()->create(['user_id' => $userLain->id]);

    $lamaranLain = Lamaran::factory()->create([
        'pelamar_id'  => $pelamarLain->id,
        'lowongan_id' => $this->lowongan->id,
        'status'      => 'menunggu',
    ]);

    $response = $this->actingAs($this->user)->get(route('pelamar.history.show', $lamaranLain));

    $response->assertStatus(403);
});

test('TC-05: Pelamar mengakses riwayat lamaran sebelum mengajukan lamaran, sistem menampilkan daftar kosong', function () {
    $response = $this->actingAs($this->user)->get(route('pelamar.history.index'));

    $response->assertStatus(200)->assertViewIs('pelamar.history.index');

    $lamarans = $response->viewData('lamarans');
    expect($lamarans->total())->toBe(0);
});

test('TC-06: Pelamar mengundurkan diri dari lamaran yang masih aktif, sistem berhasil mengubah status', function () {
    $lamaran = Lamaran::factory()->create([
        'pelamar_id'  => $this->pelamar->id,
        'lowongan_id' => $this->lowongan->id,
        'status'      => 'menunggu',
    ]);

    $response = $this->actingAs($this->user)->put(route('pelamar.history.withdraw', $lamaran));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $lamaran->refresh();
    expect($lamaran->status)->toBe('mengundurkan_diri');
});

test('TC-07: Pelamar mengundurkan diri dari lamaran yang sudah diterima, sistem menampilkan pesan error', function () {
    $lamaran = Lamaran::factory()->create([
        'pelamar_id'  => $this->pelamar->id,
        'lowongan_id' => $this->lowongan->id,
        'status'      => 'diterima',
    ]);

    $response = $this->actingAs($this->user)->put(route('pelamar.history.withdraw', $lamaran));

    $response->assertRedirect();
    $response->assertSessionHas('error');

    $lamaran->refresh();
    expect($lamaran->status)->toBe('diterima');
});

test('TC-08: Pelamar mengundurkan diri dari lamaran yang sudah ditolak, sistem menampilkan pesan error', function () {
    $lamaran = Lamaran::factory()->create([
        'pelamar_id'  => $this->pelamar->id,
        'lowongan_id' => $this->lowongan->id,
        'status'      => 'ditolak',
    ]);

    $response = $this->actingAs($this->user)->put(route('pelamar.history.withdraw', $lamaran));

    $response->assertRedirect();
    $response->assertSessionHas('error');

    $lamaran->refresh();
    expect($lamaran->status)->toBe('ditolak');
});
