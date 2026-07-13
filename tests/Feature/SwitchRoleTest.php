<?php

use App\Models\Dosen;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->prodi = Prodi::factory()->create();
});

test('TC-01: Kaprodi dengan role rangkap penguji beralih ke role penguji, sistem berhasil mengubah role', function () {
    $user = User::factory()->create([
        'role' => 'kaprodi',
        'is_kaprodi' => true,
        'is_penguji' => true,
        'prodi_id' => $this->prodi->id,
    ]);

    $response = $this->actingAs($user)->post(route('role.switch'), [
        'role' => 'penguji',
    ]);

    $response->assertRedirect(route('penguji.dashboard'));
    $response->assertSessionHas('success');

    $user->refresh();
    expect($user->role)->toBe('penguji');
});

test('TC-02: Penguji dengan role rangkap kaprodi beralih ke role kaprodi, sistem berhasil mengubah role', function () {
    $user = User::factory()->create([
        'role' => 'penguji',
        'is_kaprodi' => true,
        'is_penguji' => true,
        'prodi_id' => $this->prodi->id,
    ]);

    $response = $this->actingAs($user)->post(route('role.switch'), [
        'role' => 'kaprodi',
    ]);

    $response->assertRedirect(route('kaprodi.dashboard'));
    $response->assertSessionHas('success');

    $user->refresh();
    expect($user->role)->toBe('kaprodi');
});

test('TC-03: Kaprodi tanpa role penguji beralih ke role penguji, sistem menampilkan pesan error', function () {
    $user = User::factory()->create([
        'role' => 'kaprodi',
        'is_kaprodi' => true,
        'is_penguji' => false,
        'prodi_id' => $this->prodi->id,
    ]);

    $response = $this->actingAs($user)->post(route('role.switch'), [
        'role' => 'penguji',
    ]);

    $response->assertRedirect();
    $response->assertSessionHasErrors('role');

    $user->refresh();
    expect($user->role)->toBe('kaprodi');
});

test('TC-04: Penguji tanpa role kaprodi beralih ke role kaprodi, sistem menampilkan pesan error', function () {
    $user = User::factory()->create([
        'role' => 'penguji',
        'is_kaprodi' => false,
        'is_penguji' => true,
        'prodi_id' => $this->prodi->id,
    ]);

    $response = $this->actingAs($user)->post(route('role.switch'), [
        'role' => 'kaprodi',
    ]);

    $response->assertRedirect();
    $response->assertSessionHasErrors('role');

    $user->refresh();
    expect($user->role)->toBe('penguji');
});

test('TC-05: Pengguna menginput role tidak valid, sistem menampilkan pesan error', function () {
    $user = User::factory()->create([
        'role' => 'kaprodi',
        'is_kaprodi' => true,
        'is_penguji' => true,
        'prodi_id' => $this->prodi->id,
    ]);

    $response = $this->actingAs($user)->post(route('role.switch'), [
        'role' => 'admin',
    ]);

    $response->assertRedirect();
    $response->assertSessionHasErrors('role');
});
