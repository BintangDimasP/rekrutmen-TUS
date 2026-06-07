<?php

namespace Tests\Feature;

use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Pelamar;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Iterasi3Test extends TestCase
{
    use RefreshDatabase;

    // ═══════════════════════════════════════════════════════════
    // PENGELOLAAN PROFIL PELAMAR
    // ═══════════════════════════════════════════════════════════

    public function test_pelamar_profil_page_loads(): void
    {
        $user = User::factory()->create(['role' => 'pelamar']);
        Pelamar::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user)->get(route('pelamar.profil.index'))->assertOk();
    }

    public function test_pelamar_can_update_profil(): void
    {
        $user = User::factory()->create(['role' => 'pelamar']);
        $pelamar = Pelamar::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->put(route('pelamar.profil.update'), [
            'email' => $user->email,
            'nik' => '3201234567890123',
            'nama' => 'Updated Name',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1995-01-01',
            'no_telepon' => '081234567890',
            'jenis_kelamin' => 'L',
            'kewarganegaraan' => 'Indonesia',
            'status_pernikahan' => 'Belum Menikah',
            'alamat_domisili' => 'Jl. Test',
            'alamat_ktp' => 'Jl. Test',
        ])->assertRedirect(route('pelamar.profil.index'));

        $this->assertDatabaseHas('pelamars', ['id' => $pelamar->id, 'nama' => 'Updated Name']);
    }

    public function test_profil_rejects_dosen_internal_email(): void
    {
        $user = User::factory()->create(['role' => 'pelamar']);
        $pelamar = Pelamar::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->put(route('pelamar.profil.update'), [
            'email' => 'test@pengajar.telkomuniversity.ac.id',
            'nik' => $pelamar->nik, 'nama' => $pelamar->nama,
            'tempat_lahir' => 'X', 'tanggal_lahir' => '1995-01-01',
            'no_telepon' => $pelamar->no_telepon, 'jenis_kelamin' => 'L',
            'kewarganegaraan' => 'Indonesia', 'status_pernikahan' => 'Belum Menikah',
            'alamat_domisili' => 'X', 'alamat_ktp' => 'X',
        ])->assertSessionHasErrors('email');
    }

    public function test_profil_rejects_duplicate_nik(): void
    {
        $user1 = User::factory()->create(['role' => 'pelamar']);
        Pelamar::factory()->create(['user_id' => $user1->id, 'nik' => '1111111111111111']);

        $user2 = User::factory()->create(['role' => 'pelamar']);
        $pelamar2 = Pelamar::factory()->create(['user_id' => $user2->id]);

        $this->actingAs($user2)->put(route('pelamar.profil.update'), [
            'email' => $user2->email, 'nik' => '1111111111111111',
            'nama' => 'X', 'tempat_lahir' => 'X', 'tanggal_lahir' => '1995-01-01',
            'no_telepon' => $pelamar2->no_telepon, 'jenis_kelamin' => 'L',
            'kewarganegaraan' => 'Indonesia', 'status_pernikahan' => 'Belum Menikah',
            'alamat_domisili' => 'X', 'alamat_ktp' => 'X',
        ])->assertSessionHasErrors('nik');
    }

    // ═══════════════════════════════════════════════════════════
    // MELIHAT DAN MELAMAR LOWONGAN OLEH PELAMAR
    // ═══════════════════════════════════════════════════════════

    public function test_pelamar_lowongan_index_loads(): void
    {
        $user = User::factory()->create(['role' => 'pelamar']);
        Pelamar::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user)->get(route('pelamar.lowongan.index'))->assertOk();
    }

    public function test_pelamar_cannot_apply_twice_to_same_lowongan(): void
    {
        $prodi = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id, 'kuota' => 5]);
        $user = User::factory()->create(['role' => 'pelamar']);
        $pelamar = Pelamar::factory()->create(['user_id' => $user->id]);
        Lamaran::factory()->create(['pelamar_id' => $pelamar->id, 'lowongan_id' => $lowongan->id]);

        $this->actingAs($user)->get(route('pelamar.lowongan.apply', $lowongan))
             ->assertRedirect(route('pelamar.history.index'));
    }

    public function test_lowongan_full_blocks_new_application(): void
    {
        $prodi = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id, 'kuota' => 1]);
        $existingUser = User::factory()->create(['role' => 'pelamar']);
        $existingPelamar = Pelamar::factory()->create(['user_id' => $existingUser->id]);
        Lamaran::factory()->create(['pelamar_id' => $existingPelamar->id, 'lowongan_id' => $lowongan->id, 'status' => 'menunggu']);

        $user = User::factory()->create(['role' => 'pelamar']);
        Pelamar::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->get(route('pelamar.lowongan.apply', $lowongan))->assertRedirect();
    }

    // ═══════════════════════════════════════════════════════════
    // RIWAYAT LAMARAN PELAMAR
    // ═══════════════════════════════════════════════════════════

    public function test_pelamar_history_page_loads(): void
    {
        $user = User::factory()->create(['role' => 'pelamar']);
        Pelamar::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user)->get(route('pelamar.history.index'))->assertOk();
    }

    public function test_pelamar_cannot_view_other_pelamar_history(): void
    {
        $prodi = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
        $user1 = User::factory()->create(['role' => 'pelamar']);
        Pelamar::factory()->create(['user_id' => $user1->id]);
        $user2 = User::factory()->create(['role' => 'pelamar']);
        $pelamar2 = Pelamar::factory()->create(['user_id' => $user2->id]);
        $lamaran2 = Lamaran::factory()->create(['pelamar_id' => $pelamar2->id, 'lowongan_id' => $lowongan->id]);

        $this->actingAs($user1)->get(route('pelamar.history.show', $lamaran2))->assertStatus(403);
    }

    public function test_pelamar_can_withdraw_from_lamaran(): void
    {
        $prodi = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
        $user = User::factory()->create(['role' => 'pelamar']);
        $pelamar = Pelamar::factory()->create(['user_id' => $user->id]);
        $lamaran = Lamaran::factory()->create(['pelamar_id' => $pelamar->id, 'lowongan_id' => $lowongan->id, 'status' => 'menunggu']);

        $this->actingAs($user)->put(route('pelamar.history.withdraw', $lamaran))
             ->assertRedirect()
             ->assertSessionHas('success');

        $this->assertDatabaseHas('lamarans', ['id' => $lamaran->id, 'status' => 'mengundurkan_diri']);
    }

    // ═══════════════════════════════════════════════════════════
    // MANAJEMEN LAMARAN OLEH ADMIN (CRUD)
    // ═══════════════════════════════════════════════════════════

    public function test_admin_lamaran_index_loads(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $prodi = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
        $this->actingAs($admin)->get(route('admin.lamaran.index', $lowongan))->assertOk();
    }

    public function test_admin_can_update_lamaran_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $prodi = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
        $pUser = User::factory()->create(['role' => 'pelamar']);
        $pelamar = Pelamar::factory()->create(['user_id' => $pUser->id]);
        $lamaran = Lamaran::factory()->create(['pelamar_id' => $pelamar->id, 'lowongan_id' => $lowongan->id]);

        $this->actingAs($admin)->put(route('admin.lamaran.update', $lamaran), [
            'status' => 'seleksi_tahap1',
        ])->assertRedirect();

        $this->assertDatabaseHas('lamarans', ['id' => $lamaran->id, 'status' => 'seleksi_tahap1']);
    }

    public function test_admin_can_delete_lamaran(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $prodi = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
        $pUser = User::factory()->create(['role' => 'pelamar']);
        $pelamar = Pelamar::factory()->create(['user_id' => $pUser->id]);
        $lamaran = Lamaran::factory()->create(['pelamar_id' => $pelamar->id, 'lowongan_id' => $lowongan->id]);

        $this->actingAs($admin)->delete(route('admin.lamaran.destroy', $lamaran))->assertRedirect();
        $this->assertDatabaseMissing('lamarans', ['id' => $lamaran->id]);
    }

    public function test_all_status_labels_are_defined(): void
    {
        $statuses = ['menunggu', 'seleksi_tahap1', 'seleksi_tahap2', 'diterima', 'ditolak', 'mengundurkan_diri'];
        foreach ($statuses as $status) {
            $this->assertArrayHasKey($status, Lamaran::STATUS_LABELS);
        }
    }

    public function test_snapshot_uses_data_saat_melamar(): void
    {
        $prodi = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
        $pUser = User::factory()->create(['role' => 'pelamar']);
        $pelamar = Pelamar::factory()->create(['user_id' => $pUser->id, 'nama' => 'Original']);
        $lamaran = Lamaran::factory()->create([
            'pelamar_id' => $pelamar->id, 'lowongan_id' => $lowongan->id,
            'snapshot_data' => ['nama' => 'Snapshot Name'],
        ]);

        $this->assertEquals('Snapshot Name', $lamaran->effectivePelamar->nama);
    }

    public function test_ditolak_lamaran_does_not_fill_kuota(): void
    {
        $prodi = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id, 'kuota' => 1]);
        $pUser = User::factory()->create(['role' => 'pelamar']);
        $pelamar = Pelamar::factory()->create(['user_id' => $pUser->id]);
        Lamaran::factory()->create(['pelamar_id' => $pelamar->id, 'lowongan_id' => $lowongan->id, 'status' => 'ditolak']);

        $lowongan->refresh();
        $this->assertFalse($lowongan->isFull());
        $this->assertEquals(1, $lowongan->sisa_kuota);
    }

    public function test_mengundurkan_diri_lamaran_does_not_fill_kuota(): void
    {
        $prodi = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id, 'kuota' => 1]);
        $pUser = User::factory()->create(['role' => 'pelamar']);
        $pelamar = Pelamar::factory()->create(['user_id' => $pUser->id]);
        Lamaran::factory()->create(['pelamar_id' => $pelamar->id, 'lowongan_id' => $lowongan->id, 'status' => 'mengundurkan_diri']);

        $lowongan->refresh();
        $this->assertFalse($lowongan->isFull());
        $this->assertEquals(1, $lowongan->sisa_kuota);
    }
}
