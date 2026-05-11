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
        'role',
        'prodi_id',
        'dosen_id',
        'password_plain',
    ];

    protected $hidden = [
        'password',
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
     * Dosen yang terhubung dengan akun ini.
     */
    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class);
    }

    /**
     * Akun penguji yang terhubung dengan kaprodi ini.
     */
    public function penguji_user()
    {
        return $this->hasOne(User::class, 'dosen_id', 'dosen_id')
                    ->whereNotNull('users.dosen_id')
                    ->where('role', 'penguji')
                    ->where('id', '!=', $this->id);
    }

    /**
     * Cek apakah user ini adalah kaprodi dari prodi tertentu.
     */
    public function isKaprodiOf(int $prodiId): bool
    {
        return $this->role === 'kaprodi' && $this->prodi_id === $prodiId;
    }
}
