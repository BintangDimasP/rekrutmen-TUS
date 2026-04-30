<?php

namespace App\Http\Controllers\Penguji;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalSeleksi;
use App\Models\Penilaian;
use App\Models\Dosen;
use Illuminate\Support\Facades\Auth;

class PengujiController extends Controller
{
    private function getDosen()
    {
        return Dosen::where('email', Auth::user()->email)->first();
    }

    public function dashboard()
    {
        $dosen = $this->getDosen();
        if (!$dosen) {
            abort(403, 'Anda tidak terdaftar sebagai dosen/penguji.');
        }

        $jadwals = JadwalSeleksi::where('penguji_id', $dosen->id)->with('penilaian')->get();
        
        $totalDiuji = $jadwals->count();
        $totalDinilai = $jadwals->whereNotNull('penilaian')->count();
        $totalBelumDinilai = $totalDiuji - $totalDinilai;

        return view('penguji.dashboard', compact('totalDiuji', 'totalDinilai', 'totalBelumDinilai'));
    }

    public function index()
    {
        $dosen = $this->getDosen();
        if (!$dosen) {
            abort(403, 'Anda tidak terdaftar sebagai dosen/penguji.');
        }

        $jadwals = JadwalSeleksi::where('penguji_id', $dosen->id)
            ->with(['pelamar', 'lowongan.prodi', 'penilaian'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('sesi', 'asc')
            ->get();

        return view('penguji.pengujian.index', compact('jadwals'));
    }

    public function show(JadwalSeleksi $jadwal)
    {
        $dosen = $this->getDosen();
        if ($jadwal->penguji_id !== $dosen?->id) {
            abort(403, 'Akses ditolak.');
        }

        $jadwal->load(['pelamar.user', 'lowongan.prodi', 'penilaian']);
        
        return view('penguji.pengujian.show', compact('jadwal'));
    }

    public function uji(JadwalSeleksi $jadwal)
    {
        $dosen = $this->getDosen();
        if ($jadwal->penguji_id !== $dosen?->id) {
            abort(403, 'Akses ditolak.');
        }

        $jadwal->load(['pelamar.user', 'lowongan.prodi', 'penilaian']);
        
        return view('penguji.pengujian.uji', compact('jadwal'));
    }

    public function storeNilai(Request $request, JadwalSeleksi $jadwal)
    {
        $dosen = $this->getDosen();
        if ($jadwal->penguji_id !== $dosen?->id) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'kategori_1' => 'required|integer|min:0|max:100',
            'kategori_2' => 'required|integer|min:0|max:100',
            'kategori_3' => 'required|integer|min:0|max:100',
            'catatan' => 'nullable|string'
        ]);

        $total = round(($request->kategori_1 + $request->kategori_2 + $request->kategori_3) / 3);

        Penilaian::updateOrCreate(
            ['jadwal_seleksi_id' => $jadwal->id],
            [
                'kategori_1' => $request->kategori_1,
                'kategori_2' => $request->kategori_2,
                'kategori_3' => $request->kategori_3,
                'total_nilai' => $total,
                'catatan' => $request->catatan,
            ]
        );

        return back()->with('success', 'Penilaian berhasil disimpan.');
    }
}
