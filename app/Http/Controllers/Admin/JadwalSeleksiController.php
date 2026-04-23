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

        $jadwals  = $query->get();
        $pengujis = Dosen::where('is_penguji', true)->orderBy('nama')->get();

        return view('admin.jadwal.index', compact('jadwals', 'pengujis'));
    }

    // ── Form penjadwalan ─────────────────────────────────────────────────

    public function create()
    {
        $prodis   = Prodi::orderBy('nama')->get();
        $sessions = JadwalSeleksi::SESSIONS;
        return view('admin.jadwal.create', compact('prodis', 'sessions'));
    }

    // ── Simpan jadwal (auto-assign sesi) ─────────────────────────────────

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal'          => 'required|date|after_or_equal:today',
            'lowongan_id'      => 'required|exists:lowongans,id',
            'tipe_seleksi'     => 'required|in:tahap1,tahap2',
            'penguji_id'       => 'required|exists:dosens,id',
            'pelamar_sessions' => 'required|array|min:1',
        ]);

        $tipe      = $validated['tipe_seleksi'];
        $tanggal   = $validated['tanggal'];
        $pengujiId = (int) $validated['penguji_id'];

        $validSessions = array_keys(JadwalSeleksi::SESSIONS[$tipe]);
        $assignments   = [];
        $errors        = [];

        // Pastikan minimal ada 1 entri sesi yang dipilih
        $hasValid = collect($validated['pelamar_sessions'])->filter(fn($s) => !empty($s))->isNotEmpty();
        if (!$hasValid) {
            return back()
                ->withErrors(['pelamar_sessions' => 'Pilih minimal satu pelamar beserta sesinya.'])
                ->withInput();
        }

        DB::transaction(function () use ($validated, $tipe, $tanggal, $pengujiId, $validSessions, &$assignments, &$errors) {
            foreach ($validated['pelamar_sessions'] as $pelamarIdRaw => $sesiRaw) {
                if (empty($sesiRaw)) continue; // skip pelamar tanpa sesi

                $pelamarId = (int) $pelamarIdRaw;
                $sesi      = (int) $sesiRaw;
                $pelamar   = Pelamar::find($pelamarId);

                // Validasi sesi sesuai tipe
                if (!in_array($sesi, $validSessions)) {
                    $errors[] = "{$pelamar->nama}: sesi {$sesi} tidak valid untuk tipe ini.";
                    continue;
                }

                // Cek max 2 penguji
                $pengujiCount = JadwalSeleksi::where('tanggal', $tanggal)
                    ->where('pelamar_id', $pelamarId)
                    ->where('tipe_seleksi', $tipe)
                    ->distinct('penguji_id')
                    ->count('penguji_id');

                if ($pengujiCount >= 2) {
                    $errors[] = "{$pelamar->nama}: sudah memiliki 2 penguji, tidak bisa ditambah.";
                    continue;
                }

                // Cek duplikat penguji-pelamar
                if (JadwalSeleksi::where('tanggal', $tanggal)
                    ->where('pelamar_id', $pelamarId)
                    ->where('penguji_id', $pengujiId)
                    ->where('tipe_seleksi', $tipe)
                    ->exists()) {
                    $errors[] = "{$pelamar->nama}: penguji ini sudah pernah dijadwalkan untuk pelamar ini.";
                    continue;
                }

                // Cek bentrok penguji
                if (!JadwalSeleksi::isPengujiAvailable($tanggal, $pengujiId, $tipe, $sesi)) {
                    $label = JadwalSeleksi::SESSIONS[$tipe][$sesi]['label'];
                    $errors[] = "{$pelamar->nama}: penguji sudah terjadwal di {$label}.";
                    continue;
                }

                // Cek bentrok pelamar
                if (!JadwalSeleksi::isPelamarAvailable($tanggal, $pelamarId, $tipe, $sesi)) {
                    $label = JadwalSeleksi::SESSIONS[$tipe][$sesi]['label'];
                    $errors[] = "{$pelamar->nama}: pelamar sudah terjadwal di waktu yang sama ({$label}).";
                    continue;
                }

                JadwalSeleksi::create([
                    'tanggal'      => $tanggal,
                    'lowongan_id'  => $validated['lowongan_id'],
                    'pelamar_id'   => $pelamarId,
                    'penguji_id'   => $pengujiId,
                    'tipe_seleksi' => $tipe,
                    'sesi'         => $sesi,
                ]);

                Lamaran::where('pelamar_id', $pelamarId)
                    ->where('lowongan_id', $validated['lowongan_id'])
                    ->where('status', 'seleksi_tahap1')
                    ->update(['status' => 'seleksi_tahap2']);

                $label         = JadwalSeleksi::SESSIONS[$tipe][$sesi]['label'];
                $assignments[] = "{$pelamar->nama} → {$label}";
            }
        });

        if (empty($assignments) && !empty($errors)) {
            return back()
                ->withErrors(['jadwal' => implode('; ', $errors)])
                ->withInput();
        }

        $message = count($assignments) . ' jadwal berhasil disimpan.';
        if (!empty($errors)) {
            $message .= ' ⚠️ Sebagian gagal: ' . implode('; ', $errors);
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
    // API Endpoints (return JSON untuk AJAX dari form)
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

    /** GET /admin/api/pelamar-by-lowongan?lowongan_id=X */
    public function apiPelamar(Request $request)
    {
        $lowonganId = $request->lowongan_id;

        $pelamars = Pelamar::whereHas('lamarans', function ($q) use ($lowonganId) {
            $q->where('lowongan_id', $lowonganId)
              ->where('status', 'seleksi_tahap1');
        })
        ->with(['user', 'lamarans' => function ($q) use ($lowonganId) {
            $q->where('lowongan_id', $lowonganId);
        }])
        ->orderBy('nama')
        ->get()
        ->map(fn($p) => [
            'id'     => $p->id,
            'nama'   => $p->nama,
            'email'  => $p->user?->email ?? '-',
            'jenjang'=> $p->jenjang ?? '-',
        ]);

        return response()->json($pelamars);
    }

    /** GET /admin/api/sesi-tersedia?tanggal=X&penguji_id=Y&tipe=Z */
    public function apiAvailableSessions(Request $request)
    {
        $tipe      = $request->tipe;
        $pengujiId = $request->penguji_id;
        $tanggal   = $request->tanggal;

        if (!$tipe || !$pengujiId || !$tanggal) {
            return response()->json(['taken' => [], 'available' => []]);
        }

        $sessions  = JadwalSeleksi::SESSIONS[$tipe] ?? [];
        $taken     = [];
        $available = [];

        foreach ($sessions as $sesi => $info) {
            if (JadwalSeleksi::isPengujiAvailable($tanggal, (int)$pengujiId, $tipe, $sesi)) {
                $available[] = ['sesi' => $sesi, 'label' => $info['label']];
            } else {
                $taken[] = ['sesi' => $sesi, 'label' => $info['label']];
            }
        }

        return response()->json(compact('taken', 'available'));
    }
}
