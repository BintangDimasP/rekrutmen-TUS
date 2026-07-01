<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ManajemenProdiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    // ── INDEX ────────────────────────────────────────────────────

    public function test_admin_dapat_melihat_daftar_prodi(): void
    {
        Prodi::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.prodi.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.prodi.index');
        $response->assertViewHas('prodis');
    }

    public function test_non_admin_tidak_bisa_akses_halaman_prodi(): void
    {
        $pelamar = User::factory()->create(['role' => 'pelamar']);

        $response = $this->actingAs($pelamar)->get(route('admin.prodi.index'));

        $response->assertRedirect(route('pelamar.dashboard'));
    }

    // ── STORE ────────────────────────────────────────────────────

    public function test_tambah_prodi_berhasil(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.prodi.store'), [
            'nama' => 'Teknik Informatika',
            'kode' => 'TI',
        ]);

        $response->assertRedirect(route('admin.prodi.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('prodis', ['nama' => 'Teknik Informatika', 'kode' => 'TI']);
    }

    public function test_tambah_prodi_dengan_logo(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->admin)->post(route('admin.prodi.store'), [
            'nama' => 'Sistem Informasi',
            'kode' => 'SI',
            'logo' => UploadedFile::fake()->image('logo.png', 200, 200),
        ]);

        $response->assertRedirect(route('admin.prodi.index'));
        $prodi = Prodi::where('kode', 'SI')->first();
        $this->assertNotNull($prodi->logo);
        Storage::disk('public')->assertExists($prodi->logo);
    }

    public function test_tambah_prodi_gagal_kode_duplikat(): void
    {
        Prodi::factory()->create(['kode' => 'TI']);

        $response = $this->actingAs($this->admin)->post(route('admin.prodi.store'), [
            'nama' => 'Teknik Industri',
            'kode' => 'TI',
        ]);

        $response->assertSessionHasErrors('kode');
    }

    public function test_tambah_prodi_gagal_field_kosong(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.prodi.store'), [
            'nama' => '',
            'kode' => '',
        ]);

        $response->assertSessionHasErrors(['nama', 'kode']);
    }

    // ── SHOW ─────────────────────────────────────────────────────

    public function test_admin_dapat_melihat_detail_prodi(): void
    {
        $prodi = Prodi::factory()->create();
        Dosen::factory()->count(3)->create(['prodi_id' => $prodi->id]);

        $response = $this->actingAs($this->admin)->get(route('admin.prodi.show', $prodi));

        $response->assertStatus(200);
        $response->assertViewIs('admin.prodi.show');
        $response->assertViewHas('prodi');
        $response->assertViewHas('dosens');
    }

    // ── UPDATE ───────────────────────────────────────────────────

    public function test_update_prodi_berhasil(): void
    {
        $prodi = Prodi::factory()->create(['nama' => 'Lama', 'kode' => 'LM']);

        $response = $this->actingAs($this->admin)->put(route('admin.prodi.update', $prodi), [
            'nama' => 'Sistem Informasi',
            'kode' => 'SI',
        ]);

        $response->assertRedirect(route('admin.prodi.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('prodis', ['id' => $prodi->id, 'nama' => 'Sistem Informasi', 'kode' => 'SI']);
    }

    public function test_update_prodi_ganti_logo(): void
    {
        Storage::fake('public');

        $prodi = Prodi::factory()->create(['logo' => 'prodi_logos/old.png']);
        Storage::disk('public')->put('prodi_logos/old.png', 'fake');

        $response = $this->actingAs($this->admin)->put(route('admin.prodi.update', $prodi), [
            'nama' => $prodi->nama,
            'kode' => $prodi->kode,
            'logo' => UploadedFile::fake()->image('new_logo.png', 200, 200),
        ]);

        $response->assertRedirect(route('admin.prodi.index'));
        Storage::disk('public')->assertMissing('prodi_logos/old.png');
        $prodi->refresh();
        Storage::disk('public')->assertExists($prodi->logo);
    }

    // ── DESTROY ──────────────────────────────────────────────────

    public function test_hapus_prodi_berhasil(): void
    {
        $prodi = Prodi::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('admin.prodi.destroy', $prodi));

        $response->assertRedirect(route('admin.prodi.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('prodis', ['id' => $prodi->id]);
    }

    public function test_hapus_prodi_menghapus_logo_dari_storage(): void
    {
        Storage::fake('public');

        $prodi = Prodi::factory()->create(['logo' => 'prodi_logos/hapus.png']);
        Storage::disk('public')->put('prodi_logos/hapus.png', 'fake');

        $this->actingAs($this->admin)->delete(route('admin.prodi.destroy', $prodi));

        Storage::disk('public')->assertMissing('prodi_logos/hapus.png');
    }
}
