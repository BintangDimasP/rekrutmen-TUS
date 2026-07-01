<?php

namespace Tests\Feature;

use App\Models\Pelamar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfilPelamarTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Pelamar $pelamar;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'pelamar', 'email' => 'pelamar@test.com']);
        $this->pelamar = Pelamar::factory()->create(['user_id' => $this->user->id]);
    }

    public function test_pelamar_dapat_melihat_halaman_profil(): void
    {
        $response = $this->actingAs($this->user)->get(route('pelamar.profil.index'));

        $response->assertStatus(200);
    }

    public function test_pelamar_dapat_update_data_diri(): void
    {
        $response = $this->actingAs($this->user)->put(route('pelamar.profil.update'), [
            'email' => 'pelamar@test.com',
            'nik' => '1234567890123456',
            'nama' => 'Nama Baru',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1995-01-01',
            'no_telepon' => '081234567890',
            'jenis_kelamin' => 'L',
            'kewarganegaraan' => 'Indonesia',
            'status_pernikahan' => 'Belum Menikah',
            'alamat_domisili' => 'Jl. Test No. 1',
            'alamat_ktp' => 'Jl. Test No. 1',
        ]);

        $response->assertRedirect(route('pelamar.profil.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('pelamars', ['id' => $this->pelamar->id, 'nama' => 'Nama Baru']);
    }

    public function test_ubah_email_mereset_verifikasi(): void
    {
        $this->user->update(['email_verified_at' => now()]);

        $this->actingAs($this->user)->put(route('pelamar.profil.update'), [
            'email' => 'baru@test.com',
            'nik' => '1234567890123456',
            'nama' => 'Test',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1995-01-01',
            'no_telepon' => '081234567890',
            'jenis_kelamin' => 'L',
            'kewarganegaraan' => 'Indonesia',
            'status_pernikahan' => 'Belum Menikah',
            'alamat_domisili' => 'Jl. Test',
            'alamat_ktp' => 'Jl. Test',
        ]);

        $this->user->refresh();
        $this->assertNull($this->user->email_verified_at);
    }

    public function test_ubah_no_telepon_mereset_verifikasi_whatsapp(): void
    {
        $this->pelamar->update(['phone_verified_at' => now(), 'no_telepon' => '081111111111']);

        $this->actingAs($this->user)->put(route('pelamar.profil.update'), [
            'email' => 'pelamar@test.com',
            'nik' => '1234567890123456',
            'nama' => 'Test',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1995-01-01',
            'no_telepon' => '089999999999',
            'jenis_kelamin' => 'L',
            'kewarganegaraan' => 'Indonesia',
            'status_pernikahan' => 'Belum Menikah',
            'alamat_domisili' => 'Jl. Test',
            'alamat_ktp' => 'Jl. Test',
        ]);

        $this->pelamar->refresh();
        $this->assertNull($this->pelamar->phone_verified_at);
    }
}
