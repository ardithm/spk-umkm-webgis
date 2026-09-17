<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware: ForceHttps
 *
 * Memaksa seluruh request menggunakan protokol HTTPS (SSL/TLS) dengan
 * mengeluarkan redirect 301 (Permanent) dari HTTP ke HTTPS.
 *
 * Sesuai PRD Section 2:
 * "Sistem wajib menggunakan protokol keamanan mumpuni, seperti HTTPS (SSL/TLS)."
 *
 * Middleware ini hanya aktif di environment production. Di environment
 * local / development, redirect HTTPS dilewati agar tidak mengganggu
 * workflow pengembangan di Laragon (localhost).
 */
class ForceHttps
{
    public function handle(Request $request, Closure $next): Response
    {
        // Aktifkan paksa redirect HTTPS hanya di production
        if (! app()->environment('local', 'testing') && ! $request->isSecure()) {
            return redirect()->secure($request->getRequestUri(), 301);
        }

        // Tambahkan header keamanan HTTP standar pada semua response
        $response = $next($request);

        return $this->addSecurityHeaders($response);
    }

    /**
     * Tambahkan HTTP Security Headers ke setiap response.
     * Melindungi dari: Clickjacking, MIME sniffing, XSS, dan eavesdropping.
     */
    private function addSecurityHeaders(Response $response): Response
    {
        $response->headers->set(
            'Strict-Transport-Security',
            'max-age=31536000; includeSubDomains; preload'
        );

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        return $response;
    }
}
