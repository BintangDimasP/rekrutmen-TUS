<?php

namespace Tests\Feature\Pelamar;

use App\Models\Pelamar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Test: Pelamar — Profil (lihat & update data diri)
 */
class ProfilTest extends TestCase
{
    use RefreshDatabase;

    private function makePelamar(): array
    {
        $user    = User::factory()->create(['role' => 'pelamar']);
        $pelamar = Pelamar::factory()->create(['user_id' => $user->id]);
        return [$user, $pelamar];
    }

    // ══════════════════════════════════════════════════════════════
    // Halaman profil
    // ══════════════════════════════════════════════════════════════

    public function test_pelamar_can_view_profil_page(): void
    {
        [$user] = $this->makePelamar();

        $this->actingAs($user)
            ->get(route('pelamar.profil.index'))
            ->assertOk();
    }

    public function test_guest_cannot_view_profil(): void
    {
        $this->get(route('pelamar.profil.index'))
            ->assertRedirect('/login');
    }

    public function test_penguji_cannot_access_pelamar_profil(): void
    {
        $user = User::factory()->create(['role' => 'penguji']);

        $this->actingAs($user)
            ->get(route('pelamar.profil.index'))
            ->assertRedirect(route('penguji.dashboard'));
    }

    // ══════════════════════════════════════════════════════════════
    // Update profil — validasi field wajib
    // ══════════════════════════════════════════════════════════════

    public function test_profil_update_requires_mandatory_fields(): void
    {
        [$user] = $this->makePelamar();

        $this->actingAs($user)
            ->put(route('pelamar.profil.update'), [])
            ->assertSessionHasErrors(['nik', 'nama', 'tempat_lahir', 'tanggal_lahir',
                'no_telepon', 'jenis_kelamin', 'kewarganegaraan',
                'status_pernikahan', 'alamat_domisili', 'alamat_ktp']);
    }

    public function test_pelamar_can_update_profil_data_diri(): void
    {
        [$user, $pelamar] = $this->makePelamar();

        $this->actingAs($user)
            ->put(route('pelamar.profil.update'), [
                'nik'               => '3201010101010001',
                'nama'              => 'Budi Santoso',
                'tempat_lahir'      => 'Jakarta',
                'tanggal_lahir'     => '1990-01-01',
                'no_telepon'        => '081234567890',
                'jenis_kelamin'     => 'L',
                'kewarganegaraan'   => 'WNI',
                'status_pernikahan' => 'Belum Menikah',
                'alamat_domisili'   => 'Jl. Merdeka No. 1',
                'alamat_ktp'        => 'Jl. Merdeka No. 1',
            ])
            ->assertRedirect(route('pelamar.profil.index'))
            ->assertSessionHasNoErrors();

        $pelamar->refresh();
        $this->assertEquals('Budi Santoso', $pelamar->nama);
        $this->assertEquals('3201010101010001', $pelamar->nik);
    }

    public function test_nik_must_be_16_digits(): void
    {
        [$user] = $this->makePelamar();

        $this->actingAs($user)
            ->put(route('pelamar.profil.update'), [
                'nik'               => '123', // terlalu pendek
                'nama'              => 'Test',
                'tempat_lahir'      => 'Jakarta',
                'tanggal_lahir'     => '1990-01-01',
                'no_telepon'        => '081234567890',
                'jenis_kelamin'     => 'L',
                'kewarganegaraan'   => 'WNI',
                'status_pernikahan' => 'Belum Menikah',
                'alamat_domisili'   => 'Jl. Test',
                'alamat_ktp'        => 'Jl. Test',
            ])
            ->assertSessionHasErrors('nik');
    }

    public function test_nik_must_be_unique(): void
    {
        [$user1, $pelamar1] = $this->makePelamar();
        [$user2, $pelamar2] = $this->makePelamar();

        // Set NIK pelamar1
        $pelamar1->update(['nik' => '3201010101010001']);

        // Pelamar2 coba pakai NIK yang sama
        $this->actingAs($user2)
            ->put(route('pelamar.profil.update'), [
                'nik'               => '3201010101010001',
                'nama'              => 'Lain',
                'tempat_lahir'      => 'Bandung',
                'tanggal_lahir'     => '1991-01-01',
                'no_telepon'        => '082345678901',
                'jenis_kelamin'     => 'P',
                'kewarganegaraan'   => 'WNI',
                'status_pernikahan' => 'Belum Menikah',
                'alamat_domisili'   => 'Jl. Lain',
                'alamat_ktp'        => 'Jl. Lain',
            ])
            ->assertSessionHasErrors('nik');
    }

    public function test_jenis_kelamin_must_be_L_or_P(): void
    {
        [$user] = $this->makePelamar();

        $this->actingAs($user)
            ->put(route('pelamar.profil.update'), [
                'nik'               => '3201010101010001',
                'nama'              => 'Test',
                'tempat_lahir'      => 'Jakarta',
                'tanggal_lahir'     => '1990-01-01',
                'no_telepon'        => '081234567890',
                'jenis_kelamin'     => 'X', // tidak valid
                'kewarganegaraan'   => 'WNI',
                'status_pernikahan' => 'Belum Menikah',
                'alamat_domisili'   => 'Jl. Test',
                'alamat_ktp'        => 'Jl. Test',
            ])
            ->assertSessionHasErrors('jenis_kelamin');
    }

    public function test_pelamar_can_upload_file_ijazah(): void
    {
        Storage::fake('public');
        [$user, $pelamar] = $this->makePelamar();

        $pdf = UploadedFile::fake()->create('ijazah.pdf', 100, 'application/pdf');

        $this->actingAs($user)
            ->put(route('pelamar.profil.update'), [
                'nik'               => '3201010101010001',
                'nama'              => 'Budi',
                'tempat_lahir'      => 'Jakarta',
                'tanggal_lahir'     => '1990-01-01',
                'no_telepon'        => '081234567890',
                'jenis_kelamin'     => 'L',
                'kewarganegaraan'   => 'WNI',
                'status_pernikahan' => 'Belum Menikah',
                'alamat_domisili'   => 'Jl. Test',
                'alamat_ktp'        => 'Jl. Test',
                'file_ijazah'       => $pdf,
            ])
            ->assertRedirect(route('pelamar.profil.index'))
            ->assertSessionHasNoErrors();

        $pelamar->refresh();
        $this->assertNotNull($pelamar->file_ijazah);
    }

    public function test_profil_update_syncs_name_to_user(): void
    {
        [$user, $pelamar] = $this->makePelamar();

        $this->actingAs($user)
            ->put(route('pelamar.profil.update'), [
                'nik'               => '3201010101010001',
                'nama'              => 'Nama Baru Sync',
                'tempat_lahir'      => 'Jakarta',
                'tanggal_lahir'     => '1990-01-01',
                'no_telepon'        => '081234567890',
                'jenis_kelamin'     => 'L',
                'kewarganegaraan'   => 'WNI',
                'status_pernikahan' => 'Belum Menikah',
                'alamat_domisili'   => 'Jl. Test',
                'alamat_ktp'        => 'Jl. Test',
            ])
            ->assertRedirect();

        // Nama di tabel users harus ikut terupdate
        $this->assertEquals('Nama Baru Sync', $user->fresh()->name);
    }
}
