<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dosen extends Model
{
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

    /**
     * Prodi tempat dosen ini bernaung.
     */
    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class);
    }

    /**
     * Akun user yang terhubung dengan dosen ini (bisa lebih dari 1: penguji + kaprodi).
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'dosen_id');
    }

    /**
     * Generate email prefix dari 2 kata pertama nama dosen.
     * Contoh: "Bintang Dimas Prawira Satya" -> "bintangdimas"
     */
    public function generateEmailPrefix(): string
    {
        $parts = preg_split('/\s+/', trim($this->nama));
        return strtolower(implode('', array_slice($parts, 0, 2)));
    }

    /**
     * Generate email unik untuk domain tertentu, dengan penomoran otomatis jika duplikat.
     */
    public function generateUniqueEmail(string $domain): string
    {
        $prefix = $this->generateEmailPrefix();
        $email = $prefix . '@' . $domain;

        // Cek duplikat di tabel users (kecuali milik dosen ini sendiri)
        $counter = 1;
        while (User::where('email', $email)->where(function ($q) {
            $q->whereNull('dosen_id')->orWhere('dosen_id', '!=', $this->id);
        })->exists()) {
            $email = $prefix . $counter . '@' . $domain;
            $counter++;
        }

        return $email;
    }
}
