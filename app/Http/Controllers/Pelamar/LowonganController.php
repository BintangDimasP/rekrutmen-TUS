<?php

namespace App\Http\Controllers\Pelamar;

use App\Http\Controllers\Controller;
use App\Models\Lowongan;
use App\Models\Lamaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LowonganController extends Controller
{
    /**
     * Menampilkan daftar lowongan yang tersedia (aktif).
     */
    public function index(Request $request)
    {
        $pelamar = auth()->user()->pelamar;
        
        $query = Lowongan::with('prodi')
            ->where('status', 'aktif');

        // Filter status lowongan
        if ($request->status_lowongan === 'closed') {
            $query->where('tanggal_tutup', '<', now());
        } elseif ($request->status_lowongan === 'open') {
            $query->where('tanggal_tutup', '>=', now());
        } else {
            // Default show only open
            $query->where('tanggal_tutup', '>=', now());
        }

        // Filter prodi
        if ($request->filled('prodi_id')) {
            $query->where('prodi_id', $request->prodi_id);
        }

        $savedLowonganIds = $pelamar->savedLowongans()->pluck('lowongans.id')->toArray();
        
        // Filter saved
        if ($request->filter === 'saved') {
            $query->whereIn('id', $savedLowonganIds);
        }

        $appliedLowonganIds = $pelamar->lamarans()->pluck('lowongan_id')->toArray();

        // Get all data
        $allLowongans = $query->latest()->get();

        // Separate them
        $availableLowongans = $allLowongans->whereNotIn('id', $appliedLowonganIds)->values();
        $appliedLowongans = $allLowongans->whereIn('id', $appliedLowonganIds)->values();

        $prodis = \App\Models\Prodi::orderBy('nama')->get();

        return view('pelamar.lowongan.index', compact('availableLowongans', 'appliedLowongans', 'savedLowonganIds', 'prodis'));
    }

    /**
     * Toggle simpan lowongan
     */
    public function toggleSave(Lowongan $lowongan)
    {
        $pelamar = auth()->user()->pelamar;
        $pelamar->savedLowongans()->toggle($lowongan->id);

        return response()->json([
            'saved' => $pelamar->savedLowongans()->where('lowongan_id', $lowongan->id)->exists()
        ]);
    }

    /**
     * Menampilkan detail lowongan.
     */
    public function show(Lowongan $lowongan)
    {
        $pelamar = auth()->user()->pelamar;
        
        // Cek apakah sudah pernah melamar
        $existing = Lamaran::where('pelamar_id', $pelamar->id)
            ->where('lowongan_id', $lowongan->id)
            ->first();

        return view('pelamar.lowongan.show', compact('lowongan', 'pelamar', 'existing'));
    }

    /**
     * Halaman form pendaftaran untuk lowongan tertentu.
     */
    public function apply(Lowongan $lowongan)
    {
        $pelamar = auth()->user()->pelamar;

        // Cek apakah sudah pernah melamar di lowongan ini
        $existing = Lamaran::where('pelamar_id', $pelamar->id)
            ->where('lowongan_id', $lowongan->id)
            ->first();

        if ($existing) {
            return redirect()->route('pelamar.history.index')->with('warning', 'Anda sudah melamar pada posisi ini.');
        }

        return view('pelamar.lowongan.apply', compact('lowongan', 'pelamar'));
    }

    /**
     * Proses pengajuan lamaran.
     */
    public function storeApply(Request $request, Lowongan $lowongan)
    {
        $pelamar = auth()->user()->pelamar;

        // Validasi duplikasi
        $existing = Lamaran::where('pelamar_id', $pelamar->id)
            ->where('lowongan_id', $lowongan->id)
            ->first();

        if ($existing) {
            return redirect()->route('pelamar.history.index')->with('warning', 'Anda sudah melamar pada posisi ini.');
        }

        $request->validate([
            'file_surat_lamaran' => 'required|file|mimes:pdf|max:5120',
            'file_berkas_pendukung' => 'nullable|file|mimes:pdf|max:10240',
            'catatan' => 'nullable|string',
        ]);

        $lamaranData = [
            'pelamar_id' => $pelamar->id,
            'lowongan_id' => $lowongan->id,
            'status' => 'menunggu',
            'catatan' => $request->catatan,
        ];

        if ($request->hasFile('file_surat_lamaran')) {
            $lamaranData['file_surat_lamaran'] = $request->file('file_surat_lamaran')->store("lamaran/" . $pelamar->id, 'public');
        }

        if ($request->hasFile('file_berkas_pendukung')) {
            $lamaranData['file_berkas_pendukung'] = $request->file('file_berkas_pendukung')->store("lamaran/" . $pelamar->id, 'public');
        }

        Lamaran::create($lamaranData);

        return redirect()->route('pelamar.history.index')->with('success', 'Lamaran Anda berhasil dikirim! Silahkan pantau statusnya secara berkala.');
    }
}
