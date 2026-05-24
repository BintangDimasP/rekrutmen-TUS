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
        'is_penguji',
        'is_kaprodi',
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
            'is_penguji'        => 'boolean',
            'is_kaprodi'        => 'boolean',
        ];
    }

    // ── Role helpers ─────────────────────────────────────────────

    public function isAdmin(): bool    { return $this->role === 'admin'; }
    public function isPelamar(): bool  { return $this->role === 'pelamar'; }
    public function isPenguji(): bool  { return $this->role === 'penguji'; }
    public function isKaprodi(): bool  { return $this->role === 'kaprodi'; }

    /**
     * Apakah user (dosen) memiliki rangkap role penguji + kaprodi.
     */
    public function hasMultipleRoles(): bool
    {
        return $this->is_penguji && $this->is_kaprodi;
    }

    /**
     * Daftar role yang dimiliki user dosen (untuk switcher).
     */
    public function availableDosenRoles(): array
    {
        $roles = [];
        if ($this->is_penguji) $roles[] = 'penguji';
        if ($this->is_kaprodi) $roles[] = 'kaprodi';
        return $roles;
    }

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
     * Cek apakah user ini adalah kaprodi dari prodi tertentu.
     */
    public function isKaprodiOf(int $prodiId): bool
    {
        return $this->role === 'kaprodi' && $this->prodi_id === $prodiId;
    }
}
