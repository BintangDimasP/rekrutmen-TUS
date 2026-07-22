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
    public function index(Request $request)
    {
        $pengujis = Dosen::with(['prodi', 'user'])->where('is_penguji', true)->orderBy('nama', 'asc')->get();

        $pengujiEmails = $pengujis
            ->mapWithKeys(fn($d) => [$d->id => $d->user?->email])
            ->filter();

        $calonPengujis = Dosen::with('prodi')->where('is_penguji', false)->orderBy('nama', 'asc')->get();
        $prodis = \App\Models\Prodi::orderBy('nama')->get();

        return view('admin.penguji.index', compact('pengujis', 'pengujiEmails', 'calonPengujis', 'prodis'));
    }

    public function show(Dosen $penguji)
    {
        $penguji->load(['prodi', 'user']);
        $pengujiEmail = $penguji->user?->email;

        return view('admin.penguji.show', compact('penguji', 'pengujiEmail'));
    }

    /**
     * Tunjuk dosen-dosen terpilih menjadi penguji.
     * Buat akun user jika belum ada. Kalau sudah ada (kaprodi), cukup set flag.
     */
    public function store(Request $request)
    {
        $request->validate([
            'dosen_ids'   => 'required|array',
            'dosen_ids.*' => 'exists:dosens,id',
        ]);

        DB::transaction(function () use ($request) {
            foreach (Dosen::whereIn('id', $request->dosen_ids)->get() as $dosen) {
                $dosen->update(['is_penguji' => true]);

                $user = $dosen->getOrCreateUser();

                $update = [
                    'is_penguji' => true,
                    'password'   => Hash::make(Dosen::DEFAULT_PASSWORD),
                ];

                // Jika user belum punya role aktif → set penguji
                // Jika sudah kaprodi → biarkan role tetap kaprodi (rangkap via flag)
                if (empty($user->role)) {
                    $update['role'] = 'penguji';
                }

                $user->update($update);
            }
        });

        $adminNama = auth()->user()->name ?? 'Admin';
        $waktu = now()->translatedFormat('d F Y \p\u\k\u\l H:i');
        $count = count($request->dosen_ids);
        \App\Models\User::where('role', 'admin')->each(function($u) use ($adminNama, $count, $waktu) {
            \App\Models\Notifikasi::kirimSistem($u->id, 'Penguji Ditunjuk', "Admin {$adminNama} menunjuk {$count} dosen sebagai penguji pada {$waktu}.");
        });

        return back()->with('success', count($request->dosen_ids) . ' dosen berhasil ditunjuk sebagai penguji.');
    }

    /**
     * Cabut status penguji dari dosen.
     * Kalau masih kaprodi → set role='kaprodi', hapus flag is_penguji.
     * Kalau tidak ada role lain → hapus akun user sepenuhnya.
     */
    public function destroy(Dosen $penguji)
    {
        $pengujiNama = $penguji->nama;

        DB::transaction(function () use ($penguji) {
            $penguji->update(['is_penguji' => false]);

            $user = $penguji->user;
            if (!$user) return;

            if ($user->is_kaprodi) {
                $user->update([
                    'is_penguji' => false,
                    'role'       => 'kaprodi',
                ]);
            } else {
                // Tidak ada role tersisa → hapus akun
                $user->delete();
            }
        });

        $adminNama = auth()->user()->name ?? 'Admin';
        $waktu = now()->translatedFormat('d F Y \p\u\k\u\l H:i');
        \App\Models\User::where('role', 'admin')->each(function($u) use ($adminNama, $pengujiNama, $waktu) {
            \App\Models\Notifikasi::kirimSistem($u->id, 'Penguji Dicabut', "Admin {$adminNama} mencabut status penguji dari {$pengujiNama} pada {$waktu}.");
        });

        return redirect()->route('admin.penguji.index')
            ->with('success', 'Status penguji untuk ' . $penguji->nama . ' telah dicabut.');
    }
}
