<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prodi;
use App\Models\Dosen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProdiController extends Controller
{
    /**
     * Tampilkan semua prodi dalam tampilan grid.
     */
    public function index()
    {
        $prodis = Prodi::with('kaprodi')->withCount(['dosens', 'pengujis'])->orderBy('nama')->get();

        return view('admin.prodi.index', compact('prodis'));
    }

    /**
     * Simpan prodi baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:120'],
            'kode' => ['required', 'string', 'max:20', 'unique:prodis,kode'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('prodi_logos', 'public');
        }

        Prodi::create([
            'nama' => $request->nama,
            'kode' => strtoupper($request->kode),
            'logo' => $logoPath,
        ]);

        return redirect()->route('admin.prodi.index')->with('success', 'Prodi berhasil ditambahkan.');
    }

    /**
     * Tampilkan detail prodi beserta daftar anggotanya.
     */
    public function show(Prodi $prodi, Request $request)
    {
        $prodi->load(['kaprodi', 'pengujis']);
        $dosens = $prodi->dosens()->paginate(10)->appends($request->query());

        return view('admin.prodi.show', compact('prodi', 'dosens'));
    }

    /**
     * Update data prodi (nama, kode, kaprodi, logo).
     */
    public function update(Request $request, Prodi $prodi)
    {
        $request->validate([
            'nama'       => ['required', 'string', 'max:120'],
            'kode'       => ['required', 'string', 'max:20', 'unique:prodis,kode,' . $prodi->id],
            'logo'       => ['nullable', 'image', 'max:2048'],
        ]);

        $logoPath = $prodi->logo;
        if ($request->hasFile('logo')) {
            if ($logoPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($logoPath)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($logoPath);
            }
            $logoPath = $request->file('logo')->store('prodi_logos', 'public');
        }

        $prodi->update([
            'nama'       => $request->nama,
            'kode'       => strtoupper($request->kode),
            'logo'       => $logoPath,
        ]);

        return redirect()->route('admin.prodi.index')->with('success', 'Prodi berhasil diperbarui.');
    }


    /**
     * Hapus prodi.
     */
    public function destroy(Prodi $prodi)
    {
        if ($prodi->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($prodi->logo)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($prodi->logo);
        }
        
        $prodi->delete();

        return redirect()->route('admin.prodi.index')->with('success', 'Prodi berhasil dihapus.');
    }
}
