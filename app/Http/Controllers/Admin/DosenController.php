<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DosenController extends Controller
{
    /**
     * Simpan data dosen baru.
     */
    public function store(Request $request, Prodi $prodi)
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kode' => ['required', 'string', 'max:50', 'unique:dosens,kode'],
            'nip' => ['nullable', 'string', 'max:50'],
            'nidn' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255', 'unique:dosens,email'],
        ]);

        $isKaprodi = $request->has('is_kaprodi');
        $isPenguji = $request->has('is_penguji');

        DB::transaction(function () use ($request, $prodi, $isKaprodi, $isPenguji) {
            if ($isKaprodi) {
                // Reset kaprodi lain di prodi ini
                Dosen::where('prodi_id', $prodi->id)
                    ->where('is_kaprodi', true)
                    ->update(['is_kaprodi' => false]);
            }

            Dosen::create([
                'nama' => $request->nama,
                'kode' => strtoupper($request->kode),
                'nip' => $request->nip,
                'nidn' => $request->nidn,
                'email' => strtolower($request->email),
                'prodi_id' => $prodi->id,
                'is_kaprodi' => $isKaprodi,
                'is_penguji' => $isPenguji,
            ]);
        });

        return back()->with('success', 'Data dosen berhasil ditambahkan.');
    }

    /**
     * Update data dosen.
     */
    public function update(Request $request, Dosen $dosen)
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kode' => ['required', 'string', 'max:50', 'unique:dosens,kode,' . $dosen->id],
            'nip' => ['nullable', 'string', 'max:50'],
            'nidn' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255', 'unique:dosens,email,' . $dosen->id],
        ]);

        $isKaprodi = $request->has('is_kaprodi');
        $isPenguji = $request->has('is_penguji');

        DB::transaction(function () use ($request, $dosen, $isKaprodi, $isPenguji) {
            if ($isKaprodi) {
                // Reset kaprodi lain di prodi ini
                Dosen::where('prodi_id', $dosen->prodi_id)
                    ->where('id', '!=', $dosen->id)
                    ->where('is_kaprodi', true)
                    ->update(['is_kaprodi' => false]);
            }

            $dosen->update([
                'nama' => $request->nama,
                'kode' => strtoupper($request->kode),
                'nip' => $request->nip,
                'nidn' => $request->nidn,
                'email' => strtolower($request->email),
                'is_kaprodi' => $isKaprodi,
                'is_penguji' => $isPenguji,
            ]);
        });

        return back()->with('success', 'Data dosen berhasil diperbarui.');
    }

    /**
     * Hapus data dosen.
     */
    public function destroy(Dosen $dosen)
    {
        $dosen->delete();

        return back()->with('success', 'Data dosen berhasil dihapus.');
    }
}
