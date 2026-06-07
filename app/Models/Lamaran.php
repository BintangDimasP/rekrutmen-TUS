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
        'mengundurkan_diri' => 'Mengundurkan Diri',
    ];

    protected $fillable = [
        'pelamar_id',
        'lowongan_id',
        'file_surat_lamaran',
        'file_sk_penyetaraan',
        'file_surat_pemberhentian',
        'snapshot_data',
        'status',
        'tanggal_wawancara',
        'link_zoom',
        'catatan_admin',
    ];

    protected $casts = [
        'tanggal_wawancara' => 'date',
        'snapshot_data'     => 'array',
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

    /**
     * Kembalikan object pelamar yang "efektif" untuk tampilan:
     * pakai snapshot jika ada, fallback ke relasi live.
     */
    public function getEffectivePelamarAttribute(): object
    {
        if (!empty($this->snapshot_data)) {
            $snap = $this->snapshot_data;
            // Cast date fields agar konsisten
            foreach (['tanggal_lahir', 'tanggal_tes_bahasa'] as $field) {
                if (!empty($snap[$field])) {
                    $snap[$field] = \Carbon\Carbon::parse($snap[$field]);
                }
            }
            return (object) $snap;
        }
        return $this->pelamar;
    }
}
