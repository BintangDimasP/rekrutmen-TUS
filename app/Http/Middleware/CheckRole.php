<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * Setiap role memiliki akun user terpisah, jadi tidak perlu exception khusus.
     * Penguji login dengan email @penguji, kaprodi login dengan email @kaprodi.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect('/');
        }

        if ($user->role !== $role) {
            return redirect()->route($user->role . '.dashboard');
        }

        return $next($request);
    }
}
