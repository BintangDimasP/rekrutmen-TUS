<?php

namespace App\Http\Controllers;

use App\Models\Lowongan;
use App\Models\Pelamar;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        // ambil data lowongan
        $lowongans = Lowongan::where('status', 'aktif')
            ->whereDate('tanggal_tutup', '>=', now())
            ->with('prodi')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        // hitung pelamar
        $totalPendaftar = Pelamar::count();

        return view('landing', compact('lowongans', 'totalPendaftar'));
    }
}
