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
        'kewarganegaraan',
        'status_pernikahan',
        'alamat_domisili',
        'alamat_ktp',
        'alamat', // Keep for backward compatibility if needed temporarily

        // Riwayat Pendidikan Level 1
        'jenjang',
        'institusi',
        'prodi_pendidikan',
        'akreditas',
        'no_ijazah',
        'ipk',
        'file_ijazah',
        'file_transkrip',

        // Riwayat Pendidikan Level 2
        'jenjang_2',
        'institusi_2',
        'prodi_pendidikan_2',
        'akreditas_2',
        'no_ijazah_2',
        'ipk_2',
        'file_ijazah_2',
        'file_transkrip_2',

        // Riwayat Pendidikan Level 3
        'jenjang_3',
        'institusi_3',
        'prodi_pendidikan_3',
        'akreditas_3',
        'no_ijazah_3',
        'ipk_3',
        'file_ijazah_3',
        'file_transkrip_3',

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
        'file_jad',
        'file_pak',
        'file_registrasi_dosen',
        'file_inpassing',
        'file_serdik',
        'file_skpp_serdos',
        'file_pernyataan_lolos_butuh',
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
                    ->withPivot(['status'])
                    ->withTimestamps();
    }

    public function lamarans()
    {
        return $this->hasMany(Lamaran::class);
    }

    /**
     * Lowongan yang disimpan/di-bookmark oleh pelamar.
     */
    public function savedLowongans()
    {
        return $this->belongsToMany(Lowongan::class, 'saved_lowongans')
                    ->withTimestamps();
    }
}
