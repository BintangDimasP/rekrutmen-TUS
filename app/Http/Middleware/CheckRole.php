<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Cek role aktif user untuk akses area.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
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

        if ($user->role !== $role) {
            return redirect()->route($user->role . '.dashboard');
        }

        return $next($request);
    }
}
