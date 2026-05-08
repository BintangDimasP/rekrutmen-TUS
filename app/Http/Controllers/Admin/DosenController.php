<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Prodi;
use App\Models\User;
use App\Imports\DosenImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class DosenController extends Controller
{
    /**
     * Simpan data dosen baru.
     */
    public function store(Request $request, Prodi $prodi)
    {
        $request->validate([
            'nama'  => ['required', 'string', 'max:255'],
            'kode'  => ['required', 'string', 'max:50', 'unique:dosens,kode'],
            'nip'   => ['nullable', 'string', 'max:50'],
            'nidn'  => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255', 'unique:dosens,email'],
        ]);

        $isKaprodi = $request->boolean('is_kaprodi');
        $isPenguji = $request->boolean('is_penguji');

        DB::transaction(function () use ($request, $prodi, $isKaprodi, $isPenguji) {
            if ($isKaprodi) {
                // Reset kaprodi lain di prodi ini
                Dosen::where('prodi_id', $prodi->id)
                    ->where('is_kaprodi', true)
                    ->each(function ($d) {
                        $d->update(['is_kaprodi' => false]);
                        $this->syncUserRole($d->email, false, $d->is_penguji);
                    });
            }

            $dosen = Dosen::create([
                'nama'       => $request->nama,
                'kode'       => strtoupper($request->kode),
                'nip'        => $request->nip,
                'nidn'       => $request->nidn,
                'email'      => strtolower($request->email),
                'prodi_id'   => $prodi->id,
                'is_kaprodi' => $isKaprodi,
                'is_penguji' => $isPenguji,
            ]);

            $this->syncUserRole($dosen->email, $isKaprodi, $isPenguji, $dosen->nama, $prodi->id);
        });

        return back()->with('success', 'Data dosen berhasil ditambahkan.');
    }

    /**
     * Update data dosen.
     */
    public function update(Request $request, Dosen $dosen)
    {
        $request->validate([
            'nama'  => ['required', 'string', 'max:255'],
            'kode'  => ['required', 'string', 'max:50', 'unique:dosens,kode,' . $dosen->id],
            'nip'   => ['nullable', 'string', 'max:50'],
            'nidn'  => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255', 'unique:dosens,email,' . $dosen->id],
        ]);

        $isKaprodi = $request->boolean('is_kaprodi');
        $isPenguji = $request->boolean('is_penguji');

        DB::transaction(function () use ($request, $dosen, $isKaprodi, $isPenguji) {
            if ($isKaprodi) {
                // Reset kaprodi lain di prodi ini (kecuali dosen yang sedang diedit)
                Dosen::where('prodi_id', $dosen->prodi_id)
                    ->where('id', '!=', $dosen->id)
                    ->where('is_kaprodi', true)
                    ->each(function ($d) {
                        $d->update(['is_kaprodi' => false]);
                        $this->syncUserRole($d->email, false, $d->is_penguji);
                    });
            }

            $oldEmail = $dosen->email;
            $newEmail = strtolower($request->email);

            $dosen->update([
                'nama'       => $request->nama,
                'kode'       => strtoupper($request->kode),
                'nip'        => $request->nip,
                'nidn'       => $request->nidn,
                'email'      => $newEmail,
                'is_kaprodi' => $isKaprodi,
                'is_penguji' => $isPenguji,
            ]);

            // Sinkronisasi email di tabel users jika berubah
            if ($oldEmail !== $newEmail) {
                User::where('email', $oldEmail)->update(['email' => $newEmail]);
            }

            $this->syncUserRole($newEmail, $isKaprodi, $isPenguji, $dosen->nama, $dosen->prodi_id);
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

    /**
     * Sinkronisasi role user berdasarkan flag dosen.
     * Jika belum punya akun dan diset sebagai kaprodi, akun login otomatis dibuat.
     * Prioritas: kaprodi > penguji > (tidak diubah jika keduanya false)
     */
    private function syncUserRole(string $email, bool $isKaprodi, bool $isPenguji, string $nama = '', ?int $prodiId = null): void
    {
        $user = User::where('email', $email)->first();

        if ($isKaprodi) {
            if ($user) {
                // Update role dan pastikan prodi_id tersinkronisasi
                $user->update([
                    'role'     => 'kaprodi',
                    'prodi_id' => $prodiId ?? $user->prodi_id,
                ]);
            } else {
                // Buat akun baru otomatis jika belum ada
                User::create([
                    'name'     => $nama,
                    'email'    => $email,
                    'password' => \Illuminate\Support\Facades\Hash::make('kaprodi123'),
                    'role'     => 'kaprodi',
                    'prodi_id' => $prodiId,
                ]);
            }
        } elseif ($isPenguji) {
            if ($user && !in_array($user->role, ['admin', 'kaprodi', 'penguji'])) {
                $user->update(['role' => 'penguji']);
            }
        }
        // Jika keduanya false, role tidak diubah (biarkan admin atur manual)
    }

    /**
     * Import data dosen dari file Excel.
     */
    public function import(Request $request, Prodi $prodi)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:2048'],
        ]);

        try {
            Excel::import(new DosenImport($prodi->id), $request->file('file'));
            return back()->with('success', 'Data dosen berhasil diimport.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $messages = [];
            foreach ($failures as $failure) {
                $messages[] = "Baris " . $failure->row() . ": " . implode(', ', $failure->errors());
            }
            return back()->withErrors(['file' => implode('<br>', $messages)])->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['file' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }
}
