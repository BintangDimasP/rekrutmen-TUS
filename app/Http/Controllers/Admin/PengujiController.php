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
    public function index()
    {
        // Penguji adalah dosen yang is_penguji = true
        $pengujis = Dosen::with('prodi')->where('is_penguji', true)->get();
        
        // Calon penguji adalah dosen yang belum menjadi penguji (untuk form Tunjuk)
        $calonPengujis = Dosen::with('prodi')->where('is_penguji', false)->get();

        // Daftar prodi unik dari calon penguji untuk filter dropdown
        $prodis = $calonPengujis->pluck('prodi')->filter()->unique('id')->sortBy('nama')->values();

        return view('admin.penguji.index', compact('pengujis', 'calonPengujis', 'prodis'));
    }

    /**
     * Detail penguji.
     */
    public function show(Dosen $penguji)
    {
        $penguji->load('prodi');
        return view('admin.penguji.show', compact('penguji'));
    }

    /**
     * Tunjuk dosen-dosen terpilih menjadi penguji.
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

                // Cek apakah sudah punya akun
                $user = User::where('email', $dosen->email)->first();

                if (!$user) {
                    // Belum punya akun: buat akun penguji baru
                    User::create([
                        'name'     => $dosen->nama,
                        'email'    => $dosen->email,
                        'password' => Hash::make('penguji123'),
                        'role'     => 'penguji',
                        'prodi_id' => $dosen->prodi_id
                    ]);
                } elseif ($user->role === 'kaprodi') {
                    // Kaprodi yang ditunjuk penguji: simpan penguji_password terpisah
                    // Role TIDAK diubah agar tetap bisa akses kaprodi dashboard
                    $user->update([
                        'penguji_password' => Hash::make('penguji123'),
                    ]);
                } elseif (!in_array($user->role, ['admin', 'penguji'])) {
                    // Role lain (pelamar, dll): upgrade ke penguji
                    $user->update(['role' => 'penguji']);
                }
            }
        });

        return back()->with('success', count($request->dosen_ids) . ' dosen berhasil ditunjuk sebagai penguji.');
    }

    /**
     * Cabut status penguji dari dosen.
     */
    public function destroy(Dosen $penguji)
    {
        DB::transaction(function () use ($penguji) {
            $penguji->update(['is_penguji' => false]);

            $user = User::where('email', $penguji->email)->first();
            if ($user) {
                if ($user->role === 'kaprodi') {
                    // Kaprodi: hapus saja penguji_password-nya
                    $user->update(['penguji_password' => null]);
                } elseif ($user->role === 'penguji') {
                    // Penguji murni: hapus akun loginnya
                    $user->delete();
                }
            }
        });

        return redirect()->route('admin.penguji.index')->with('success', 'Status penguji untuk ' . $penguji->nama . ' telah dicabut.');
    }
}

