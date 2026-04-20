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
     * Hapus lamaran.
     */
    public function destroy(Lamaran $lamaran)
    {
        $lowongan_id = $lamaran->lowongan_id;
        $lamaran->delete();

        return redirect()->route('admin.lowongan.show', $lowongan_id)->with('success', 'Data lamaran berhasil dihapus.');
    }
}
