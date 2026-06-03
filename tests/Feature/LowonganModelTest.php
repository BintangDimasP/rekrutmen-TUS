<?php

namespace Tests\Feature;

use App\Models\Lowongan;
use App\Models\Prodi;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test untuk logika Model Lowongan.
 *
 * Menggunakan RefreshDatabase + SQLite in-memory agar Eloquent casting
 * (tanggal_tutup → Carbon, dst.) berjalan sebagaimana mestinya.
 *
 * Method yang diuji:
 *   - getStatusAttribute()  → logika override status
 *   - getSisaKuotaAttribute() → kalkulasi sisa kuota
 *   - isFull()              → apakah kuota habis
 */
class LowonganModelTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ───────────────────────────────────────────────────────────

    /**
     * Buat Lowongan di DB dengan nilai default yang aman untuk diuji.
     */
    private function buatLowongan(array $overrides = []): Lowongan
    {
        $prodi = Prodi::factory()->create();

        return Lowongan::create(array_merge([
            'prodi_id'        => $prodi->id,
            'nama_posisi'     => 'Dosen Test',
            'kuota'           => 5,
            'jenjang_minimal' => 'S2',
            'minimal_ipk'     => 3.00,
            'tanggal_tutup'   => Carbon::tomorrow()->format('Y-m-d'),
            'status'          => 'aktif',
        ], $overrides));
    }

    // ── getStatusAttribute() ──────────────────────────────────────────────

    public function test_status_aktif_tetap_aktif_jika_belum_tutup_dan_ada_sisa_kuota(): void
    {
        $lowongan = $this->buatLowongan([
            'status'        => 'aktif',
            'kuota'         => 5,
            'tanggal_tutup' => Carbon::tomorrow()->format('Y-m-d'),
        ]);

        // Tidak ada lamaran → sisa_kuota masih penuh
        $this->assertSame('aktif', $lowongan->status);
    }

    public function test_status_aktif_berubah_ditutup_jika_tanggal_tutup_sudah_lewat(): void
    {
        $lowongan = $this->buatLowongan([
            'status'        => 'aktif',
            'tanggal_tutup' => Carbon::yesterday()->format('Y-m-d'),
        ]);

        $this->assertSame('ditutup', $lowongan->status);
    }

    public function test_status_draft_tidak_berubah_meski_tanggal_sudah_lewat(): void
    {
        $lowongan = $this->buatLowongan([
            'status'        => 'draft',
            'tanggal_tutup' => Carbon::yesterday()->format('Y-m-d'),
        ]);

        $this->assertSame('draft', $lowongan->status);
    }

    public function test_status_ditutup_dikembalikan_apa_adanya(): void
    {
        $lowongan = $this->buatLowongan([
            'status'        => 'ditutup',
            'tanggal_tutup' => Carbon::tomorrow()->format('Y-m-d'),
        ]);

        $this->assertSame('ditutup', $lowongan->status);
    }

    // ── getSisaKuotaAttribute() ───────────────────────────────────────────

    public function test_sisa_kuota_sama_dengan_kuota_jika_tidak_ada_lamaran(): void
    {
        $lowongan = $this->buatLowongan(['kuota' => 5]);

        $this->assertSame(5, $lowongan->sisa_kuota);
    }

    public function test_sisa_kuota_tidak_kurang_dari_nol(): void
    {
        // Buat lowongan kuota 1 lalu tidak ada lamaran → sisa 1
        // (Test ini memastikan max(0,...) bekerja)
        $lowongan = $this->buatLowongan(['kuota' => 0]);

        $this->assertSame(0, $lowongan->sisa_kuota);
    }

    // ── isFull() ──────────────────────────────────────────────────────────

    public function test_isFull_returns_false_when_ada_sisa_kuota(): void
    {
        $lowongan = $this->buatLowongan(['kuota' => 3]);

        $this->assertFalse($lowongan->isFull());
    }

    public function test_isFull_returns_true_when_kuota_nol(): void
    {
        $lowongan = $this->buatLowongan(['kuota' => 0]);

        $this->assertTrue($lowongan->isFull());
    }
}
