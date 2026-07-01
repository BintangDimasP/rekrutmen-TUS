<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManajemenPengujiTest extends TestCase
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

    public function test_admin_dapat_melihat_daftar_penguji(): void
    {
        $dosen = Dosen::factory()->create(['prodi_id' => $this->prodi->id, 'is_penguji' => true]);

        $response = $this->actingAs($this->admin)->get(route('admin.penguji.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.penguji.index');
        $response->assertViewHas('pengujis');
        $response->assertViewHas('calonPengujis');
    }

    public function test_non_admin_tidak_bisa_akses_halaman_penguji(): void
    {
        $pelamar = User::factory()->create(['role' => 'pelamar']);

        $response = $this->actingAs($pelamar)->get(route('admin.penguji.index'));

        $response->assertRedirect(route('pelamar.dashboard'));
    }

    // ── SHOW ─────────────────────────────────────────────────────

    public function test_admin_dapat_melihat_detail_penguji(): void
    {
        $dosen = Dosen::factory()->create(['prodi_id' => $this->prodi->id, 'is_penguji' => true]);
        User::factory()->create(['dosen_id' => $dosen->id, 'role' => 'penguji', 'is_penguji' => true]);

        $response = $this->actingAs($this->admin)->get(route('admin.penguji.show', $dosen));

        $response->assertStatus(200);
        $response->assertViewIs('admin.penguji.show');
        $response->assertViewHas('penguji');
    }

    // ── STORE (Tunjuk Penguji) ───────────────────────────────────

    public function test_tunjuk_penguji_berhasil(): void
    {
        $dosen1 = Dosen::factory()->create(['prodi_id' => $this->prodi->id, 'is_penguji' => false]);
        $dosen2 = Dosen::factory()->create(['prodi_id' => $this->prodi->id, 'is_penguji' => false]);

        $response = $this->actingAs($this->admin)->post(route('admin.penguji.store'), [
            'dosen_ids' => [$dosen1->id, $dosen2->id],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $dosen1->refresh();
        $dosen2->refresh();
        $this->assertTrue($dosen1->is_penguji);
        $this->assertTrue($dosen2->is_penguji);
        $this->assertNotNull($dosen1->user);
        $this->assertNotNull($dosen2->user);
    }

    public function test_tunjuk_penguji_membuat_akun_user_dengan_role_penguji(): void
    {
        $dosen = Dosen::factory()->create(['prodi_id' => $this->prodi->id, 'is_penguji' => false]);

        $this->actingAs($this->admin)->post(route('admin.penguji.store'), [
            'dosen_ids' => [$dosen->id],
        ]);

        $dosen->refresh();
        $user = $dosen->user;
        $this->assertNotNull($user);
        $this->assertEquals('penguji', $user->role);
        $this->assertTrue($user->is_penguji);
    }

    public function test_tunjuk_penguji_dosen_kaprodi_tetap_role_kaprodi(): void
    {
        $dosen = Dosen::factory()->create([
            'prodi_id'   => $this->prodi->id,
            'is_penguji' => false,
            'is_kaprodi' => true,
        ]);
        $user = User::factory()->create([
            'dosen_id'   => $dosen->id,
            'role'       => 'kaprodi',
            'is_kaprodi' => true,
            'is_penguji' => false,
            'prodi_id'   => $this->prodi->id,
        ]);

        $this->actingAs($this->admin)->post(route('admin.penguji.store'), [
            'dosen_ids' => [$dosen->id],
        ]);

        $user->refresh();
        // Role tetap kaprodi karena sudah punya role
        $this->assertEquals('kaprodi', $user->role);
        $this->assertTrue($user->is_penguji);
    }

    public function test_tunjuk_penguji_gagal_dosen_ids_kosong(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.penguji.store'), [
            'dosen_ids' => [],
        ]);

        $response->assertSessionHasErrors('dosen_ids');
    }

    public function test_tunjuk_penguji_gagal_dosen_tidak_ada(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.penguji.store'), [
            'dosen_ids' => [99999],
        ]);

        $response->assertSessionHasErrors('dosen_ids.0');
    }

    // ── DESTROY (Cabut Penguji) ──────────────────────────────────

    public function test_cabut_penguji_dosen_biasa_hapus_akun(): void
    {
        $dosen = Dosen::factory()->create([
            'prodi_id'   => $this->prodi->id,
            'is_penguji' => true,
        ]);
        $user = User::factory()->create([
            'dosen_id'   => $dosen->id,
            'role'       => 'penguji',
            'is_penguji' => true,
            'is_kaprodi' => false,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.penguji.destroy', $dosen));

        $response->assertRedirect(route('admin.penguji.index'));
        $response->assertSessionHas('success');

        $dosen->refresh();
        $this->assertFalse($dosen->is_penguji);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_cabut_penguji_dosen_rangkap_kaprodi_role_kembali_kaprodi(): void
    {
        $dosen = Dosen::factory()->create([
            'prodi_id'   => $this->prodi->id,
            'is_penguji' => true,
            'is_kaprodi' => true,
        ]);
        $user = User::factory()->create([
            'dosen_id'   => $dosen->id,
            'role'       => 'kaprodi',
            'is_penguji' => true,
            'is_kaprodi' => true,
            'prodi_id'   => $this->prodi->id,
        ]);

        $this->actingAs($this->admin)->delete(route('admin.penguji.destroy', $dosen));

        $dosen->refresh();
        $user->refresh();
        $this->assertFalse($dosen->is_penguji);
        $this->assertFalse($user->is_penguji);
        $this->assertEquals('kaprodi', $user->role);
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }
}
