<?php

use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createNotif($userId, $dibaca = false, $count = 1)
{
    for ($i = 0; $i < $count; $i++) {
        Notifikasi::create([
            'user_id'    => $userId,
            'judul'      => 'Test ' . $i,
            'pesan'      => 'Pesan test',
            'tipe'       => 'info',
            'dibaca'     => $dibaca,
        ]);
    }
}

beforeEach(function () {
    $this->user = User::factory()->create(['role' => 'pelamar']);
});

test('TC-01: Pengguna mengakses notifikasi dengan notifikasi yang belum dibaca, sistem mengembalikan daftar dan jumlah belum dibaca', function () {
    createNotif($this->user->id, false, 3);
    createNotif($this->user->id, true, 2);

    $response = $this->actingAs($this->user)->getJson(route('notifikasi.index'));

    $response->assertStatus(200);
    $response->assertJsonCount(5, 'notifikasis');
    $response->assertJsonPath('belum_dibaca', 3);
});

test('TC-02: Pengguna mengakses notifikasi tanpa notifikasi yang belum dibaca, sistem mengembalikan jumlah belum dibaca 0', function () {
    createNotif($this->user->id, true, 2);

    $response = $this->actingAs($this->user)->getJson(route('notifikasi.index'));

    $response->assertStatus(200);
    $response->assertJsonPath('belum_dibaca', 0);
});

test('TC-03: Pengguna menandai notifikasi miliknya sebagai dibaca, sistem berhasil memperbarui status', function () {
    createNotif($this->user->id, false, 1);
    $notifikasi = Notifikasi::where('user_id', $this->user->id)->first();

    $response = $this->actingAs($this->user)->postJson(route('notifikasi.baca', $notifikasi));

    $response->assertStatus(200);
    $response->assertJson(['ok' => true]);

    $notifikasi->refresh();
    expect($notifikasi->dibaca)->toBeTrue();
});

test('TC-04: Pengguna menandai notifikasi milik pengguna lain sebagai dibaca, sistem menampilkan error 403', function () {
    $userLain = User::factory()->create(['role' => 'pelamar']);
    createNotif($userLain->id, false, 1);
    $notifikasi = Notifikasi::where('user_id', $userLain->id)->first();

    $response = $this->actingAs($this->user)->postJson(route('notifikasi.baca', $notifikasi));

    $response->assertStatus(403);

    $notifikasi->refresh();
    expect($notifikasi->dibaca)->toBeFalse();
});

test('TC-05: Pengguna menandai semua notifikasi sebagai dibaca, sistem berhasil memperbarui seluruh notifikasi', function () {
    createNotif($this->user->id, false, 4);

    $response = $this->actingAs($this->user)->postJson(route('notifikasi.baca.semua'));

    $response->assertStatus(200);
    $response->assertJson(['ok' => true]);

    $belumDibaca = Notifikasi::where('user_id', $this->user->id)
        ->where('dibaca', false)
        ->count();
    expect($belumDibaca)->toBe(0);
});
