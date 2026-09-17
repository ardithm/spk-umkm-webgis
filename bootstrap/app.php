<?php

use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\EnsureUserIsUmkm;
use App\Http\Middleware\ForceHttps;
use App\Http\Middleware\LoginRateLimiter;
use App\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

/*
|--------------------------------------------------------------------------
| Bootstrap/app.php — SPK UMKM WebGIS Banjarmasin
|--------------------------------------------------------------------------
|
| Pendaftaran middleware ke kernel Laravel 11+.
| Menggunakan middleware aliases agar dapat dipanggil di routes/web.php
| dan di route group dengan string pendek yang bersih.
|
| Sesuai PRD Section 2 - Requirements Keamanan:
| - 'force.https'     → HTTPS + Security Headers
| - 'role.admin'      → Guard rute Admin
| - 'role.umkm'       → Guard rute UMKM
| - 'login.throttle'  → Brute Force Rate Limiting
| - 'guest'           → Redirect jika sudah login
|
*/

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // ── 1. Global Middleware ──────────────────────────────────────────
        // ForceHttps diaplikasikan di level Route Group pada web.php
        // untuk fleksibilitas (dapat dinonaktifkan di local dev).

        // ── 2. Middleware Alias (shorthand untuk routes) ──────────────────
        $middleware->alias([
            'role.admin'     => EnsureUserIsAdmin::class,
            'role.umkm'      => EnsureUserIsUmkm::class,
            'login.throttle' => LoginRateLimiter::class,
            'force.https'    => ForceHttps::class,
            'guest'          => RedirectIfAuthenticated::class,
        ]);

        // ── 3. Middleware Prioritas (urutan eksekusi) ─────────────────────
        // ForceHttps harus berjalan lebih awal dari middleware auth
        $middleware->priority([
            ForceHttps::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\Auth\Middleware\Authenticate::class,
            EnsureUserIsAdmin::class,
            EnsureUserIsUmkm::class,
        ]);

        // ── 4. Tambahan pada grup 'web' middleware ────────────────────────
        // CSRF token protection aktif secara default via grup 'web'
        // yang sudah mengandung VerifyCsrfToken middleware bawaan Laravel.
        // (PRD Section 2: "CSRF Token")
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handler 403 Forbidden (akses role tidak sah)
        $exceptions->renderable(function (
            \Illuminate\Auth\Access\AuthorizationException $e,
            $request
        ) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Akses ditolak.'], 403);
            }
            return redirect()->route('login')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman tersebut.');
        });

        // Handler 401 Unauthenticated
        $exceptions->renderable(function (
            \Illuminate\Auth\AuthenticationException $e,
            $request
        ) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Silakan login terlebih dahulu.'], 401);
            }
            return redirect()->route('login')->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
        });
    })
    ->create();
