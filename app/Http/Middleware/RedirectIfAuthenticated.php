<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware: RedirectIfAuthenticated
 *
 * Mencegah pengguna yang sudah login mengakses halaman guest-only
 * (seperti login, register). Redirect ke dashboard sesuai role masing-masing.
 *
 * Sesuai PRD Section 2: Dua peran pengguna → dua destinasi redirect berbeda.
 */
class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                // Arahkan ke dashboard sesuai role
                return redirect()->route(
                    $user->role === 'admin' ? 'admin.dashboard' : 'umkm.dashboard'
                );
            }
        }

        return $next($request);
    }
}
