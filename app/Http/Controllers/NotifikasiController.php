<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    /**
     * Ambil semua notifikasi milik user yang sedang login (JSON).
     */
    public function index()
    {
        $notifikasis = Notifikasi::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->limit(50)
            ->get(['id', 'judul', 'pesan', 'tipe', 'dibaca', 'created_at']);

        $belumDibaca = $notifikasis->where('dibaca', false)->count();

        return response()->json([
            'notifikasis' => $notifikasis,
            'belum_dibaca' => $belumDibaca,
        ]);
    }

    /**
     * Tandai satu notifikasi sudah dibaca.
     */
    public function markRead(Notifikasi $notifikasi)
    {
        // Pastikan hanya pemilik yang bisa menandai
        if ($notifikasi->user_id !== auth()->id()) {
            abort(403);
        }
        $notifikasi->update(['dibaca' => true]);
        return response()->json(['ok' => true]);
    }

    /**
     * Tandai semua notifikasi milik user sebagai sudah dibaca.
     */
    public function markAllRead()
    {
        Notifikasi::where('user_id', auth()->id())
            ->where('dibaca', false)
            ->update(['dibaca' => true]);

        return response()->json(['ok' => true]);
    }
}
