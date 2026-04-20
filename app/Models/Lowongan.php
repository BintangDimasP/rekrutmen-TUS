<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lowongan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_posisi',
        'prodi_id',
        'jenjang_minimal',
        'minimal_ipk',
        'prodi_prioritas',
        'skill_dibutuhkan',
        'kuota',
        'tanggal_tutup',
        'deskripsi',
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
                    ->withPivot(['status', 'catatan'])
                    ->withTimestamps();
    }

    // ── Computed ──────────────────────────────────────────────────

    /**
     * Sisa kuota (kuota dikurangi jumlah pendaftar yang aktif).
     */
    public function getSisaKuotaAttribute(): int
    {
        return max(0, $this->kuota - $this->lamarans()->count());
    }

    public function isAktif(): bool
    {
        return $this->status === 'aktif' && $this->tanggal_tutup->isFuture();
    }
}
