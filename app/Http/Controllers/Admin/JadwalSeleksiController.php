<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\JadwalSeleksi;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Pelamar;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JadwalSeleksiController extends Controller
{
    // ── Halaman daftar jadwal ────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = JadwalSeleksi::with(['pelamar.user', 'penguji', 'lowongan.prodi'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('tipe_seleksi')
            ->orderBy('sesi');

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->filled('penguji_id')) {
            $query->where('penguji_id', $request->penguji_id);
        }

        $jadwals = $query->get();
        $pengujis = Dosen::where('is_penguji', true)->orderBy('nama')->get();

        return view('admin.jadwal.index', compact('jadwals', 'pengujis'));
    }

    // ── Form penjadwalan ─────────────────────────────────────────────────

    public function create()
    {
        $prodis = Prodi::orderBy('nama')->get();
        $sessions = JadwalSeleksi::SESSIONS;
        return view('admin.jadwal.create', compact('prodis', 'sessions'));
    }

    // ── Simpan jadwal (multi-penguji) ─────────────────────────────────────
    //
    // Request payload shape (dari Alpine.js):
    //   tanggal      : 'YYYY-MM-DD'
    //   lowongan_id  : int
    //   schedule     : {
    //     [pelamar_id]: {
    //       wawancara: [
    //         { penguji_id: int, sesi: int, link: 'https://...' },
    //         ...
    //       ],
    //       micro: [
    //         { penguji_id: int, sesi: int },
    //         ...
    //       ]
    //     }
    //   }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date|after_or_equal:today',
            'lowongan_id' => 'required|exists:lowongans,id',
            'schedule' => 'required|array|min:1',
        ]);

        $tanggal = $request->tanggal;
        $lowonganId = (int) $request->lowongan_id;
        $schedule = $request->input('schedule', []);

        $saved = 0;
        $errors = [];

        DB::transaction(function () use ($tanggal, $lowonganId, $schedule, &$saved, &$errors) {

            foreach ($schedule as $pelamarIdRaw => $timedSlots) {
                $pelamarId = (int) $pelamarIdRaw;
                $pelamar = Pelamar::find($pelamarId);
                if (!$pelamar)
                    continue;

                // Tiap tipe: wawancara (tahap1) dan micro (tahap2)
                $tipeMap = [
                    'wawancara' => 'tahap1',
                    'micro' => 'tahap2',
                ];

                foreach ($tipeMap as $formKey => $dbTipe) {
                    $pengujiIds = array_filter(array_map('intval', $timedSlots[$formKey]['penguji_ids'] ?? []));
                    $sesi = (int) ($timedSlots[$formKey]['sesi'] ?? 0);
                    $link = $timedSlots[$formKey]['link'] ?? null;

                    foreach ($pengujiIds as $pengujiId) {
                        $pengujiId = (int) ($row['penguji_id'] ?? 0);
                        $sesi = (int) ($row['sesi'] ?? 0);
                        $link = $row['link'] ?? null;

                        // Lewati baris kosong
                        if (!$pengujiId || !$sesi)
                            continue;

                        // Validasi sesi sesuai tipe
                        $validSessions = array_keys(JadwalSeleksi::SESSIONS[$dbTipe] ?? []);
                        if (!in_array($sesi, array_map('intval', $validSessions))) {
                            $errors[] = "{$pelamar->nama} ({$dbTipe}): sesi {$sesi} tidak valid.";
                            continue;
                        }

                        // Cek penguji exists
                        if (!Dosen::where('id', $pengujiId)->where('is_penguji', true)->exists()) {
                            $errors[] = "{$pelamar->nama} ({$dbTipe}): penguji ID {$pengujiId} tidak ditemukan.";
                            continue;
                        }

                        // Cek duplikat: pelamar + penguji + tipe sudah ada di hari ini
                        if (
                            JadwalSeleksi::where('tanggal', $tanggal)
                                ->where('pelamar_id', $pelamarId)
                                ->where('penguji_id', $pengujiId)
                                ->where('tipe_seleksi', $dbTipe)
                                ->where('sesi', $sesi)
                                ->exists()
                        ) {
                            $errors[] = "{$pelamar->nama} ({$dbTipe} S{$sesi}): jadwal duplikat, dilewati.";
                            continue;
                        }

                        // Cek bentrok penguji (termasuk lintas tipe jam 13.00)
                        if (!JadwalSeleksi::isPengujiAvailable($tanggal, $pengujiId, $dbTipe, $sesi)) {
                            $label = JadwalSeleksi::SESSIONS[$dbTipe][$sesi]['label'] ?? "S{$sesi}";
                            $errors[] = "{$pelamar->nama} ({$dbTipe}): penguji bentrok di {$label}.";
                            continue;
                        }

                        // Cek bentrok pelamar (termasuk lintas tipe)
                        if (!JadwalSeleksi::isPelamarAvailable($tanggal, $pelamarId, $dbTipe, $sesi)) {
                            $label = JadwalSeleksi::SESSIONS[$dbTipe][$sesi]['label'] ?? "S{$sesi}";
                            $errors[] = "{$pelamar->nama} ({$dbTipe}): pelamar bentrok di {$label}.";
                            continue;
                        }

                        JadwalSeleksi::create([
                            'tanggal' => $tanggal,
                            'lowongan_id' => $lowonganId,
                            'pelamar_id' => $pelamarId,
                            'penguji_id' => $pengujiId,
                            'tipe_seleksi' => $dbTipe,
                            'sesi' => $sesi,
                            'link_meeting' => $link ?: null,
                        ]);

                        $saved++;
                    }

                    // Update status lamaran setelah wawancara (tahap1) terjadwal
                    if ($formKey === 'wawancara' && $saved > 0) {
                        Lamaran::where('pelamar_id', $pelamarId)
                            ->where('lowongan_id', $lowonganId)
                            ->where('status', 'seleksi_tahap1')
                            ->update(['status' => 'seleksi_tahap2']);
                    }
                }
            }
        });

        if ($saved === 0 && !empty($errors)) {
            return back()
                ->withErrors(['jadwal' => implode('; ', $errors)])
                ->withInput();
        }

        $message = "{$saved} jadwal berhasil disimpan.";
        if (!empty($errors)) {
            $message .= ' Sebagian gagal: ' . implode('; ', $errors);
        }

        return redirect()->route('admin.jadwal.index')->with('success', $message);
    }

    // ── Hapus satu jadwal ────────────────────────────────────────────────

    public function destroy(JadwalSeleksi $jadwal)
    {
        $jadwal->delete();
        return back()->with('success', 'Jadwal seleksi berhasil dihapus.');
    }

    // ═══════════════════════════════════════════════════════════════════
    // API Endpoints
    // ═══════════════════════════════════════════════════════════════════

    /** GET /admin/api/lowongan-by-prodi?prodi_id=X */
    public function apiLowongan(Request $request)
    {
        $lowongans = Lowongan::where('prodi_id', $request->prodi_id)
            ->where('status', 'aktif')
            ->orderBy('nama_posisi')
            ->get(['id', 'nama_posisi']);

        return response()->json($lowongans);
    }

    /** GET /admin/api/penguji-by-prodi?prodi_id=X */
    public function apiPenguji(Request $request)
    {
        $pengujis = Dosen::where('prodi_id', $request->prodi_id)
            ->where('is_penguji', true)
            ->orderBy('nama')
            ->get(['id', 'nama', 'kode']);

        return response()->json($pengujis);
    }

    /**
     * GET /admin/api/pelamar-by-lowongan?lowongan_id=X
     * Hanya pelamar berstatus seleksi_tahap1 yang tampil.
     */
    public function apiPelamar(Request $request)
    {
        $lowonganId = $request->lowongan_id;

        $pelamars = Pelamar::whereHas('lamarans', function ($q) use ($lowonganId) {
            $q->where('lowongan_id', $lowonganId)
                ->where('status', 'seleksi_tahap1');
        })
            ->with(['user'])
            ->orderBy('nama')
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'nama' => $p->nama,
                'email' => $p->user?->email ?? '-',
                'jenjang' => $p->jenjang ?? '-',
            ]);

        return response()->json($pelamars);
    }

    /**
     * GET /admin/api/sesi-taken-all?tanggal=YYYY-MM-DD&penguji_ids=1,2,3
     *
     * Mengembalikan sesi terpakai semua penguji yang diminta pada tanggal tsb,
     * termasuk efek cross-conflict jam 13.00 (tahap1 S4 ↔ tahap2 S1).
     *
     * Response: { "1": { "tahap1": [1,3], "tahap2": [2] }, "2": { ... } }
     */
    public function apiSesiTakenAll(Request $request)
    {
        $tanggal = $request->tanggal;
        $pengujiIds = array_filter(array_map('intval', explode(',', $request->penguji_ids ?? '')));

        if (!$tanggal || empty($pengujiIds)) {
            return response()->json([]);
        }

        // Ambil semua jadwal pada tanggal tsb untuk penguji yg diminta
        $rows = JadwalSeleksi::whereDate('tanggal', $tanggal)
            ->whereIn('penguji_id', $pengujiIds)
            ->get(['penguji_id', 'tipe_seleksi', 'sesi']);

        $result = [];
        foreach ($pengujiIds as $id) {
            $result[$id] = ['tahap1' => [], 'tahap2' => []];
        }

        foreach ($rows as $row) {
            $pid = (int) $row->penguji_id;
            $tipe = $row->tipe_seleksi;
            $sesi = (int) $row->sesi;

            if (!isset($result[$pid]))
                continue;

            $result[$pid][$tipe][] = $sesi;

            // Propagasi cross-conflict jam 13.00
            // tahap1 S4 (13.00) → blok tahap2 S1
            if ($tipe === 'tahap1' && $sesi === 4 && !in_array(1, $result[$pid]['tahap2'])) {
                $result[$pid]['tahap2'][] = 1;
            }
            // tahap2 S1 (13.00) → blok tahap1 S4
            if ($tipe === 'tahap2' && $sesi === 1 && !in_array(4, $result[$pid]['tahap1'])) {
                $result[$pid]['tahap1'][] = 4;
            }
        }

        // Deduplicate
        foreach ($result as &$v) {
            $v['tahap1'] = array_values(array_unique($v['tahap1']));
            $v['tahap2'] = array_values(array_unique($v['tahap2']));
        }

        return response()->json($result);
    }

    /**
     * GET /admin/api/sesi-tersedia?tanggal=X&penguji_id=Y&tipe=Z
     * (Tetap disediakan untuk kompatibilitas endpoint lama.)
     */
    public function apiAvailableSessions(Request $request)
    {
        $tipe = $request->tipe;
        $pengujiId = $request->penguji_id;
        $tanggal = $request->tanggal;

        if (!$tipe || !$pengujiId || !$tanggal) {
            return response()->json(['taken' => [], 'available' => []]);
        }

        $sessions = JadwalSeleksi::SESSIONS[$tipe] ?? [];
        $taken = [];
        $available = [];

        foreach ($sessions as $sesi => $info) {
            if (JadwalSeleksi::isPengujiAvailable($tanggal, (int) $pengujiId, $tipe, $sesi)) {
                $available[] = ['sesi' => $sesi, 'label' => $info['label']];
            } else {
                $taken[] = ['sesi' => $sesi, 'label' => $info['label']];
            }
        }

        return response()->json(compact('taken', 'available'));
    }
}