<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware: EnsureUserIsAdmin
 *
 * Menjaga seluruh rute di grup /admin agar hanya bisa diakses oleh
 * pengguna yang telah login dengan role 'admin' (Petugas Dinas Koperasi).
 *
 * Sesuai PRD Section 2: "Sistem harus mengakomodasi dua peran (role) pengguna."
 * Sesuai PRD Section 3: "Modul Manajemen Periode, Verifikasi Dokumen, Proses Perhitungan SPK."
 */
class EnsureUserIsAdmin
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
                ->with('error', 'Anda harus login untuk mengakses halaman ini.');
        }

        // 2. Cek: Pengguna memiliki role 'admin'?
        if ($request->user()->role !== 'admin') {
            // Jika login sebagai UMKM, arahkan ke dasbor UMKM
            if ($request->user()->role === 'umkm') {
                return redirect()
                    ->route('umkm.dashboard')
                    ->with('warning', 'Akses ditolak. Halaman Admin hanya untuk Petugas Dinas.');
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
