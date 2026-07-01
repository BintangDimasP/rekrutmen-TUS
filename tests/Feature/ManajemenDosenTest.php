<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ManajemenDosenTest extends TestCase
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

    // ── STORE ────────────────────────────────────────────────────

    public function test_tambah_dosen_berhasil(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.dosen.store', $this->prodi), [
            'nama'       => 'Dr. Andi Wijaya',
            'kode'       => 'AND',
            'nip'        => '123456789012345678',
            'nidn'       => '0012345678',
            'no_telepon' => '081234567890',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('dosens', [
            'nama'     => 'Dr. Andi Wijaya',
            'kode'     => 'AND',
            'prodi_id' => $this->prodi->id,
        ]);
    }

    public function test_tambah_dosen_gagal_kode_duplikat(): void
    {
        Dosen::factory()->create(['kode' => 'DUP', 'prodi_id' => $this->prodi->id]);

        $response = $this->actingAs($this->admin)->post(route('admin.dosen.store', $this->prodi), [
            'nama' => 'Dosen Baru',
            'kode' => 'DUP',
        ]);

        $response->assertSessionHasErrors('kode');
    }

    public function test_tambah_dosen_gagal_field_kosong(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.dosen.store', $this->prodi), [
            'nama' => '',
            'kode' => '',
        ]);

        $response->assertSessionHasErrors(['nama', 'kode']);
    }

    public function test_tambah_dosen_gagal_no_telepon_format_salah(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.dosen.store', $this->prodi), [
            'nama'       => 'Dr. Test',
            'kode'       => 'TST',
            'no_telepon' => '1234abc',
        ]);

        $response->assertSessionHasErrors('no_telepon');
    }

    public function test_tambah_dosen_sebagai_kaprodi(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.dosen.store', $this->prodi), [
            'nama'       => 'Prof. Kaprodi',
            'kode'       => 'KPR',
            'is_kaprodi' => true,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $dosen = Dosen::where('kode', 'KPR')->first();
        $this->assertTrue($dosen->is_kaprodi);
        $this->assertNotNull($dosen->user);
        $this->assertEquals('kaprodi', $dosen->user->role);
    }

    public function test_tambah_dosen_kaprodi_mengganti_kaprodi_lama(): void
    {
        $kaprodiLama = Dosen::factory()->create([
            'prodi_id'   => $this->prodi->id,
            'is_kaprodi' => true,
        ]);
        $userLama = User::factory()->create([
            'dosen_id'   => $kaprodiLama->id,
            'role'       => 'kaprodi',
            'is_kaprodi' => true,
            'prodi_id'   => $this->prodi->id,
        ]);

        $this->actingAs($this->admin)->post(route('admin.dosen.store', $this->prodi), [
            'nama'       => 'Kaprodi Baru',
            'kode'       => 'KPB',
            'is_kaprodi' => true,
        ]);

        $kaprodiLama->refresh();
        $this->assertFalse($kaprodiLama->is_kaprodi);
    }

    // ── UPDATE ───────────────────────────────────────────────────

    public function test_update_dosen_berhasil(): void
    {
        $dosen = Dosen::factory()->create(['prodi_id' => $this->prodi->id, 'nama' => 'Lama']);

        $response = $this->actingAs($this->admin)->put(route('admin.dosen.update', $dosen), [
            'nama' => 'Dr. Budi Santoso',
            'kode' => $dosen->kode,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('dosens', ['id' => $dosen->id, 'nama' => 'Dr. Budi Santoso']);
    }

    public function test_update_dosen_tunjuk_jadi_kaprodi(): void
    {
        $dosen = Dosen::factory()->create([
            'prodi_id'   => $this->prodi->id,
            'is_kaprodi' => false,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.dosen.update', $dosen), [
            'nama'       => $dosen->nama,
            'kode'       => $dosen->kode,
            'is_kaprodi' => true,
        ]);

        $response->assertRedirect();
        $dosen->refresh();
        $this->assertTrue($dosen->is_kaprodi);
        $this->assertNotNull($dosen->user);
        $this->assertEquals('kaprodi', $dosen->user->role);
    }

    public function test_update_dosen_cabut_kaprodi_tanpa_role_lain(): void
    {
        $dosen = Dosen::factory()->create([
            'prodi_id'   => $this->prodi->id,
            'is_kaprodi' => true,
            'is_penguji' => false,
        ]);
        $user = User::factory()->create([
            'dosen_id'   => $dosen->id,
            'role'       => 'kaprodi',
            'is_kaprodi' => true,
            'is_penguji' => false,
            'prodi_id'   => $this->prodi->id,
        ]);

        $this->actingAs($this->admin)->put(route('admin.dosen.update', $dosen), [
            'nama'       => $dosen->nama,
            'kode'       => $dosen->kode,
            'is_kaprodi' => false,
        ]);

        $dosen->refresh();
        $this->assertFalse($dosen->is_kaprodi);
        // User dihapus karena tidak ada role lain
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_update_dosen_cabut_kaprodi_masih_penguji(): void
    {
        $dosen = Dosen::factory()->create([
            'prodi_id'   => $this->prodi->id,
            'is_kaprodi' => true,
            'is_penguji' => true,
        ]);
        $user = User::factory()->create([
            'dosen_id'   => $dosen->id,
            'role'       => 'kaprodi',
            'is_kaprodi' => true,
            'is_penguji' => true,
            'prodi_id'   => $this->prodi->id,
        ]);

        $this->actingAs($this->admin)->put(route('admin.dosen.update', $dosen), [
            'nama'       => $dosen->nama,
            'kode'       => $dosen->kode,
            'is_kaprodi' => false,
        ]);

        $user->refresh();
        $this->assertFalse($user->is_kaprodi);
        $this->assertEquals('penguji', $user->role);
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    // ── DESTROY ──────────────────────────────────────────────────

    public function test_hapus_dosen_berhasil(): void
    {
        $dosen = Dosen::factory()->create(['prodi_id' => $this->prodi->id]);

        $response = $this->actingAs($this->admin)->delete(route('admin.dosen.destroy', $dosen));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('dosens', ['id' => $dosen->id]);
    }

    public function test_hapus_dosen_yang_punya_akun_user(): void
    {
        $dosen = Dosen::factory()->create([
            'prodi_id'   => $this->prodi->id,
            'is_penguji' => true,
        ]);
        $user = User::factory()->create([
            'dosen_id'   => $dosen->id,
            'role'       => 'penguji',
            'is_penguji' => true,
        ]);

        $this->actingAs($this->admin)->delete(route('admin.dosen.destroy', $dosen));

        $this->assertDatabaseMissing('dosens', ['id' => $dosen->id]);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    // ── IMPORT ───────────────────────────────────────────────────

    public function test_import_dosen_gagal_format_file_salah(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.dosen.import', $this->prodi), [
            'file' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
        ]);

        $response->assertSessionHasErrors('file');
    }

    public function test_import_dosen_gagal_tanpa_file(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.dosen.import', $this->prodi), []);

        $response->assertSessionHasErrors('file');
    }
}
