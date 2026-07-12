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

        // Guard: jika record Pelamar belum ada, arahkan ke halaman profil
        if (!$pelamar) {
            return redirect()->route('pelamar.profil.index')
                ->with('warning', 'Lengkapi profil Anda terlebih dahulu sebelum melihat lowongan.');
        }

        $allLowongans = Lowongan::with('prodi')
            ->where('status', 'aktif')
            ->where('tanggal_tutup', '>=', now())
            ->latest()
            ->get();

        $savedLowonganIds = $pelamar->savedLowongans()->pluck('lowongans.id')->toArray();
        $appliedLowonganIds = $pelamar->lamarans()->pluck('lowongan_id')->toArray();

        // Separate — lowongan penuh tidak masuk available
        $availableLowongans = $allLowongans
            ->whereNotIn('id', $appliedLowonganIds)
            ->filter(fn($l) => !$l->isFull())
            ->values();
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

        // Cek kuota
        if ($lowongan->isFull()) {
            return redirect()->route('pelamar.lowongan.show', $lowongan->id)
                ->with('warning', 'Kuota lowongan ini sudah penuh.');
        }

        // Cek verifikasi email
        if (!auth()->user()->hasVerifiedEmail()) {
            return redirect()->route('pelamar.profil.index')
                ->with('warning', 'Verifikasi email Anda terlebih dahulu sebelum melamar.');
        }

        // Cek nomor WhatsApp sudah diisi (wajib untuk notifikasi)
        if (empty($pelamar->no_telepon)) {
            return redirect()->route('pelamar.profil.index')
                ->with('warning', 'Lengkapi nomor WhatsApp Anda terlebih dahulu untuk menerima notifikasi.');
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

        // Cek kuota (double-check saat submit)
        if ($lowongan->isFull()) {
            return redirect()->route('pelamar.lowongan.show', $lowongan->id)
                ->with('warning', 'Kuota lowongan ini sudah penuh, lamaran tidak dapat dikirim.');
        }

        // Cek verifikasi email
        if (!auth()->user()->hasVerifiedEmail()) {
            return redirect()->route('pelamar.profil.index')
                ->with('warning', 'Verifikasi email Anda terlebih dahulu sebelum melamar.');
        }

        // Cek nomor WhatsApp sudah diisi (wajib untuk notifikasi)
        if (empty($pelamar->no_telepon)) {
            return redirect()->route('pelamar.profil.index')
                ->with('warning', 'Lengkapi nomor WhatsApp Anda terlebih dahulu untuk menerima notifikasi.');
        }

        $request->validate([
            'file_surat_lamaran'       => 'required|file|mimes:pdf|max:5120',
            'file_sk_penyetaraan'      => 'nullable|file|mimes:pdf|max:5120',
            'file_surat_pemberhentian' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $lamaranData = [
            'pelamar_id'    => $pelamar->id,
            'lowongan_id'   => $lowongan->id,
            'status'        => 'menunggu',
            'snapshot_data' => $pelamar->toArray(),
        ];

        if ($request->hasFile('file_surat_lamaran')) {
            $lamaranData['file_surat_lamaran'] = $request->file('file_surat_lamaran')->store("lamaran/" . $pelamar->id, 'public');
        }

        if ($request->hasFile('file_sk_penyetaraan')) {
            $lamaranData['file_sk_penyetaraan'] = $request->file('file_sk_penyetaraan')->store("lamaran/" . $pelamar->id, 'public');
        }

        if ($request->hasFile('file_surat_pemberhentian')) {
            $lamaranData['file_surat_pemberhentian'] = $request->file('file_surat_pemberhentian')->store("lamaran/" . $pelamar->id, 'public');
        }

        Lamaran::create($lamaranData);

        return redirect()->route('pelamar.history.index')->with('success', 'Lamaran Anda berhasil dikirim! Silahkan pantau statusnya secara berkala.');
    }
}
