<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelamar;
use Illuminate\Http\Request;

class PelamarController extends Controller
{
    /**
     * Tampilkan seluruh data pelamar yang sudah registrasi di sistem.
     */
    public function index(Request $request)
    {
        $pelamars = Pelamar::with(['user', 'lamarans.lowongan'])->latest()->get();
        $prodis = \App\Models\Prodi::orderBy('nama')->get();

        return view('admin.pelamar.index', compact('pelamars', 'prodis'));
    }

    /**
     * Halaman detail seorang pelamar beserta semua lamarannya.
     */
    public function show(Pelamar $pelamar)
    {
        $pelamar->load(['user', 'lamarans.lowongan.prodi']);
        return view('admin.pelamar.show', compact('pelamar'));
    }
}

