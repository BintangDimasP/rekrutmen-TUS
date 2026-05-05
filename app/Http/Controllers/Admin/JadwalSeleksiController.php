<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\JadwalSeleksi;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Notifikasi;
use App\Models\Pelamar;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JadwalSeleksiController extends Controller
{
    // ── Halaman daftar jadwal ────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = JadwalSeleksi::with(['pelamar.user', 'penguji', 'lowongan.prodi', 'penilaian'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('tipe_seleksi')
            ->orderBy('sesi');

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->filled('penguji_id')) {
            $query->where('penguji_id', $request->penguji_id);
        }

        $jadwals  = $query->get();
        $pengujis = Dosen::where('is_penguji', true)->orderBy('nama')->get();

        // Gabungkan baris pelamar+lowongan yang sama menjadi satu baris tabel
        $rows = $jadwals
            ->groupBy(fn($j) => $j->pelamar_id . '_' . $j->lowongan_id)
            ->map(function ($group) {
                $wawancara = $group->where('tipe_seleksi', 'tahap1')->values();
                $micro     = $group->where('tipe_seleksi', 'tahap2')->values();
                $first     = $group->first();

                // Kumpulkan semua penguji unik dari kedua tipe
                $allPengujis = $group->map->penguji->unique('id')->values();

                // Tanggal: gunakan tahap1 jika ada, fallback ke tahap2
                $tanggal = $wawancara->isNotEmpty()
                    ? $wawancara->first()->tanggal
                    : $first->tanggal;

                return (object) [
                    'pelamar'   => $first->pelamar,
                    'lowongan'  => $first->lowongan,
                    'tanggal'   => $tanggal,
                    'wawancara' => $wawancara,
                    'micro'     => $micro,
                    'pengujis'  => $allPengujis,
                ];
            })
            ->values();

        return view('admin.jadwal.index', compact('rows', 'jadwals', 'pengujis'));
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

                    // Lewati tipe yang belum diisi
                    if (!$sesi) continue;

                    foreach ($pengujiIds as $pengujiId) {
                        $pengujiId = (int) $pengujiId;

                        // Lewati baris kosong
                        if (!$pengujiId)
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

        // ── Kirim notifikasi jadwal baru ────────────────────────────
        $lowongan = Lowongan::find($lowonganId);
        $posisi   = $lowongan?->nama_posisi ?? 'Lowongan';

        foreach ($schedule as $pelamarIdRaw => $timedSlots) {
            $pelamarId = (int) $pelamarIdRaw;
            $pelamar   = Pelamar::with('user')->find($pelamarId);
            if ($pelamar?->user) {
                Notifikasi::kirim(
                    $pelamar->user->id,
                    'Jadwal Seleksi Ditetapkan',
                    "Jadwal seleksi Anda untuk posisi \"{$posisi}\" telah ditetapkan pada {$tanggal}. Silakan cek detail jadwal di portal.",
                    'jadwal'
                );
            }
        }

        // Notifikasi ke penguji yang dijadwalkan
        $pengujiIds = JadwalSeleksi::where('lowongan_id', $lowonganId)
            ->where('tanggal', $tanggal)
            ->pluck('penguji_id')
            ->unique();
        foreach ($pengujiIds as $pengujiId) {
            $dosen = Dosen::find($pengujiId);
            if ($dosen && $dosen->email) {
                $userPenguji = User::where('email', $dosen->email)->first();
                if ($userPenguji) {
                    Notifikasi::kirim(
                        $userPenguji->id,
                        'Jadwal Pengujian Ditetapkan',
                        "Anda dijadwalkan sebagai penguji untuk posisi \"{$posisi}\" pada {$tanggal}. Silakan cek jadwal di portal.",
                        'jadwal'
                    );
                }
            }
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

    // ── Update jadwal (tanggal & sesi via modal) ──────────────────────

    public function update(Request $request, JadwalSeleksi $jadwal)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'sesi'    => 'required|integer|min:1',
        ]);

        $tanggal   = $request->tanggal;
        $sesi      = (int) $request->sesi;
        $tipe      = $jadwal->tipe_seleksi;
        $pengujiId = $jadwal->penguji_id;
        $pelamarId = $jadwal->pelamar_id;

        // Validasi sesi sesuai tipe
        $validSessions = array_keys(JadwalSeleksi::SESSIONS[$tipe] ?? []);
        if (!in_array($sesi, array_map('intval', $validSessions))) {
            return back()->withErrors(['edit' => "Sesi {$sesi} tidak valid untuk tipe {$tipe}."])->withInput();
        }

        // Cek duplikat (kecuali jadwal ini sendiri)
        $dupQuery = JadwalSeleksi::where('tanggal', $tanggal)
            ->where('pelamar_id', $pelamarId)
            ->where('penguji_id', $pengujiId)
            ->where('tipe_seleksi', $tipe)
            ->where('sesi', $sesi)
            ->where('id', '!=', $jadwal->id);

        if ($dupQuery->exists()) {
            return back()->withErrors(['edit' => 'Jadwal duplikat — kombinasi tanggal/tipe/sesi/penguji sudah ada.'])->withInput();
        }

        // Cek bentrok penguji (exclude jadwal ini sendiri)
        foreach (JadwalSeleksi::getConflictingSlots($tipe, $sesi) as $c) {
            if (JadwalSeleksi::where('tanggal', $tanggal)
                ->where('penguji_id', $pengujiId)
                ->where('tipe_seleksi', $c['tipe'])
                ->where('sesi', $c['sesi'])
                ->where('id', '!=', $jadwal->id)
                ->exists()) {
                $label = JadwalSeleksi::SESSIONS[$c['tipe']][$c['sesi']]['label'] ?? "S{$c['sesi']}";
                return back()->withErrors(['edit' => "Penguji sudah terjadwal di {$label} — terjadi bentrok waktu."])->withInput();
            }
        }

        // Cek bentrok pelamar (exclude jadwal ini sendiri)
        foreach (JadwalSeleksi::getConflictingSlots($tipe, $sesi) as $c) {
            if (JadwalSeleksi::where('tanggal', $tanggal)
                ->where('pelamar_id', $pelamarId)
                ->where('tipe_seleksi', $c['tipe'])
                ->where('sesi', $c['sesi'])
                ->where('id', '!=', $jadwal->id)
                ->exists()) {
                $label = JadwalSeleksi::SESSIONS[$c['tipe']][$c['sesi']]['label'] ?? "S{$c['sesi']}";
                return back()->withErrors(['edit' => "Pelamar sudah terjadwal di {$label} — terjadi bentrok waktu."])->withInput();
            }
        }

        $jadwal->update([
            'tanggal' => $tanggal,
            'sesi'    => $sesi,
        ]);

        // Kirim notifikasi perubahan jadwal
        $jadwal->load(['pelamar.user', 'penguji', 'lowongan']);
        $posisi    = $jadwal->lowongan?->nama_posisi ?? 'Lowongan';
        $tipeLabel = $jadwal->tipe_seleksi === 'tahap1' ? 'Wawancara' : 'Micro Teaching';
        if ($jadwal->pelamar?->user) {
            Notifikasi::kirim(
                $jadwal->pelamar->user->id,
                'Jadwal Seleksi Diubah',
                "Jadwal {$tipeLabel} Anda untuk posisi \"{$posisi}\" telah diubah menjadi tanggal {$tanggal}.",
                'jadwal'
            );
        }
        if ($jadwal->penguji && $jadwal->penguji->email) {
            $userPenguji = User::where('email', $jadwal->penguji->email)->first();
            if ($userPenguji) {
                Notifikasi::kirim(
                    $userPenguji->id,
                    'Jadwal Pengujian Diubah',
                    "Jadwal {$tipeLabel} untuk posisi \"{$posisi}\" telah diubah menjadi tanggal {$tanggal}.",
                    'jadwal'
                );
            }
        }

        return back()->with('success', 'Jadwal berhasil diperbarui.');
    }

    // ── Update group jadwal (1 baris = semua record pelamar+lowongan) ──

    public function updateGroup(Request $request)
    {
        $request->validate([
            'pelamar_id'     => 'required|exists:pelamars,id',
            'lowongan_id'    => 'required|exists:lowongans,id',
            'tanggal'        => 'required|date',
            'wawancara_sesi' => 'nullable|integer|min:1',
            'micro_sesi'     => 'nullable|integer|min:1',
            'wawancara_link' => 'nullable|url',
            'micro_link'     => 'nullable|url',
        ]);

        $pelamarId  = (int) $request->pelamar_id;
        $lowonganId = (int) $request->lowongan_id;
        $tanggal    = $request->tanggal;
        $wSesi      = $request->filled('wawancara_sesi') ? (int) $request->wawancara_sesi : null;
        $mSesi      = $request->filled('micro_sesi')     ? (int) $request->micro_sesi     : null;
        $wLink      = $request->wawancara_link;
        $mLink      = $request->micro_link;

        // Ambil semua jadwal dalam group
        $group    = JadwalSeleksi::with('penguji')
            ->where('pelamar_id', $pelamarId)
            ->where('lowongan_id', $lowonganId)
            ->get();
        $groupIds = $group->pluck('id')->toArray();

        $errors = [];

        DB::transaction(function () use ($group, $groupIds, $tanggal, $wSesi, $mSesi, $wLink, $mLink, &$errors) {
            // ── Update wawancara (tahap1) ──────────────────────────────
            if ($wSesi !== null) {
                $valid = array_keys(JadwalSeleksi::SESSIONS['tahap1'] ?? []);
                if (!in_array($wSesi, array_map('intval', $valid))) {
                    $errors[] = "Sesi wawancara {$wSesi} tidak valid.";
                } else {
                    $wGroup = $group->where('tipe_seleksi', 'tahap1');
                    foreach ($wGroup as $jadwal) {
                        foreach (JadwalSeleksi::getConflictingSlots('tahap1', $wSesi) as $c) {
                            if (JadwalSeleksi::where('tanggal', $tanggal)
                                ->where('penguji_id', $jadwal->penguji_id)
                                ->where('tipe_seleksi', $c['tipe'])
                                ->where('sesi', $c['sesi'])
                                ->whereNotIn('id', $groupIds)
                                ->exists()) {
                                $label = JadwalSeleksi::SESSIONS[$c['tipe']][$c['sesi']]['label'] ?? "S{$c['sesi']}";
                                $errors[] = "Penguji {$jadwal->penguji->nama} bentrok di {$label} (Wawancara).";
                            }
                        }
                    }
                    if (empty($errors)) {
                        foreach ($wGroup as $jadwal) {
                            $jadwal->update(['tanggal' => $tanggal, 'sesi' => $wSesi, 'link_meeting' => $wLink]);
                        }
                    }
                }
            }

            // ── Update micro teaching (tahap2) ────────────────────────
            if ($mSesi !== null && empty($errors)) {
                $valid = array_keys(JadwalSeleksi::SESSIONS['tahap2'] ?? []);
                if (!in_array($mSesi, array_map('intval', $valid))) {
                    $errors[] = "Sesi micro teaching {$mSesi} tidak valid.";
                } else {
                    $mGroup = $group->where('tipe_seleksi', 'tahap2');
                    foreach ($mGroup as $jadwal) {
                        foreach (JadwalSeleksi::getConflictingSlots('tahap2', $mSesi) as $c) {
                            if (JadwalSeleksi::where('tanggal', $tanggal)
                                ->where('penguji_id', $jadwal->penguji_id)
                                ->where('tipe_seleksi', $c['tipe'])
                                ->where('sesi', $c['sesi'])
                                ->whereNotIn('id', $groupIds)
                                ->exists()) {
                                $label = JadwalSeleksi::SESSIONS[$c['tipe']][$c['sesi']]['label'] ?? "S{$c['sesi']}";
                                $errors[] = "Penguji {$jadwal->penguji->nama} bentrok di {$label} (Micro).";
                            }
                        }
                    }
                    if (empty($errors)) {
                        foreach ($mGroup as $jadwal) {
                            $jadwal->update(['tanggal' => $tanggal, 'sesi' => $mSesi, 'link_meeting' => $mLink]);
                        }
                    }
                }
            }
        });

        if (!empty($errors)) {
            return back()->withErrors(['edit' => implode('; ', $errors)])->withInput();
        }

        // Kirim notifikasi perubahan group jadwal
        $pelamar  = Pelamar::with('user')->find($pelamarId);
        $lowongan = Lowongan::find($lowonganId);
        $posisi   = $lowongan?->nama_posisi ?? 'Lowongan';

        if ($pelamar?->user) {
            Notifikasi::kirim(
                $pelamar->user->id,
                'Jadwal Seleksi Diperbarui',
                "Jadwal seleksi Anda untuk posisi \"{$posisi}\" telah diperbarui menjadi tanggal {$tanggal}.",
                'jadwal'
            );
        }

        $pengujiIds = $group->pluck('penguji_id')->unique();
        foreach ($pengujiIds as $pengujiId) {
            $dosen = Dosen::find($pengujiId);
            if ($dosen && $dosen->email) {
                $userPenguji = User::where('email', $dosen->email)->first();
                if ($userPenguji) {
                    Notifikasi::kirim(
                        $userPenguji->id,
                        'Jadwal Pengujian Diperbarui',
                        "Jadwal pengujian untuk posisi \"{$posisi}\" telah diperbarui menjadi tanggal {$tanggal}.",
                        'jadwal'
                    );
                }
            }
        }

        return back()->with('success', 'Jadwal berhasil diperbarui.');
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