<?php

namespace App\Http\Controllers\Pelamar;

use App\Http\Controllers\Controller;
use App\Models\Lamaran;
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
}
