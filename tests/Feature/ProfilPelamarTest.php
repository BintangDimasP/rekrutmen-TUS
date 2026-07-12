<?php

use App\Models\Pelamar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'role'              => 'pelamar',
        'email'             => 'pelamar@example.com',
        'email_verified_at' => now(),
        'password'          => Hash::make('password'),
    ]);

    $this->pelamar = Pelamar::factory()->create([
        'user_id'          => $this->user->id,
        'nama'             => 'Budi Santoso',
        'nik'              => '3201234567890001',
        'no_telepon'       => '081234567890',
        'jenis_kelamin'    => 'L',
        'tempat_lahir'     => 'Bandung',
        'tanggal_lahir'    => '1990-01-01',
        'kewarganegaraan'  => 'WNI',
        'status_pernikahan'=> 'Belum Menikah',
        'alamat_domisili'  => 'Jl. Sudirman No. 1',
        'alamat_ktp'       => 'Jl. Sudirman No. 1',
    ]);
});

function basePayload(): array
{
    return [
        'email'             => 'pelamar@example.com',
        'nik'               => '3201234567890001',
        'nama'              => 'Budi Santoso',
        'tempat_lahir'      => 'Bandung',
        'tanggal_lahir'     => '1990-01-01',
        'no_telepon'        => '081234567890',
        'jenis_kelamin'     => 'L',
        'kewarganegaraan'   => 'WNI',
        'status_pernikahan' => 'Belum Menikah',
        'alamat_domisili'   => 'Jl. Sudirman No. 1',
        'alamat_ktp'        => 'Jl. Sudirman No. 1',
    ];
}

test('TC-01: Pelamar memperbarui data diri, sistem berhasil menyimpan perubahan', function () {
    $payload = array_merge(basePayload(), ['nama' => 'Budi Santoso Updated']);

    $response = $this->actingAs($this->user)->put(route('pelamar.profil.update'), $payload);

    $response->assertRedirect(route('pelamar.profil.index'));
    $response->assertSessionHas('success');

    $this->pelamar->refresh();
    expect($this->pelamar->nama)->toBe('Budi Santoso Updated');

    $this->user->refresh();
    expect($this->user->name)->toBe('Budi Santoso Updated');
});

test('TC-02: Pelamar mengubah email ke email baru, sistem mereset verifikasi dan mengirim email verifikasi', function () {
    $payload = array_merge(basePayload(), ['email' => 'budi.baru@example.com']);

    $response = $this->actingAs($this->user)->put(route('pelamar.profil.update'), $payload);

    $response->assertRedirect(route('pelamar.profil.index'));

    $this->user->refresh();
    expect($this->user->email)->toBe('budi.baru@example.com');
    expect($this->user->email_verified_at)->toBeNull();
});

test('TC-03: Pelamar memperbarui profil dengan email yang sama, sistem menyimpan tanpa mereset verifikasi', function () {
    $payload = basePayload();

    $response = $this->actingAs($this->user)->put(route('pelamar.profil.update'), $payload);

    $response->assertRedirect(route('pelamar.profil.index'));

    $this->user->refresh();
    expect($this->user->email_verified_at)->not->toBeNull();
});

test('TC-04: Pelamar mengubah nomor telepon, sistem mereset verifikasi nomor telepon', function () {
    $payload = array_merge(basePayload(), ['no_telepon' => '089876543210']);

    $response = $this->actingAs($this->user)->put(route('pelamar.profil.update'), $payload);

    $response->assertRedirect(route('pelamar.profil.index'));

    $this->pelamar->refresh();
    expect($this->pelamar->no_telepon)->toBe('089876543210');
    expect($this->pelamar->phone_verified_at)->toBeNull();
});

test('TC-05: Pelamar mengunggah file ijazah, sistem berhasil menyimpan file', function () {
    Storage::fake('public');

    $payload = array_merge(basePayload(), [
        'file_ijazah' => UploadedFile::fake()->create('ijazah.pdf', 500, 'application/pdf'),
    ]);

    $response = $this->actingAs($this->user)->put(route('pelamar.profil.update'), $payload);

    $response->assertRedirect(route('pelamar.profil.index'));

    $this->pelamar->refresh();
    expect($this->pelamar->file_ijazah)->not->toBeNull();
    Storage::disk('public')->assertExists($this->pelamar->file_ijazah);
});

test('TC-06: Pelamar mengganti file ijazah yang sudah ada, sistem menghapus file lama dan menyimpan file baru', function () {
    Storage::fake('public');

    $oldPath = 'pelamar/' . $this->user->id . '/ijazah_lama.pdf';
    Storage::disk('public')->put($oldPath, 'fake-content');
    $this->pelamar->update(['file_ijazah' => $oldPath]);

    $payload = array_merge(basePayload(), [
        'file_ijazah' => UploadedFile::fake()->create('ijazah_baru.pdf', 500, 'application/pdf'),
    ]);

    $response = $this->actingAs($this->user)->put(route('pelamar.profil.update'), $payload);

    $response->assertRedirect(route('pelamar.profil.index'));

    Storage::disk('public')->assertMissing($oldPath);

    $this->pelamar->refresh();
    expect($this->pelamar->file_ijazah)->not->toBe($oldPath);
    Storage::disk('public')->assertExists($this->pelamar->file_ijazah);
});

test('TC-07: Pelamar menginput NIK yang sudah digunakan akun lain, sistem menampilkan pesan error', function () {
    Pelamar::factory()->create(['nik' => '9999888877776666']);

    $payload = array_merge(basePayload(), ['nik' => '9999888877776666']);

    $response = $this->actingAs($this->user)->put(route('pelamar.profil.update'), $payload);

    $response->assertSessionHasErrors(['nik']);
});

test('TC-08: Pelamar menginput email dengan domain internal dosen, sistem menampilkan pesan error', function () {
    $payload = array_merge(basePayload(), ['email' => 'test@pengajar.telkomuniversity.ac.id']);

    $response = $this->actingAs($this->user)->put(route('pelamar.profil.update'), $payload);

    $response->assertSessionHasErrors(['email']);
});
