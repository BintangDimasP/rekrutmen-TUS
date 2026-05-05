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

        // Filter Lowongan
        if ($request->filled('lowongan_id')) {
            $lowonganId = $request->lowongan_id;
            $query->whereHas('lamarans', function($q) use ($lowonganId) {
                $q->where('lowongan_id', $lowonganId);
            });
        }

        $pelamars = $query->latest()->get();
        $lowongans = \App\Models\Lowongan::orderBy('nama_posisi')->get();

        return view('admin.pelamar.index', compact('pelamars', 'lowongans'));
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

