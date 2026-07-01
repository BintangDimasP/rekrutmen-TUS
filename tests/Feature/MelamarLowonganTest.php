<?php

namespace Tests\Feature;

use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Pelamar;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MelamarLowonganTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Pelamar $pelamar;
    private Lowongan $lowongan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'pelamar', 'email_verified_at' => now()]);
        $this->pelamar = Pelamar::factory()->create(['user_id' => $this->user->id, 'phone_verified_at' => now()]);
        $prodi = Prodi::factory()->create();
        $this->lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id, 'status' => 'aktif']);
    }

    public function test_pelamar_dapat_melihat_daftar_lowongan(): void
    {
        $response = $this->actingAs($this->user)->get(route('pelamar.lowongan.index'));

        $response->assertStatus(200);
    }

    public function test_pelamar_dapat_melihat_detail_lowongan(): void
    {
        $response = $this->actingAs($this->user)->get(route('pelamar.lowongan.show', $this->lowongan));

        $response->assertStatus(200);
    }

    public function test_pelamar_dapat_menyimpan_lowongan(): void
    {
        $response = $this->actingAs($this->user)->postJson(route('pelamar.lowongan.save', $this->lowongan));

        $response->assertStatus(200);
        $response->assertJson(['saved' => true]);
    }

    public function test_pelamar_dapat_melamar_lowongan(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->user)->post(route('pelamar.lowongan.storeApply', $this->lowongan), [
            'file_surat_lamaran' => UploadedFile::fake()->create('lamaran.pdf', 100, 'application/pdf'),
        ]);

        $response->assertRedirect(route('pelamar.history.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('lamarans', [
            'pelamar_id' => $this->pelamar->id,
            'lowongan_id' => $this->lowongan->id,
            'status' => 'menunggu',
        ]);
    }

    public function test_pelamar_tidak_bisa_melamar_dua_kali(): void
    {
        Lamaran::factory()->create([
            'pelamar_id' => $this->pelamar->id,
            'lowongan_id' => $this->lowongan->id,
        ]);

        Storage::fake('public');

        $response = $this->actingAs($this->user)->post(route('pelamar.lowongan.storeApply', $this->lowongan), [
            'file_surat_lamaran' => UploadedFile::fake()->create('lamaran.pdf', 100, 'application/pdf'),
        ]);

        $response->assertRedirect(route('pelamar.history.index'));
        $response->assertSessionHas('warning');
    }
}
