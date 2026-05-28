<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pelamar;
use App\Rules\NotDosenInternalDomain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Tampilkan daftar user.
     * Sejak konsolidasi 1 dosen = 1 akun, tidak perlu deduplication kompleks.
     */
    public function index(Request $request)
    {
        $users = User::with('dosen.prodi', 'prodi')
            ->whereNotNull('role')
            ->orderBy('role')
            ->orderBy('name')
            ->get();

        return view('admin.user', compact('users'));
    }

    /**
     * Hapus/cabut akses user.
     * - Pelamar: hapus user + data pelamar sepenuhnya.
     * - Penguji/Kaprodi/Rangkap (dosen): cabut role saja, data dosen tetap ada.
     */
    public function destroy(User $user)
    {
        // Jangan izinkan hapus admin
        if ($user->role === 'admin') {
            return back()->withErrors(['delete' => 'Akun admin tidak dapat dihapus.']);
        }

        $isPelamarRole = $user->role === 'pelamar';

        DB::transaction(function () use ($user, $isPelamarRole) {
            if ($isPelamarRole) {
                // Hapus data pelamar dan user sepenuhnya
                if ($user->pelamar) {
                    $user->pelamar->delete();
                }
                $user->delete();
            } else {
                // Dosen (penguji/kaprodi/rangkap): hapus akun user, kembalikan dosen jadi biasa
                if ($user->dosen) {
                    $user->dosen->update([
                        'is_penguji' => false,
                        'is_kaprodi' => false,
                    ]);
                }

                // Hapus akun user sepenuhnya — dosen tidak lagi bisa akses sistem
                $user->delete();
            }
        });

        $message = $isPelamarRole
            ? 'Akun pelamar berhasil dihapus.'
            : 'Akun & role dosen berhasil dihapus. Dosen kembali tanpa akses sistem.';

        return back()->with('success', $message);
    }
    public function update(Request $request, User $user)
    {
        $isPelamar = $user->role === 'pelamar';

        $rules = [
            'password' => ['nullable', \Illuminate\Validation\Rules\Password::defaults()],
        ];

        // Hanya pelamar yang bisa ubah email
        if ($isPelamar) {
            $rules['email'] = [
                'required', 'string', 'lowercase', 'email', 'max:255',
                'unique:users,email,' . $user->id,
                new NotDosenInternalDomain(),
            ];
        }

        $request->validate($rules);

        DB::transaction(function () use ($request, $user, $isPelamar) {
            $updateData = [];

            if ($isPelamar) {
                $oldEmail = $user->email;
                $newEmail = $request->email;
                $updateData['email'] = $newEmail;

                if ($oldEmail !== $newEmail) {
                    Pelamar::where('email', $oldEmail)->update(['email' => $newEmail]);
                }
            }

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
                $updateData['password_plain'] = $request->password;
            }

            if (!empty($updateData)) {
                $user->update($updateData);
            }
        });

        return back()->with('success', 'Kredensial berhasil diperbarui.');
    }
}
