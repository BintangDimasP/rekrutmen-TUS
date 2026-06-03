<?php

namespace Tests\Unit;

use App\Models\JadwalSeleksi;
use PHPUnit\Framework\TestCase;

/**
 * Unit test untuk Model JadwalSeleksi.
 *
 * Method yang diuji (pure logic):
 *   - getSessionLabelAttribute()  → label sesi berdasarkan tipe & nomor sesi
 *   - getTipeLabelAttribute()     → label tipe seleksi (human-readable)
 *   - SESSIONS constant           → struktur data slot waktu
 */
class JadwalSeleksiModelTest extends TestCase
{
    // ── Helpers ───────────────────────────────────────────────────────────

    private function makeJadwal(string $tipe, int $sesi): JadwalSeleksi
    {
        $jadwal               = new JadwalSeleksi();
        $jadwal->tipe_seleksi = $tipe;
        $jadwal->sesi         = $sesi;
        return $jadwal;
    }

    // ── getTipeLabelAttribute() ───────────────────────────────────────────

    public function test_tipe_label_wawancara(): void
    {
        $jadwal = $this->makeJadwal('wawancara', 1);

        $this->assertSame('Wawancara', $jadwal->getTipeLabelAttribute());
    }

    public function test_tipe_label_micro_teaching(): void
    {
        $jadwal = $this->makeJadwal('micro_teaching', 1);

        $this->assertSame('Micro Teaching', $jadwal->getTipeLabelAttribute());
    }

    // ── getSessionLabelAttribute() ────────────────────────────────────────

    public function test_session_label_micro_teaching_sesi_1(): void
    {
        $jadwal = $this->makeJadwal('micro_teaching', 1);

        $this->assertSame('Micro Teaching (08.00–08.30)', $jadwal->getSessionLabelAttribute());
    }

    public function test_session_label_micro_teaching_sesi_5(): void
    {
        $jadwal = $this->makeJadwal('micro_teaching', 5);

        $this->assertSame('Micro Teaching (13.00–13.30)', $jadwal->getSessionLabelAttribute());
    }

    public function test_session_label_wawancara_sesi_1(): void
    {
        $jadwal = $this->makeJadwal('wawancara', 1);

        $this->assertSame('Wawancara (08.30–09.00)', $jadwal->getSessionLabelAttribute());
    }

    public function test_session_label_wawancara_sesi_8(): void
    {
        $jadwal = $this->makeJadwal('wawancara', 8);

        $this->assertSame('Wawancara (16.30–17.00)', $jadwal->getSessionLabelAttribute());
    }

    public function test_session_label_returns_dash_for_invalid_tipe(): void
    {
        $jadwal = $this->makeJadwal('tidak_ada', 1);

        $this->assertSame('-', $jadwal->getSessionLabelAttribute());
    }

    public function test_session_label_returns_dash_for_invalid_sesi_number(): void
    {
        $jadwal = $this->makeJadwal('micro_teaching', 99);

        $this->assertSame('-', $jadwal->getSessionLabelAttribute());
    }

    // ── SESSIONS constant structure ───────────────────────────────────────

    public function test_sessions_constant_has_micro_teaching_and_wawancara_keys(): void
    {
        $this->assertArrayHasKey('micro_teaching', JadwalSeleksi::SESSIONS);
        $this->assertArrayHasKey('wawancara', JadwalSeleksi::SESSIONS);
    }

    public function test_sessions_constant_has_8_slots_per_type(): void
    {
        $this->assertCount(8, JadwalSeleksi::SESSIONS['micro_teaching']);
        $this->assertCount(8, JadwalSeleksi::SESSIONS['wawancara']);
    }

    public function test_each_session_slot_has_required_keys(): void
    {
        $requiredKeys = ['label', 'block_label', 'start', 'end'];

        foreach (JadwalSeleksi::SESSIONS as $tipe => $slots) {
            foreach ($slots as $no => $slot) {
                foreach ($requiredKeys as $key) {
                    $this->assertArrayHasKey(
                        $key,
                        $slot,
                        "Key '$key' tidak ada di SESSIONS[$tipe][$no]"
                    );
                }
            }
        }
    }

    public function test_micro_teaching_and_wawancara_share_same_block_labels(): void
    {
        // Setiap sesi micro_teaching dan wawancara harus punya block_label yang sama
        // karena keduanya berada dalam satu blok 1 jam yang sama
        for ($sesi = 1; $sesi <= 8; $sesi++) {
            $mtBlock = JadwalSeleksi::SESSIONS['micro_teaching'][$sesi]['block_label'];
            $wwBlock = JadwalSeleksi::SESSIONS['wawancara'][$sesi]['block_label'];

            $this->assertSame(
                $mtBlock,
                $wwBlock,
                "block_label sesi $sesi tidak sama antara micro_teaching dan wawancara"
            );
        }
    }
}
