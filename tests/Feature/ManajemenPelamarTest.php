<?php

namespace Tests\Feature;

use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Pelamar;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ManajemenPelamarTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    // ── INDEX ────────────────────────────────────────────────────

    public function test_admin_dapat_melihat_daftar_pelamar(): void
    {
        Pelamar::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.pelamar.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.pelamar.index');
        $response->assertViewHas('pelamars');
        $response->assertViewHas('prodis');
    }

    public function test_non_admin_tidak_bisa_akses_halaman_pelamar(): void
    {
        $pelamarUser = User::factory()->create(['role' => 'pelamar']);

        $response = $this->actingAs($pelamarUser)->get(route('admin.pelamar.index'));

        $response->assertRedirect(route('pelamar.dashboard'));
    }

    // ── SHOW ─────────────────────────────────────────────────────

    public function test_admin_dapat_melihat_detail_pelamar(): void
    {
        $prodi = Prodi::factory()->create();
        $pelamar = Pelamar::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
        Lamaran::factory()->create([
            'pelamar_id'  => $pelamar->id,
            'lowongan_id' => $lowongan->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.pelamar.show', $pelamar));

        $response->assertStatus(200);
        $response->assertViewIs('admin.pelamar.show');
        $response->assertViewHas('pelamar');
        $response->assertViewHas('activeLamaran');
    }

    public function test_detail_pelamar_dengan_lamaran_id_tertentu(): void
    {
        $prodi = Prodi::factory()->create();
        $pelamar = Pelamar::factory()->create();
        $lowongan1 = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
        $lowongan2 = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
        $lamaran1 = Lamaran::factory()->create(['pelamar_id' => $pelamar->id, 'lowongan_id' => $lowongan1->id]);
        $lamaran2 = Lamaran::factory()->create(['pelamar_id' => $pelamar->id, 'lowongan_id' => $lowongan2->id]);

        $response = $this->actingAs($this->admin)->get(
            route('admin.pelamar.show', $pelamar) . '?lamaran_id=' . $lamaran2->id
        );

        $response->assertStatus(200);
        $response->assertViewHas('activeLamaran', function ($activeLamaran) use ($lamaran2) {
            return $activeLamaran->id === $lamaran2->id;
        });
    }

    // ── IMPORT ───────────────────────────────────────────────────

    public function test_import_pelamar_gagal_format_file_salah(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.pelamar.import'), [
            'file' => UploadedFile::fake()->create('gambar.jpg', 100, 'image/jpeg'),
        ]);

        $response->assertSessionHasErrors('file');
    }

    public function test_import_pelamar_gagal_ukuran_melebihi_batas(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.pelamar.import'), [
            'file' => UploadedFile::fake()->create('besar.xlsx', 6000, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
        ]);

        $response->assertSessionHasErrors('file');
    }

    public function test_import_pelamar_gagal_tanpa_file(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.pelamar.import'), []);

        $response->assertSessionHasErrors('file');
    }
}
