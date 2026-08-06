<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prodi extends Model
{
    use HasFactory;
    protected $fillable = [
        'nama',
        'kode',
        'logo',
        'kaprodi_id',
    ];

    /**
     * Kaprodi yang memimpin prodi ini (melalui Dosen dengan is_kaprodi = true).
     */
    public function kaprodi(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Dosen::class, 'prodi_id')->where('is_kaprodi', true);
    }

    /**
     * Semua dosen (termasuk penguji/kaprodi) yang tergabung dalam prodi ini.
     */
    public function dosens(): HasMany
    {
        return $this->hasMany(Dosen::class, 'prodi_id');
    }

    /**
     * Penguji yang tergabung dalam prodi ini.
     */
    public function pengujis(): HasMany
    {
        return $this->hasMany(Dosen::class, 'prodi_id')->where('is_penguji', true);
    }

    /**
     * Placeholder relasi lowongan (tabel belum dibuat).
     * Diaktifkan saat model Lowongan & tabel tersedia.
     */
    /**
     * URL logo prodi. Jika logo tidak diisi/diunggah, fallback ke logo Telkom University.
     */
    public function getLogoUrlAttribute(): string
    {
        if ($this->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->logo)) {
            return asset('storage/' . $this->logo);
        }
        return asset('images/logo-icon.png');
    }
}
