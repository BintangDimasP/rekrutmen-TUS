<?php

namespace App\Models;

use App\Services\FonnteService;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $fillable = [
        'user_id',
        'judul',
        'pesan',
        'tipe',
        'dibaca',
    ];

    protected $casts = [
        'dibaca' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Tipe notifikasi yang juga dikirim via WhatsApp.
     */
    private const WA_TIPES = ['jadwal', 'status'];

    /**
     * Kirim notifikasi ke satu user.
     * Jika tipe termasuk jadwal/status, otomatis kirim juga via WhatsApp.
     */
    public static function kirim(int $userId, string $judul, string $pesan, string $tipe = 'info'): void
    {
        // 1. Simpan notifikasi in-app
        static::create([
            'user_id' => $userId,
            'judul'   => $judul,
            'pesan'   => $pesan,
            'tipe'    => $tipe,
            'dibaca'  => false,
        ]);

        // 2. Kirim WhatsApp jika tipe termasuk yang di-broadcast
        if (in_array($tipe, self::WA_TIPES)) {
            static::kirimWhatsApp($userId, $judul, $pesan);
        }
    }

    /**
     * Kirim pesan WhatsApp ke user berdasarkan nomor telepon yang tersedia.
     */
    private static function kirimWhatsApp(int $userId, string $judul, string $pesan): void
    {
        $user = User::with(['pelamar', 'dosen'])->find($userId);

        if (!$user) {
            return;
        }

        // Ambil nomor telepon: pelamar atau dosen
        $noTelepon = null;

        if ($user->pelamar && $user->pelamar->no_telepon) {
            $noTelepon = $user->pelamar->no_telepon;
        } elseif ($user->dosen && $user->dosen->no_telepon) {
            $noTelepon = $user->dosen->no_telepon;
        }

        if (!$noTelepon) {
            return;
        }

        // Format pesan WhatsApp
        $waMessage = "*{$judul}*\n\n{$pesan}\n\n— Rekrutmen Telkom University";

        FonnteService::send($noTelepon, $waMessage);
    }
}
