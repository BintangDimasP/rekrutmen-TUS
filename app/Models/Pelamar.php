<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelamar extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',

        // Data Diri
        'nik',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'no_telepon',
        'jenis_kelamin',
        'alamat',

        // Riwayat Pendidikan
        'jenjang',
        'institusi',
        'prodi_pendidikan',
        'ipk',
        'file_ijazah',
        'file_transkrip',

        // Dokumen Pendukung
        'file_cv',
        'file_pas_foto',
        'file_ktp',
        'kategori_sertifikat',
        'file_sertifikat',
        'jenis_tes_bahasa',
        'skor_bahasa',
        'tanggal_tes_bahasa',
        'file_sertifikat_bahasa',

        // Riwayat Akademik
        'nidn',
        'homebase',
        'jabatan_akademik',
        'minat_riset',
        'h_index',
        'file_kartu_dosen',
    ];

    protected $casts = [
        'tanggal_lahir'      => 'date',
        'tanggal_tes_bahasa' => 'date',
        'ipk'                => 'decimal:2',
        'skor_bahasa'        => 'decimal:2',
    ];

    // ── Relasi ──────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Pelamar bisa mengajukan lamaran ke banyak lowongan.
     */
    public function lowongans()
    {
        return $this->belongsToMany(Lowongan::class, 'lamarans')
                    ->withPivot(['status', 'catatan'])
                    ->withTimestamps();
    }

    public function lamarans()
    {
        return $this->hasMany(Lamaran::class);
    }
}
