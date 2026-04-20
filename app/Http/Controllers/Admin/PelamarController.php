<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lamaran;
use Illuminate\Http\Request;

class PelamarController extends Controller
{
    /**
     * Tampilkan seluruh data pelamar beserta lamarannya di seluruh lowongan sistem.
     */
    public function index()
    {
        // Kita mengambil data dari entitas 'Lamaran' karena 1 pelamar bisa melamar ke banyak lowongan.
        // Tiap baris data merepresentasikan "Satu Berkas Lamaran".
        $lamarans = Lamaran::with(['pelamar.user', 'lowongan.prodi'])->latest()->get();

        return view('admin.pelamar.index', compact('lamarans'));
    }
}
