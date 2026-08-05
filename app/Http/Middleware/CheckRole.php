<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * 
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect('/');
        }

        // Dosen tanpa role aktif (belum ditunjuk) → tidak boleh masuk area apapun
        if (empty($user->role)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/login')->withErrors(['email' => 'Akun Anda belum memiliki akses. Hubungi administrator.']);
        }

        // Support multiple roles: role:pelamar,penguji,kaprodi
        if (!in_array($user->role, $roles)) {
            return redirect()->route($user->role . '.dashboard');
        }

        return $next($request);
    }
}
