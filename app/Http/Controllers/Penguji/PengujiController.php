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

        $upcomingJadwals = JadwalSeleksi::where('penguji_id', $dosen->id)
            ->whereBetween('tanggal', [now()->startOfDay(), now()->addDays(7)->endOfDay()])
            ->with(['pelamar', 'lowongan.prodi', 'penilaian'])
            ->orderBy('tanggal', 'asc')
            ->orderBy('sesi', 'asc')
            ->get();

        return view('penguji.dashboard', compact('totalDiuji', 'totalDinilai', 'totalBelumDinilai', 'upcomingJadwals'));
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

        // Validate individual item scores (1-5 scale)
        $rules = ['catatan' => 'nullable|string'];
        for ($k = 1; $k <= 3; $k++) {
            for ($i = 1; $i <= 5; $i++) {
                $rules["k{$k}_item_{$i}"] = 'required|integer|min:1|max:5';
            }
        }
        $request->validate($rules);

        // Build detail scores array and compute category averages
        $detail = [];
        $categoryScores = [];
        for ($k = 1; $k <= 3; $k++) {
            $sum = 0;
            for ($i = 1; $i <= 5; $i++) {
                $val = (int) $request->input("k{$k}_item_{$i}");
                $detail["k{$k}_item_{$i}"] = $val;
                $sum += $val;
            }
            // Map 1-5 average to 0-100 scale: (avg/5)*100
            $categoryScores[$k] = round(($sum / 5) * 20);
        }

        $total = round(($categoryScores[1] + $categoryScores[2] + $categoryScores[3]) / 3);

        Penilaian::updateOrCreate(
            ['jadwal_seleksi_id' => $jadwal->id],
            [
                'kategori_1' => $categoryScores[1],
                'kategori_2' => $categoryScores[2],
                'kategori_3' => $categoryScores[3],
                'detail_nilai' => $detail,
                'total_nilai' => $total,
                'catatan' => $request->catatan,
            ]
        );

        return redirect()->route('penguji.pengujian.show', $jadwal->id)->with('success', 'Penilaian berhasil disimpan.');
    }
}
