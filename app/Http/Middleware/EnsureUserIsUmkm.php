<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware: EnsureUserIsUmkm
 *
 * Menjaga seluruh rute di grup /umkm agar hanya bisa diakses oleh
 * pengguna yang telah login dengan role 'umkm' (Pelaku Usaha / Pemohon).
 *
 * Sesuai PRD Section 2: "Sistem harus mengakomodasi dua peran (role) pengguna."
 * Sesuai PRD Section 4: "User Flow Pelaku UMKM (Pemohon)."
 */
class EnsureUserIsUmkm
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Cek: Pengguna sudah login?
        if (! $request->user()) {
            return redirect()
                ->route('login')
                ->with('error', 'Anda harus login untuk mengakses portal UMKM.');
        }

        // 2. Cek: Pengguna memiliki role 'umkm'?
        if ($request->user()->role !== 'umkm') {
            // Jika admin mencoba masuk ke area UMKM, arahkan ke dasbor admin
            if ($request->user()->role === 'admin') {
                return redirect()
                    ->route('admin.dashboard')
                    ->with('info', 'Anda login sebagai Admin. Gunakan portal Admin.');
            }

            // Role tidak dikenal: logout paksa
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->with('error', 'Sesi tidak valid. Silakan login kembali.');
        }

        return $next($request);
    }
}
