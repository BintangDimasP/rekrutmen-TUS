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

    public function lowonganList()
    {
        $allLowongans = Lowongan::with('prodi')
            ->where('status', 'aktif')
            ->whereDate('tanggal_tutup', '>=', now())
            ->latest()
            ->get();

        $availableLowongans = $allLowongans
            ->filter(fn($l) => !$l->isFull())
            ->values();

        $prodis = \App\Models\Prodi::orderBy('nama')->get();

        return view('lowongan-list', compact('availableLowongans', 'prodis'));
    }

    public function show(Lowongan $lowongan)
    {
        // jika lowongan tidak aktif atau sudah ditutup, mungkin ada penanganan khusus, tapi tampilkan saja.
        $lowongan->load('prodi');
        return view('lowongan-detail', compact('lowongan'));
    }
}
