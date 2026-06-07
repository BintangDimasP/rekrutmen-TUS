<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Lowongan;
use App\Models\Notifikasi;
use App\Models\Pelamar;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Iterasi5Test extends TestCase
{
    use RefreshDatabase;

    // ═══════════════════════════════════════════════════════════
    // NOTIFIKASI PELAMAR DAN PENGUJI
    // ═══════════════════════════════════════════════════════════

    public function test_notifikasi_requires_auth(): void
    {
        $this->get('/notifikasi')->assertRedirect('/login');
    }

    public function test_notifikasi_accessible_by_pelamar(): void
    {
        $user = User::factory()->create(['role' => 'pelamar']);
        Pelamar::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user)->get(route('notifikasi.index'))->assertOk();
    }

    public function test_notifikasi_accessible_by_penguji(): void
    {
        $prodi = Prodi::factory()->create();
        $dosen = Dosen::factory()->create(['is_penguji' => true, 'prodi_id' => $prodi->id]);
        $user = User::factory()->create(['role' => 'penguji', 'dosen_id' => $dosen->id]);
        $this->actingAs($user)->get(route('notifikasi.index'))->assertOk();
    }

    public function test_mark_notifikasi_as_read(): void
    {
        $user = User::factory()->create(['role' => 'pelamar']);
        Pelamar::factory()->create(['user_id' => $user->id]);
        $notif = Notifikasi::create([
            'user_id' => $user->id, 'judul' => 'Test', 'pesan' => 'Pesan test',
            'tipe' => 'status', 'dibaca' => false,
        ]);

        $this->actingAs($user)->post(route('notifikasi.baca', $notif))->assertOk();
        $this->assertTrue($notif->fresh()->dibaca);
    }

    public function test_mark_all_notifikasi_as_read(): void
    {
        $user = User::factory()->create(['role' => 'pelamar']);
        Pelamar::factory()->create(['user_id' => $user->id]);
        Notifikasi::create(['user_id' => $user->id, 'judul' => 'A', 'pesan' => 'X', 'tipe' => 'status', 'dibaca' => false]);
        Notifikasi::create(['user_id' => $user->id, 'judul' => 'B', 'pesan' => 'Y', 'tipe' => 'jadwal', 'dibaca' => false]);

        $this->actingAs($user)->post(route('notifikasi.baca.semua'))->assertOk();
        $this->assertEquals(0, Notifikasi::where('user_id', $user->id)->where('dibaca', false)->count());
    }

    public function test_user_cannot_mark_other_user_notifikasi(): void
    {
        $user1 = User::factory()->create(['role' => 'pelamar']);
        Pelamar::factory()->create(['user_id' => $user1->id]);
        $user2 = User::factory()->create(['role' => 'pelamar']);
        Pelamar::factory()->create(['user_id' => $user2->id]);

        $notif = Notifikasi::create([
            'user_id' => $user2->id, 'judul' => 'Test', 'pesan' => 'X',
            'tipe' => 'status', 'dibaca' => false,
        ]);

        $this->actingAs($user1)->post(route('notifikasi.baca', $notif))->assertStatus(403);
    }

    // ═══════════════════════════════════════════════════════════
    // BERITA ACARA HASIL SELEKSI
    // ═══════════════════════════════════════════════════════════

    public function test_berita_acara_page_loads(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $prodi = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
        $this->actingAs($admin)->get(route('admin.lowongan.beritaAcara', $lowongan))->assertOk();
    }

    // ═══════════════════════════════════════════════════════════
    // SWITCH ROLE DOSEN RANGKAP
    // ═══════════════════════════════════════════════════════════

    public function test_dosen_rangkap_can_switch_to_penguji(): void
    {
        $prodi = Prodi::factory()->create();
        $dosen = Dosen::factory()->create(['is_penguji' => true, 'is_kaprodi' => true, 'prodi_id' => $prodi->id]);
        $user = User::factory()->create([
            'role' => 'kaprodi', 'dosen_id' => $dosen->id, 'prodi_id' => $prodi->id,
            'is_penguji' => true, 'is_kaprodi' => true,
        ]);

        $this->actingAs($user)->post(route('role.switch'), ['role' => 'penguji'])
             ->assertRedirect(route('penguji.dashboard'));
        $this->assertEquals('penguji', $user->fresh()->role);
    }

    public function test_dosen_rangkap_can_switch_to_kaprodi(): void
    {
        $prodi = Prodi::factory()->create();
        $dosen = Dosen::factory()->create(['is_penguji' => true, 'is_kaprodi' => true, 'prodi_id' => $prodi->id]);
        $user = User::factory()->create([
            'role' => 'penguji', 'dosen_id' => $dosen->id, 'prodi_id' => $prodi->id,
            'is_penguji' => true, 'is_kaprodi' => true,
        ]);

        $this->actingAs($user)->post(route('role.switch'), ['role' => 'kaprodi'])
             ->assertRedirect(route('kaprodi.dashboard'));
        $this->assertEquals('kaprodi', $user->fresh()->role);
    }

    public function test_pelamar_cannot_switch_to_penguji(): void
    {
        $user = User::factory()->create(['role' => 'pelamar', 'is_penguji' => false]);
        $this->actingAs($user)->post(route('role.switch'), ['role' => 'penguji'])
             ->assertSessionHasErrors('role');
    }

    public function test_role_switch_rejects_admin_role(): void
    {
        $user = User::factory()->create(['role' => 'penguji', 'is_penguji' => true]);
        $this->actingAs($user)->post(route('role.switch'), ['role' => 'admin'])
             ->assertSessionHasErrors('role');
    }

    public function test_penguji_only_cannot_switch_to_kaprodi(): void
    {
        $prodi = Prodi::factory()->create();
        $dosen = Dosen::factory()->create(['is_penguji' => true, 'is_kaprodi' => false, 'prodi_id' => $prodi->id]);
        $user = User::factory()->create([
            'role' => 'penguji', 'dosen_id' => $dosen->id,
            'is_penguji' => true, 'is_kaprodi' => false,
        ]);

        $this->actingAs($user)->post(route('role.switch'), ['role' => 'kaprodi'])
             ->assertSessionHasErrors('role');
    }

    // ═══════════════════════════════════════════════════════════
    // LANDING PAGE
    // ═══════════════════════════════════════════════════════════

    public function test_landing_page_loads(): void
    {
        $this->get('/')->assertOk();
    }
}
