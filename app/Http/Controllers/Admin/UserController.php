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
    public function index(Request $request)
    {
        $query = User::whereIn('role', ['admin', 'pelamar', 'penguji', 'kaprodi']);

        // Filter Role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Search Name/Email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('role')
            ->orderBy('name')
            ->get();

        // Visual Grouping for Dual Role Dosens (Kaprodi & Penguji)
        if (!$request->filled('role')) {
            $finalUsers = collect();
            $pengujiMap = [];

            // First pass: identify pure penguji who might be paired
            foreach ($users as $user) {
                if ($user->role === 'penguji' && $user->dosen_id) {
                    $pengujiMap[$user->dosen_id] = $user;
                }
            }

            foreach ($users as $user) {
                if ($user->role === 'penguji' && $user->dosen_id) {
                    $hasKaprodi = $users->contains(function ($u) use ($user) {
                        return $u->role === 'kaprodi' && $u->dosen_id === $user->dosen_id;
                    });
                    if ($hasKaprodi) {
                        continue; // Skip standalone penguji if kaprodi exists, it will be attached
                    }
                }

                if ($user->role === 'kaprodi' && $user->dosen_id) {
                    if (isset($pengujiMap[$user->dosen_id])) {
                        $user->penguji_user = $pengujiMap[$user->dosen_id];
                    }
                }

                $finalUsers->push($user);
            }
            $users = $finalUsers;
        }

        return view('admin.user', compact('users'));
    }

    /**
     * Update email dan/atau password user.
     */
    public function update(Request $request, User $user)
    {
        $isPelamar = $user->role === 'pelamar';
        
        $rules = [
            'password' => ['nullable', \Illuminate\Validation\Rules\Password::defaults()],
        ];

        // Hanya pelamar yang bisa ubah email
        if ($isPelamar) {
            $rules['email'] = ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id];
        }

        $pengujiUser = null;
        if ($request->has('penguji_email') && $user->dosen_id && $user->role === 'kaprodi') {
            $pengujiUser = User::where('dosen_id', $user->dosen_id)->where('role', 'penguji')->first();
            if ($pengujiUser) {
                // Email penguji tidak bisa diubah (karena role bukan pelamar), tapi tetap divalidasi jika dikirim
                // Namun sesuai request: "hanya pelamar yang dapat diubah emailnya". 
                // Jadi penguji_email tidak akan diproses perubahannya jika bukan pelamar.
                $rules['penguji_password'] = ['nullable', \Illuminate\Validation\Rules\Password::defaults()];
            }
        }

        $request->validate($rules);

        DB::transaction(function () use ($request, $user, $pengujiUser, $isPelamar) {
            $updateData = [];
            
            // Update email hanya jika pelamar
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

            // Update akun penguji jika ada
            if ($pengujiUser) {
                $pUpdateData = [];
                // Email penguji TIDAK diubah sesuai request
                
                if ($request->filled('penguji_password')) {
                    $pUpdateData['password'] = Hash::make($request->penguji_password);
                    $pUpdateData['password_plain'] = $request->penguji_password;
                }
                
                if (!empty($pUpdateData)) {
                    $pengujiUser->update($pUpdateData);
                }
            }
        });

        return back()->with('success', 'Kredensial berhasil diperbarui.');
    }
}
