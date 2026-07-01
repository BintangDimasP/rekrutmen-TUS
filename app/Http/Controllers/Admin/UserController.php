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
     * Tambah admin baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9._]+$/',
                'unique:users,email', // cek unik sebelum append domain
            ],
            'password' => ['required', \Illuminate\Validation\Rules\Password::defaults()],
        ], [
            'username.regex' => 'Username hanya boleh huruf kecil, angka, titik, dan underscore.',
            'username.unique' => 'Username sudah digunakan.',
        ]);

        $email = strtolower($request->username) . '@admin.telkomuniversity.ac.id';

        // Cek unik email lengkap
        if (User::where('email', $email)->exists()) {
            return back()->withErrors(['username' => 'Username sudah digunakan.'])->withInput();
        }

        User::create([
            'name'     => $request->name,
            'email'    => $email,
            'password' => Hash::make($request->password),
            'role'     => 'admin',
        ]);

        return back()->with('success', "Admin {$request->name} berhasil ditambahkan dengan email {$email}.");
    }

    /**
     * Hapus/cabut akses user.
     * - Pelamar: hapus user + data pelamar sepenuhnya.
     * - Penguji/Kaprodi/Rangkap (dosen): cabut role saja, data dosen tetap ada.
     */
    public function destroy(User $user)
    {
        // Jangan izinkan hapus diri sendiri
        if ($user->id === auth()->id()) {
            return back()->withErrors(['delete' => 'Anda tidak dapat menghapus akun sendiri.']);
        }

        $isPelamarRole = $user->role === 'pelamar';
        $isAdminRole = $user->role === 'admin';

        DB::transaction(function () use ($user, $isPelamarRole, $isAdminRole) {
            if ($isAdminRole) {
                // Hapus akun admin sepenuhnya
                $user->delete();
            } elseif ($isPelamarRole) {
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

        if ($isAdminRole) {
            $message = 'Akun admin berhasil dihapus.';
        } elseif ($isPelamarRole) {
            $message = 'Akun pelamar berhasil dihapus.';
        } else {
            $message = 'Akun & role dosen berhasil dihapus. Dosen kembali tanpa akses sistem.';
        }

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
                $newEmail = $request->email;
                $updateData['email'] = $newEmail;
            }

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            if (!empty($updateData)) {
                $user->update($updateData);
            }
        });

        return back()->with('success', 'Kredensial berhasil diperbarui.');
    }
}
