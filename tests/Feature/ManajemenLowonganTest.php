<?php

namespace Tests\Feature;

use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Pelamar;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManajemenLowonganTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Prodi $prodi;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->prodi = Prodi::factory()->create();
    }

    // ── INDEX ────────────────────────────────────────────────────

    public function test_admin_dapat_melihat_daftar_lowongan(): void
    {
        Lowongan::factory()->count(3)->create(['prodi_id' => $this->prodi->id]);

        $response = $this->actingAs($this->admin)->get(route('admin.lowongan.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.lowongan.index');
        $response->assertViewHas('lowongans');
        $response->assertViewHas('prodis');
    }

    public function test_non_admin_tidak_bisa_akses_halaman_lowongan(): void
    {
        $pelamar = User::factory()->create(['role' => 'pelamar']);

        $response = $this->actingAs($pelamar)->get(route('admin.lowongan.index'));

        $response->assertRedirect(route('pelamar.dashboard'));
    }

    // ── CREATE ───────────────────────────────────────────────────

    public function test_admin_dapat_melihat_form_buat_lowongan(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.lowongan.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.lowongan.create');
        $response->assertViewHas('prodis');
        $response->assertViewHas('defaultDeskripsi');
    }

    // ── STORE ────────────────────────────────────────────────────

    public function test_buat_lowongan_berhasil(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.lowongan.store'), [
            'nama_posisi'      => 'Dosen Teknik Informatika',
            'prodi_id'         => $this->prodi->id,
            'jenjang_minimal'  => 'S2',
            'minimal_ipk'      => 3.00,
            'kuota'            => 5,
            'tanggal_tutup'    => now()->addDays(30)->format('Y-m-d'),
            'deskripsi'        => 'Deskripsi lowongan',
            'status'           => 'aktif',
        ]);

        $response->assertRedirect(route('admin.lowongan.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('lowongans', [
            'nama_posisi' => 'Dosen Teknik Informatika',
            'prodi_id'    => $this->prodi->id,
        ]);
    }

    public function test_buat_lowongan_dengan_prodi_prioritas_dan_skill(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.lowongan.store'), [
            'nama_posisi'      => 'Dosen SI',
            'prodi_id'         => $this->prodi->id,
            'jenjang_minimal'  => 'S3',
            'minimal_ipk'      => 3.25,
            'prodi_prioritas'  => 'Teknik Informatika||Sistem Informasi',
            'skill_dibutuhkan' => 'Machine Learning||Data Science',
            'kuota'            => 3,
            'tanggal_tutup'    => now()->addDays(60)->format('Y-m-d'),
            'status'           => 'aktif',
        ]);

        $response->assertRedirect(route('admin.lowongan.index'));
        $lowongan = Lowongan::where('nama_posisi', 'Dosen SI')->first();
        $this->assertEquals('Teknik Informatika, Sistem Informasi', $lowongan->prodi_prioritas);
        $this->assertEquals('Machine Learning, Data Science', $lowongan->skill_dibutuhkan);
    }

    public function test_buat_lowongan_gagal_field_kosong(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.lowongan.store'), [
            'nama_posisi'     => '',
            'prodi_id'        => '',
            'jenjang_minimal' => '',
            'minimal_ipk'     => '',
            'kuota'           => '',
            'tanggal_tutup'   => '',
            'status'          => '',
        ]);

        $response->assertSessionHasErrors([
            'nama_posisi', 'prodi_id', 'jenjang_minimal',
            'minimal_ipk', 'kuota', 'tanggal_tutup', 'status',
        ]);
    }

    public function test_buat_lowongan_gagal_tanggal_tutup_masa_lalu(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.lowongan.store'), [
            'nama_posisi'      => 'Dosen Test',
            'prodi_id'         => $this->prodi->id,
            'jenjang_minimal'  => 'S2',
            'minimal_ipk'      => 3.00,
            'kuota'            => 2,
            'tanggal_tutup'    => '2020-01-01',
            'status'           => 'aktif',
        ]);

        $response->assertSessionHasErrors('tanggal_tutup');
    }

    public function test_buat_lowongan_gagal_ipk_diluar_range(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.lowongan.store'), [
            'nama_posisi'      => 'Dosen Test',
            'prodi_id'         => $this->prodi->id,
            'jenjang_minimal'  => 'S2',
            'minimal_ipk'      => 5.0,
            'kuota'            => 2,
            'tanggal_tutup'    => now()->addDays(30)->format('Y-m-d'),
            'status'           => 'aktif',
        ]);

        $response->assertSessionHasErrors('minimal_ipk');
    }

    // ── EDIT ─────────────────────────────────────────────────────

    public function test_admin_dapat_melihat_form_edit_lowongan(): void
    {
        $lowongan = Lowongan::factory()->create(['prodi_id' => $this->prodi->id]);

        $response = $this->actingAs($this->admin)->get(route('admin.lowongan.edit', $lowongan));

        $response->assertStatus(200);
        $response->assertViewIs('admin.lowongan.edit');
        $response->assertViewHas('lowongan');
    }

    // ── UPDATE ───────────────────────────────────────────────────

    public function test_update_lowongan_berhasil(): void
    {
        $lowongan = Lowongan::factory()->create(['prodi_id' => $this->prodi->id]);

        $response = $this->actingAs($this->admin)->put(route('admin.lowongan.update', $lowongan), [
            'nama_posisi'      => 'Dosen Sistem Informasi',
            'prodi_id'         => $this->prodi->id,
            'jenjang_minimal'  => 'S3',
            'minimal_ipk'      => 3.50,
            'kuota'            => 10,
            'tanggal_tutup'    => now()->addDays(90)->format('Y-m-d'),
            'status'           => 'aktif',
        ]);

        $response->assertRedirect(route('admin.lowongan.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('lowongans', ['id' => $lowongan->id, 'nama_posisi' => 'Dosen Sistem Informasi']);
    }

    // ── TOGGLE STATUS ────────────────────────────────────────────

    public function test_toggle_status_aktif_ke_ditutup(): void
    {
        $lowongan = Lowongan::factory()->create([
            'prodi_id' => $this->prodi->id,
            'status'   => 'aktif',
            'tanggal_tutup' => now()->addDays(30),
            'kuota'    => 5,
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.lowongan.toggleStatus', $lowongan));

        $response->assertRedirect(route('admin.lowongan.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('lowongans', ['id' => $lowongan->id, 'status' => 'ditutup']);
    }

    public function test_toggle_status_ditutup_ke_aktif_berhasil(): void
    {
        $lowongan = Lowongan::factory()->create([
            'prodi_id'      => $this->prodi->id,
            'status'        => 'ditutup',
            'tanggal_tutup' => now()->addDays(30),
            'kuota'         => 5,
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.lowongan.toggleStatus', $lowongan));

        $response->assertRedirect(route('admin.lowongan.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('lowongans', ['id' => $lowongan->id, 'status' => 'aktif']);
    }

    public function test_toggle_status_gagal_tanggal_tutup_sudah_lewat(): void
    {
        $lowongan = Lowongan::factory()->create([
            'prodi_id'      => $this->prodi->id,
            'status'        => 'ditutup',
            'tanggal_tutup' => now()->subDays(5),
            'kuota'         => 5,
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.lowongan.toggleStatus', $lowongan));

        $response->assertRedirect(route('admin.lowongan.index'));
        $response->assertSessionHas('error');
    }

    public function test_toggle_status_gagal_kuota_habis(): void
    {
        $lowongan = Lowongan::factory()->create([
            'prodi_id'      => $this->prodi->id,
            'status'        => 'ditutup',
            'tanggal_tutup' => now()->addDays(30),
            'kuota'         => 1,
        ]);

        // Tambahkan lamaran agar kuota penuh
        $pelamar = Pelamar::factory()->create();
        Lamaran::factory()->create([
            'lowongan_id' => $lowongan->id,
            'pelamar_id'  => $pelamar->id,
            'status'      => 'diproses',
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.lowongan.toggleStatus', $lowongan));

        $response->assertRedirect(route('admin.lowongan.index'));
        $response->assertSessionHas('error');
    }

    // ── DESTROY ──────────────────────────────────────────────────

    public function test_hapus_lowongan_berhasil(): void
    {
        $lowongan = Lowongan::factory()->create(['prodi_id' => $this->prodi->id]);

        $response = $this->actingAs($this->admin)->delete(route('admin.lowongan.destroy', $lowongan));

        $response->assertRedirect(route('admin.lowongan.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('lowongans', ['id' => $lowongan->id]);
    }
}
