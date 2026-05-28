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

/**
 * BugDetectionTest
 *
 * Menguji celah keamanan, halaman duplikat, dan redirect yang salah
 * berdasarkan hasil audit sistem rekrutmen.
 *
 * Bug yang diuji:
 *  [BUG-01] Route admin/api/sesi-pelamar → method apiSesiPelamar tidak ada → 500
 *  [BUG-02] /dashboard dengan role null → RouteNotFoundException → 500
 *  [BUG-03] Pelamar tanpa record Pelamar → null pointer di LowonganController
 *  [BUG-04] Pelamar tanpa record Pelamar → null pointer di HistoryController
 *  [BUG-05] Admin lowongan show → redirect ke lamaran (bukan halaman show)
 *  [BUG-06] Admin tidak bisa akses settings (by design, tapi harus redirect benar)
 *  [BUG-07] Pelamar bisa akses /profile (Breeze) tanpa batasan role
 *  [BUG-08] RoleSwitch: dosen tanpa flag is_penguji tidak bisa switch ke penguji
 *  [BUG-09] RoleSwitch: dosen tanpa flag is_kaprodi tidak bisa switch ke kaprodi
 *  [BUG-10] Pelamar tidak bisa apply ke lowongan yang sudah penuh (via POST)
 *  [BUG-11] Pelamar tidak bisa apply ke lowongan yang sudah ditutup (via POST)
 *  [BUG-12] Penguji tidak bisa submit nilai dua kali untuk jadwal yang sama
 *  [BUG-13] Admin API endpoint sesi-taken-all tanpa parameter → array kosong (bukan error)
 *  [BUG-14] Kaprodi tidak bisa akses settings (harus bisa)
 *  [BUG-15] Notifikasi: pelamar tidak bisa mark notifikasi milik user lain
 */
class BugDetectionTest extends TestCase
{
    use RefreshDatabase;

    // ─────────────────────────────────────────────────────────────────────
    // [BUG-01] Route admin/api/sesi-pelamar → method apiSesiPelamar tidak ada
    // Ekspektasi: harus 500 (method missing) — ini adalah bug nyata di sistem
    // ─────────────────────────────────────────────────────────────────────

    public function test_bug01_admin_api_sesi_pelamar_route_exists_and_responds(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Route ini terdaftar di web.php tapi method apiSesiPelamar TIDAK ADA
        // di JadwalSeleksiController — seharusnya mengembalikan 500
        $response = $this->actingAs($admin)
            ->get(route('admin.api.sesi.pelamar'));

        // Bug: method tidak ada → 500. Jika sudah diperbaiki → 200
        // Test ini mendokumentasikan bug: response TIDAK boleh 200 saat method hilang
        $this->assertNotEquals(
            200,
            $response->status(),
            '[BUG-01] apiSesiPelamar method tidak ada di controller — route akan error 500'
        );
    }

    // ─────────────────────────────────────────────────────────────────────
    // [BUG-02] /dashboard dengan role null → RouteNotFoundException
    // Middleware CheckRole menangkap null role di sub-routes, tapi /dashboard
    // sendiri hanya pakai middleware 'auth' + 'verified', bukan 'role:*'
    // ─────────────────────────────────────────────────────────────────────

    public function test_bug02_dashboard_with_null_role_does_not_throw_500(): void
    {
        // User dengan role null (dosen belum ditunjuk)
        $user = User::factory()->create(['role' => null, 'email_verified_at' => now()]);

        $response = $this->actingAs($user)->get('/dashboard');

        // Seharusnya tidak 500 — harus redirect atau error yang terkontrol
        $this->assertNotEquals(
            500,
            $response->status(),
            '[BUG-02] /dashboard dengan role null melempar RouteNotFoundException (500)'
        );
    }

    // ─────────────────────────────────────────────────────────────────────
    // [BUG-03] Pelamar user tanpa record Pelamar → null pointer di LowonganController
    // auth()->user()->pelamar bisa null jika record Pelamar belum dibuat
    // ─────────────────────────────────────────────────────────────────────

    public function test_bug03_pelamar_user_without_pelamar_record_on_lowongan_index(): void
    {
        // User dengan role pelamar tapi TIDAK punya record di tabel pelamars
        $user = User::factory()->create(['role' => 'pelamar']);
        // Sengaja tidak buat Pelamar::factory()->create()

        $response = $this->actingAs($user)->get(route('pelamar.lowongan.index'));

        // Bug: $pelamar->savedLowongans() akan null pointer → 500
        $this->assertNotEquals(
            500,
            $response->status(),
            '[BUG-03] LowonganController@index crash saat pelamar record tidak ada (null pointer)'
        );
    }

    // ─────────────────────────────────────────────────────────────────────
    // [BUG-04] Pelamar user tanpa record Pelamar → null pointer di HistoryController
    // ─────────────────────────────────────────────────────────────────────

