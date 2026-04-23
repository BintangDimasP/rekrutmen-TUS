<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lamaran;
use Illuminate\Http\Request;

class LamaranController extends Controller
{
    /**
     * Tampilkan detail lamaran pelamar.
     */
    public function show(Lamaran $lamaran)
    {
        $lamaran->load(['pelamar.user', 'lowongan.prodi']);
        return view('admin.lamaran.show', compact('lamaran'));
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

        $lamaran->update($validated);

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

