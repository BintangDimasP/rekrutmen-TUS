<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PelamarNilaiExport;
use App\Http\Controllers\Controller;
use App\Imports\PelamarImport;
use App\Models\Pelamar;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PelamarController extends Controller
{
    /**
     * Tampilkan seluruh data pelamar yang sudah registrasi di sistem.
     */
    public function index(Request $request)
    {
        $pelamars = Pelamar::with(['user', 'lamarans.lowongan'])->latest()->get();
        $prodis = \App\Models\Prodi::orderBy('nama')->get();

        return view('admin.pelamar.index', compact('pelamars', 'prodis'));
    }

    /**
     * Halaman detail seorang pelamar beserta semua lamarannya.
     */
    public function show(Pelamar $pelamar)
    {
        $pelamar->load(['user', 'lamarans.lowongan.prodi']);

        $activeLamaranId = request('lamaran_id');
        $activeLamaran = null;

        if ($activeLamaranId) {
            $activeLamaran = $pelamar->lamarans->firstWhere('id', $activeLamaranId);
        }

        if (!$activeLamaran) {
            $activeLamaran = $pelamar->lamarans->first();
        }

        $snapshot = null;
        if ($activeLamaran) {
            $snapshot = $activeLamaran->effective_pelamar;
        }

        // Jadwal seleksi untuk lamaran aktif
        $micro = collect();
        $wawancara = collect();
        if ($activeLamaran) {
            $jadwals = \App\Models\JadwalSeleksi::where('pelamar_id', $pelamar->id)
                ->where('lowongan_id', $activeLamaran->lowongan_id)
                ->with(['penguji', 'penilaian', 'lowongan'])
                ->orderBy('sesi')
                ->get();
            $micro     = $jadwals->where('tipe_seleksi', 'micro_teaching')->values();
            $wawancara = $jadwals->where('tipe_seleksi', 'wawancara')->values();
        }

        return view('admin.pelamar.show', compact('pelamar', 'activeLamaran', 'snapshot', 'micro', 'wawancara'));
    }

    /**
     * Import pelamar dari file Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'file.required' => 'File harus dipilih.',
            'file.mimes' => 'File harus berformat Excel (.xlsx, .xls) atau CSV.',
            'file.max' => 'Ukuran file maksimal 5MB.',
        ]);

        try {
            Excel::import(new PelamarImport(), $request->file('file'));

            $adminNama = auth()->user()->name ?? 'Admin';
            $waktu = now()->translatedFormat('d F Y \p\u\k\u\l H:i');
            \App\Models\User::where('role', 'admin')->each(function($u) use ($adminNama, $waktu) {
                \App\Models\Notifikasi::kirimSistem($u->id, 'Import Pelamar', "Admin {$adminNama} mengimpor data pelamar pada {$waktu}.");
            });

            return back()->with('success', 'Data pelamar berhasil diimpor dari Excel.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            
            foreach ($failures as $failure) {
                $errorMessages[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
            }
            
            return back()->withErrors(['import' => implode(' | ', $errorMessages)]);
        } catch (\Exception $e) {
            return back()->withErrors(['import' => 'Terjadi kesalahan saat mengimpor: ' . $e->getMessage()]);
        }
    }
}

