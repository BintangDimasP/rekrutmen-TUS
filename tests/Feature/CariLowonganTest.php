<?php

use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Pelamar;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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

test('TC-01: Pelamar mengakses halaman cari lowongan, sistem menampilkan daftar lowongan aktif', function () {
    $response = $this->actingAs($this->user)->get(route('pelamar.lowongan.index'));

    $response->assertStatus(200)->assertViewIs('pelamar.lowongan.index');

    $available = $response->viewData('availableLowongans');
    expect($available)->not->toBeEmpty();
});

test('TC-02: Pelamar mengakses halaman cari lowongan tanpa data pelamar, sistem mengarahkan ke halaman profil', function () {
    $userTanpaPelamar = User::factory()->create(['role' => 'pelamar', 'email_verified_at' => now()]);

    $response = $this->actingAs($userTanpaPelamar)->get(route('pelamar.lowongan.index'));

    $response->assertRedirect(route('pelamar.profil.index'));
});

test('TC-03: Pelamar mengakses halaman cari lowongan setelah melamar, sistem memisahkan lowongan yang sudah dilamar', function () {
    Lamaran::factory()->create([
        'pelamar_id'  => $this->pelamar->id,
        'lowongan_id' => $this->lowongan->id,
        'status'      => 'menunggu',
    ]);

    $response = $this->actingAs($this->user)->get(route('pelamar.lowongan.index'));

    $response->assertStatus(200);

    $available = $response->viewData('availableLowongans');
    $applied   = $response->viewData('appliedLowongans');

    expect($available)->toBeEmpty();
    expect($applied)->not->toBeEmpty();
});

test('TC-04: Pelamar mengakses detail lowongan, sistem menampilkan informasi lowongan', function () {
    $response = $this->actingAs($this->user)->get(route('pelamar.lowongan.show', $this->lowongan));

    $response->assertStatus(200)->assertViewIs('pelamar.lowongan.show');

    $existing = $response->viewData('existing');
    expect($existing)->toBeNull();
});

test('TC-05: Pelamar mengakses detail lowongan yang sudah dilamar, sistem menampilkan status lamaran', function () {
    $lamaran = Lamaran::factory()->create([
        'pelamar_id'  => $this->pelamar->id,
        'lowongan_id' => $this->lowongan->id,
        'status'      => 'menunggu',
    ]);

    $response = $this->actingAs($this->user)->get(route('pelamar.lowongan.show', $this->lowongan));

    $response->assertStatus(200);

    $existing = $response->viewData('existing');
    expect($existing)->not->toBeNull();
    expect($existing->id)->toBe($lamaran->id);
});

test('TC-06: Pelamar mengakses form lamaran yang sudah pernah dilamar, sistem mengarahkan ke riwayat lamaran', function () {
    Lamaran::factory()->create([
        'pelamar_id'  => $this->pelamar->id,
        'lowongan_id' => $this->lowongan->id,
        'status'      => 'menunggu',
    ]);

    $response = $this->actingAs($this->user)->get(route('pelamar.lowongan.apply', $this->lowongan));

    $response->assertRedirect(route('pelamar.history.index'));
});

test('TC-07: Pelamar mengakses form lamaran dengan email belum terverifikasi, sistem mengarahkan ke halaman profil', function () {
    $userUnverified = User::factory()->unverified()->create(['role' => 'pelamar']);
    $pelamarUnverified = Pelamar::factory()->create([
        'user_id'    => $userUnverified->id,
        'no_telepon' => '081234567890',
    ]);

    $response = $this->actingAs($userUnverified)->get(route('pelamar.lowongan.apply', $this->lowongan));

    $response->assertRedirect(route('pelamar.profil.index'));
});

test('TC-08: Pelamar mengakses form lamaran dengan nomor telepon belum diisi, sistem mengarahkan ke halaman profil', function () {
    $this->pelamar->update(['no_telepon' => null]);

    $response = $this->actingAs($this->user)->get(route('pelamar.lowongan.apply', $this->lowongan));

    $response->assertRedirect(route('pelamar.profil.index'));
});

test('TC-09: Pelamar mengakses form lamaran pada lowongan yang penuh, sistem mengarahkan ke detail lowongan', function () {
    $lowonganPenuh = Lowongan::factory()->create([
        'prodi_id'      => $this->prodi->id,
        'status'        => 'aktif',
        'tanggal_tutup' => now()->addDays(30),
        'kuota'         => 1,
    ]);

    $pelamarLain = Pelamar::factory()->create();
    Lamaran::factory()->create([
        'pelamar_id'  => $pelamarLain->id,
        'lowongan_id' => $lowonganPenuh->id,
        'status'      => 'menunggu',
    ]);

    $response = $this->actingAs($this->user)->get(route('pelamar.lowongan.apply', $lowonganPenuh));

    $response->assertRedirect(route('pelamar.lowongan.show', $lowonganPenuh->id));
});

test('TC-10: Pelamar mengajukan lamaran dengan data lengkap, sistem berhasil menyimpan lamaran', function () {
    Storage::fake('public');

    $response = $this->actingAs($this->user)->post(route('pelamar.lowongan.storeApply', $this->lowongan), [
        'file_surat_lamaran' => UploadedFile::fake()->create('surat_lamaran.pdf', 500, 'application/pdf'),
    ]);

    $response->assertRedirect(route('pelamar.history.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('lamarans', [
        'pelamar_id'  => $this->pelamar->id,
        'lowongan_id' => $this->lowongan->id,
        'status'      => 'menunggu',
    ]);
});

test('TC-11: Pelamar mengajukan lamaran yang sudah pernah dilamar, sistem mengarahkan ke riwayat lamaran', function () {
    Storage::fake('public');

    Lamaran::factory()->create([
        'pelamar_id'  => $this->pelamar->id,
        'lowongan_id' => $this->lowongan->id,
        'status'      => 'menunggu',
    ]);

    $response = $this->actingAs($this->user)->post(route('pelamar.lowongan.storeApply', $this->lowongan), [
        'file_surat_lamaran' => UploadedFile::fake()->create('surat_lamaran.pdf', 500, 'application/pdf'),
    ]);

    $response->assertRedirect(route('pelamar.history.index'));
    $response->assertSessionHas('warning');
});

test('TC-12: Pelamar mengajukan lamaran tanpa file surat lamaran, sistem menampilkan pesan error', function () {
    Storage::fake('public');

    $response = $this->actingAs($this->user)->post(route('pelamar.lowongan.storeApply', $this->lowongan), []);

    $response->assertSessionHasErrors(['file_surat_lamaran']);
});
