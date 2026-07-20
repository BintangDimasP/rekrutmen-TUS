<?php

namespace App\Http\Controllers\Admin;

use App\Exports\LamaranExport;
use App\Exports\LamaranNilaiExport;
use App\Http\Controllers\Controller;
use App\Models\JadwalSeleksi;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LamaranController extends Controller
{
    /**
     * Tampilkan daftar lamaran untuk lowongan tertentu.
     */
    public function index(Lowongan $lowongan)
    {
        $lowongan->load(['lamarans.pelamar.user', 'prodi']);
        return view('admin.lamaran.index', compact('lowongan'));
    }

    /**
     * Export daftar lamaran ke Excel.
     */
    public function exportExcel(Lowongan $lowongan)
    {
        $filename = 'Lamaran_' . str_replace(' ', '_', $lowongan->nama_posisi) . '_' . now()->format('Ymd') . '.xlsx';

        return Excel::download(new LamaranExport($lowongan), $filename);
    }

    /**
     * Export rekap nilai pelamar per lowongan ke Excel.
     */
    public function exportNilai(Lowongan $lowongan)
    {
        $filename = 'Nilai_' . str_replace(' ', '_', $lowongan->nama_posisi) . '_' . now()->format('Ymd') . '.xlsx';

        return Excel::download(new LamaranNilaiExport($lowongan), $filename);
    }

    /**
     * Tampilkan detail lamaran pelamar.
     */
    public function show(Lamaran $lamaran)
    {
        $lamaran->load(['pelamar.user', 'lowongan.prodi']);

        $jadwals = JadwalSeleksi::with(['penguji', 'penilaian'])
            ->where('pelamar_id', $lamaran->pelamar_id)
            ->where('lowongan_id', $lamaran->lowongan_id)
            ->get();

        $wawancara = $jadwals->where('tipe_seleksi', 'wawancara')->values();
        $micro     = $jadwals->where('tipe_seleksi', 'micro_teaching')->values();

        return view('admin.lamaran.show', compact('lamaran', 'wawancara', 'micro'));
    }

    public function cetak(Lamaran $lamaran)
    {
        $lamaran->load(['pelamar.user', 'lowongan.prodi']);

        $jadwals = JadwalSeleksi::with(['penguji', 'penilaian'])
            ->where('pelamar_id', $lamaran->pelamar_id)
            ->where('lowongan_id', $lamaran->lowongan_id)
            ->get();

        $wawancara = $jadwals->where('tipe_seleksi', 'wawancara')->values();
        $micro     = $jadwals->where('tipe_seleksi', 'micro_teaching')->values();

        return view('admin.lamaran.cetak', compact('lamaran', 'wawancara', 'micro'));
    }

    /**
     * Update status, jadwal wawancara, dan catatan lamaran.
     */
    public function update(Request $request, Lamaran $lamaran)
    {
        $validated = $request->validate([
            'status'             => 'required|in:menunggu,seleksi_tahap1,seleksi_tahap2,diterima,ditolak',
            'tanggal_wawancara'  => 'nullable|date',
            'link_zoom'          => 'nullable|url|max:500',
            'catatan_admin'      => 'nullable|string|max:1000',
        ]);

        $statusLama = $lamaran->status;

        // VALIDASI WAJIB: Admin tidak bisa loloskan ke Tahap 1 tanpa rekomendasi Kaprodi
        if ($validated['status'] === 'seleksi_tahap1' && !$lamaran->is_direkomendasikan_kaprodi) {
            return back()->withErrors([
                'status' => 'Pelamar ini belum mendapatkan rekomendasi dari Kaprodi. Lolos Tahap 1 tidak dapat diproses.',
            ])->withInput();
        }

        $lamaran->update($validated);

        // AUTO-WITHDRAW: Jika pelamar diterima, gugurkan semua lamaran aktif lainnya
        if ($validated['status'] === 'diterima' && $statusLama !== 'diterima') {
            Lamaran::where('pelamar_id', $lamaran->pelamar_id)
                ->where('id', '!=', $lamaran->id)
                ->whereIn('status', ['menunggu', 'seleksi_tahap1', 'seleksi_tahap2'])
                ->update(['status' => 'mengundurkan_diri']);
        }

        // Kirim notifikasi jika status berubah
        if ($statusLama !== $validated['status']) {
            $lamaran->load(['pelamar.user', 'lowongan']);
            $userId    = $lamaran->pelamar?->user?->id;
            $posisi    = $lamaran->lowongan?->nama_posisi ?? 'Lowongan';

            $statusLabels = [
                'menunggu'       => 'Menunggu',
                'seleksi_tahap1' => 'Seleksi Tahap 1 (Administrasi)',
                'seleksi_tahap2' => 'Seleksi Tahap 2 (Micro Teaching & Wawancara)',
                'diterima'       => 'Diterima',
                'ditolak'        => 'Ditolak',
            ];
            $labelBaru = $statusLabels[$validated['status']] ?? $validated['status'];

            if ($userId) {
                Notifikasi::kirim(
                    $userId,
                    'Status Lamaran Diperbarui',
                    "Status lamaran Anda untuk posisi \"{$posisi}\" telah diubah menjadi: {$labelBaru}." .
                    ($lamaran->catatan_admin ? " Catatan: {$lamaran->catatan_admin}" : ''),
                    'status',
                    'rekrutmen_informasi_status_pelamar',
                    [$posisi, $labelBaru, $lamaran->catatan_admin ?? '-']
                );
            }

            // Notify kaprodi when status changes to diterima or ditolak
            if (in_array($validated['status'], ['diterima', 'ditolak'])) {
                $lamaran->load(['lowongan.prodi']);
                $namaP = $lamaran->pelamar->nama ?? '-';
                $posisiLog = $lamaran->lowongan->nama_posisi ?? '-';
                $prodiNama = $lamaran->lowongan->prodi->nama ?? '-';
                $waktu = now()->translatedFormat('d F Y \p\u\k\u\l H:i');
                $statusTeks = $validated['status'] === 'diterima' ? 'diterima' : 'ditolak';
                $msgLog = "Pelamar {$namaP} telah {$statusTeks} pada lowongan {$posisiLog} ({$prodiNama}) pada {$waktu}.";
                $judulLog = $validated['status'] === 'diterima' ? 'Pelamar Diterima' : 'Pelamar Ditolak';
                // Notify kaprodi of that prodi
                $prodiId = $lamaran->lowongan->prodi_id ?? null;
                if ($prodiId) {
                    \App\Models\User::where('is_kaprodi', true)->where('prodi_id', $prodiId)->each(function($u) use ($msgLog, $judulLog) {
                        \App\Models\Notifikasi::kirimAktivitasPelamar($u->id, $judulLog, $msgLog);
                    });
                }
            }

        }

        return back()->with('success', 'Data lamaran berhasil diperbarui.');
    }

    /**
     * Hapus semua lamaran berstatus mengundurkan_diri untuk lowongan tertentu.
     */
    public function destroyWithdrawn(Lowongan $lowongan)
    {
        $pelamarIds = $lowongan->lamarans()->where('status', 'mengundurkan_diri')->pluck('pelamar_id');

        if ($pelamarIds->isNotEmpty()) {
            \App\Models\JadwalSeleksi::where('lowongan_id', $lowongan->id)
                ->whereIn('pelamar_id', $pelamarIds)
                ->delete();
        }

        $deleted = $lowongan->lamarans()
            ->where('status', 'mengundurkan_diri')
            ->delete();

        return back()->with('success', "{$deleted} data pelamar yang mengundurkan diri berhasil dihapus.");
    }

    /**
     * Hapus lamaran.
     */
    public function destroy(Lamaran $lamaran)
    {
        $lowongan_id = $lamaran->lowongan_id;
        $pelamar_id = $lamaran->pelamar_id;

        \App\Models\JadwalSeleksi::where('lowongan_id', $lowongan_id)
            ->where('pelamar_id', $pelamar_id)
            ->delete();

        $lamaran->delete();

        return redirect()->route('admin.lamaran.index', $lowongan_id)->with('success', 'Data lamaran berhasil dihapus.');
    }

    /**
     * Filter dan search lamaran via AJAX
     */
    public function filter(Request $request, Lowongan $lowongan)
    {
        $search = $request->input('search', '');
        $status = $request->input('status', '');

        $lamarans = $lowongan->lamarans()
            ->with(['pelamar.user'])
            ->get()
            ->filter(function ($lamaran) use ($search, $status) {
                $matchSearch = empty($search) || 
                    stripos($lamaran->pelamar->nama, $search) !== false ||
                    stripos($lamaran->pelamar->no_telepon, $search) !== false;
                
                $matchStatus = empty($status) || $lamaran->status === $status;
                
                return $matchSearch && $matchStatus;
            })
            ->values();

        return response()->json([
            'lamarans' => $lamarans->map(function ($lamaran) {
                return [
                    'id' => $lamaran->id,
                    'nama' => $lamaran->pelamar->nama,
                    'jenjang' => $lamaran->pelamar->jenjang,
                    'institusi' => $lamaran->pelamar->institusi,
                    'prodi_pendidikan' => $lamaran->pelamar->prodi_pendidikan,
                    'no_telepon' => $lamaran->pelamar->no_telepon,
                    'email' => $lamaran->pelamar->user?->email,
                    'status' => $lamaran->status,
                    'status_label' => $lamaran->status_label,
                ];
            }),
        ]);
    }
}

