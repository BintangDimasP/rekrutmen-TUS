<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'penguji_password',
        'role',
        'prodi_id',
    ];

    protected $hidden = [
        'password',
        'penguji_password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ── Role helpers ─────────────────────────────────────────────

    public function isAdmin(): bool    { return $this->role === 'admin'; }
    public function isPelamar(): bool  { return $this->role === 'pelamar'; }
    public function isPenguji(): bool  { return $this->role === 'penguji'; }
    public function isKaprodi(): bool  { return $this->role === 'kaprodi'; }

    // ── Relasi ────────────────────────────────────────────────────

    public function pelamar()
    {
        return $this->hasOne(Pelamar::class);
    }

    /**
     * Prodi tempat user (dosen / penguji / kaprodi) mengajar.
     */
    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class);
    }

    /**
     * Cek apakah user ini adalah kaprodi dari prodi tertentu.
     */
    public function isKaprodiOf(int $prodiId): bool
    {
        return $this->role === 'kaprodi' && $this->prodi_id === $prodiId;
    }
}
