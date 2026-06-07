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
        $user = Auth::user();
        if ($user->dosen_id) {
            return Dosen::find($user->dosen_id);
        }
        return null;
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
            ->get()
            ->sortBy([
                // 1. Pending (belum dinilai) diutamakan
                fn ($a, $b) => ($a->penilaian ? 1 : 0) <=> ($b->penilaian ? 1 : 0),
                // 2. Micro teaching diutamakan
                fn ($a, $b) => ($a->tipe_seleksi === 'micro_teaching' ? 0 : 1) <=> ($b->tipe_seleksi === 'micro_teaching' ? 0 : 1),
                // 3. Tanggal terdekat (ASC)
                fn ($a, $b) => $a->tanggal <=> $b->tanggal,
                // 4. Sesi terdekat (ASC)
                fn ($a, $b) => $a->sesi <=> $b->sesi,
            ])
            ->values();

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
            ->get()
            ->sortBy([
                // 1. Pending (belum dinilai) diutamakan
                fn ($a, $b) => ($a->penilaian ? 1 : 0) <=> ($b->penilaian ? 1 : 0),
                // 2. Micro teaching diutamakan
                fn ($a, $b) => ($a->tipe_seleksi === 'micro_teaching' ? 0 : 1) <=> ($b->tipe_seleksi === 'micro_teaching' ? 0 : 1),
                // 3. Tanggal terdekat (ASC)
                fn ($a, $b) => $a->tanggal <=> $b->tanggal,
                // 4. Sesi terdekat (ASC)
                fn ($a, $b) => $a->sesi <=> $b->sesi,
            ])
            ->values();

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

        if ($jadwal->penilaian) {
            return redirect()->route('penguji.pengujian.show', $jadwal->id)
                ->with('success', 'Penilaian sudah dilakukan. Anda tidak dapat mengubah nilai yang sudah disubmit.');
        }

        if ($jadwal->tipe_seleksi === 'wawancara') {
            $microJadwals = JadwalSeleksi::where('pelamar_id', $jadwal->pelamar_id)
                ->where('tipe_seleksi', 'micro_teaching')
                ->with('penilaian')
                ->get();

            if ($microJadwals->isEmpty()) {
                abort(403, 'Belum ada jadwal Micro Teaching untuk pelamar ini.');
            }

            $belumDinilai = $microJadwals->filter(fn($j) => $j->penilaian === null);
            if ($belumDinilai->isNotEmpty()) {
                $count = $microJadwals->count();
                $done  = $microJadwals->count() - $belumDinilai->count();
                abort(403, "Semua penilaian Micro Teaching harus selesai terlebih dahulu ({$done}/{$count} penguji sudah menilai).");
            }
        }

        if ($jadwal->tipe_seleksi === 'micro_teaching') {
            return view('penguji.pengujian.uji_micro', compact('jadwal'));
        }

        return view('penguji.pengujian.uji', compact('jadwal'));
    }

    public function storeNilai(Request $request, JadwalSeleksi $jadwal)
    {
        $dosen = $this->getDosen();
        if ($jadwal->penguji_id !== $dosen?->id) {
            abort(403, 'Akses ditolak.');
        }

        if ($jadwal->penilaian) {
            return redirect()->route('penguji.pengujian.show', $jadwal->id)
                ->with('success', 'Penilaian sudah dilakukan sebelumnya. Tidak dapat mengubah nilai.');
        }

        if ($jadwal->tipe_seleksi === 'micro_teaching') {
            return $this->storeMicroTeaching($request, $jadwal);
        }

        return $this->storeWawancara($request, $jadwal);
    }

    private function storeWawancara(Request $request, JadwalSeleksi $jadwal)
    {
        // 8 indikator flat, semua dalam k1_item_1 s/d k1_item_8
        $totalItems = 8;

        $rules = [
            'catatan'          => 'nullable|string',
            'rekomendasi'      => 'required|in:direkomendasikan,tidak_direkomendasikan,perlu_dipertimbangkan',
            'prodi_tujuan'     => 'required|string|max:255',
            'status_rekrutmen' => 'nullable|in:on_going,praktisi_part_time,profesional_full_time',
        ];
        for ($i = 1; $i <= $totalItems; $i++) {
            $rules["k1_item_{$i}"] = 'required|integer|min:1|max:5';
        }
        $request->validate($rules);

        $detail = [];
        $sum = 0;
        for ($i = 1; $i <= $totalItems; $i++) {
            $val = (int) $request->input("k1_item_{$i}");
            $detail["k1_item_{$i}"] = $val;
            $sum += $val;
        }

        // rata-rata 8 indikator, skala 1-5
        $total = round($sum / $totalItems, 2);

        Penilaian::create([
            'jadwal_seleksi_id' => $jadwal->id,
            'kategori_1'        => $total,
            'detail_nilai'      => $detail,
            'total_nilai'       => $total,
            'catatan'           => $request->catatan,
            'rekomendasi'       => $request->rekomendasi,
            'prodi_tujuan'      => $request->prodi_tujuan,
            'status_rekrutmen'  => $request->status_rekrutmen,
        ]);

        return redirect()->route('penguji.pengujian.show', $jadwal->id)->with('success', 'Penilaian Wawancara berhasil disimpan.');
    }

    private function storeMicroTeaching(Request $request, JadwalSeleksi $jadwal)
    {
        // item counts per kategori: k1=2, k2=3, k3=6, k4=3, k5=1
        $itemCounts = [1 => 2, 2 => 3, 3 => 6, 4 => 3, 5 => 1];

        $rules = [
            'catatan'          => 'nullable|string',
            'rekomendasi'      => 'required|in:direkomendasikan,tidak_direkomendasikan,perlu_dipertimbangkan',
            'prodi_tujuan'     => 'required|string|max:255',
            'kelompok_keahlian'=> 'required|in:scout,ethes,riib',
            'bidang_keahlian'  => 'required|string|max:255',
        ];
        foreach ($itemCounts as $k => $count) {
            for ($i = 1; $i <= $count; $i++) {
                $rules["k{$k}_item_{$i}"] = 'required|integer|min:1|max:5';
            }
        }
        $request->validate($rules);

        $detail = [];
        $categoryScores = [];
        foreach ($itemCounts as $k => $count) {
            $sum = 0;
            for ($i = 1; $i <= $count; $i++) {
                $val = (int) $request->input("k{$k}_item_{$i}");
                $detail["k{$k}_item_{$i}"] = $val;
                $sum += $val;
            }
            // rata-rata per kategori, skala 1-5
            $categoryScores[$k] = round($sum / $count, 2);
        }

        // rata-rata keseluruhan, skala 1-5
        $total = round(array_sum($categoryScores) / count($categoryScores), 2);

        Penilaian::create([
            'jadwal_seleksi_id' => $jadwal->id,
            'kategori_1'        => $categoryScores[1],
            'kategori_2'        => $categoryScores[2],
            'kategori_3'        => $categoryScores[3],
            'kategori_4'        => $categoryScores[4],
            'kategori_5'        => $categoryScores[5],
            'detail_nilai'      => $detail,
            'total_nilai'       => $total,
            'catatan'           => $request->catatan,
            'rekomendasi'       => $request->rekomendasi,
            'prodi_tujuan'      => $request->prodi_tujuan,
            'kelompok_keahlian' => $request->kelompok_keahlian,
            'bidang_keahlian'   => $request->bidang_keahlian,
        ]);

        return redirect()->route('penguji.pengujian.show', $jadwal->id)->with('success', 'Penilaian Micro Teaching berhasil disimpan.');
    }
}