    public function test_bug04_pelamar_user_without_pelamar_record_on_history(): void
    {
        $user = User::factory()->create(['role' => 'pelamar']);
        // Sengaja tidak buat Pelamar record

        $response = $this->actingAs($user)->get(route('pelamar.history.index'));

        // Bug: $pelamar->id akan null pointer → 500
        $this->assertNotEquals(
            500,
            $response->status(),
            '[BUG-04] HistoryController@index crash saat pelamar record tidak ada (null pointer)'
        );
    }

    // ─────────────────────────────────────────────────────────────────────
    // [BUG-05] Admin lowongan show → redirect ke lamaran (bukan halaman show)
    // Ini by design tapi harus terdokumentasi — tidak boleh 404 atau 500
    // ─────────────────────────────────────────────────────────────────────

    public function test_bug05_admin_lowongan_show_redirects_to_lamaran_not_404(): void
    {
        $admin    = User::factory()->create(['role' => 'admin']);
        $prodi    = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);

        $response = $this->actingAs($admin)
            ->get(route('admin.lowongan.show', $lowongan));

        // Harus redirect ke lamaran index, bukan 404 atau 500
        $response->assertRedirect(route('admin.lamaran.index', $lowongan));
    }

    // ─────────────────────────────────────────────────────────────────────
    // [BUG-06] Admin tidak bisa akses /settings → harus redirect ke admin.dashboard
    // ─────────────────────────────────────────────────────────────────────

    public function test_bug06_admin_redirected_from_settings_to_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('settings.index'))
            ->assertRedirect(route('admin.dashboard'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // [BUG-07] /profile (Breeze) bisa diakses semua role termasuk admin
    // Ini by design (Breeze default) tapi harus terdokumentasi
    // ─────────────────────────────────────────────────────────────────────

    public function test_bug07_profile_page_accessible_by_all_authenticated_roles(): void
    {
        foreach (['admin', 'pelamar', 'penguji', 'kaprodi'] as $role) {
            $user = User::factory()->create(['role' => $role]);

            $response = $this->actingAs($user)->get('/profile');

            // /profile hanya butuh 'auth', semua role bisa akses
            $response->assertOk(
                "[BUG-07] /profile tidak bisa diakses oleh role: {$role}"
            );
        }
    }

    // ─────────────────────────────────────────────────────────────────────
    // [BUG-08] RoleSwitch: user tanpa is_penguji tidak bisa switch ke penguji
    // ─────────────────────────────────────────────────────────────────────

    public function test_bug08_role_switch_blocked_without_is_penguji_flag(): void
    {
        // Dosen yang hanya kaprodi (bukan penguji)
        $prodi = Prodi::factory()->create();
        $dosen = Dosen::factory()->create(['is_kaprodi' => true, 'is_penguji' => false, 'prodi_id' => $prodi->id]);
        $user  = User::factory()->create([
            'role'       => 'kaprodi',
            'is_kaprodi' => true,
            'is_penguji' => false,
            'dosen_id'   => $dosen->id,
        ]);

        $this->actingAs($user)
            ->post(route('role.switch'), ['role' => 'penguji'])
            ->assertSessionHasErrors('role');
    }

    // ─────────────────────────────────────────────────────────────────────
    // [BUG-09] RoleSwitch: user tanpa is_kaprodi tidak bisa switch ke kaprodi
    // ─────────────────────────────────────────────────────────────────────

    public function test_bug09_role_switch_blocked_without_is_kaprodi_flag(): void
    {
        $prodi = Prodi::factory()->create();
        $dosen = Dosen::factory()->create(['is_penguji' => true, 'is_kaprodi' => false, 'prodi_id' => $prodi->id]);
        $user  = User::factory()->create([
            'role'       => 'penguji',
            'is_penguji' => true,
            'is_kaprodi' => false,
            'dosen_id'   => $dosen->id,
        ]);

        $this->actingAs($user)
            ->post(route('role.switch'), ['role' => 'kaprodi'])
            ->assertSessionHasErrors('role');
    }

    // ─────────────────────────────────────────────────────────────────────
    // [BUG-10] Pelamar tidak bisa apply ke lowongan penuh via POST storeApply
    // ─────────────────────────────────────────────────────────────────────

    public function test_bug10_pelamar_cannot_post_apply_to_full_lowongan(): void
    {
        $prodi    = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id, 'kuota' => 1]);

        // Isi kuota dengan pelamar lain
        $existingUser    = User::factory()->create(['role' => 'pelamar']);
        $existingPelamar = Pelamar::factory()->create(['user_id' => $existingUser->id]);
        Lamaran::factory()->create([
            'pelamar_id'  => $existingPelamar->id,
            'lowongan_id' => $lowongan->id,
            'status'      => 'menunggu',
        ]);

        // Pelamar baru coba apply
        $user    = User::factory()->create(['role' => 'pelamar']);
        $pelamar = Pelamar::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->post(route('pelamar.lowongan.storeApply', $lowongan), [
                'file_surat_lamaran' => \Illuminate\Http\UploadedFile::fake()->create('surat.pdf', 100, 'application/pdf'),
            ]);

        // Harus redirect (bukan 200 atau 500) karena kuota penuh
        $response->assertRedirect();
        $this->assertDatabaseMissing('lamarans', [
            'pelamar_id'  => $pelamar->id,
            'lowongan_id' => $lowongan->id,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // [BUG-11] Pelamar tidak bisa apply dua kali via POST storeApply
    // ─────────────────────────────────────────────────────────────────────

    public function test_bug11_pelamar_cannot_post_apply_twice(): void
    {
        $prodi    = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id, 'kuota' => 10]);
        $user     = User::factory()->create(['role' => 'pelamar']);
        $pelamar  = Pelamar::factory()->create(['user_id' => $user->id]);

        // Lamaran pertama sudah ada
        Lamaran::factory()->create([
            'pelamar_id'  => $pelamar->id,
            'lowongan_id' => $lowongan->id,
        ]);

        // Coba POST apply lagi
        $response = $this->actingAs($user)
            ->post(route('pelamar.lowongan.storeApply', $lowongan), [
                'file_surat_lamaran' => \Illuminate\Http\UploadedFile::fake()->create('surat.pdf', 100, 'application/pdf'),
            ]);

        // Harus redirect ke history dengan warning
        $response->assertRedirect(route('pelamar.history.index'));

        // Pastikan tidak ada lamaran duplikat
        $this->assertEquals(
            1,
            \App\Models\Lamaran::where('pelamar_id', $pelamar->id)
                ->where('lowongan_id', $lowongan->id)
                ->count(),
            '[BUG-11] Lamaran duplikat berhasil dibuat — double apply tidak dicegah di POST'
        );
    }

    // ─────────────────────────────────────────────────────────────────────
    // [BUG-12] Penguji tidak bisa submit nilai dua kali (penilaian duplikat)
    // ─────────────────────────────────────────────────────────────────────

    public function test_bug12_penguji_cannot_submit_nilai_twice(): void
    {
        $prodi    = Prodi::factory()->create();
        $dosen    = Dosen::factory()->create(['is_penguji' => true, 'prodi_id' => $prodi->id]);
        $user     = User::factory()->create(['role' => 'penguji', 'dosen_id' => $dosen->id]);
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
        $pUser    = User::factory()->create(['role' => 'pelamar']);
        $pelamar  = Pelamar::factory()->create(['user_id' => $pUser->id]);

        $jadwal = JadwalSeleksi::factory()->create([
            'penguji_id'   => $dosen->id,
            'pelamar_id'   => $pelamar->id,
            'lowongan_id'  => $lowongan->id,
            'tipe_seleksi' => 'micro_teaching',
        ]);

        // Payload lengkap untuk micro_teaching (k1=2, k2=3, k3=6, k4=3, k5=1 items)
        $nilaiPayload = [
            'k1_item_1' => 4, 'k1_item_2' => 4,
            'k2_item_1' => 4, 'k2_item_2' => 4, 'k2_item_3' => 4,
            'k3_item_1' => 4, 'k3_item_2' => 4, 'k3_item_3' => 4,
            'k3_item_4' => 4, 'k3_item_5' => 4, 'k3_item_6' => 4,
            'k4_item_1' => 4, 'k4_item_2' => 4, 'k4_item_3' => 4,
            'k5_item_1' => 4,
            'rekomendasi'       => 'direkomendasikan',
            'prodi_tujuan'      => 'Teknik Informatika',
            'kelompok_keahlian' => 'scout',
            'bidang_keahlian'   => 'Pemrograman',
            'catatan'           => 'Baik',
        ];

        // Submit pertama — harus berhasil
        $this->actingAs($user)
            ->post(route('penguji.pengujian.storeNilai', $jadwal), $nilaiPayload)
            ->assertRedirect();

        // Pastikan penilaian pertama tersimpan
        $this->assertEquals(
            1,
            \App\Models\Penilaian::where('jadwal_seleksi_id', $jadwal->id)->count(),
            '[BUG-12] Penilaian pertama tidak tersimpan'
        );

        // Submit kedua — harus ditolak (penilaian sudah ada)
        $this->actingAs($user)
            ->post(route('penguji.pengujian.storeNilai', $jadwal), $nilaiPayload)
            ->assertRedirect();

        // Pastikan hanya ada 1 penilaian (tidak duplikat)
        $this->assertEquals(
            1,
            \App\Models\Penilaian::where('jadwal_seleksi_id', $jadwal->id)->count(),
            '[BUG-12] Penilaian duplikat berhasil dibuat — submit nilai dua kali tidak dicegah'
        );
    }

    // ─────────────────────────────────────────────────────────────────────
    // [BUG-13] Admin API sesi-taken-all tanpa parameter → array kosong, bukan error
    // ─────────────────────────────────────────────────────────────────────

    public function test_bug13_api_sesi_taken_all_without_params_returns_empty_array(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->getJson(route('admin.api.sesi.taken'));

        $response->assertOk();
        $response->assertJson([]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // [BUG-14] Kaprodi bisa akses settings (harus bisa — by design)
    // ─────────────────────────────────────────────────────────────────────

    public function test_bug14_kaprodi_can_access_settings(): void
    {
        $prodi = Prodi::factory()->create();
        $dosen = Dosen::factory()->create(['is_kaprodi' => true, 'prodi_id' => $prodi->id]);
        $user  = User::factory()->create([
            'role'       => 'kaprodi',
            'is_kaprodi' => true,
            'dosen_id'   => $dosen->id,
            'prodi_id'   => $prodi->id,
        ]);

        $this->actingAs($user)
            ->get(route('settings.index'))
            ->assertOk();
    }

    // ─────────────────────────────────────────────────────────────────────
    // [BUG-15] Notifikasi: pelamar tidak bisa mark notifikasi milik user lain
    // ─────────────────────────────────────────────────────────────────────

    public function test_bug15_pelamar_cannot_mark_other_user_notifikasi_as_read(): void
    {
        $user1    = User::factory()->create(['role' => 'pelamar']);
        $pelamar1 = Pelamar::factory()->create(['user_id' => $user1->id]);

        $user2    = User::factory()->create(['role' => 'pelamar']);
        $pelamar2 = Pelamar::factory()->create(['user_id' => $user2->id]);

        // Buat notifikasi milik user2
        $notif = \App\Models\Notifikasi::create([
            'user_id' => $user2->id,
            'judul'   => 'Test Notif',
            'pesan'   => 'Isi notifikasi',
            'tipe'    => 'info',
            'dibaca'  => false,
        ]);

        // User1 coba mark notifikasi user2 sebagai dibaca
        $response = $this->actingAs($user1)
            ->post(route('notifikasi.baca', $notif));

        // Harus 403 atau redirect — notifikasi user2 tidak boleh diubah user1
        $this->assertNotEquals(
            200,
            $response->status(),
            '[BUG-15] User lain bisa mark notifikasi milik user lain sebagai dibaca'
        );

        // Notifikasi harus tetap belum dibaca
        $this->assertFalse(
            \App\Models\Notifikasi::find($notif->id)->dibaca,
            '[BUG-15] Notifikasi user lain berhasil diubah statusnya'
        );
    }

    // ─────────────────────────────────────────────────────────────────────
    // [EXTRA] Halaman duplikat: cetak dan show lamaran load data yang sama
    // Keduanya harus return 200 dan tidak error
    // ─────────────────────────────────────────────────────────────────────

    public function test_extra_admin_lamaran_show_and_cetak_both_load(): void
    {
        $admin    = User::factory()->create(['role' => 'admin']);
        $prodi    = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);
        $pUser    = User::factory()->create(['role' => 'pelamar']);
        $pelamar  = Pelamar::factory()->create(['user_id' => $pUser->id]);
        $lamaran  = Lamaran::factory()->create([
            'pelamar_id'  => $pelamar->id,
            'lowongan_id' => $lowongan->id,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.lamaran.show', $lamaran))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('admin.lamaran.cetak', $lamaran))
            ->assertOk();
    }

    // ─────────────────────────────────────────────────────────────────────
    // [EXTRA] Dosen dengan role null di-logout saat akses route berproteksi
    // ─────────────────────────────────────────────────────────────────────

    public function test_extra_dosen_with_empty_role_is_logged_out_on_protected_route(): void
    {
        $user = User::factory()->create(['role' => null]);

        // Akses route yang dilindungi role middleware
        $response = $this->actingAs($user)->get('/pelamar/dashboard');

        // Harus redirect ke login (bukan 500)
        $response->assertRedirect('/login');
    }

    // ─────────────────────────────────────────────────────────────────────
    // [EXTRA] Admin bisa akses berita acara lowongan
    // ─────────────────────────────────────────────────────────────────────

    public function test_extra_admin_berita_acara_loads(): void
    {
        $admin    = User::factory()->create(['role' => 'admin']);
        $prodi    = Prodi::factory()->create();
        $lowongan = Lowongan::factory()->create(['prodi_id' => $prodi->id]);

        $this->actingAs($admin)
            ->get(route('admin.lowongan.beritaAcara', $lowongan))
            ->assertOk();
    }
}
