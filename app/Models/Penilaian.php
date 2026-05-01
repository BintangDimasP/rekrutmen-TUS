<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
    protected $fillable = [
        'jadwal_seleksi_id',
        'kategori_1',
        'kategori_2',
        'kategori_3',
        'detail_nilai',
        'total_nilai',
        'catatan',
    ];

    protected $casts = [
        'detail_nilai' => 'array',
    ];

    public function jadwal()
    {
        return $this->belongsTo(JadwalSeleksi::class, 'jadwal_seleksi_id');
    }
}
