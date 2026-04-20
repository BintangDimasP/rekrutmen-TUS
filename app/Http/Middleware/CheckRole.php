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
        if (! $request->user() || $request->user()->role !== $role) {
            // Jika role berbeda, langsung alihkan ke dashboard utamanya sendiri
            if ($request->user()) {
                return redirect()->route($request->user()->role . '.dashboard');
            }
            
            return redirect('/');
        }

        return $next($request);
    }
}
