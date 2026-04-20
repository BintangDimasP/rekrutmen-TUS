<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
