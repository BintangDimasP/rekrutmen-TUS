<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Models\Lamaran;
use App\Models\Pelamar;
use App\Models\Lowongan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KaprodiController extends Controller
{
    /**
     * Dapatkan prodi_id dari user kaprodi yang sedang login.
     */
    private function getProdiId(): ?int
    {
        return Auth::user()->prodi_id;
    }

    /**
     * Dashboard Kaprodi:
     * Statistik pelamar di prodi kaprodi tsb.
     */
    public function dashboard()
    {
        $prodiId = $this->getProdiId();

        // IDs lowongan milik prodi ini
        $lowonganIds = Lowongan::where('prodi_id', $prodiId)->pluck('id');

        // Statistik lamaran di prodi ini
        $totalPelamar  = Lamaran::whereIn('lowongan_id', $lowonganIds)
                            ->distinct('pelamar_id')->count('pelamar_id');

        $totalDiterima = Lamaran::whereIn('lowongan_id', $lowonganIds)
                            ->where('status', 'diterima')
                            ->distinct('pelamar_id')->count('pelamar_id');

        $totalDitolak  = Lamaran::whereIn('lowongan_id', $lowonganIds)
                            ->where('status', 'ditolak')
                            ->distinct('pelamar_id')->count('pelamar_id');

        $totalProses   = Lamaran::whereIn('lowongan_id', $lowonganIds)
                            ->whereNotIn('status', ['diterima', 'ditolak'])
                            ->distinct('pelamar_id')->count('pelamar_id');

        // Lamaran terbaru di prodi ini (untuk tabel)
        $lamaranTerbaru = Lamaran::with(['pelamar.user', 'lowongan'])
                            ->whereIn('lowongan_id', $lowonganIds)
                            ->latest()
                            ->take(10)
                            ->get();

        return view('kaprodi.dashboard', compact(
            'totalPelamar', 'totalDiterima', 'totalDitolak', 'totalProses', 'lamaranTerbaru'
        ));
    }

    /**
     * Daftar Pelamar yang mendaftar di prodi kaprodi ini.
     */
    public function pelamar(Request $request)
    {
        $prodiId = $this->getProdiId();

        // IDs lowongan milik prodi ini
        $lowonganIds = Lowongan::where('prodi_id', $prodiId)->pluck('id');

        // Ambil pelamar yang melamar ke lowongan di prodi ini
        $query = Pelamar::with(['user', 'lamarans' => function($q) use ($lowonganIds) {
                    $q->whereIn('lowongan_id', $lowonganIds)->with('lowongan');
                }])
                ->whereHas('lamarans', function($q) use ($lowonganIds) {
                    $q->whereIn('lowongan_id', $lowonganIds);
                });

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_telepon', 'like', "%{$search}%");
            });
        }

        // Filter lowongan (hanya lowongan di prodi ini)
        if ($request->filled('lowongan_id')) {
            $lowonganId = $request->lowongan_id;
            // Pastikan lowongan yang dipilih memang milik prodi kaprodi
            if ($lowonganIds->contains($lowonganId)) {
                $query->whereHas('lamarans', function($q) use ($lowonganId) {
                    $q->where('lowongan_id', $lowonganId);
                });
            }
        }

        // Filter status
        if ($request->filled('status')) {
            $status = $request->status;
            $query->whereHas('lamarans', function($q) use ($status, $lowonganIds) {
                $q->whereIn('lowongan_id', $lowonganIds)->where('status', $status);
            });
        }

        $pelamars  = $query->latest()->paginate(10)->appends($request->query());
        $lowongans = Lowongan::where('prodi_id', $prodiId)->orderBy('nama_posisi')->get();

        return view('kaprodi.pelamar', compact('pelamars', 'lowongans'));
    }

    /**
     * Detail Pelamar (view only, layout seperti admin/pelamar/show).
     */
    public function showPelamar(Pelamar $pelamar)
    {
        $prodiId = $this->getProdiId();
        $lowonganIds = Lowongan::where('prodi_id', $prodiId)->pluck('id');

        // Pastikan pelamar ini memang melamar ke prodi kaprodi
        $hasLamaran = $pelamar->lamarans()->whereIn('lowongan_id', $lowonganIds)->exists();
        if (!$hasLamaran) {
            abort(403, 'Pelamar ini tidak melamar ke prodi Anda.');
        }

        // Load relasi yang diperlukan
        $pelamar->load(['user', 'lamarans' => function($q) use ($lowonganIds) {
            $q->whereIn('lowongan_id', $lowonganIds)->with('lowongan');
        }]);

        return view('kaprodi.pelamar-show', compact('pelamar'));
    }
}
