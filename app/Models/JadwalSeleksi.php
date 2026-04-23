<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalSeleksi extends Model
{
    use HasFactory;

    protected $table = 'jadwal_seleksis';

    // ── Slot sesi berdasarkan tipe seleksi ──────────────────────────────
    const SESSIONS = [
        'tahap1' => [
            1 => ['label' => 'Wawancara Sesi 1 (08.00–09.00)', 'start' => '08:00', 'end' => '09:00'],
            2 => ['label' => 'Wawancara Sesi 2 (09.00–10.00)', 'start' => '09:00', 'end' => '10:00'],
            3 => ['label' => 'Wawancara Sesi 3 (10.00–11.00)', 'start' => '10:00', 'end' => '11:00'],
            4 => ['label' => 'Wawancara Sesi 4 (13.00–14.00)', 'start' => '13:00', 'end' => '14:00'],
        ],
        'tahap2' => [
            1 => ['label' => 'Micro Teaching Sesi 1 (13.00–14.00)', 'start' => '13:00', 'end' => '14:00'],
            2 => ['label' => 'Micro Teaching Sesi 2 (14.00–15.00)', 'start' => '14:00', 'end' => '15:00'],
            3 => ['label' => 'Micro Teaching Sesi 3 (15.00–16.00)', 'start' => '15:00', 'end' => '16:00'],
        ],
    ];

    // ── Konflik jam: tahap1-sesi4 = tahap2-sesi1 (sama-sama 13:00-14:00) ──
    const TIME_CONFLICTS = [
        'tahap1_1' => [['tipe' => 'tahap1', 'sesi' => 1]],
        'tahap1_2' => [['tipe' => 'tahap1', 'sesi' => 2]],
        'tahap1_3' => [['tipe' => 'tahap1', 'sesi' => 3]],
        'tahap1_4' => [['tipe' => 'tahap1', 'sesi' => 4], ['tipe' => 'tahap2', 'sesi' => 1]],
        'tahap2_1' => [['tipe' => 'tahap2', 'sesi' => 1], ['tipe' => 'tahap1', 'sesi' => 4]],
        'tahap2_2' => [['tipe' => 'tahap2', 'sesi' => 2]],
        'tahap2_3' => [['tipe' => 'tahap2', 'sesi' => 3]],
    ];

    protected $fillable = [
        'tanggal', 'lowongan_id', 'pelamar_id', 'penguji_id', 'tipe_seleksi', 'sesi',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // ── Relasi ──────────────────────────────────────────────────────────

    public function pelamar()
    {
        return $this->belongsTo(Pelamar::class);
    }

    public function penguji()
    {
        return $this->belongsTo(Dosen::class, 'penguji_id');
    }

    public function lowongan()
    {
        return $this->belongsTo(Lowongan::class);
    }

    // ── Computed ────────────────────────────────────────────────────────

    public function getSessionLabelAttribute(): string
    {
        return self::SESSIONS[$this->tipe_seleksi][$this->sesi]['label'] ?? '-';
    }

    public function getTipeLabelAttribute(): string
    {
        return $this->tipe_seleksi === 'tahap1' ? 'Wawancara' : 'Micro Teaching';
    }

    // ── Static Helpers ──────────────────────────────────────────────────

    /**
     * Return all (tipe, sesi) pairs that share the same time slot.
     */
    public static function getConflictingSlots(string $tipe, int $sesi): array
    {
        return self::TIME_CONFLICTS["{$tipe}_{$sesi}"] ?? [];
    }

    /**
     * Cek apakah penguji bebas di slot tertentu (tidak konflik jam).
     */
    public static function isPengujiAvailable(string $tanggal, int $pengujiId, string $tipe, int $sesi): bool
    {
        foreach (self::getConflictingSlots($tipe, $sesi) as $c) {
            if (self::where('tanggal', $tanggal)
                ->where('penguji_id', $pengujiId)
                ->where('tipe_seleksi', $c['tipe'])
                ->where('sesi', $c['sesi'])
                ->exists()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Cek apakah pelamar bebas di slot tertentu (tidak konflik jam).
     */
    public static function isPelamarAvailable(string $tanggal, int $pelamarId, string $tipe, int $sesi): bool
    {
        foreach (self::getConflictingSlots($tipe, $sesi) as $c) {
            if (self::where('tanggal', $tanggal)
                ->where('pelamar_id', $pelamarId)
                ->where('tipe_seleksi', $c['tipe'])
                ->where('sesi', $c['sesi'])
                ->exists()) {
                return false;
            }
        }
        return true;
    }
}
