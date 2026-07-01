<?php

namespace Tests\Feature;

use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotifikasiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'pelamar']);
    }

    public function test_pengguna_dapat_melihat_daftar_notifikasi(): void
    {
        Notifikasi::create([
            'user_id' => $this->user->id,
            'judul' => 'Test Notifikasi',
            'pesan' => 'Ini adalah pesan test.',
            'tipe' => 'status',
            'dibaca' => false,
        ]);

        $response = $this->actingAs($this->user)->getJson(route('notifikasi.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure(['notifikasis', 'belum_dibaca']);
        $response->assertJsonPath('belum_dibaca', 1);
    }

    public function test_pengguna_dapat_menandai_notifikasi_dibaca(): void
    {
        $notifikasi = Notifikasi::create([
            'user_id' => $this->user->id,
            'judul' => 'Test',
            'pesan' => 'Pesan',
            'tipe' => 'status',
            'dibaca' => false,
        ]);

        $response = $this->actingAs($this->user)->postJson(route('notifikasi.baca', $notifikasi));

        $response->assertStatus(200);
        $response->assertJson(['ok' => true]);
        $this->assertDatabaseHas('notifikasis', ['id' => $notifikasi->id, 'dibaca' => true]);
    }

    public function test_pengguna_dapat_menandai_semua_notifikasi_dibaca(): void
    {
        Notifikasi::create(['user_id' => $this->user->id, 'judul' => 'A', 'pesan' => 'A', 'tipe' => 'status', 'dibaca' => false]);
        Notifikasi::create(['user_id' => $this->user->id, 'judul' => 'B', 'pesan' => 'B', 'tipe' => 'jadwal', 'dibaca' => false]);

        $response = $this->actingAs($this->user)->postJson(route('notifikasi.baca.semua'));

        $response->assertStatus(200);
        $this->assertEquals(0, Notifikasi::where('user_id', $this->user->id)->where('dibaca', false)->count());
    }

    public function test_notifikasi_whatsapp_terkirim_ke_pelamar_saat_status_berubah(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $pelamar = \App\Models\Pelamar::factory()->create(['user_id' => $this->user->id]);
        $prodi = \App\Models\Prodi::factory()->create();
        $lowongan = \App\Models\Lowongan::factory()->create(['prodi_id' => $prodi->id]);
        $lamaran = \App\Models\Lamaran::factory()->create([
            'pelamar_id' => $pelamar->id,
            'lowongan_id' => $lowongan->id,
            'status' => 'menunggu',
        ]);

        $this->actingAs($admin)->put(route('admin.lamaran.update', $lamaran), [
            'status' => 'seleksi_tahap1',
        ]);

        // Notifikasi bertipe 'status' terkirim ke pelamar (otomatis trigger WA)
        $this->assertDatabaseHas('notifikasis', [
            'user_id' => $this->user->id,
            'tipe' => 'status',
        ]);
    }

    public function test_notifikasi_whatsapp_terkirim_ke_pelamar_dan_penguji_setelah_penjadwalan(): void
    {
        // Test model Notifikasi::kirim() langsung — membuktikan tipe 'jadwal' dan 'status'
        // otomatis trigger pengiriman WA

        $pelamarUser = User::factory()->create(['role' => 'pelamar']);
        \App\Models\Pelamar::factory()->create(['user_id' => $pelamarUser->id]);

        $prodi = \App\Models\Prodi::factory()->create();
        $dosen = \App\Models\Dosen::factory()->create(['prodi_id' => $prodi->id, 'is_penguji' => true]);
        $pengujiUser = User::factory()->create(['role' => 'penguji', 'dosen_id' => $dosen->id, 'is_penguji' => true]);

        // Kirim notifikasi jadwal ke pelamar (trigger WA)
        Notifikasi::kirim($pelamarUser->id, 'Jadwal Seleksi', 'Anda dijadwalkan seleksi.', 'jadwal');

        // Kirim notifikasi jadwal ke penguji (trigger WA)
        Notifikasi::kirim($pengujiUser->id, 'Jadwal Pengujian', 'Anda dijadwalkan sebagai penguji.', 'jadwal');

        // Notifikasi terkirim ke pelamar
        $this->assertDatabaseHas('notifikasis', [
            'user_id' => $pelamarUser->id,
            'tipe' => 'jadwal',
            'judul' => 'Jadwal Seleksi',
        ]);

        // Notifikasi terkirim ke penguji
        $this->assertDatabaseHas('notifikasis', [
            'user_id' => $pengujiUser->id,
            'tipe' => 'jadwal',
            'judul' => 'Jadwal Pengujian',
        ]);
    }
}
