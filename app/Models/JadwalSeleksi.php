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
        'micro_teaching' => [ // 30 Menit Pertama
            1 => ['label' => 'Micro Teaching (08.00–08.30)', 'block_label' => 'Sesi 1 (08.00-09.00)', 'start' => '08:00', 'end' => '08:30'],
            2 => ['label' => 'Micro Teaching (09.00–09.30)', 'block_label' => 'Sesi 2 (09.00-10.00)', 'start' => '09:00', 'end' => '09:30'],
            3 => ['label' => 'Micro Teaching (10.00–10.30)', 'block_label' => 'Sesi 3 (10.00-11.00)', 'start' => '10:00', 'end' => '10:30'],
            4 => ['label' => 'Micro Teaching (11.00–11.30)', 'block_label' => 'Sesi 4 (11.00-12.00)', 'start' => '11:00', 'end' => '11:30'],
            5 => ['label' => 'Micro Teaching (13.00–13.30)', 'block_label' => 'Sesi 5 (13.00-14.00)', 'start' => '13:00', 'end' => '13:30'],
            6 => ['label' => 'Micro Teaching (14.00–14.30)', 'block_label' => 'Sesi 6 (14.00-15.00)', 'start' => '14:00', 'end' => '14:30'],
            7 => ['label' => 'Micro Teaching (15.00–15.30)', 'block_label' => 'Sesi 7 (15.00-16.00)', 'start' => '15:00', 'end' => '15:30'],
            8 => ['label' => 'Micro Teaching (16.00–16.30)', 'block_label' => 'Sesi 8 (16.00-17.00)', 'start' => '16:00', 'end' => '16:30'],
        ],
        'wawancara' => [ // 30 Menit Kedua
            1 => ['label' => 'Wawancara (08.30–09.00)', 'block_label' => 'Sesi 1 (08.00-09.00)', 'start' => '08:30', 'end' => '09:00'],
            2 => ['label' => 'Wawancara (09.30–10.00)', 'block_label' => 'Sesi 2 (09.00-10.00)', 'start' => '09:30', 'end' => '10:00'],
            3 => ['label' => 'Wawancara (10.30–11.00)', 'block_label' => 'Sesi 3 (10.00-11.00)', 'start' => '10:30', 'end' => '11:00'],
            4 => ['label' => 'Wawancara (11.30–12.00)', 'block_label' => 'Sesi 4 (11.00-12.00)', 'start' => '11:30', 'end' => '12:00'],
            5 => ['label' => 'Wawancara (13.30–14.00)', 'block_label' => 'Sesi 5 (13.00-14.00)', 'start' => '13:30', 'end' => '14:00'],
            6 => ['label' => 'Wawancara (14.30–15.00)', 'block_label' => 'Sesi 6 (14.00-15.00)', 'start' => '14:30', 'end' => '15:00'],
            7 => ['label' => 'Wawancara (15.30–16.00)', 'block_label' => 'Sesi 7 (15.00-16.00)', 'start' => '15:30', 'end' => '16:00'],
            8 => ['label' => 'Wawancara (16.30–17.00)', 'block_label' => 'Sesi 8 (16.00-17.00)', 'start' => '16:30', 'end' => '17:00'],
        ],
    ];

    protected $fillable = [
        'tanggal', 'lowongan_id', 'pelamar_id', 'penguji_id', 'tipe_seleksi', 'sesi', 'link_meeting', 'jenis_sesi', 'lokasi'
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

    public function penilaian()
    {
        return $this->hasOne(Penilaian::class, 'jadwal_seleksi_id');
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
        return $this->tipe_seleksi === 'wawancara' ? 'Wawancara' : 'Micro Teaching';
    }

    // ── Static Helpers ──────────────────────────────────────────────────

    // ── Static Helpers ──────────────────────────────────────────────────

    /**
     * Cek apakah penguji bebas di slot tertentu.
     * Karena Wawancara dan Micro Teaching berada pada 30-menit yang berbeda
     * di dalam sesi yang sama, penguji hanya bentrok jika tipe dan sesi sama.
     */
    public static function isPengujiAvailable(string $tanggal, int $pengujiId, string $tipe, int $sesi): bool
    {
        return !self::whereDate('tanggal', $tanggal)
            ->where('penguji_id', $pengujiId)
            ->where('tipe_seleksi', $tipe)
            ->where('sesi', $sesi)
            ->exists();
    }

    /**
     * Cek apakah pelamar bebas di slot tertentu.
     * Pelamar bentrok jika sudah ada jadwal APAPUN (MT/WWC, lowongan manapun)
     * di sesi dan tanggal yang sama — karena 1 sesi = pelamar harus hadir
     * MT + WWC secara berurutan.
     *
     * @param array $excludeIds  ID jadwal yang dikecualikan (untuk mode edit)
     */
    public static function isPelamarAvailable(string $tanggal, int $pelamarId, int $sesi, array $excludeIds = []): bool
    {
        $q = self::whereDate('tanggal', $tanggal)
            ->where('pelamar_id', $pelamarId)
            ->where('sesi', $sesi);

        if (!empty($excludeIds)) {
            $q->whereNotIn('id', $excludeIds);
        }

        return !$q->exists();
    }
}
