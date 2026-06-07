<?php

namespace Tests\Feature\Pelamar;

use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Pelamar;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

/**
 * Test: Pelamar — apply lowongan & riwayat
 */
class LamaranTest extends TestCase
{
    use RefreshDatabase;

    private function makePelamar(): array
    {
        $user    = User::factory()->create(['role' => 'pelamar']);
        $pelamar = Pelamar::factory()->create([
            'user_id'           => $user->id,
            'phone_verified_at' => now(),
        ]);

        return [$user, $pelamar];
    }

    public function test_pelamar_can_view_lowongan_list(): void
    {
        [$user] = $this->makePelamar();

        $this->actingAs($user)
             ->get(route('pelamar.lowongan.index'))
             ->assertOk();
    }

    public function test_pelamar_can_view_lowongan_detail(): void
    {
        [$user] = $this->makePelamar();
        $prodi  = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create([
            'prodi_id' => $prodi->id,
            'status'   => 'aktif',
        ]);

        $this->actingAs($user)
             ->get(route('pelamar.lowongan.show', $lowongan))
             ->assertOk();
    }

    public function test_pelamar_can_apply_to_lowongan(): void
    {
        [$user, $pelamar] = $this->makePelamar();
        $prodi   = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create([
            'prodi_id' => $prodi->id,
            'status'   => 'aktif',
        ]);

        $pdf = UploadedFile::fake()->create('surat.pdf', 100, 'application/pdf');

        $this->actingAs($user)
             ->post(route('pelamar.lowongan.storeApply', $lowongan), [
                 'file_surat_lamaran' => $pdf,
             ])
             ->assertRedirect();

        $this->assertDatabaseHas('lamarans', [
            'pelamar_id'  => $pelamar->id,
            'lowongan_id' => $lowongan->id,
        ]);
    }

    public function test_pelamar_cannot_apply_twice_to_same_lowongan(): void
    {
        [$user, $pelamar] = $this->makePelamar();
        $prodi   = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id, 'status' => 'aktif']);

        // Lamaran pertama
        Lamaran::factory()->create(['pelamar_id' => $pelamar->id, 'lowongan_id' => $lowongan->id]);

        // Coba lamar lagi
        $this->actingAs($user)
             ->post(route('pelamar.lowongan.storeApply', $lowongan))
             ->assertRedirect();

        // Hanya ada 1 lamaran
        $this->assertDatabaseCount('lamarans', 1);
    }

    public function test_pelamar_can_view_riwayat(): void
    {
        [$user, $pelamar] = $this->makePelamar();
        $prodi   = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);

        Lamaran::factory()->create(['pelamar_id' => $pelamar->id, 'lowongan_id' => $lowongan->id]);

        $this->actingAs($user)
             ->get(route('pelamar.history.index'))
             ->assertOk();
    }

    public function test_guest_cannot_apply(): void
    {
        $prodi    = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);

        $this->post(route('pelamar.lowongan.storeApply', $lowongan))
             ->assertRedirect('/login');
    }
}
