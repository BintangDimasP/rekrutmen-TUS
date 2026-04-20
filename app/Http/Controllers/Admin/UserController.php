<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Dosen;
use App\Models\Pelamar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Tampilkan daftar user.
     */
    public function index()
    {
        $users = User::whereIn('role', ['admin', 'pelamar', 'penguji', 'kaprodi'])
            ->orderBy('role')
            ->orderBy('name')
            ->get();

        return view('admin.user', compact('users'));
    }

    /**
     * Update email dan/atau password user.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $oldEmail = $user->email;
        $newEmail = $request->email;

        DB::transaction(function () use ($request, $user, $oldEmail, $newEmail) {
            // Update email & password di tabel Users
            $updateData = ['email' => $newEmail];
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }
            $user->update($updateData);

            // Sinkronisasi data ke tabel master terkait jika email berubah
            if ($oldEmail !== $newEmail) {
                if ($user->role === 'pelamar') {
                    Pelamar::where('email', $oldEmail)->update(['email' => $newEmail]);
                } elseif (in_array($user->role, ['kaprodi', 'penguji'])) {
                    Dosen::where('email', $oldEmail)->update(['email' => $newEmail]);
                }
            }
        });

        return back()->with('success', 'Akun ' . $user->name . ' berhasil diperbarui.');
    }
}
