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
    public function index(Request $request)
    {
        $pelamar = auth()->user()->pelamar;

        // Guard: jika record Pelamar belum ada, arahkan ke halaman profil
        if (!$pelamar) {
            return redirect()->route('pelamar.profil.index')
                ->with('warning', 'Lengkapi profil Anda terlebih dahulu.');
        }

        $lamarans = Lamaran::with(['lowongan.prodi'])
            ->where('pelamar_id', $pelamar->id)
            ->latest()
            ->paginate(100); // Load all — pelamar biasanya punya < 50 lamaran

        $prodis = \App\Models\Prodi::orderBy('nama')->get();

        return view('pelamar.history.index', compact('lamarans', 'prodis'));
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

        $jadwals = JadwalSeleksi::with(['penguji', 'penilaian'])
            ->where('pelamar_id', $pelamar->id)
            ->where('lowongan_id', $lamaran->lowongan_id)
            ->get();

        // Get all jadwals grouped by tipe_seleksi
        $wawancara = $jadwals->where('tipe_seleksi', 'wawancara')->values();
        $micro = $jadwals->where('tipe_seleksi', 'micro_teaching')->values();

        return view('pelamar.history.show', compact('lamaran', 'pelamar', 'wawancara', 'micro'));
    }
}
