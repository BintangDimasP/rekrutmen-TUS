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

        return view('admin.penguji.index', compact('pengujis', 'calonPengujis'));
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

                // Auto-provisioning User Account untuk Login Penguji
                // Cek apakah sudah punya akun (mungkin dari status Kaprodi atau lainnya)
                $user = User::where('email', $dosen->email)->first();

                if (!$user) {
                    User::create([
                        'name' => $dosen->nama,
                        'email' => $dosen->email,
                        'password' => Hash::make('penguji123'),
                        'role' => 'penguji',
                        'prodi_id' => $dosen->prodi_id
                    ]);
                } else {
                    // Jika user sudah ada (misal dia kaprodi), maka ia tetap memakai akun lamanya.
                    // Namun kita tetap harus pastikan bahwa role utamanya setidaknya bisa login.
                    // Karena enum role ('admin', 'pelamar', 'penguji', 'kaprodi'), jika dia kaprodi, 
                    // role tetap kaprodi, tapi dia bisa diakses karena logic kaprodi mencakup penguji nantinya
                    // atau ubah role menjadi penguji jika dia sebelumnya bukan apa-apa (misal akun mati)
                    if (!in_array($user->role, ['admin', 'kaprodi', 'penguji'])) {
                        $user->update(['role' => 'penguji']);
                    }
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

            // Jika dosen ini bukan kaprodi, maka "disable" akun pengujinya dengan menghapus akunnya atau mengubah rolenya
            // Di sistem ini kita hapus saja akun usernya untuk keamanan, kecuali dia kaprodi
            if (!$penguji->is_kaprodi) {
                User::where('email', $penguji->email)->where('role', 'penguji')->delete();
            }
        });

        return redirect()->route('admin.penguji.index')->with('success', 'Status penguji untuk ' . $penguji->nama . ' telah dicabut.');
    }
}

