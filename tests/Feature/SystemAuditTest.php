<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\JadwalSeleksi;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Pelamar;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemAuditTest extends TestCase
{
    use RefreshDatabase;

    // ═══════════════════════════════════════════════════════════
    // SECTION 1: GUEST ACCESS — semua route harus redirect ke login
    // ═══════════════════════════════════════════════════════════

    public function test_guest_cannot_access_admin_routes(): void
    {
        $prodi = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);

        $routes = [
            '/admin/dashboard',
            '/admin/prodi',
            '/admin/lowongan',
            '/admin/penguji',
            '/admin/pelamar',
            '/admin/user',
            '/admin/jadwal',
            '/admin/lowongan/' . $lowongan->id . '/lamaran',
        ];

        foreach ($routes as $route) {
            $this->get($route)->assertRedirect('/login', "Guest should be redirected from {$route}");
        }
    }

    public function test_guest_cannot_access_pelamar_routes(): void
    {
        $routes = [
            '/pelamar/dashboard',
            '/pelamar/profil',
            '/pelamar/lowongan',
            '/pelamar/history',
        ];

        foreach ($routes as $route) {
            $this->get($route)->assertRedirect('/login', "Guest should be redirected from {$route}");
        }
    }

    public function test_guest_cannot_access_penguji_routes(): void
    {
        $routes = ['/penguji/dashboard', '/penguji/pengujian'];
        foreach ($routes as $route) {
            $this->get($route)->assertRedirect('/login', "Guest should be redirected from {$route}");
        }
    }

    public function test_guest_cannot_access_kaprodi_routes(): void
    {
        $routes = ['/kaprodi/dashboard', '/kaprodi/pelamar'];
        foreach ($routes as $route) {
            $this->get($route)->assertRedirect('/login', "Guest should be redirected from {$route}");
        }
    }

    // ═══════════════════════════════════════════════════════════
    // SECTION 2: CROSS-ROLE ACCESS — role isolation
    // ═══════════════════════════════════════════════════════════

    public function test_pelamar_cannot_access_admin_area(): void
    {
        $user = User::factory()->create(['role' => 'pelamar']);
        $this->actingAs($user)->get('/admin/dashboard')
             ->assertRedirect(route('pelamar.dashboard'));
    }

    public function test_pelamar_cannot_access_penguji_area(): void
    {
        $user = User::factory()->create(['role' => 'pelamar']);
        $this->actingAs($user)->get('/penguji/dashboard')
             ->assertRedirect(route('pelamar.dashboard'));
    }

    public function test_pelamar_cannot_access_kaprodi_area(): void
    {
        $user = User::factory()->create(['role' => 'pelamar']);
        $this->actingAs($user)->get('/kaprodi/dashboard')
             ->assertRedirect(route('pelamar.dashboard'));
    }

    public function test_penguji_cannot_access_admin_area(): void
    {
        $user = User::factory()->create(['role' => 'penguji']);
        $this->actingAs($user)->get('/admin/dashboard')
             ->assertRedirect(route('penguji.dashboard'));
    }

    public function test_penguji_cannot_access_pelamar_area(): void
    {
        $user = User::factory()->create(['role' => 'penguji']);
        $this->actingAs($user)->get('/pelamar/dashboard')
             ->assertRedirect(route('penguji.dashboard'));
    }

    public function test_kaprodi_cannot_access_admin_area(): void
    {
        $user = User::factory()->create(['role' => 'kaprodi']);
        $this->actingAs($user)->get('/admin/dashboard')
             ->assertRedirect(route('kaprodi.dashboard'));
    }

    public function test_admin_cannot_access_pelamar_area(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user)->get('/pelamar/dashboard')
             ->assertRedirect(route('admin.dashboard'));
    }

    // ═══════════════════════════════════════════════════════════
    // SECTION 3: DASHBOARD REDIRECT — /dashboard harus redirect benar
    // ═══════════════════════════════════════════════════════════

    public function test_dashboard_redirects_admin_correctly(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user)->get('/dashboard')
             ->assertRedirect(route('admin.dashboard'));
    }

    public function test_dashboard_redirects_pelamar_correctly(): void
    {
        $user = User::factory()->create(['role' => 'pelamar']);
        $pelamar = Pelamar::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user)->get('/dashboard')
             ->assertRedirect(route('pelamar.dashboard'));
    }

    public function test_dashboard_redirects_penguji_correctly(): void
    {
        $user = User::factory()->create(['role' => 'penguji']);
        $this->actingAs($user)->get('/dashboard')
             ->assertRedirect(route('penguji.dashboard'));
    }

    public function test_dashboard_redirects_kaprodi_correctly(): void
    {
        $user = User::factory()->create(['role' => 'kaprodi']);
        $this->actingAs($user)->get('/dashboard')
             ->assertRedirect(route('kaprodi.dashboard'));
    }

    // ═══════════════════════════════════════════════════════════
    // SECTION 4: ADMIN PAGES — 200 OK, no errors
    // ═══════════════════════════════════════════════════════════

    public function test_admin_dashboard_loads(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
    }

    public function test_admin_prodi_index_loads(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get(route('admin.prodi.index'))->assertOk();
    }

    public function test_admin_lowongan_index_loads(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get(route('admin.lowongan.index'))->assertOk();
    }

    public function test_admin_penguji_index_loads(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get(route('admin.penguji.index'))->assertOk();
    }

    public function test_admin_pelamar_index_loads(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get(route('admin.pelamar.index'))->assertOk();
    }

    public function test_admin_user_index_loads(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get(route('admin.user.index'))->assertOk();
    }

    public function test_admin_jadwal_index_loads(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get(route('admin.jadwal.index'))->assertOk();
    }

    public function test_admin_lamaran_index_loads(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $prodi = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
        $this->actingAs($admin)->get(route('admin.lamaran.index', $lowongan))->assertOk();
    }

    // ═══════════════════════════════════════════════════════════
    // SECTION 5: PELAMAR PAGES — 200 OK
    // ═══════════════════════════════════════════════════════════

    public function test_pelamar_dashboard_loads(): void
    {
        $user = User::factory()->create(['role' => 'pelamar']);
        Pelamar::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user)->get(route('pelamar.dashboard'))->assertOk();
    }

    public function test_pelamar_profil_loads(): void
    {
        $user = User::factory()->create(['role' => 'pelamar']);
        Pelamar::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user)->get(route('pelamar.profil.index'))->assertOk();
    }

    public function test_pelamar_lowongan_index_loads(): void
    {
        $user = User::factory()->create(['role' => 'pelamar']);
        Pelamar::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user)->get(route('pelamar.lowongan.index'))->assertOk();
    }

    public function test_pelamar_history_loads(): void
    {
        $user = User::factory()->create(['role' => 'pelamar']);
        Pelamar::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user)->get(route('pelamar.history.index'))->assertOk();
    }

    // ═══════════════════════════════════════════════════════════
    // SECTION 6: PENGUJI PAGES — 200 OK
    // ═══════════════════════════════════════════════════════════

    public function test_penguji_dashboard_loads(): void
    {
        $prodi = Prodi::factory()->create();
        $dosen = Dosen::factory()->create(['is_penguji' => true, 'prodi_id' => $prodi->id]);
        $user = User::factory()->create(['role' => 'penguji', 'dosen_id' => $dosen->id]);
        $this->actingAs($user)->get(route('penguji.dashboard'))->assertOk();
    }

    public function test_penguji_pengujian_index_loads(): void
    {
        $prodi = Prodi::factory()->create();
        $dosen = Dosen::factory()->create(['is_penguji' => true, 'prodi_id' => $prodi->id]);
        $user = User::factory()->create(['role' => 'penguji', 'dosen_id' => $dosen->id]);
        $this->actingAs($user)->get(route('penguji.pengujian.index'))->assertOk();
    }

    // ═══════════════════════════════════════════════════════════
    // SECTION 7: KAPRODI PAGES — 200 OK
    // ═══════════════════════════════════════════════════════════

    public function test_kaprodi_dashboard_loads(): void
    {
        $prodi = Prodi::factory()->create();
        $dosen = Dosen::factory()->create(['is_kaprodi' => true, 'prodi_id' => $prodi->id]);
        $user = User::factory()->create(['role' => 'kaprodi', 'dosen_id' => $dosen->id, 'prodi_id' => $prodi->id]);
        $this->actingAs($user)->get(route('kaprodi.dashboard'))->assertOk();
    }

    public function test_kaprodi_pelamar_index_loads(): void
    {
        $prodi = Prodi::factory()->create();
        $dosen = Dosen::factory()->create(['is_kaprodi' => true, 'prodi_id' => $prodi->id]);
        $user = User::factory()->create(['role' => 'kaprodi', 'dosen_id' => $dosen->id, 'prodi_id' => $prodi->id]);
        $this->actingAs($user)->get(route('kaprodi.pelamar.index'))->assertOk();
    }

    // ═══════════════════════════════════════════════════════════
    // SECTION 8: DATA ISOLATION — pelamar hanya lihat datanya sendiri
    // ═══════════════════════════════════════════════════════════

    public function test_pelamar_cannot_view_other_pelamar_history(): void
    {
        $prodi = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);

        $user1 = User::factory()->create(['role' => 'pelamar']);
        $pelamar1 = Pelamar::factory()->create(['user_id' => $user1->id]);

        $user2 = User::factory()->create(['role' => 'pelamar']);
        $pelamar2 = Pelamar::factory()->create(['user_id' => $user2->id]);

        $lamaran2 = Lamaran::factory()->create([
            'pelamar_id' => $pelamar2->id,
            'lowongan_id' => $lowongan->id,
        ]);

        // User1 trying to access user2's lamaran
        $this->actingAs($user1)
             ->get(route('pelamar.history.show', $lamaran2))
             ->assertStatus(403);
    }

    // ═══════════════════════════════════════════════════════════
    // SECTION 9: KAPRODI ISOLATION — hanya lihat prodi sendiri
    // ═══════════════════════════════════════════════════════════

    public function test_kaprodi_cannot_view_pelamar_from_other_prodi(): void
    {
        $prodi1 = Prodi::factory()->create();
        $prodi2 = Prodi::factory()->create();

        $dosen = Dosen::factory()->create(['is_kaprodi' => true, 'prodi_id' => $prodi1->id]);
        $kUser = User::factory()->create([
            'role' => 'kaprodi', 'dosen_id' => $dosen->id, 'prodi_id' => $prodi1->id,
        ]);

        $lowongan2 = Lowongan::factory()->create(['prodi_id' => $prodi2->id]);
        $pUser = User::factory()->create(['role' => 'pelamar']);
        $pelamar2 = Pelamar::factory()->create(['user_id' => $pUser->id]);
        Lamaran::factory()->create(['pelamar_id' => $pelamar2->id, 'lowongan_id' => $lowongan2->id]);

        $this->actingAs($kUser)
             ->get(route('kaprodi.pelamar.show', $pelamar2))
             ->assertStatus(403);
    }

    // ═══════════════════════════════════════════════════════════
    // SECTION 10: PENGUJI ISOLATION — hanya akses jadwal sendiri
    // ═══════════════════════════════════════════════════════════

    public function test_penguji_cannot_access_other_penguji_jadwal(): void
    {
        $prodi = Prodi::factory()->create();
        $dosen1 = Dosen::factory()->create(['is_penguji' => true, 'prodi_id' => $prodi->id]);
        $dosen2 = Dosen::factory()->create(['is_penguji' => true, 'prodi_id' => $prodi->id]);
        $user1 = User::factory()->create(['role' => 'penguji', 'dosen_id' => $dosen1->id]);
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
        $pUser = User::factory()->create(['role' => 'pelamar']);
        $pelamar = Pelamar::factory()->create(['user_id' => $pUser->id]);

        $jadwal = JadwalSeleksi::factory()->create([
            'penguji_id' => $dosen2->id,
            'pelamar_id' => $pelamar->id,
            'lowongan_id' => $lowongan->id,
        ]);

        $this->actingAs($user1)
             ->get(route('penguji.pengujian.show', $jadwal))
             ->assertStatus(403);
    }

    // ═══════════════════════════════════════════════════════════
    // SECTION 11: BUSINESS RULES
    // ═══════════════════════════════════════════════════════════

    public function test_pelamar_cannot_apply_twice_to_same_lowongan(): void
    {
        $prodi = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id, 'kuota' => 5]);
        $user = User::factory()->create(['role' => 'pelamar']);
        $pelamar = Pelamar::factory()->create(['user_id' => $user->id]);

        Lamaran::factory()->create([
            'pelamar_id' => $pelamar->id,
            'lowongan_id' => $lowongan->id,
        ]);

        // Try to access apply page again
        $this->actingAs($user)
             ->get(route('pelamar.lowongan.apply', $lowongan))
             ->assertRedirect(route('pelamar.history.index'));
    }

    public function test_lowongan_full_blocks_new_applications(): void
    {
        $prodi = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id, 'kuota' => 1]);

        // Fill the quota
        $existingUser = User::factory()->create(['role' => 'pelamar']);
        $existingPelamar = Pelamar::factory()->create(['user_id' => $existingUser->id]);
        Lamaran::factory()->create([
            'pelamar_id' => $existingPelamar->id,
            'lowongan_id' => $lowongan->id,
            'status' => 'menunggu',
        ]);

        // New pelamar tries to apply
        $user = User::factory()->create(['role' => 'pelamar']);
        $pelamar = Pelamar::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
             ->get(route('pelamar.lowongan.apply', $lowongan))
             ->assertRedirect();
    }

    public function test_expired_lowongan_status_is_ditutup(): void
    {
        $prodi = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create([
            'prodi_id' => $prodi->id,
            'status' => 'aktif',
            'tanggal_tutup' => now()->subDay()->format('Y-m-d'),
        ]);

        $this->assertEquals('ditutup', $lowongan->status);
    }

    public function test_ditolak_lamaran_does_not_fill_kuota(): void
    {
        $prodi = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id, 'kuota' => 1]);
        $pUser = User::factory()->create(['role' => 'pelamar']);
        $pelamar = Pelamar::factory()->create(['user_id' => $pUser->id]);

        Lamaran::factory()->create([
            'pelamar_id' => $pelamar->id,
            'lowongan_id' => $lowongan->id,
            'status' => 'ditolak',
        ]);

        $lowongan->refresh();
        $this->assertFalse($lowongan->isFull());
        $this->assertEquals(1, $lowongan->sisa_kuota);
    }

    // ═══════════════════════════════════════════════════════════
    // SECTION 12: SNAPSHOT DATA INTEGRITY
    // ═══════════════════════════════════════════════════════════

    public function test_effective_pelamar_uses_snapshot_when_available(): void
    {
        $prodi = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
        $pUser = User::factory()->create(['role' => 'pelamar']);
        $pelamar = Pelamar::factory()->create(['user_id' => $pUser->id, 'nama' => 'Original Name']);

        $lamaran = Lamaran::factory()->create([
            'pelamar_id' => $pelamar->id,
            'lowongan_id' => $lowongan->id,
            'snapshot_data' => ['nama' => 'Snapshot Name', 'jenjang' => 'S2'],
        ]);

        $effective = $lamaran->effectivePelamar;
        $this->assertEquals('Snapshot Name', $effective->nama);
        $this->assertEquals('S2', $effective->jenjang);
    }

    public function test_effective_pelamar_falls_back_to_live_when_no_snapshot(): void
    {
        $prodi = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
        $pUser = User::factory()->create(['role' => 'pelamar']);
        $pelamar = Pelamar::factory()->create(['user_id' => $pUser->id, 'nama' => 'Live Name']);

        $lamaran = Lamaran::factory()->create([
            'pelamar_id' => $pelamar->id,
            'lowongan_id' => $lowongan->id,
            'snapshot_data' => null,
        ]);

        $effective = $lamaran->effectivePelamar;
        $this->assertEquals('Live Name', $effective->nama);
    }

    // ═══════════════════════════════════════════════════════════
    // SECTION 13: ROLE SWITCH SECURITY
    // ═══════════════════════════════════════════════════════════

    public function test_pelamar_cannot_switch_to_penguji_role(): void
    {
        $user = User::factory()->create(['role' => 'pelamar', 'is_penguji' => false]);

        $this->actingAs($user)
             ->post(route('role.switch'), ['role' => 'penguji'])
             ->assertSessionHasErrors('role');
    }

    public function test_role_switch_only_accepts_valid_roles(): void
    {
        $user = User::factory()->create(['role' => 'penguji', 'is_penguji' => true]);

        $this->actingAs($user)
             ->post(route('role.switch'), ['role' => 'admin'])
             ->assertSessionHasErrors('role');
    }

    // ═══════════════════════════════════════════════════════════
    // SECTION 14: SETTINGS ACCESS
    // ═══════════════════════════════════════════════════════════

    public function test_settings_page_accessible_by_pelamar(): void
    {
        $user = User::factory()->create(['role' => 'pelamar']);
        Pelamar::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user)->get(route('settings.index'))->assertOk();
    }

    public function test_settings_page_not_accessible_by_admin(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user)->get(route('settings.index'))
             ->assertRedirect(route('admin.dashboard'));
    }

    // ═══════════════════════════════════════════════════════════
    // SECTION 15: LANDING & AUTH PAGES
    // ═══════════════════════════════════════════════════════════

    public function test_landing_page_loads(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_login_page_loads(): void
    {
        $this->get('/login')->assertOk();
    }

    public function test_register_page_loads(): void
    {
        $this->get('/register')->assertOk();
    }

    // ═══════════════════════════════════════════════════════════
    // SECTION 16: ADMIN DELETE OPERATIONS — CSRF & method
    // ═══════════════════════════════════════════════════════════

    public function test_admin_can_delete_lamaran(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $prodi = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
        $pUser = User::factory()->create(['role' => 'pelamar']);
        $pelamar = Pelamar::factory()->create(['user_id' => $pUser->id]);
        $lamaran = Lamaran::factory()->create([
            'pelamar_id' => $pelamar->id,
            'lowongan_id' => $lowongan->id,
        ]);

        $this->actingAs($admin)
             ->delete(route('admin.lamaran.destroy', $lamaran))
             ->assertRedirect();

        $this->assertDatabaseMissing('lamarans', ['id' => $lamaran->id]);
    }

    // ═══════════════════════════════════════════════════════════
    // SECTION 17: STATUS LABELS COMPLETENESS
    // ═══════════════════════════════════════════════════════════

    public function test_all_status_labels_are_defined(): void
    {
        $statuses = ['menunggu', 'seleksi_tahap1', 'seleksi_tahap2', 'diterima', 'ditolak'];
        foreach ($statuses as $status) {
            $this->assertArrayHasKey($status, Lamaran::STATUS_LABELS);
            $this->assertNotEmpty(Lamaran::STATUS_LABELS[$status]);
        }
    }

    // ═══════════════════════════════════════════════════════════
    // SECTION 18: NOTIFIKASI ACCESS
    // ═══════════════════════════════════════════════════════════

    public function test_notifikasi_page_requires_auth(): void
    {
        $this->get('/notifikasi')->assertRedirect('/login');
    }

    public function test_notifikasi_accessible_by_any_authenticated_user(): void
    {
        $user = User::factory()->create(['role' => 'pelamar']);
        Pelamar::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user)->get(route('notifikasi.index'))->assertOk();
    }

    // ═══════════════════════════════════════════════════════════
    // SECTION 19: PASSWORD SECURITY
    // ═══════════════════════════════════════════════════════════

    public function test_password_change_requires_correct_old_password(): void
    {
        $user = User::factory()->create(['role' => 'pelamar', 'password' => bcrypt('oldpass123')]);
        Pelamar::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
             ->put(route('settings.password.update'), [
                 'current_password' => 'wrongpassword',
                 'password' => 'newpass123',
                 'password_confirmation' => 'newpass123',
             ])
             ->assertSessionHasErrors('current_password');
    }
}
