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
        $users = User::with('penguji_user')
            ->whereIn('role', ['admin', 'pelamar', 'penguji', 'kaprodi'])
            ->where(function ($q) {
                // Exclude penguji rows that have a kaprodi counterpart to avoid duplicate display
                $q->where('role', '!=', 'penguji')
                  ->orWhereNull('dosen_id')
                  ->orWhereNotExists(function ($sub) {
                      $sub->select(DB::raw(1))
                          ->from('users as u2')
                          ->whereColumn('u2.dosen_id', 'users.dosen_id')
                          ->where('u2.role', 'kaprodi');
                  });
            })
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
        $isPelamar = $user->role === 'pelamar';
        
        $rules = [
            'password' => ['nullable', \Illuminate\Validation\Rules\Password::defaults()],
        ];

        // Hanya pelamar yang bisa ubah email
        if ($isPelamar) {
            $rules['email'] = ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id];
        }

        $pengujiUser = null;
        if ($user->dosen_id && $user->role === 'kaprodi') {
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
