<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_registrasi_dapat_ditampilkan(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function test_registrasi_berhasil_dengan_data_valid(): void
    {
        $response = $this->post('/register', [
            'email' => 'pelamar@gmail.com',
            'password' => 'password123',
            'nik' => '3201010101010001',
            'nama' => 'Test Pelamar',
            'tempat_lahir' => 'Surabaya',
            'tanggal_lahir' => '1995-01-01',
            'no_telepon' => '081234567890',
            'jenis_kelamin' => 'L',
            'kewarganegaraan' => 'Indonesia',
            'status_pernikahan' => 'Belum Menikah',
            'alamat_domisili' => 'Jl. Test No. 1',
            'alamat_ktp' => 'Jl. Test No. 1',
        ]);

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'pelamar@gmail.com', 'role' => 'pelamar']);
    }

    public function test_registrasi_gagal_dengan_email_sudah_terdaftar(): void
    {
        User::factory()->create(['email' => 'sudahada@gmail.com']);

        $response = $this->post('/register', [
            'email' => 'sudahada@gmail.com',
            'password' => 'password123',
            'nik' => '3201010101010002',
            'nama' => 'Test',
            'tempat_lahir' => 'Surabaya',
            'tanggal_lahir' => '1995-01-01',
            'no_telepon' => '081234567891',
            'jenis_kelamin' => 'L',
            'kewarganegaraan' => 'Indonesia',
            'status_pernikahan' => 'Belum Menikah',
            'alamat_domisili' => 'Jl. Test',
            'alamat_ktp' => 'Jl. Test',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_registrasi_gagal_dengan_domain_internal(): void
    {
        $response = $this->post('/register', [
            'email' => 'dosen@pengajar.telkomuniversity.ac.id',
            'password' => 'password123',
            'nik' => '3201010101010003',
            'nama' => 'Test',
            'tempat_lahir' => 'Surabaya',
            'tanggal_lahir' => '1995-01-01',
            'no_telepon' => '081234567892',
            'jenis_kelamin' => 'L',
            'kewarganegaraan' => 'Indonesia',
            'status_pernikahan' => 'Belum Menikah',
            'alamat_domisili' => 'Jl. Test',
            'alamat_ktp' => 'Jl. Test',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_registrasi_gagal_dengan_field_wajib_kosong(): void
    {
        $response = $this->post('/register', []);

        $response->assertSessionHasErrors(['email', 'password', 'nik', 'nama']);
    }

    public function test_registrasi_gagal_dengan_nik_sudah_terdaftar(): void
    {
        $existingUser = User::factory()->create(['role' => 'pelamar']);
        \App\Models\Pelamar::factory()->create([
            'user_id' => $existingUser->id,
            'nik' => '3201010101010099',
        ]);

        $response = $this->post('/register', [
            'email' => 'baru@gmail.com',
            'password' => 'password123',
            'nik' => '3201010101010099',
            'nama' => 'Test Baru',
            'tempat_lahir' => 'Surabaya',
            'tanggal_lahir' => '1995-01-01',
            'no_telepon' => '081299999999',
            'jenis_kelamin' => 'L',
            'kewarganegaraan' => 'Indonesia',
            'status_pernikahan' => 'Belum Menikah',
            'alamat_domisili' => 'Jl. Test',
            'alamat_ktp' => 'Jl. Test',
        ]);

        $response->assertSessionHasErrors(['nik']);
    }
}
