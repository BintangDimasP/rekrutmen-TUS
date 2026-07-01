<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Pelamar;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManajemenPenggunaTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_halaman_manajemen_pengguna_dapat_ditampilkan(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('admin.user.index'));
        $response->assertStatus(200);
    }

    public function test_tambah_admin_berhasil(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('admin.user.store'), [
            'name' => 'Admin Baru',
            'username' => 'adminbaru',
            'password' => 'Secret123!',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'email' => 'adminbaru@admin.telkomuniversity.ac.id',
            'role' => 'admin',
        ]);
    }

    public function test_tambah_admin_gagal_username_duplikat(): void
    {
        $admin = $this->createAdmin();
        User::factory()->create(['email' => 'adminsatu@admin.telkomuniversity.ac.id']);

        $response = $this->actingAs($admin)->post(route('admin.user.store'), [
            'name' => 'Admin Lain',
            'username' => 'adminsatu',
            'password' => 'Secret123!',
        ]);

        $response->assertSessionHasErrors(['username']);
    }

    public function test_tambah_admin_gagal_field_kosong(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('admin.user.store'), [
            'name' => '',
            'username' => '',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'username', 'password']);
    }

    public function test_update_kredensial_pelamar_berhasil(): void
    {
        $admin = $this->createAdmin();
        $pelamar = User::factory()->create(['role' => 'pelamar', 'email' => 'lama@gmail.com']);
        Pelamar::factory()->create(['user_id' => $pelamar->id]);

        $response = $this->actingAs($admin)->put(route('admin.user.update', $pelamar), [
            'email' => 'baru@gmail.com',
            'password' => 'Newpass123!',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', ['id' => $pelamar->id, 'email' => 'baru@gmail.com']);
    }

    public function test_hapus_akun_pelamar_berhasil(): void
    {
        $admin = $this->createAdmin();
        $pelamar = User::factory()->create(['role' => 'pelamar']);
        Pelamar::factory()->create(['user_id' => $pelamar->id]);

        $response = $this->actingAs($admin)->delete(route('admin.user.destroy', $pelamar));

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $pelamar->id]);
    }

    public function test_hapus_akun_sendiri_gagal(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->delete(route('admin.user.destroy', $admin));

        $response->assertSessionHasErrors(['delete']);
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }
}
