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
     * Dilist per-lamaran agar konsisten dengan hitungan dashboard.
     */
    public function pelamar(Request $request)
    {
        $prodiId     = $this->getProdiId();
        $lowonganIds = Lowongan::where('prodi_id', $prodiId)->pluck('id');

        $query = Lamaran::with(['pelamar.user', 'lowongan'])
            ->whereIn('lowongan_id', $lowonganIds);

        // Search by nama atau no telepon
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('pelamar', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_telepon', 'like', "%{$search}%");
            });
        }

        // Filter lowongan
        if ($request->filled('lowongan_id') && $lowonganIds->contains($request->lowongan_id)) {
            $query->where('lowongan_id', $request->lowongan_id);
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $lamaransQuery = $query->latest()->get();
        $initialLamarans = $lamaransQuery->map(function ($lamaran) {
            return [
                'id' => $lamaran->id,
                'pelamar_id' => $lamaran->pelamar_id,
                'nama' => $lamaran->pelamar->nama,
                'jenjang' => $lamaran->pelamar->jenjang,
                'prodi_pendidikan' => $lamaran->pelamar->prodi_pendidikan,
                'no_telepon' => $lamaran->pelamar->no_telepon,
                'email' => $lamaran->pelamar->user?->email,
                'lowongan_id' => $lamaran->lowongan_id,
                'lowongan_nama' => $lamaran->lowongan->nama_posisi,
                'status' => $lamaran->status,
                'status_label' => $lamaran->status_label,
                'is_direkomendasikan_kaprodi' => $lamaran->is_direkomendasikan_kaprodi,
                'instansi' => $lamaran->pelamar->institusi,
            ];
        });

        $lowongans = Lowongan::where('prodi_id', $prodiId)->orderBy('nama_posisi')->get();

        return view('kaprodi.pelamar', compact('initialLamarans', 'lowongans'));
    }

    /**
     * Filter dan search pelamar via AJAX
     */
    public function filterPelamar(Request $request)
    {
        $prodiId     = $this->getProdiId();
        $lowonganIds = Lowongan::where('prodi_id', $prodiId)->pluck('id');

        $search      = $request->input('search', '');
        $lowongan_id = $request->input('lowongan_id', '');
        $status      = $request->input('status', '');

        $lamarans = Lamaran::with(['pelamar.user', 'lowongan'])
            ->whereIn('lowongan_id', $lowonganIds)
            ->get()
            ->filter(function ($lamaran) use ($search, $lowongan_id, $status) {
                $matchSearch = empty($search) || 
                    stripos($lamaran->pelamar->nama, $search) !== false ||
                    stripos($lamaran->pelamar->no_telepon, $search) !== false;
                
                $matchLowongan = empty($lowongan_id) || $lamaran->lowongan_id == $lowongan_id;
                $matchStatus = empty($status) || $lamaran->status === $status;
                
                return $matchSearch && $matchLowongan && $matchStatus;
            })
            ->values();

        return response()->json([
            'lamarans' => $lamarans->map(function ($lamaran) {
                return [
                    'id' => $lamaran->id,
                    'pelamar_id' => $lamaran->pelamar_id,
                    'nama' => $lamaran->pelamar->nama,
                    'jenjang' => $lamaran->pelamar->jenjang,
                    'prodi_pendidikan' => $lamaran->pelamar->prodi_pendidikan,
                    'no_telepon' => $lamaran->pelamar->no_telepon,
                    'email' => $lamaran->pelamar->user?->email,
                    'lowongan_id' => $lamaran->lowongan_id,
                    'lowongan_nama' => $lamaran->lowongan->nama_posisi,
                    'status' => $lamaran->status,
                    'status_label' => $lamaran->status_label,
                    'is_direkomendasikan_kaprodi' => $lamaran->is_direkomendasikan_kaprodi,
                    'instansi' => $lamaran->pelamar->institusi,
                ];
            }),
        ]);
    }

    /**
     * Detail Pelamar (view only, layout seperti admin/pelamar/show).
     */
    public function showPelamar(Pelamar $pelamar)
    {
        $prodiId     = $this->getProdiId();
        $lowonganIds = Lowongan::where('prodi_id', $prodiId)->pluck('id');

        // Pastikan pelamar ini memang melamar ke prodi kaprodi
        $hasLamaran = $pelamar->lamarans()->whereIn('lowongan_id', $lowonganIds)->exists();
        if (!$hasLamaran) {
            abort(403, 'Pelamar ini tidak melamar ke prodi Anda.');
        }

        // Load lamaran + lowongan milik prodi ini
        $pelamar->load(['user', 'lamarans' => function ($q) use ($lowonganIds) {
            $q->whereIn('lowongan_id', $lowonganIds)->with('lowongan');
        }]);

        // Ambil lamaran spesifik yang diklik (via lamaran_id), fallback ke yang pertama
        $activeLamaranId = request('lamaran_id');
        $activeLamaran = $activeLamaranId
            ? $pelamar->lamarans->firstWhere('id', $activeLamaranId)
            : $pelamar->lamarans->first();

        if (!$activeLamaran) {
            abort(404, 'Lamaran tidak ditemukan.');
        }

        // Snapshot dari lamaran yang diklik
        $snapshot = $activeLamaran->effective_pelamar;

        // Jadwal untuk lamaran yang diklik saja
        $jadwals = \App\Models\JadwalSeleksi::with(['penguji', 'penilaian'])
            ->where('pelamar_id', $pelamar->id)
            ->where('lowongan_id', $activeLamaran->lowongan_id)
            ->get();

        $micro     = $jadwals->where('tipe_seleksi', 'micro_teaching')->values();
        $wawancara = $jadwals->where('tipe_seleksi', 'wawancara')->values();

        return view('kaprodi.pelamar-show', compact('pelamar', 'activeLamaran', 'snapshot', 'micro', 'wawancara'));
    }

    /**
     * PATCH /kaprodi/lamaran/{lamaran}/toggle-rekomendasi
     * Toggle status rekomendasi Kaprodi (AJAX).
     */
    public function toggleRekomendasi(Request $request, \App\Models\Lamaran $lamaran)
    {
        $prodiId     = $this->getProdiId();
        $lowonganIds = Lowongan::where('prodi_id', $prodiId)->pluck('id');

        // Pastikan lamaran ini milik prodi kaprodi
        if (!$lowonganIds->contains($lamaran->lowongan_id)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        if ($lamaran->status !== 'menunggu') {
            return response()->json(['success' => false, 'message' => 'Status lamaran sudah diproses oleh Admin.'], 403);
        }

        $nilai = $request->input('value');
        if ($nilai !== null) {
            $nilai = filter_var($nilai, FILTER_VALIDATE_BOOLEAN);
        }
        
        $lamaran->update(['is_direkomendasikan_kaprodi' => $nilai]);

        return response()->json([
            'success' => true,
            'value'   => $lamaran->is_direkomendasikan_kaprodi,
        ]);
    }
}
