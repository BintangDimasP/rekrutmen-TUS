<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Hash;

class Dosen extends Model
{
    public const PENGAJAR_DOMAIN  = 'pengajar.telkomuniversity.ac.id';
    public const DEFAULT_PASSWORD = 'dosen123';

    protected $fillable = [
        'nama',
        'kode',
        'nip',
        'nidn',
        'email',
        'prodi_id',
        'is_penguji',
        'is_kaprodi',
    ];

    protected $casts = [
        'is_penguji' => 'boolean',
        'is_kaprodi' => 'boolean',
    ];

    // ── Relasi ────────────────────────────────────────────────────

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class);
    }

    /**
     * Akun user aktif milik dosen ini (hanya ada kalau sedang menjabat penguji/kaprodi).
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'dosen_id');
    }

    // ── Email helpers ─────────────────────────────────────────────

    public function generateEmailPrefix(): string
    {
        $parts  = preg_split('/\s+/', trim($this->nama));
        $prefix = strtolower(implode('', array_slice($parts, 0, 2)));
        return preg_replace('/[^a-z0-9]/', '', $prefix);
    }

    /**
     * Generate email @pengajar yang unik (terhadap users lain, kecuali user milik dosen ini sendiri).
     */
    public function generateUniqueEmail(?int $exceptUserId = null): string
    {
        $prefix  = $this->generateEmailPrefix();
        $email   = $prefix . '@' . self::PENGAJAR_DOMAIN;
        $counter = 1;

        while (
            User::where('email', $email)
                ->when($exceptUserId, fn($q) => $q->where('id', '!=', $exceptUserId))
                ->exists()
        ) {
            $email = $prefix . $counter . '@' . self::PENGAJAR_DOMAIN;
            $counter++;
        }

        return $email;
    }

    /**
     * Ambil user yang sudah ada, atau buat baru dengan password default.
     * HANYA dipanggil saat dosen ditunjuk sebagai penguji atau kaprodi.
     */
    public function getOrCreateUser(): User
    {
        $user = $this->user;

        if ($user) {
            return $user;
        }

        return User::create([
            'name'           => $this->nama,
            'email'          => $this->generateUniqueEmail(),
            'password'       => Hash::make(self::DEFAULT_PASSWORD),
            'password_plain' => self::DEFAULT_PASSWORD,
            'role'           => null,   // caller akan set role yang tepat
            'is_penguji'     => false,
            'is_kaprodi'     => false,
            'prodi_id'       => $this->prodi_id,
            'dosen_id'       => $this->id,
        ]);
    }
}
