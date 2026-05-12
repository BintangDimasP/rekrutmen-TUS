<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalSeleksi;
use App\Models\Lamaran;
use App\Models\Notifikasi;
use Illuminate\Http\Request;

class LamaranController extends Controller
{
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

        // Get all jadwals grouped by tipe_seleksi
        $wawancara = $jadwals->where('tipe_seleksi', 'wawancara')->values();
        $micro     = $jadwals->where('tipe_seleksi', 'micro_teaching')->values();

        return view('admin.lamaran.show', compact('lamaran', 'wawancara', 'micro'));
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
        $lamaran->update($validated);

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
                    'status'
                );
            }
        }

        return back()->with('success', 'Data lamaran berhasil diperbarui.');
    }

    /**
     * Hapus lamaran.
     */
    public function destroy(Lamaran $lamaran)
    {
        $lowongan_id = $lamaran->lowongan_id;
        $lamaran->delete();

        return redirect()->route('admin.lowongan.show', $lowongan_id)->with('success', 'Data lamaran berhasil dihapus.');
    }
}

