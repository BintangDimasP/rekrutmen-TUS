<?php

namespace App\Http\Controllers\Pelamar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lamaran;
use App\Models\Lowongan;

class DashboardController extends Controller
{
    public function index()
    {
        $pelamar = auth()->user()->pelamar;
        
        $totalLamaran = 0;
        $lamaranAktif = 0;
        $lamaranDiterima = 0;
        $lamaranDitolak = 0;
        
        $recentLamarans = collect();

        if ($pelamar) {
            $lamarans = Lamaran::where('pelamar_id', $pelamar->id)->get();
            $totalLamaran = $lamarans->count();
            $lamaranAktif = $lamarans->whereIn('status', ['menunggu', 'seleksi_tahap1', 'seleksi_tahap2'])->count();
            $lamaranDiterima = $lamarans->where('status', 'diterima')->count();
            $lamaranDitolak = $lamarans->where('status', 'ditolak')->count();
            
            $recentLamarans = Lamaran::with('lowongan.prodi')
                ->where('pelamar_id', $pelamar->id)
                ->latest()
                ->take(5)
                ->get();
        }

        $lowonganCount = Lowongan::where('status', 'aktif')->count();

        return view('pelamar.dashboard', compact(
            'pelamar', 
            'totalLamaran', 
            'lamaranAktif', 
            'lamaranDiterima', 
            'lamaranDitolak',
            'recentLamarans',
            'lowonganCount'
        ));
    }
}
