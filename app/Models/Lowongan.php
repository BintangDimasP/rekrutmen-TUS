<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lowongan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_posisi',
        'kategori',
        'prodi_id',
        'jenjang_minimal',
        'minimal_ipk',
        'prodi_prioritas',
        'skill_dibutuhkan',
        'kuota',
        'tanggal_tutup',
        'deskripsi',
        'materi_micro_teaching',
        'status',
    ];

    protected $casts = [
        'tanggal_tutup' => 'date',
        'minimal_ipk'   => 'decimal:2',
    ];

    // ── Relasi ──────────────────────────────────────────────────

    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }

    public function lamarans()
    {
        return $this->hasMany(Lamaran::class);
    }

    public function pelamars()
    {
        return $this->belongsToMany(Pelamar::class, 'lamarans')
                    ->withPivot(['status'])
                    ->withTimestamps();
    }

    // ── Computed ──────────────────────────────────────────────────

    /**
     * Sisa kuota — hanya hitung lamaran yang aktif (bukan ditolak).
     */
    public function getSisaKuotaAttribute(): int
    {
        $aktif = $this->lamarans()->whereNotIn('status', ['ditolak', 'mengundurkan_diri'])->count();
        return max(0, $this->kuota - $aktif);
    }

    /**
     * Override status attribute.
     * Jika status aktif di DB, tapi tanggal_tutup sudah lewat atau sisa_kuota <= 0,
     * otomatis anggap statusnya adalah 'ditutup'.
     */
    public function getStatusAttribute($value): string
    {
        if ($value === 'aktif') {
            if ($this->tanggal_tutup && $this->tanggal_tutup->endOfDay()->isPast()) {
                return 'ditutup';
            }
            if ($this->sisa_kuota <= 0) {
                return 'ditutup';
            }
        }
        return $value ?? 'draft';
    }

    /**
     * Cek apakah kuota lowongan sudah penuh.
     */
    public function isFull(): bool
    {
        return $this->sisa_kuota <= 0;
    }

    /**
     * URL logo lowongan (mengikuti logo prodi, fallback ke logo Telkom).
     */
    public function getLogoUrlAttribute(): string
    {
        if ($this->prodi && $this->prodi->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->prodi->logo)) {
            return asset('storage/' . $this->prodi->logo);
        }
        return asset('images/logo-icon.png');
    }
}
