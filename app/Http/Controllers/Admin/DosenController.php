<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Prodi;
use App\Models\User;
use App\Imports\DosenImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class DosenController extends Controller
{
    /**
     * Simpan data dosen baru.
     * Email otomatis diset ke '-' (dosen biasa tidak punya akses login).
     */
    public function store(Request $request, Prodi $prodi)
    {
        $request->validate([
            'nama'  => ['required', 'string', 'max:255'],
            'kode'  => ['required', 'string', 'max:50', 'unique:dosens,kode'],
            'nip'   => ['nullable', 'string', 'max:50'],
            'nidn'  => ['nullable', 'string', 'max:50'],
        ]);

        $isKaprodi = $request->boolean('is_kaprodi');

        DB::transaction(function () use ($request, $prodi, $isKaprodi) {
            if ($isKaprodi) {
                // Reset kaprodi lain di prodi ini
                Dosen::where('prodi_id', $prodi->id)
                    ->where('is_kaprodi', true)
                    ->each(function ($d) {
                        $d->update(['is_kaprodi' => false]);
                        // Hapus akun kaprodi dari user lama
                        User::where('dosen_id', $d->id)->where('role', 'kaprodi')->delete();
                    });
            }

            $dosen = Dosen::create([
                'nama'       => $request->nama,
                'kode'       => strtoupper($request->kode),
                'nip'        => $request->nip,
                'nidn'       => $request->nidn,
                'email'      => '-',
                'prodi_id'   => $prodi->id,
                'is_kaprodi' => $isKaprodi,
                'is_penguji' => false,
            ]);

            // Jika ditandai kaprodi, buat akun user otomatis
            if ($isKaprodi) {
                $this->createRoleAccount($dosen, 'kaprodi', $prodi->id);
            }
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
        ]);

        $isKaprodi = $request->boolean('is_kaprodi');
        $wasKaprodi = $dosen->is_kaprodi;

        DB::transaction(function () use ($request, $dosen, $isKaprodi, $wasKaprodi) {
            if ($isKaprodi && !$wasKaprodi) {
                // Baru ditunjuk kaprodi: reset kaprodi lain di prodi ini
                Dosen::where('prodi_id', $dosen->prodi_id)
                    ->where('id', '!=', $dosen->id)
                    ->where('is_kaprodi', true)
                    ->each(function ($d) {
                        $d->update(['is_kaprodi' => false]);
                        User::where('dosen_id', $d->id)->where('role', 'kaprodi')->delete();
                    });
            }

            $oldNama = $dosen->nama;

            $dosen->update([
                'nama'       => $request->nama,
                'kode'       => strtoupper($request->kode),
                'nip'        => $request->nip,
                'nidn'       => $request->nidn,
                'is_kaprodi' => $isKaprodi,
                // is_penguji tidak diubah di sini — dikelola via PengujiController
            ]);

            // Handle perubahan status kaprodi
            if ($isKaprodi && !$wasKaprodi) {
                // Baru ditunjuk: buat akun kaprodi
                $this->createRoleAccount($dosen, 'kaprodi', $dosen->prodi_id);
            } elseif (!$isKaprodi && $wasKaprodi) {
                // Dicabut: hapus akun kaprodi
                User::where('dosen_id', $dosen->id)->where('role', 'kaprodi')->delete();
            }

            // Jika nama berubah, update email akun-akun yang ada
            if ($oldNama !== $request->nama) {
                $this->regenerateUserEmails($dosen);
            }

            // Update nama di semua akun user terkait
            User::where('dosen_id', $dosen->id)->update(['name' => $dosen->nama]);
        });

        return back()->with('success', 'Data dosen berhasil diperbarui.');
    }

    /**
     * Hapus data dosen.
     */
    public function destroy(Dosen $dosen)
    {
        DB::transaction(function () use ($dosen) {
            // Hapus semua akun user terkait
            User::where('dosen_id', $dosen->id)->delete();
            $dosen->delete();
        });

        return back()->with('success', 'Data dosen berhasil dihapus.');
    }

    /**
     * Buat akun user untuk dosen dengan role tertentu.
     */
    private function createRoleAccount(Dosen $dosen, string $role, ?int $prodiId = null): User
    {
        $domain = $role === 'kaprodi'
            ? 'kaprodi.telkomuniversity.ac.id'
            : 'penguji.telkomuniversity.ac.id';

        $email = $dosen->generateUniqueEmail($domain);
        $defaultPassword = $role === 'kaprodi' ? 'kaprodi123' : 'penguji123';

        return User::create([
            'name'     => $dosen->nama,
            'email'    => $email,
            'password' => Hash::make($defaultPassword),
            'password_plain' => $defaultPassword,
            'role'     => $role,
            'prodi_id' => $prodiId ?? $dosen->prodi_id,
            'dosen_id' => $dosen->id,
        ]);
    }

    /**
     * Regenerate email akun-akun user dosen (misal saat nama berubah).
     */
    private function regenerateUserEmails(Dosen $dosen): void
    {
        $users = User::where('dosen_id', $dosen->id)->get();
        foreach ($users as $user) {
            $domain = $user->role === 'kaprodi'
                ? 'kaprodi.telkomuniversity.ac.id'
                : 'penguji.telkomuniversity.ac.id';
            $newEmail = $dosen->generateUniqueEmail($domain);
            $user->update(['email' => $newEmail]);
        }
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
