<?php

namespace App\Models;

use App\Traits\NotificationMessage;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    use NotificationMessage;

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
     * Jika tipe termasuk jadwal/status DAN ada template WA, kirim juga via WhatsApp.
     *
     * @param int         $userId
     * @param string      $judul
     * @param string      $pesan
     * @param string      $tipe         info|jadwal|status|sistem|pelamar
     * @param string|null $waTemplate   Nama template Wappin (null = tidak kirim WA)
     * @param array       $waParams     Parameter template ['param1', 'param2', ...]
     * @param string      $waUrl        URL untuk button template ('-' = tidak ada)
     */
    public static function kirim(
        int $userId,
        string $judul,
        string $pesan,
        string $tipe = 'info',
        ?string $waTemplate = null,
        array $waParams = [],
        string $waUrl = '-'
    ): void {
        // 1. Simpan notifikasi in-app
        static::create([
            'user_id' => $userId,
            'judul'   => $judul,
            'pesan'   => $pesan,
            'tipe'    => $tipe,
            'dibaca'  => false,
        ]);

        // 2. Kirim WhatsApp jika tipe termasuk yang di-broadcast DAN template disediakan
        if (in_array($tipe, self::WA_TIPES) && $waTemplate) {
            static::kirimWhatsApp($userId, $waTemplate, $waParams, $waUrl);
        }
    }

    /**
     * Kirim notifikasi sistem (log aktivitas) - tanpa WhatsApp
     */
    public static function kirimSistem(int $userId, string $judul, string $pesan): void
    {
        static::create([
            'user_id' => $userId,
            'judul'   => $judul,
            'pesan'   => $pesan,
            'tipe'    => 'sistem',
            'dibaca'  => false,
        ]);
    }

    /**
     * Kirim notifikasi aktivitas pelamar - tanpa WhatsApp
     */
    public static function kirimAktivitasPelamar(int $userId, string $judul, string $pesan): void
    {
        static::create([
            'user_id' => $userId,
            'judul'   => $judul,
            'pesan'   => $pesan,
            'tipe'    => 'pelamar',
            'dibaca'  => false,
        ]);
    }

    /**
     * Kirim notifikasi aksi kaprodi (rekomendasi/tidak rekomendasi) - tanpa WhatsApp
     */
    public static function kirimKaprodi(int $userId, string $judul, string $pesan): void
    {
        static::create([
            'user_id' => $userId,
            'judul'   => $judul,
            'pesan'   => $pesan,
            'tipe'    => 'kaprodi',
            'dibaca'  => false,
        ]);
    }

    /**
     * Kirim pesan WhatsApp via Wappin template.
     */
    private static function kirimWhatsApp(int $userId, string $templateName, array $params, string $url = '-'): void
{
    $user = User::with(['pelamar', 'dosen'])->find($userId);

    if (!$user) {
        \Log::info('WA skip: user tidak ditemukan', ['user_id' => $userId]);
        return;
    }

    $noTelepon = null;
    if ($user->pelamar && $user->pelamar->no_telepon) {
        $noTelepon = $user->pelamar->no_telepon;
    } elseif ($user->dosen && $user->dosen->no_telepon) {
        $noTelepon = $user->dosen->no_telepon;
    }

    if (!$noTelepon) {
        \Log::info('WA skip: no_telepon kosong', [
            'user_id' => $userId,
            'has_pelamar' => (bool) $user->pelamar,
            'has_dosen' => (bool) $user->dosen,
        ]);
        return;
    }

    $result = (new static)->sendWhatsapp($url, $noTelepon, $templateName, $params);
    \Log::info('WA send result', ['user_id' => $userId, 'to' => $noTelepon, 'template' => $templateName, 'result' => $result]);
}
}
