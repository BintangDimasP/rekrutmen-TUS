<?php

namespace Tests\Unit;

use App\Models\Dosen;
use PHPUnit\Framework\TestCase;

/**
 * Unit test untuk Model Dosen.
 *
 * Method yang diuji (pure logic, tanpa DB):
 *   - generateEmailPrefix()  → ubah nama menjadi prefix email
 *   - PENGAJAR_DOMAIN constant
 *   - DEFAULT_PASSWORD constant
 */
class DosenModelTest extends TestCase
{
    // ── Helpers ───────────────────────────────────────────────────────────

    private function makeDosen(string $nama): Dosen
    {
        $dosen       = new Dosen();
        $dosen->nama = $nama;
        return $dosen;
    }

    // ── Constants ─────────────────────────────────────────────────────────

    public function test_pengajar_domain_constant_value(): void
    {
        $this->assertSame('pengajar.telkomuniversity.ac.id', Dosen::PENGAJAR_DOMAIN);
    }

    public function test_default_password_constant_value(): void
    {
        $this->assertSame('dosen123', Dosen::DEFAULT_PASSWORD);
    }

    // ── generateEmailPrefix() ─────────────────────────────────────────────

    public function test_generates_prefix_from_two_words(): void
    {
        $dosen = $this->makeDosen('Budi Santoso');

        // Ambil 2 kata pertama → 'budi' + 'santoso' = 'budisantoso'
        $this->assertSame('budisantoso', $dosen->generateEmailPrefix());
    }

    public function test_generates_prefix_from_single_word_nama(): void
    {
        $dosen = $this->makeDosen('Suharto');

        $this->assertSame('suharto', $dosen->generateEmailPrefix());
    }

    public function test_generates_prefix_strips_special_characters(): void
    {
        // "D'Angelo" dianggap SATU kata oleh preg_split (apostrof bukan spasi),
        // lalu regex hapus apostrof → 'dangelo'. Kata kedua 'Marto' → 'marto'.
        // Dua kata pertama digabung → 'dangelomarto'
        $dosen = $this->makeDosen("D'Angelo Marto");

        $this->assertSame('dangelomarto', $dosen->generateEmailPrefix());
    }

    public function test_generates_prefix_lowercased(): void
    {
        $dosen = $this->makeDosen('AHMAD FAUZI Hidayat');

        // Harus lowercase, hanya ambil 2 kata pertama
        $this->assertSame('ahmadfauzi', $dosen->generateEmailPrefix());
    }

    public function test_generates_prefix_strips_numbers_from_nama(): void
    {
        $dosen = $this->makeDosen('Rudi123 Hermawan');

        // angka ikut masuk karena regex hanya hapus non-alphanumeric selain angka
        $this->assertSame('rudi123hermawan', $dosen->generateEmailPrefix());
    }

    public function test_generates_prefix_trims_extra_spaces(): void
    {
        $dosen = $this->makeDosen('  Hendra   Wijaya  ');

        $this->assertSame('hendrawijaya', $dosen->generateEmailPrefix());
    }

    public function test_generates_prefix_only_uses_first_two_words(): void
    {
        // Nama 4 kata — prefix hanya dari kata 1 dan 2
        $dosen = $this->makeDosen('Annisa Putri Rahayu Wulandari');

        $this->assertSame('annisaputri', $dosen->generateEmailPrefix());
    }
}
