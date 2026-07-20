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
     * Dosen biasa TIDAK memiliki akun user — akun hanya dibuat saat ditunjuk penguji/kaprodi.
     */
    public function store(Request $request, Prodi $prodi)
    {
        $request->validate([
            'nama'       => ['required', 'string', 'max:255'],
            'kode'       => ['required', 'string', 'max:50', 'unique:dosens,kode'],
            'nip'        => ['nullable', 'string', 'max:18'],
            'nidn'       => ['nullable', 'string', 'max:10'],
            'no_telepon' => ['nullable', 'string', 'regex:/^08[0-9]{6,13}$/', 'max:15'],
        ]);

        $isKaprodi = $request->boolean('is_kaprodi');

        $dosen = null;
        DB::transaction(function () use ($request, $prodi, $isKaprodi, &$dosen) {
            if ($isKaprodi) {
                $this->demoteCurrentKaprodi($prodi->id);
            }

            $dosen = Dosen::create([
                'nama'       => $request->nama,
                'kode'       => strtoupper($request->kode),
                'nip'        => $request->nip,
                'nidn'       => $request->nidn,
                'no_telepon' => $request->no_telepon,
                'email'      => '-',
                'prodi_id'   => $prodi->id,
                'is_kaprodi' => $isKaprodi,
                'is_penguji' => false,
            ]);

            if ($isKaprodi) {
                // Ditunjuk kaprodi langsung saat create → buat akun sekarang
                $user = $dosen->getOrCreateUser();
                $this->activateKaprodiRole($user, $prodi->id);
            }
            // Dosen biasa: tidak ada akun dibuat
        });

        // Notify admins - sistem log
        if ($dosen) {
            $adminNama = auth()->user()->name ?? 'Admin';
            $prodiNama = $prodi->nama ?? '-';
            $waktu = now()->translatedFormat('d F Y \p\u\k\u\l H:i');
            User::where('role', 'admin')->each(function($u) use ($adminNama, $dosen, $prodiNama, $waktu) {
                \App\Models\Notifikasi::kirimSistem(
                    $u->id,
                    'Dosen Ditambahkan',
                    "Admin {$adminNama} menambahkan dosen {$dosen->nama} pada prodi {$prodiNama} pada {$waktu}."
                );
            });
        }

        return back()->with('success', 'Data dosen berhasil ditambahkan.');
    }

    /**
     * Update data dosen.
     */
    public function update(Request $request, Dosen $dosen)
    {
        $request->validate([
            'nama'       => ['required', 'string', 'max:255'],
            'kode'       => ['required', 'string', 'max:50', 'unique:dosens,kode,' . $dosen->id],
            'nip'        => ['nullable', 'string', 'max:18'],
            'nidn'       => ['nullable', 'string', 'max:10'],
            'no_telepon' => ['nullable', 'string', 'regex:/^08[0-9]{6,13}$/', 'max:15'],
        ]);

        $isKaprodi  = $request->boolean('is_kaprodi');
        $wasKaprodi = $dosen->is_kaprodi;

        DB::transaction(function () use ($request, $dosen, $isKaprodi, $wasKaprodi) {
            if ($isKaprodi && !$wasKaprodi) {
                $this->demoteCurrentKaprodi($dosen->prodi_id, $dosen->id);
            }

            $oldNama = $dosen->nama;

            $dosen->update([
                'nama'       => $request->nama,
                'kode'       => strtoupper($request->kode),
                'nip'        => $request->nip,
                'nidn'       => $request->nidn,
                'no_telepon' => $request->no_telepon,
                'is_kaprodi' => $isKaprodi,
                // is_penguji dikelola via PengujiController
            ]);

            if ($isKaprodi && !$wasKaprodi) {
                // Baru ditunjuk kaprodi → buat/ambil akun lalu aktifkan
                $user = $dosen->fresh()->getOrCreateUser();
                $this->activateKaprodiRole($user, $dosen->prodi_id);
            } elseif (!$isKaprodi && $wasKaprodi) {
                // Kaprodi dicabut
                $user = $dosen->user;
                if ($user) {
                    $this->deactivateKaprodiRole($user);
                }
            }
            
            // Selalu sinkronkan data User (nama & email) jika dosen punya akun
            // (baik sebagai kaprodi, penguji, maupun keduanya)
            $user = $dosen->fresh()->user;
            if ($user) {
                if ($oldNama !== $request->nama) {
                    $user->update([
                        'name'  => $dosen->fresh()->nama,
                        'email' => $dosen->fresh()->generateUniqueEmail($user->id),
                    ]);
                } else {
                    $user->update(['name' => $dosen->fresh()->nama]);
                }
            }
        });

        $adminNama = auth()->user()->name ?? 'Admin';
        $prodiNama = $dosen->fresh()->prodi->nama ?? '-';
        $waktu = now()->translatedFormat('d F Y \p\u\k\u\l H:i');
        \App\Models\User::where('role', 'admin')->each(function($u) use ($adminNama, $dosen, $prodiNama, $waktu) {
            \App\Models\Notifikasi::kirimSistem($u->id, 'Dosen Diperbarui', "Admin {$adminNama} memperbarui data dosen {$dosen->nama} pada prodi {$prodiNama} pada {$waktu}.");
        });

        return back()->with('success', 'Data dosen berhasil diperbarui.');
    }

    /**
     * Hapus data dosen.
     * Akun user dihapus lewat cascade FK kalau ada.
     */
    public function destroy(Dosen $dosen)
    {
        $dosenNama = $dosen->nama;
        $prodiNama = $dosen->prodi->nama ?? '-';

        DB::transaction(function () use ($dosen) {
            User::where('dosen_id', $dosen->id)->delete();
            $dosen->delete();
        });

        $adminNama = auth()->user()->name ?? 'Admin';
        $waktu = now()->translatedFormat('d F Y \p\u\k\u\l H:i');
        \App\Models\User::where('role', 'admin')->each(function($u) use ($adminNama, $dosenNama, $prodiNama, $waktu) {
            \App\Models\Notifikasi::kirimSistem($u->id, 'Dosen Dihapus', "Admin {$adminNama} menghapus dosen {$dosenNama} dari prodi {$prodiNama} pada {$waktu}.");
        });

        return back()->with('success', 'Data dosen berhasil dihapus.');
    }

    // ── Private helpers ──────────────────────────────────────────

    /**
     * Cabut kaprodi dari dosen lain di prodi yang sama (sebelum menunjuk yang baru).
     */
    private function demoteCurrentKaprodi(int $prodiId, ?int $exceptDosenId = null): void
    {
        Dosen::where('prodi_id', $prodiId)
            ->where('is_kaprodi', true)
            ->when($exceptDosenId, fn($q) => $q->where('id', '!=', $exceptDosenId))
            ->each(function (Dosen $d) {
                $d->update(['is_kaprodi' => false]);
                $u = $d->user;
                if ($u) {
                    $this->deactivateKaprodiRole($u);
                }
            });
    }

    /**
     * Aktifkan role kaprodi pada akun user dosen.
     */
    private function activateKaprodiRole(User $user, int $prodiId): void
    {
        // Load dosen to sync is_penguji flag
        $dosen = \App\Models\Dosen::find($user->dosen_id);

        $user->update([
            'is_kaprodi' => true,
            'is_penguji' => $dosen ? (bool) $dosen->is_penguji : $user->is_penguji,
            'role'       => 'kaprodi',
            'prodi_id'   => $prodiId,
            'password'   => Hash::make(Dosen::DEFAULT_PASSWORD),
        ]);
    }

    /**
     * Cabut role kaprodi.
     * Kalau masih punya is_penguji=true → fallback ke 'penguji'.
     * Kalau tidak ada role lain → hapus akun user sepenuhnya.
     */
    private function deactivateKaprodiRole(User $user): void
    {
        if ($user->is_penguji) {
            $user->update([
                'is_kaprodi' => false,
                'role'       => 'penguji',
            ]);
        } else {
            // Tidak ada role tersisa → hapus akun
            $user->delete();
        }
    }

    /**
     * Import data dosen dari file Excel.
     * Dosen biasa tidak mendapat akun user.
     */
    public function import(Request $request, Prodi $prodi)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ]);

        try {
            Excel::import(new DosenImport($prodi->id), $request->file('file'));

            $adminNama = auth()->user()->name ?? 'Admin';
            $prodiNama = $prodi->nama ?? '-';
            $waktu = now()->translatedFormat('d F Y \p\u\k\u\l H:i');
            \App\Models\User::where('role', 'admin')->each(function($u) use ($adminNama, $prodiNama, $waktu) {
                \App\Models\Notifikasi::kirimSistem($u->id, 'Import Dosen', "Admin {$adminNama} mengimpor data dosen pada prodi {$prodiNama} pada {$waktu}.");
            });

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
