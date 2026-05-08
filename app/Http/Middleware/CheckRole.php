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
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect('/');
        }

        // Kaprodi yang juga penguji bisa akses route penguji
        $allowedRoles = [$role];
        if ($role === 'penguji') {
            $allowedRoles[] = 'kaprodi';
        }

        if (! in_array($user->role, $allowedRoles)) {
            return redirect()->route($user->role . '.dashboard');
        }

        return $next($request);
    }
}
