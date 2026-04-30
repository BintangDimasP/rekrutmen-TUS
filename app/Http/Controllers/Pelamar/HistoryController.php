<?php

namespace App\Http\Controllers\Pelamar;

use App\Http\Controllers\Controller;
use App\Models\Lamaran;
use App\Models\JadwalSeleksi;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    /**
     * Menampilkan riwayat pendaftaran pelamar.
     */
    public function index()
    {
        $pelamar = auth()->user()->pelamar;
        
        $lamarans = Lamaran::with(['lowongan.prodi'])
            ->where('pelamar_id', $pelamar->id)
            ->latest()
            ->get();

        return view('pelamar.history.index', compact('lamarans'));
    }

    /**
     * Menampilkan detail lamaran pelamar.
     */
    public function show(Lamaran $lamaran)
    {
        $pelamar = auth()->user()->pelamar;
        
        if ($lamaran->pelamar_id !== $pelamar->id) {
            abort(403, 'Anda tidak memiliki akses ke data lamaran ini.');
        }

        $lamaran->load(['lowongan.prodi']);

        $jadwals = JadwalSeleksi::with('penguji')
            ->where('pelamar_id', $pelamar->id)
            ->where('lowongan_id', $lamaran->lowongan_id)
            ->get();

        $wawancara = $jadwals->where('tipe_seleksi', 'tahap1')->first();
        $micro = $jadwals->where('tipe_seleksi', 'tahap2')->first();

        return view('pelamar.history.show', compact('lamaran', 'pelamar', 'wawancara', 'micro'));
    }
}
