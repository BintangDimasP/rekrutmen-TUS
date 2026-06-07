<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Lowongan;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Iterasi2Test extends TestCase
{
    use RefreshDatabase;

    // ═══════════════════════════════════════════════════════════
    // MANAJEMEN PENGGUNA OLEH ADMIN
    // ═══════════════════════════════════════════════════════════

    public function test_admin_user_index_loads(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get(route('admin.user.index'))->assertOk();
    }

    public function test_admin_can_delete_other_admin_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin2 = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->delete(route('admin.user.destroy', $admin2))
             ->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $admin2->id]);
    }

    public function test_admin_cannot_delete_self(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->delete(route('admin.user.destroy', $admin))
             ->assertSessionHasErrors('delete');
    }

    public function test_admin_can_delete_pelamar_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $pelamarUser = User::factory()->create(['role' => 'pelamar']);

        $this->actingAs($admin)->delete(route('admin.user.destroy', $pelamarUser))->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $pelamarUser->id]);
    }

    public function test_admin_can_update_user_password(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $pelamarUser = User::factory()->create(['role' => 'pelamar']);

        $this->actingAs($admin)->put(route('admin.user.update', $pelamarUser), [
            'password' => 'newpassword123',
        ])->assertRedirect();
    }

    // ═══════════════════════════════════════════════════════════
    // MANAJEMEN PROGRAM STUDI OLEH ADMIN (CRUD)
    // ═══════════════════════════════════════════════════════════

    public function test_admin_prodi_index_loads(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get(route('admin.prodi.index'))->assertOk();
    }

    public function test_admin_can_create_prodi(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.prodi.store'), [
            'nama' => 'Teknik Informatika',
            'kode' => 'TI',
        ])->assertRedirect(route('admin.prodi.index'));

        $this->assertDatabaseHas('prodis', ['kode' => 'TI']);
    }

    public function test_admin_can_update_prodi(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $prodi = Prodi::factory()->create();

        $this->actingAs($admin)->put(route('admin.prodi.update', $prodi), [
            'nama' => 'Updated Name',
            'kode' => $prodi->kode,
        ])->assertRedirect(route('admin.prodi.index'));

        $this->assertDatabaseHas('prodis', ['id' => $prodi->id, 'nama' => 'Updated Name']);
    }

    public function test_admin_can_delete_prodi(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $prodi = Prodi::factory()->create();

        $this->actingAs($admin)->delete(route('admin.prodi.destroy', $prodi))->assertRedirect();
        $this->assertDatabaseMissing('prodis', ['id' => $prodi->id]);
    }

    public function test_prodi_kode_must_be_unique(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Prodi::factory()->create(['kode' => 'TI']);

        $this->actingAs($admin)->post(route('admin.prodi.store'), [
            'nama' => 'Another', 'kode' => 'TI',
        ])->assertSessionHasErrors('kode');
    }

    // ═══════════════════════════════════════════════════════════
    // MANAJEMEN DOSEN BERDASARKAN PRODI (CRUD)
    // ═══════════════════════════════════════════════════════════

    public function test_admin_prodi_show_loads(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $prodi = Prodi::factory()->create();
        $this->actingAs($admin)->get(route('admin.prodi.show', $prodi))->assertOk();
    }

    public function test_admin_can_add_dosen_to_prodi(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $prodi = Prodi::factory()->create();

        $this->actingAs($admin)->post(route('admin.dosen.store', $prodi), [
            'nama' => 'Dr. Test', 'kode' => 'TST', 'nip' => '12345678901234',
        ])->assertRedirect();

        $this->assertDatabaseHas('dosens', ['kode' => 'TST', 'prodi_id' => $prodi->id]);
    }

    public function test_dosen_kode_must_be_unique(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $prodi = Prodi::factory()->create();
        Dosen::factory()->create(['kode' => 'DUP', 'prodi_id' => $prodi->id]);

        $this->actingAs($admin)->post(route('admin.dosen.store', $prodi), [
            'nama' => 'X', 'kode' => 'DUP',
        ])->assertSessionHasErrors('kode');
    }

    // ═══════════════════════════════════════════════════════════
    // MANAJEMEN PENGUJI OLEH ADMIN
    // ═══════════════════════════════════════════════════════════

    public function test_admin_penguji_index_loads(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get(route('admin.penguji.index'))->assertOk();
    }

    public function test_admin_can_assign_dosen_as_penguji(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $prodi = Prodi::factory()->create();
        $dosen = Dosen::factory()->create(['prodi_id' => $prodi->id, 'is_penguji' => false]);

        $this->actingAs($admin)->post(route('admin.penguji.store'), [
            'dosen_ids' => [$dosen->id],
        ])->assertRedirect();

        $this->assertTrue($dosen->fresh()->is_penguji);
    }

    // ═══════════════════════════════════════════════════════════
    // MANAJEMEN LOWONGAN OLEH ADMIN (CRUD)
    // ═══════════════════════════════════════════════════════════

    public function test_admin_lowongan_index_loads(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get(route('admin.lowongan.index'))->assertOk();
    }

    public function test_admin_can_create_lowongan(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $prodi = Prodi::factory()->create();

        $this->actingAs($admin)->post(route('admin.lowongan.store'), [
            'nama_posisi' => 'Dosen AI',
            'prodi_id' => $prodi->id,
            'jenjang_minimal' => 'S2',
            'minimal_ipk' => 3.0,
            'kuota' => 5,
            'tanggal_tutup' => now()->addDays(30)->format('Y-m-d'),
            'status' => 'aktif',
        ])->assertRedirect(route('admin.lowongan.index'));

        $this->assertDatabaseHas('lowongans', ['nama_posisi' => 'Dosen AI']);
    }

    public function test_admin_can_delete_lowongan(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $prodi = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);

        $this->actingAs($admin)->delete(route('admin.lowongan.destroy', $lowongan))->assertRedirect();
        $this->assertDatabaseMissing('lowongans', ['id' => $lowongan->id]);
    }

    // ═══════════════════════════════════════════════════════════
    // AKSES KONTROL — NON-ADMIN TIDAK BISA AKSES ADMIN AREA
    // ═══════════════════════════════════════════════════════════

    public function test_pelamar_cannot_access_admin_area(): void
    {
        $user = User::factory()->create(['role' => 'pelamar']);
        $this->actingAs($user)->get('/admin/dashboard')
             ->assertRedirect(route('pelamar.dashboard'));
    }

    public function test_penguji_cannot_access_admin_area(): void
    {
        $user = User::factory()->create(['role' => 'penguji']);
        $this->actingAs($user)->get('/admin/dashboard')
             ->assertRedirect(route('penguji.dashboard'));
    }

    public function test_guest_cannot_access_admin_area(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/login');
    }
}
