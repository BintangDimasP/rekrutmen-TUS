<?php

namespace Tests\Feature\Admin;

use App\Models\Dosen;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

/**
 * Test: Admin — CRUD Prodi & Manajemen Dosen
 * Mencakup: prodi store/show/update/destroy, dosen store/update/destroy/import,
 * penunjukan kaprodi, pencabutan kaprodi, dan isolasi akses.
 */
class ProdiDosenTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    // ══════════════════════════════════════════════════════════════
    // PRODI
    // ══════════════════════════════════════════════════════════════

    public function test_admin_can_view_prodi_index(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.prodi.index'))
            ->assertOk();
    }

    public function test_admin_can_create_prodi(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.prodi.store'), [
                'nama' => 'Teknik Informatika',
                'kode' => 'TI',
            ])
            ->assertRedirect(route('admin.prodi.index'));

        $this->assertDatabaseHas('prodis', ['kode' => 'TI']);
    }

    public function test_prodi_store_requires_nama_and_kode(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.prodi.store'), [])
            ->assertSessionHasErrors(['nama', 'kode']);
    }

    public function test_prodi_kode_must_be_unique(): void
    {
        Prodi::factory()->create(['kode' => 'TI']);

        $this->actingAs($this->admin())
            ->post(route('admin.prodi.store'), ['nama' => 'Lain', 'kode' => 'TI'])
            ->assertSessionHasErrors('kode');
    }

    public function test_admin_can_view_prodi_show(): void
    {
        $prodi = Prodi::factory()->create();

        $this->actingAs($this->admin())
            ->get(route('admin.prodi.show', $prodi))
            ->assertOk();
    }

    public function test_admin_can_update_prodi(): void
    {
        $prodi = Prodi::factory()->create(['nama' => 'Lama', 'kode' => 'LM']);

        $this->actingAs($this->admin())
            ->put(route('admin.prodi.update', $prodi), [
                'nama' => 'Baru',
                'kode' => 'BR',
            ])
            ->assertRedirect(route('admin.prodi.index'));

        $this->assertDatabaseHas('prodis', ['id' => $prodi->id, 'nama' => 'Baru', 'kode' => 'BR']);
    }

    public function test_admin_can_delete_prodi(): void
    {
        $prodi = Prodi::factory()->create();

        $this->actingAs($this->admin())
            ->delete(route('admin.prodi.destroy', $prodi))
            ->assertRedirect(route('admin.prodi.index'));

        $this->assertDatabaseMissing('prodis', ['id' => $prodi->id]);
    }

    public function test_non_admin_cannot_access_prodi_management(): void
    {
        $pelamar = User::factory()->create(['role' => 'pelamar']);

        $this->actingAs($pelamar)
            ->get(route('admin.prodi.index'))
            ->assertRedirect(route('pelamar.dashboard'));
    }

    // ══════════════════════════════════════════════════════════════
    // DOSEN — store
    // ══════════════════════════════════════════════════════════════

    public function test_admin_can_add_dosen_to_prodi(): void
    {
        $prodi = Prodi::factory()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.dosen.store', $prodi), [
                'nama' => 'Dr. Budi',
                'kode' => 'BUD',
                'nip'  => '198001012010011001',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('dosens', ['kode' => 'BUD', 'prodi_id' => $prodi->id]);
    }

    public function test_dosen_store_requires_nama_and_kode(): void
    {
        $prodi = Prodi::factory()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.dosen.store', $prodi), [])
            ->assertSessionHasErrors(['nama', 'kode']);
    }

    public function test_dosen_kode_must_be_unique(): void
    {
        $prodi = Prodi::factory()->create();
        Dosen::factory()->create(['kode' => 'BUD', 'prodi_id' => $prodi->id]);

        $this->actingAs($this->admin())
            ->post(route('admin.dosen.store', $prodi), [
                'nama' => 'Lain',
                'kode' => 'BUD',
            ])
            ->assertSessionHasErrors('kode');
    }

    public function test_adding_dosen_as_kaprodi_creates_user_account(): void
    {
        $prodi = Prodi::factory()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.dosen.store', $prodi), [
                'nama'       => 'Dr. Kaprodi',
                'kode'       => 'KAP',
                'is_kaprodi' => true,
            ])
            ->assertRedirect();

        $dosen = Dosen::where('kode', 'KAP')->first();
        $this->assertNotNull($dosen);
        $this->assertTrue((bool) $dosen->is_kaprodi);

        // Akun user harus dibuat otomatis
        $this->assertDatabaseHas('users', ['dosen_id' => $dosen->id, 'role' => 'kaprodi']);
    }

    // ══════════════════════════════════════════════════════════════
    // DOSEN — update
    // ══════════════════════════════════════════════════════════════

    public function test_admin_can_update_dosen(): void
    {
        $prodi = Prodi::factory()->create();
        $dosen = Dosen::factory()->create(['kode' => 'OLD', 'prodi_id' => $prodi->id]);

        $this->actingAs($this->admin())
            ->put(route('admin.dosen.update', $dosen), [
                'nama' => 'Nama Baru',
                'kode' => 'NEW',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('dosens', ['id' => $dosen->id, 'kode' => 'NEW']);
    }

    public function test_promoting_dosen_to_kaprodi_creates_user_and_demotes_old_kaprodi(): void
    {
        $prodi = Prodi::factory()->create();

        // Kaprodi lama
        $oldKaprodi = Dosen::factory()->create(['is_kaprodi' => true, 'prodi_id' => $prodi->id]);
        $oldUser    = User::factory()->create([
            'role'       => 'kaprodi',
            'is_kaprodi' => true,
            'dosen_id'   => $oldKaprodi->id,
            'prodi_id'   => $prodi->id,
        ]);

        // Dosen biasa yang akan dipromosikan
        $newKaprodi = Dosen::factory()->create(['is_kaprodi' => false, 'prodi_id' => $prodi->id]);

        $this->actingAs($this->admin())
            ->put(route('admin.dosen.update', $newKaprodi), [
                'nama'       => $newKaprodi->nama,
                'kode'       => $newKaprodi->kode,
                'is_kaprodi' => true,
            ])
            ->assertRedirect();

        // Kaprodi lama harus di-demote
        $oldKaprodi->refresh();
        $this->assertFalse((bool) $oldKaprodi->is_kaprodi);

        // Kaprodi baru harus punya akun
        $newKaprodi->refresh();
        $this->assertTrue((bool) $newKaprodi->is_kaprodi);
        $this->assertDatabaseHas('users', ['dosen_id' => $newKaprodi->id, 'role' => 'kaprodi']);
    }

    // ══════════════════════════════════════════════════════════════
    // DOSEN — destroy
    // ══════════════════════════════════════════════════════════════

    public function test_admin_can_delete_dosen(): void
    {
        $prodi = Prodi::factory()->create();
        $dosen = Dosen::factory()->create(['prodi_id' => $prodi->id]);

        $this->actingAs($this->admin())
            ->delete(route('admin.dosen.destroy', $dosen))
            ->assertRedirect();

        $this->assertDatabaseMissing('dosens', ['id' => $dosen->id]);
    }

    public function test_deleting_dosen_also_deletes_user_account(): void
    {
        $prodi = Prodi::factory()->create();
        $dosen = Dosen::factory()->create(['is_penguji' => true, 'prodi_id' => $prodi->id]);
        $user  = User::factory()->create(['role' => 'penguji', 'dosen_id' => $dosen->id]);

        $this->actingAs($this->admin())
            ->delete(route('admin.dosen.destroy', $dosen))
            ->assertRedirect();

        $this->assertDatabaseMissing('dosens', ['id' => $dosen->id]);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    // ══════════════════════════════════════════════════════════════
    // DOSEN — import
    // ══════════════════════════════════════════════════════════════

    public function test_admin_can_import_dosen_from_xlsx(): void
    {
        Excel::fake();
        $prodi = Prodi::factory()->create();
        $file  = UploadedFile::fake()->create('dosen.xlsx', 100,
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $this->actingAs($this->admin())
            ->post(route('admin.dosen.import', $prodi), ['file' => $file])
            ->assertRedirect();
    }

    public function test_dosen_import_rejects_non_excel_file(): void
    {
        $prodi = Prodi::factory()->create();
        $file  = UploadedFile::fake()->create('dosen.pdf', 100, 'application/pdf');

        $this->actingAs($this->admin())
            ->post(route('admin.dosen.import', $prodi), ['file' => $file])
            ->assertSessionHasErrors('file');
    }

    public function test_dosen_import_requires_file(): void
    {
        $prodi = Prodi::factory()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.dosen.import', $prodi), [])
            ->assertSessionHasErrors('file');
    }
}
