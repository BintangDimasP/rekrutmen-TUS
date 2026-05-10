<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PengujiController extends Controller
{
    /**
     * Tampilkan tabel dosen yang merupakan penguji.
     */
    public function index(Request $request)
    {
        $query = Dosen::with('prodi')->where('is_penguji', true);

        // Filter Prodi
        if ($request->filled('prodi_id')) {
            $query->where('prodi_id', $request->prodi_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%");
            });
        }

        $pengujis = $query->get();

        // Ambil email penguji dari tabel users untuk setiap penguji
        $pengujiEmails = User::whereIn('dosen_id', $pengujis->pluck('id'))
            ->where('role', 'penguji')
            ->pluck('email', 'dosen_id');

        // Calon penguji adalah dosen yang belum menjadi penguji (untuk form Tunjuk)
        $calonPengujis = Dosen::with('prodi')->where('is_penguji', false)->get();

        // Daftar semua prodi untuk filter
        $prodis = \App\Models\Prodi::orderBy('nama')->get();

        return view('admin.penguji.index', compact('pengujis', 'pengujiEmails', 'calonPengujis', 'prodis'));
    }

    /**
     * Detail penguji.
     */
    public function show(Dosen $penguji)
    {
        $penguji->load('prodi');

        // Ambil email akun penguji
        $pengujiEmail = User::where('dosen_id', $penguji->id)
            ->where('role', 'penguji')
            ->value('email');

        return view('admin.penguji.show', compact('penguji', 'pengujiEmail'));
    }

    /**
     * Tunjuk dosen-dosen terpilih menjadi penguji.
     * Buat akun user baru dengan email @penguji.telkomuniversity.ac.id
     */
    public function store(Request $request)
    {
        $request->validate([
            'dosen_ids' => 'required|array',
            'dosen_ids.*' => 'exists:dosens,id',
        ]);

        DB::transaction(function () use ($request) {
            $dosens = Dosen::whereIn('id', $request->dosen_ids)->get();

            foreach ($dosens as $dosen) {
                // Update status dosen
                $dosen->update(['is_penguji' => true]);

                // Cek apakah sudah punya akun penguji
                $existingPenguji = User::where('dosen_id', $dosen->id)
                    ->where('role', 'penguji')
                    ->first();

                if (!$existingPenguji) {
                    // Generate email penguji dan buat akun baru
                    $email = $dosen->generateUniqueEmail('penguji.telkomuniversity.ac.id');

                    User::create([
                        'name'     => $dosen->nama,
                        'email'    => $email,
                        'password' => Hash::make('penguji123'),
                        'role'     => 'penguji',
                        'prodi_id' => $dosen->prodi_id,
                        'dosen_id' => $dosen->id,
                    ]);
                }
            }
        });

        return back()->with('success', count($request->dosen_ids) . ' dosen berhasil ditunjuk sebagai penguji.');
    }

    /**
     * Cabut status penguji dari dosen.
     * Hapus akun user penguji. Jika juga bukan kaprodi, email dosen kembali ke '-'.
     */
    public function destroy(Dosen $penguji)
    {
        DB::transaction(function () use ($penguji) {
            $penguji->update(['is_penguji' => false]);

            // Hapus akun penguji (akun kaprodi tetap ada jika ada)
            User::where('dosen_id', $penguji->id)
                ->where('role', 'penguji')
                ->delete();
        });

        return redirect()->route('admin.penguji.index')->with('success', 'Status penguji untuk ' . $penguji->nama . ' telah dicabut.');
    }
}
