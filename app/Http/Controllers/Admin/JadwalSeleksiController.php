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
        $prodis   = Prodi::orderBy('nama')->get();

        // Gabungkan baris pelamar+lowongan yang sama menjadi satu baris tabel
        $rows = $jadwals
            ->groupBy(fn($j) => $j->pelamar_id . '_' . $j->lowongan_id)
            ->map(function ($group) {
                $wawancara = $group->where('tipe_seleksi', 'wawancara')->values();
                $micro     = $group->where('tipe_seleksi', 'micro_teaching')->values();
                $first     = $group->first();

                // Kumpulkan semua penguji unik dari kedua tipe
                $allPengujis = $group->map->penguji->filter()->unique('id')->values();

                // Tanggal: gunakan wawancara jika ada, fallback ke micro atau first
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
                    'lamaran'   => Lamaran::where('pelamar_id', $first->pelamar_id)
                                      ->where('lowongan_id', $first->lowongan_id)
                                      ->first(),
                ];
            })
            ->values();

        return view('admin.jadwal.index', compact('rows', 'jadwals', 'pengujis', 'prodis'));
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

        $lowongan = Lowongan::find($lowonganId);

        $saved = 0;
        $savedPelamars = 0; // jumlah pelamar yang berhasil dijadwalkan
        $errors = [];

        DB::transaction(function () use ($tanggal, $lowonganId, $lowongan, $schedule, &$saved, &$savedPelamars, &$errors) {

            foreach ($schedule as $pelamarIdRaw => $slotInfo) {
                $pelamarId = (int) $pelamarIdRaw;
                $pelamar = Pelamar::find($pelamarId);
                if (!$pelamar)
                    continue;

                $sesi  = (int) ($slotInfo['sesi'] ?? 0);
                $jenis = in_array($slotInfo['jenis'] ?? 'online', ['online', 'offline'])
                    ? ($slotInfo['jenis'] ?? 'online')
                    : 'online';
                $lokasi = $slotInfo['lokasi'] ?? null;

                if (!$sesi) continue;

                $tipeMap = [
                    'wawancara' => $slotInfo['penguji_wawancara_ids'] ?? [],
                    'micro_teaching' => $slotInfo['penguji_micro_ids'] ?? [],
                ];

                $validSessions = array_keys(JadwalSeleksi::SESSIONS['wawancara'] ?? []);
                if (!in_array($sesi, array_map('intval', $validSessions))) {
                    $errors[] = "{$pelamar->nama}: sesi {$sesi} tidak valid.";
                    continue;
                }

                $anySavedForPelamar = false;

                // Cek ketersediaan pelamar — cek per sesi tanpa filter tipe
                if (!JadwalSeleksi::isPelamarAvailable($tanggal, $pelamarId, $sesi)) {
                    $label = JadwalSeleksi::SESSIONS['wawancara'][$sesi]['block_label'] ?? "Sesi {$sesi}";
                    $errors[] = "{$pelamar->nama}: pelamar sudah terjadwal di {$label}.";
                    continue;
                }

                foreach ($tipeMap as $dbTipe => $pengujiIdsRaw) {
                    $pengujiIds = array_filter(array_map('intval', $pengujiIdsRaw));

                    foreach ($pengujiIds as $pengujiId) {
                        if (!$pengujiId) continue;

                        if (!Dosen::where('id', $pengujiId)->where('is_penguji', true)->exists()) {
                            $errors[] = "{$pelamar->nama} ({$dbTipe}): penguji ID {$pengujiId} tidak ditemukan.";
                            continue;
                        }

                        if (
                            JadwalSeleksi::whereDate('tanggal', $tanggal)
                                ->where('pelamar_id', $pelamarId)
                                ->where('penguji_id', $pengujiId)
                                ->where('tipe_seleksi', $dbTipe)
                                ->where('sesi', $sesi)
                                ->exists()
                        ) {
                            $errors[] = "{$pelamar->nama} ({$dbTipe} S{$sesi}): jadwal duplikat, dilewati.";
                            continue;
                        }

                        if (!JadwalSeleksi::isPengujiAvailable($tanggal, $pengujiId, $dbTipe, $sesi)) {
                            $label = JadwalSeleksi::SESSIONS[$dbTipe][$sesi]['label'] ?? "S{$sesi}";
                            $errors[] = "{$pelamar->nama} ({$dbTipe}): penguji bentrok di {$label}.";
                            continue;
                        }

                        JadwalSeleksi::create([
                            'tanggal'      => $tanggal,
                            'lowongan_id'  => $lowonganId,
                            'pelamar_id'   => $pelamarId,
                            'penguji_id'   => $pengujiId,
                            'tipe_seleksi' => $dbTipe,
                            'sesi'         => $sesi,
                            'jenis_sesi'   => $jenis,
                            'lokasi'       => $lokasi ?: null,
                            'link_meeting' => ($jenis === 'online') ? ($lokasi ?: null) : null,
                        ]);

                        $saved++;
                        $anySavedForPelamar = true;
                    }
                }

                if ($anySavedForPelamar) {
                    $savedPelamars++;
                    
                    $lamaranToUpdate = Lamaran::where('pelamar_id', $pelamarId)
                        ->where('lowongan_id', $lowonganId)
                        ->whereIn('status', ['menunggu', 'seleksi_tahap1'])
                        ->first();
                        
                    if ($lamaranToUpdate) {
                        $lamaranToUpdate->update(['status' => 'seleksi_tahap2']);
                        
                        $pelamarUser = $pelamar->user;
                        if ($pelamarUser) {
                            $posisi = $lowongan->nama_posisi ?? 'Lowongan';
                            $statusLabel = Lamaran::NOTIF_LABELS['seleksi_tahap2'] ?? 'Seleksi Tahap 2 (Micro Teaching & Wawancara)';
                            
                            \App\Models\Notifikasi::kirim(
                                $pelamarUser->id,
                                'Status Lamaran Diperbarui',
                                "Status lamaran Anda untuk posisi \"{$posisi}\" telah diubah menjadi: {$statusLabel}.",
                                'status',
                                'rekrutmen_informasi_status_pelamar',
                                [$posisi, $statusLabel, '-']
                            );
                        }
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

        // Notify ALL admins - sistem log
        $adminNama = auth()->user()->name ?? 'Admin';
        $waktu = now()->translatedFormat('d F Y \p\u\k\u\l H:i');
        \App\Models\User::where('role', 'admin')->each(function($u) use ($adminNama, $posisi, $tanggal, $waktu) {
            \App\Models\Notifikasi::kirimSistem(
                $u->id,
                'Penjadwalan Dibuat',
                "Admin {$adminNama} melakukan penjadwalan seleksi untuk lowongan {$posisi} pada tanggal {$tanggal} pukul {$waktu}."
            );
        });

        // Kumpulkan sesi per pelamar untuk pesan notifikasi
        $pelamarSesiMap = [];
        foreach ($schedule as $pelamarIdRaw => $timedSlots) {
            $pelamarSesiMap[(int)$pelamarIdRaw] = $timedSlots;
        }

        // Ambil seluruh jadwal yang baru saja disimpan untuk mendapat label sesi
        $newJadwals = JadwalSeleksi::where('lowongan_id', $lowonganId)
            ->where('tanggal', $tanggal)
            ->get();

        // Notifikasi pelamar — ambil block_label dari sesi pertama yang tersimpan
        foreach ($pelamarSesiMap as $pelamarIdKey => $timedSlots) {
            $pelamar = Pelamar::with('user')->find($pelamarIdKey);
            if (!$pelamar?->user) continue;

            $firstSesiKey = array_key_first($timedSlots);
            // $firstSesiKey bisa berupa integer sesi
            $firstJadwal = $newJadwals->where('pelamar_id', $pelamarIdKey)->first();
            $sesiLabel   = $firstJadwal ? (JadwalSeleksi::SESSIONS[$firstJadwal->tipe_seleksi][$firstJadwal->sesi]['block_label'] ?? "Sesi {$firstJadwal->sesi}") : '-';

            Notifikasi::kirim(
                $pelamar->user->id,
                'Jadwal Seleksi Ditetapkan',
                "Anda dijadwalkan mengikuti seleksi untuk posisi \"{$posisi}\" pada {$tanggal} di {$sesiLabel}. Silakan periksa kembali riwayat lamaran Anda untuk melihat detail kedua tahapan pengujian pada sesi ini.",
                'jadwal',
                'rekrutmen_informasi_pelamar_jadwal_ujian',
                [$posisi, $tanggal, $sesiLabel]
            );
        }

        // Notifikasi ke penguji yang dijadwalkan
        $pengujiIds = $newJadwals->pluck('penguji_id')->unique();
        foreach ($pengujiIds as $pengujiId) {
            $firstJadwal  = $newJadwals->where('penguji_id', $pengujiId)->first();
            $sesiLabel    = $firstJadwal ? (JadwalSeleksi::SESSIONS[$firstJadwal->tipe_seleksi][$firstJadwal->sesi]['block_label'] ?? "Sesi {$firstJadwal->sesi}") : '-';
            $userPengujis = User::where('dosen_id', $pengujiId)->get();
            foreach ($userPengujis as $userPenguji) {
                Notifikasi::kirim(
                    $userPenguji->id,
                    'Jadwal Pengujian Ditetapkan',
                    "Anda dijadwalkan sebagai penguji untuk posisi \"{$posisi}\" pada {$tanggal} di {$sesiLabel}. Silakan periksa daftar jadwal pengujian Anda untuk melihat detail pelamar dan urutan pengujiannya.",
                    'jadwal',
                    'rekrutmen_information_jadwal_uji',
                    [$posisi, $tanggal, $sesiLabel]
                );
            }
        }

        $message = "{$savedPelamars} jadwal berhasil disimpan.";
        if (!empty($errors)) {
            $message .= ' Sebagian gagal: ' . implode('; ', $errors);
        }

        return redirect()->route('admin.jadwal.index')->with('success', $message);
    }

    // ── Hapus satu jadwal ────────────────────────────────────────────────

    public function destroy(JadwalSeleksi $jadwal)
    {
        $jadwal->delete();

        $adminNama = auth()->user()->name ?? 'Admin';
        $waktu = now()->translatedFormat('d F Y \p\u\k\u\l H:i');
        \App\Models\User::where('role', 'admin')->each(function($u) use ($adminNama, $waktu) {
            \App\Models\Notifikasi::kirimSistem($u->id, 'Jadwal Dihapus', "Admin {$adminNama} menghapus jadwal seleksi pada {$waktu}.");
        });

        return back()->with('success', 'Jadwal seleksi berhasil dihapus.');
    }

    // ── Update jadwal (tanggal & sesi via modal) ──────────────────────

    // ── Update jadwal individual ──────────────────────────────────────────
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

        if (!JadwalSeleksi::isPengujiAvailable($tanggal, $pengujiId, $tipe, $sesi)) {
            // Ignore self
            if (!JadwalSeleksi::where('tanggal', $tanggal)->where('penguji_id', $pengujiId)->where('tipe_seleksi', $tipe)->where('sesi', $sesi)->where('id', '!=', $jadwal->id)->exists()) {
                $label = JadwalSeleksi::SESSIONS[$tipe][$sesi]['label'] ?? "S{$sesi}";
                return back()->withErrors(['edit' => "Penguji sudah terjadwal di {$label} — terjadi bentrok waktu."])->withInput();
            }
        }

        if (!JadwalSeleksi::isPelamarAvailable($tanggal, $pelamarId, $sesi, [$jadwal->id])) {
            $label = JadwalSeleksi::SESSIONS[$tipe][$sesi]['label'] ?? "S{$sesi}";
            return back()->withErrors(['edit' => "Pelamar sudah terjadwal di {$label} — terjadi bentrok waktu."])->withInput();
        }

        $jadwal->update([
            'tanggal' => $tanggal,
            'sesi'    => $sesi,
        ]);

        // Kirim notifikasi perubahan jadwal
        $jadwal->load(['pelamar.user', 'penguji', 'lowongan']);
        $posisi    = $jadwal->lowongan?->nama_posisi ?? 'Lowongan';
        $sesiLabel = JadwalSeleksi::SESSIONS[$jadwal->tipe_seleksi][$sesi]['block_label'] ?? "Sesi {$sesi}";
        if ($jadwal->pelamar?->user) {
            Notifikasi::kirim(
                $jadwal->pelamar->user->id,
                'Jadwal Seleksi Diperbarui',
                "Jadwal seleksi Anda untuk posisi \"{$posisi}\" telah diperbarui menjadi {$tanggal} pada {$sesiLabel}. Silakan periksa kembali riwayat lamaran Anda untuk menyesuaikan persiapan pengujian.",
                'jadwal',
                'rekrutmen_informasi_pelamar_pergantian_jadwal_ujian',
                [$posisi, $tanggal, $sesiLabel]
            );
        }
        if ($jadwal->penguji) {
            $userPengujis = User::where('dosen_id', $jadwal->penguji->id)->get();
            foreach ($userPengujis as $userPenguji) {
                Notifikasi::kirim(
                    $userPenguji->id,
                    'Jadwal Pengujian Diperbarui',
                    "Jadwal Anda sebagai penguji untuk posisi \"{$posisi}\" telah diperbarui menjadi {$tanggal} pada {$sesiLabel}. Silakan periksa kembali daftar jadwal pengujian untuk penyesuaian agenda Anda.",
                    'jadwal',
                    'rekrutmen_informasi_pergantian_jadwal',
                    [$posisi, $tanggal, $sesiLabel]
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
            'sesi'           => 'nullable|integer|min:1',
            'jenis_sesi'     => 'required|in:online,offline',
            'lokasi'         => 'required|string',
            'wawancara_penguji_ids'   => 'nullable|array',
            'wawancara_penguji_ids.*' => 'integer|exists:dosens,id',
            'micro_penguji_ids'       => 'nullable|array',
            'micro_penguji_ids.*'     => 'integer|exists:dosens,id',
        ]);

        $pelamarId  = (int) $request->pelamar_id;
        $lowonganId = (int) $request->lowongan_id;

        // Cek apakah semua penilaian sudah done — jika iya, tolak edit
        $group = JadwalSeleksi::with('penilaian')
            ->where('pelamar_id', $pelamarId)
            ->where('lowongan_id', $lowonganId)
            ->get();

        $micro     = $group->where('tipe_seleksi', 'micro_teaching');
        $wawancara = $group->where('tipe_seleksi', 'wawancara');
        $allDone   = $micro->isNotEmpty() && $micro->every(fn($j) => $j->penilaian !== null)
                  && $wawancara->isNotEmpty() && $wawancara->every(fn($j) => $j->penilaian !== null);

        if ($allDone) {
            return back()->withErrors(['edit' => 'Jadwal tidak dapat diedit karena semua penilaian sudah selesai.']);
        }
        $tanggal    = $request->tanggal;
        $sesi       = $request->filled('sesi') ? (int) $request->sesi : null;
        $jenis      = $request->jenis_sesi;
        $lokasi     = $request->lokasi;
        $link       = ($jenis === 'online') ? ($lokasi ?: null) : null;
        $newWPengujiIds = $request->input('wawancara_penguji_ids', []);
        $newMPengujiIds = $request->input('micro_penguji_ids', []);

        // Reload group dengan relasi penguji untuk proses selanjutnya
        $group    = JadwalSeleksi::with('penguji')
            ->where('pelamar_id', $pelamarId)
            ->where('lowongan_id', $lowonganId)
            ->get();
        $groupIds = $group->pluck('id')->toArray();
        $oldWPengujiIds = $group->where('tipe_seleksi', 'wawancara')->pluck('penguji_id')->unique()->toArray();
        $oldMPengujiIds = $group->where('tipe_seleksi', 'micro_teaching')->pluck('penguji_id')->unique()->toArray();

        $errors = [];

        DB::transaction(function () use ($group, $groupIds, $tanggal, $sesi, $jenis, $lokasi, $link, $newWPengujiIds, $newMPengujiIds, $pelamarId, $lowonganId, &$errors) {

            if ($sesi !== null) {
                $valid = array_keys(JadwalSeleksi::SESSIONS['wawancara'] ?? []);
                if (!in_array($sesi, array_map('intval', $valid))) {
                    $errors[] = "Sesi {$sesi} tidak valid.";
                } else {
                    $wGroup = $group->where('tipe_seleksi', 'wawancara');
                    $mGroup = $group->where('tipe_seleksi', 'micro_teaching');

                    // Cek ketersediaan pelamar (kecuali jadwal yang sedang diedit)
                    if (!JadwalSeleksi::isPelamarAvailable($tanggal, $pelamarId, $sesi, $groupIds)) {
                        $label = JadwalSeleksi::SESSIONS['wawancara'][$sesi]['block_label'] ?? "Sesi {$sesi}";
                        $errors[] = "Pelamar sudah terjadwal di {$label} pada tanggal tersebut.";
                    }

                    // ── Update Wawancara ──────────────────────────────
                    if (!empty($newWPengujiIds)) {
                        $newWPengujiIds = array_map('intval', $newWPengujiIds);
                        foreach ($newWPengujiIds as $pgId) {
                            if (!JadwalSeleksi::isPengujiAvailable($tanggal, $pgId, 'wawancara', $sesi)) {
                                if (!JadwalSeleksi::where('tanggal', $tanggal)->where('penguji_id', $pgId)->where('tipe_seleksi', 'wawancara')->where('sesi', $sesi)->whereIn('id', $groupIds)->exists()) {
                                    $dosen = Dosen::find($pgId);
                                    $label = JadwalSeleksi::SESSIONS['wawancara'][$sesi]['label'] ?? "S{$sesi}";
                                    $errors[] = "Penguji " . ($dosen->nama ?? $pgId) . " bentrok di {$label} (Wawancara).";
                                }
                            }
                        }

                        if (empty($errors)) {
                            JadwalSeleksi::whereIn('id', $wGroup->pluck('id')->toArray())->delete();
                            foreach ($newWPengujiIds as $pgId) {
                                JadwalSeleksi::create([
                                    'tanggal'       => $tanggal,
                                    'lowongan_id'   => $lowonganId,
                                    'pelamar_id'    => $pelamarId,
                                    'penguji_id'    => $pgId,
                                    'tipe_seleksi'  => 'wawancara',
                                    'sesi'          => $sesi,
                                    'jenis_sesi'    => $jenis,
                                    'lokasi'        => $lokasi ?: null,
                                    'link_meeting'  => $link,
                                ]);
                            }
                        }
                    } else {
                        foreach ($wGroup as $jadwal) {
                            if (!JadwalSeleksi::isPengujiAvailable($tanggal, $jadwal->penguji_id, 'wawancara', $sesi)) {
                                if (!JadwalSeleksi::where('tanggal', $tanggal)->where('penguji_id', $jadwal->penguji_id)->where('tipe_seleksi', 'wawancara')->where('sesi', $sesi)->whereIn('id', $groupIds)->exists()) {
                                    $label = JadwalSeleksi::SESSIONS['wawancara'][$sesi]['label'] ?? "S{$sesi}";
                                    $errors[] = "Penguji {$jadwal->penguji->nama} bentrok di {$label} (Wawancara).";
                                }
                            }
                        }
                        if (empty($errors)) {
                            foreach ($wGroup as $jadwal) {
                                $jadwal->update([
                                    'tanggal'      => $tanggal, 
                                    'sesi'         => $sesi, 
                                    'jenis_sesi'   => $jenis,
                                    'lokasi'       => $lokasi ?: null,
                                    'link_meeting' => $link
                                ]);
                            }
                        }
                    }

                    // ── Update Micro Teaching ────────────────────────
                    if (!empty($newMPengujiIds) && empty($errors)) {
                        $newMPengujiIds = array_map('intval', $newMPengujiIds);
                        foreach ($newMPengujiIds as $pgId) {
                            if (!JadwalSeleksi::isPengujiAvailable($tanggal, $pgId, 'micro_teaching', $sesi)) {
                                if (!JadwalSeleksi::where('tanggal', $tanggal)->where('penguji_id', $pgId)->where('tipe_seleksi', 'micro_teaching')->where('sesi', $sesi)->whereIn('id', $groupIds)->exists()) {
                                    $dosen = Dosen::find($pgId);
                                    $label = JadwalSeleksi::SESSIONS['micro_teaching'][$sesi]['label'] ?? "S{$sesi}";
                                    $errors[] = "Penguji " . ($dosen->nama ?? $pgId) . " bentrok di {$label} (Micro).";
                                }
                            }
                        }

                        if (empty($errors)) {
                            JadwalSeleksi::whereIn('id', $mGroup->pluck('id')->toArray())->delete();
                            foreach ($newMPengujiIds as $pgId) {
                                JadwalSeleksi::create([
                                    'tanggal'       => $tanggal,
                                    'lowongan_id'   => $lowonganId,
                                    'pelamar_id'    => $pelamarId,
                                    'penguji_id'    => $pgId,
                                    'tipe_seleksi'  => 'micro_teaching',
                                    'sesi'          => $sesi,
                                    'jenis_sesi'    => $jenis,
                                    'lokasi'        => $lokasi ?: null,
                                    'link_meeting'  => $link,
                                ]);
                            }
                        }
                    } elseif (empty($errors)) {
                        foreach ($mGroup as $jadwal) {
                            if (!JadwalSeleksi::isPengujiAvailable($tanggal, $jadwal->penguji_id, 'micro_teaching', $sesi)) {
                                if (!JadwalSeleksi::where('tanggal', $tanggal)->where('penguji_id', $jadwal->penguji_id)->where('tipe_seleksi', 'micro_teaching')->where('sesi', $sesi)->whereIn('id', $groupIds)->exists()) {
                                    $label = JadwalSeleksi::SESSIONS['micro_teaching'][$sesi]['label'] ?? "S{$sesi}";
                                    $errors[] = "Penguji {$jadwal->penguji->nama} bentrok di {$label} (Micro).";
                                }
                            }
                        }
                        if (empty($errors)) {
                            foreach ($mGroup as $jadwal) {
                                $jadwal->update([
                                    'tanggal'      => $tanggal, 
                                    'sesi'         => $sesi, 
                                    'jenis_sesi'   => $jenis,
                                    'lokasi'       => $lokasi ?: null,
                                    'link_meeting' => $link
                                ]);
                            }
                        }
                    }
                }
            }
        });

        if (!empty($errors)) {
            return back()->withErrors(['edit' => implode('; ', $errors)])->withInput();
        }

        // ── Kirim notifikasi ────────────────────────────────────────
        $pelamar  = Pelamar::with('user')->find($pelamarId);
        $lowongan = Lowongan::find($lowonganId);
        $posisi   = $lowongan?->nama_posisi ?? 'Lowongan';

        // Tentukan sesi label dari wawancara (basis sesi group update)
        $sesiNum      = $sesi ?? 1;
        $sesiLabel    = JadwalSeleksi::SESSIONS['wawancara'][$sesiNum]['block_label']
                        ?? "Sesi {$sesiNum}";

        // Notifikasi ke pelamar
        if ($pelamar?->user) {
            Notifikasi::kirim(
                $pelamar->user->id,
                'Jadwal Seleksi Diperbarui',
                "Jadwal seleksi Anda untuk posisi \"{$posisi}\" telah diperbarui menjadi {$tanggal} pada {$sesiLabel}. Silakan periksa kembali riwayat lamaran Anda untuk menyesuaikan persiapan pengujian.",
                'jadwal',
                'rekrutmen_informasi_pelamar_pergantian_jadwal_ujian',
                [$posisi, $tanggal, $sesiLabel]
            );
        }

        // Tentukan penguji baru setelah update
        $currentWPengujiIds = !empty($newWPengujiIds) ? array_map('intval', $newWPengujiIds) : $oldWPengujiIds;
        $currentMPengujiIds = !empty($newMPengujiIds) ? array_map('intval', $newMPengujiIds) : $oldMPengujiIds;
        $allCurrentPengujiIds = array_unique(array_merge($currentWPengujiIds, $currentMPengujiIds));
        $allOldPengujiIds = array_unique(array_merge($oldWPengujiIds, $oldMPengujiIds));

        // Penguji yang dihapus dari jadwal
        $removedPengujiIds = array_diff($allOldPengujiIds, $allCurrentPengujiIds);
        foreach ($removedPengujiIds as $pengujiId) {
            $userPengujis = User::where('dosen_id', $pengujiId)->get();
            foreach ($userPengujis as $userPenguji) {
                Notifikasi::kirim(
                    $userPenguji->id,
                    'Jadwal Pengujian Dibatalkan',
                    "Anda tidak lagi dijadwalkan sebagai penguji untuk posisi \"{$posisi}\" pada {$tanggal} di {$sesiLabel}.",
                    'jadwal',
                    'rekrutmen_informasi_penghentian_jadwal_uji',
                    [$posisi, $tanggal, $sesiLabel]
                );
            }
        }

        // Penguji baru yang ditambahkan
        $addedPengujiIds = array_diff($allCurrentPengujiIds, $allOldPengujiIds);
        foreach ($addedPengujiIds as $pengujiId) {
            $userPengujis = User::where('dosen_id', $pengujiId)->get();
            foreach ($userPengujis as $userPenguji) {
                Notifikasi::kirim(
                    $userPenguji->id,
                    'Jadwal Pengujian Ditetapkan',
                    "Anda dijadwalkan sebagai penguji untuk posisi \"{$posisi}\" pada {$tanggal} di {$sesiLabel}. Silakan periksa daftar jadwal pengujian Anda untuk melihat detail pelamar dan urutan pengujiannya.",
                    'jadwal',
                    'rekrutmen_information_jadwal_uji',
                    [$posisi, $tanggal, $sesiLabel]
                );
            }
        }

        // Penguji yang tetap (notif perubahan jadwal)
        $keptPengujiIds = array_intersect($allOldPengujiIds, $allCurrentPengujiIds);
        foreach ($keptPengujiIds as $pengujiId) {
            $userPengujis = User::where('dosen_id', $pengujiId)->get();
            foreach ($userPengujis as $userPenguji) {
                Notifikasi::kirim(
                    $userPenguji->id,
                    'Jadwal Pengujian Diperbarui',
                    "Jadwal Anda sebagai penguji untuk posisi \"{$posisi}\" telah diperbarui menjadi {$tanggal} pada {$sesiLabel}. Silakan periksa kembali daftar jadwal pengujian untuk penyesuaian agenda Anda.",
                    'jadwal',
                    'rekrutmen_informasi_pergantian_jadwal',
                    [$posisi, $tanggal, $sesiLabel]
                );
            }
        }

        $adminNamaLog = auth()->user()->name ?? 'Admin';
        $waktuLog = now()->translatedFormat('d F Y \p\u\k\u\l H:i');
        $lowonganLog = \App\Models\Lowongan::find($lowonganId);
        $posisiLog = $lowonganLog?->nama_posisi ?? '-';
        $pelamarLog = \App\Models\Pelamar::find($pelamarId);
        $namaLog = $pelamarLog?->nama ?? '-';
        \App\Models\User::where('role', 'admin')->each(function($u) use ($adminNamaLog, $namaLog, $posisiLog, $tanggal, $waktuLog) {
            \App\Models\Notifikasi::kirimSistem($u->id, 'Jadwal Diperbarui', "Admin {$adminNamaLog} memperbarui jadwal seleksi {$namaLog} untuk lowongan {$posisiLog} pada tanggal {$tanggal} pukul {$waktuLog}.");
        });

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
     * Hanya pelamar berstatus seleksi_tahap1 DAN sudah direkomendasikan Kaprodi yang tampil.
     * Ini memastikan penjadwalan hanya dapat dilakukan untuk kandidat yang layak.
     */
    public function apiPelamar(Request $request)
    {
        $lowonganId = $request->lowongan_id;

        $pelamars = Pelamar::whereHas('lamarans', function ($q) use ($lowonganId) {
            $q->where('lowongan_id', $lowonganId)
                ->where('status', 'seleksi_tahap1')
                ->where('is_direkomendasikan_kaprodi', true); // Wajib sudah direkomendasikan Kaprodi
        })
            ->with(['user'])
            ->orderBy('nama')
            ->get()
            ->map(fn($p) => [
                'id'     => $p->id,
                'nama'   => $p->nama,
                'email'  => $p->user?->email ?? '-',
                'jenjang' => $p->jenjang ?? '-',
            ]);

        return response()->json($pelamars);
    }

    /**
     * GET /admin/api/sesi-taken-all?tanggal=YYYY-MM-DD&penguji_ids=1,2,3
     */
    public function apiSesiTakenAll(Request $request)
    {
        $tanggal = $request->tanggal;
        $pengujiIds = array_filter(array_map('intval', explode(',', $request->penguji_ids ?? '')));

        if (!$tanggal || empty($pengujiIds)) {
            return response()->json([]);
        }

        $rows = JadwalSeleksi::whereDate('tanggal', $tanggal)
            ->whereIn('penguji_id', $pengujiIds)
            ->get(['penguji_id', 'tipe_seleksi', 'sesi']);

        $result = [];
        foreach ($pengujiIds as $id) {
            $result[$id] = ['wawancara' => [], 'micro_teaching' => []];
        }

        foreach ($rows as $row) {
            $pid = (int) $row->penguji_id;
            $tipe = $row->tipe_seleksi;
            $sesi = (int) $row->sesi;

            if (!isset($result[$pid]))
                continue;

            if (isset($result[$pid][$tipe])) {
                $result[$pid][$tipe][] = $sesi;
            }
        }

        foreach ($result as &$v) {
            $v['wawancara'] = array_values(array_unique($v['wawancara']));
            $v['micro_teaching'] = array_values(array_unique($v['micro_teaching']));
        }

        return response()->json($result);
    }

    /**
     * GET /admin/api/sesi-taken-pelamar?tanggal=YYYY-MM-DD&pelamar_ids=1,2,3
     * Mengembalikan sesi yang sudah terpakai oleh masing-masing pelamar pada tanggal tsb.
     */
    public function apiSesiTakenPelamar(Request $request)
    {
        $tanggal   = $request->tanggal;
        $pelamarIds = array_filter(array_map('intval', explode(',', $request->pelamar_ids ?? '')));
        $excludeLowonganId = $request->exclude_lowongan_id;

        if (!$tanggal || empty($pelamarIds)) {
            return response()->json([]);
        }

        $q = JadwalSeleksi::whereDate('tanggal', $tanggal)
            ->whereIn('pelamar_id', $pelamarIds);

        if ($excludeLowonganId) {
            $q->where('lowongan_id', '!=', $excludeLowonganId);
        }

        $rows = $q->get(['pelamar_id', 'sesi']);

        $result = [];
        foreach ($pelamarIds as $id) {
            $result[$id] = [];
        }

        foreach ($rows as $row) {
            $pid  = (int) $row->pelamar_id;
            $sesi = (int) $row->sesi;
            if (isset($result[$pid]) && !in_array($sesi, $result[$pid])) {
                $result[$pid][] = $sesi;
            }
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

    /**
     * Hapus jadwal grup pelamar
     */
    public function destroyGroup(Request $request)
    {
        $request->validate([
            'pelamar_id' => 'required|exists:pelamars,id',
            'lowongan_id' => 'required|exists:lowongans,id',
        ]);

        JadwalSeleksi::where('pelamar_id', $request->pelamar_id)
            ->where('lowongan_id', $request->lowongan_id)
            ->delete();

        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal seleksi berhasil dihapus.');
    }
}
