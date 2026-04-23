<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lamaran extends Model
{
    use HasFactory;

    const STATUS_LABELS = [
        'menunggu'       => 'Menunggu',
        'seleksi_tahap1' => 'Seleksi Tahap 1',
        'seleksi_tahap2' => 'Seleksi Tahap 2',
        'diterima'       => 'Diterima',
        'ditolak'        => 'Ditolak',
    ];

    protected $fillable = [
        'pelamar_id',
        'lowongan_id',
        'file_surat_lamaran',
        'file_berkas_pendukung',
        'status',
        'catatan',
        'tanggal_wawancara',
        'link_zoom',
        'catatan_admin',
    ];

    protected $casts = [
        'tanggal_wawancara' => 'date',
    ];

    public function pelamar()
    {
        return $this->belongsTo(Pelamar::class);
    }

    public function lowongan()
    {
        return $this->belongsTo(Lowongan::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }
}
