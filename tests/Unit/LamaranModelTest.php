<?php

namespace Tests\Unit;

use App\Models\Lamaran;
use App\Models\Pelamar;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

/**
 * Unit test untuk Model Lamaran.
 *
 * Method yang diuji:
 *   - getStatusLabelAttribute()     → label teks dari status enum
 *   - getEffectivePelamarAttribute() → snapshot vs relasi live
 */
class LamaranModelTest extends TestCase
{
    // ── Helpers ───────────────────────────────────────────────────────────

    private function makeLamaran(array $attributes = []): Lamaran
    {
        $lamaran = new Lamaran();
        foreach ($attributes as $key => $value) {
            $lamaran->$key = $value;
        }
        return $lamaran;
    }

    // ── getStatusLabelAttribute() ─────────────────────────────────────────

    public function test_status_label_menunggu(): void
    {
        $lamaran = $this->makeLamaran(['status' => 'menunggu']);

        $this->assertSame('Menunggu', $lamaran->getStatusLabelAttribute());
    }

    public function test_status_label_seleksi_tahap1(): void
    {
        $lamaran = $this->makeLamaran(['status' => 'seleksi_tahap1']);

        $this->assertSame('Seleksi Tahap 1 (Administrasi)', $lamaran->getStatusLabelAttribute());
    }

    public function test_status_label_seleksi_tahap2(): void
    {
        $lamaran = $this->makeLamaran(['status' => 'seleksi_tahap2']);

        $this->assertSame('Seleksi Tahap 2 (Micro Teaching & Wawancara)', $lamaran->getStatusLabelAttribute());
    }

    public function test_status_label_diterima(): void
    {
        $lamaran = $this->makeLamaran(['status' => 'diterima']);

        $this->assertSame('Diterima', $lamaran->getStatusLabelAttribute());
    }

    public function test_status_label_ditolak(): void
    {
        $lamaran = $this->makeLamaran(['status' => 'ditolak']);

        $this->assertSame('Ditolak', $lamaran->getStatusLabelAttribute());
    }

    public function test_status_label_unknown_status_returns_raw_value(): void
    {
        // Status tidak dikenal → kembalikan nilai mentahnya
        $lamaran = $this->makeLamaran(['status' => 'status_aneh']);

        $this->assertSame('status_aneh', $lamaran->getStatusLabelAttribute());
    }

    // ── getEffectivePelamarAttribute() ────────────────────────────────────

    public function test_effective_pelamar_returns_snapshot_when_snapshot_data_exists(): void
    {
        $snapshot = [
            'nama'         => 'Budi Snapshot',
            'nik'          => '1234567890123456',
            'tanggal_lahir' => '1990-01-15',
        ];

        $lamaran = $this->makeLamaran(['snapshot_data' => $snapshot]);

        $result = $lamaran->getEffectivePelamarAttribute();

        // Harus berupa object (stdClass-like), bukan model Pelamar
        $this->assertIsObject($result);
        $this->assertSame('Budi Snapshot', $result->nama);
    }

    public function test_effective_pelamar_parses_date_fields_from_snapshot(): void
    {
        $snapshot = [
            'nama'               => 'Citra Test',
            'tanggal_lahir'      => '1995-06-20',
            'tanggal_tes_bahasa' => '2023-03-10',
        ];

        $lamaran = $this->makeLamaran(['snapshot_data' => $snapshot]);

        $result = $lamaran->getEffectivePelamarAttribute();

        // tanggal_lahir dan tanggal_tes_bahasa harus jadi Carbon instance
        $this->assertInstanceOf(Carbon::class, $result->tanggal_lahir);
        $this->assertInstanceOf(Carbon::class, $result->tanggal_tes_bahasa);
        $this->assertSame('1995-06-20', $result->tanggal_lahir->format('Y-m-d'));
    }

    public function test_effective_pelamar_returns_pelamar_relation_when_no_snapshot(): void
    {
        // Buat Pelamar model palsu
        $pelamar        = new Pelamar();
        $pelamar->nama  = 'Doni Live';

        // Mock Lamaran agar relasi pelamar bisa dikontrol tanpa DB
        $lamaran = $this->getMockBuilder(Lamaran::class)
            ->onlyMethods(['getRelationValue'])
            ->getMock();

        $lamaran->snapshot_data = null; // tidak ada snapshot

        $lamaran->method('getRelationValue')
            ->with('pelamar')
            ->willReturn($pelamar);

        // Set relasi secara langsung pada atribut relasi
        $lamaran->setRelation('pelamar', $pelamar);

        $result = $lamaran->getEffectivePelamarAttribute();

        $this->assertInstanceOf(Pelamar::class, $result);
        $this->assertSame('Doni Live', $result->nama);
    }

    // ── STATUS_LABELS constant ────────────────────────────────────────────

    public function test_status_labels_constant_contains_all_expected_keys(): void
    {
        $expected = ['menunggu', 'seleksi_tahap1', 'seleksi_tahap2', 'diterima', 'ditolak'];

        foreach ($expected as $key) {
            $this->assertArrayHasKey($key, Lamaran::STATUS_LABELS, "Key '$key' tidak ada di STATUS_LABELS");
        }
    }
}
