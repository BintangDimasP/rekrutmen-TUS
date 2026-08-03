<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleSwitchController extends Controller
{
    /**
     * Pindah role aktif untuk dosen yang punya rangkap (penguji + kaprodi).
     */
    public function switch(Request $request)
    {
        $request->validate([
            'role' => 'required|string|in:penguji,kaprodi',
        ]);

        $user = Auth::user();
        $target = $request->role;

        // Hanya role kaprodi dan penguji yang boleh melakukan switch role
        if (!in_array($user->role, ['kaprodi', 'penguji'])) {
            return back()->withErrors(['role' => 'Fitur ganti akses (switch role) hanya diperbolehkan untuk Kaprodi.']);
        }

        // Hanya boleh pindah ke role yang dimiliki
        if ($target === 'penguji' && !$user->is_penguji) {
            return back()->withErrors(['role' => 'Anda tidak memiliki akses penguji.']);
        }
        if ($target === 'kaprodi' && !$user->is_kaprodi) {
            return back()->withErrors(['role' => 'Anda tidak memiliki akses kaprodi.']);
        }

        $user->update(['role' => $target]);

        return redirect()->route("{$target}.dashboard")->with('success', 'Berpindah ke laman ' . ucfirst($target) . '.');
    }
}
