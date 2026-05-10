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
        $query = Pelamar::with(['user', 'lamarans.lowongan']);

        // Search Name/Phone
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_telepon', 'like', "%{$search}%");
            });
        }

        // Filter Prodi (Berdasarkan prodi dari lowongan yang dilamar)
        if ($request->filled('prodi_id')) {
            $prodiId = $request->prodi_id;
            $query->whereHas('lamarans.lowongan', function($q) use ($prodiId) {
                $q->where('prodi_id', $prodiId);
            });
        }

        $pelamars = $query->latest()->get();
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

