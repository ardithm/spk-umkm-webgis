<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware: LoginRateLimiter
 *
 * Melindungi endpoint login dari serangan Brute Force dengan membatasi
 * jumlah percobaan login per IP Address + Username.
 *
 * Sesuai PRD Section 2:
 * "Sistem harus melindungi data kredensial dengan skema hashing Bcrypt dan
 * dilengkapi Rate Limiting guna menghindari peretasan Brute Force."
 */
class LoginRateLimiter
{
    /**
     * Maksimum percobaan login yang diperbolehkan dalam satu window waktu.
     */
    private const MAX_ATTEMPTS  = 5;

    /**
     * Durasi cooldown dalam detik (10 menit lockout setelah melewati batas).
     */
    private const DECAY_SECONDS = 600;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($key);

            // Kembalikan response 429 jika request adalah AJAX/API
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Terlalu banyak percobaan login.',
                    'retry_after' => $seconds,
                ], Response::HTTP_TOO_MANY_REQUESTS);
            }

            return redirect()
                ->route('login')
                ->with('error', sprintf(
                    'Terlalu banyak percobaan login. Silakan coba lagi dalam %d menit %d detik.',
                    intdiv($seconds, 60),
                    $seconds % 60
                ));
        }

        $response = $next($request);

        // Catat percobaan gagal (hanya jika status redirect ke login kembali)
        if ($this->isFailedLogin($request, $response)) {
            RateLimiter::hit($key, self::DECAY_SECONDS);
        } else {
            // Login berhasil: reset counter untuk pasangan IP + username ini
            RateLimiter::clear($key);
        }

        return $response;
    }

    /**
     * Buat kunci throttle unik berdasarkan username + IP Address pengguna.
     * Menggabungkan keduanya agar pemblokiran tepat sasaran.
     */
    private function throttleKey(Request $request): string
    {
        $username = strtolower((string) $request->input('username', ''));
        $ip       = $request->ip() ?? '0.0.0.0';

        return 'login:' . sha1($username . '|' . $ip);
    }

    /**
     * Deteksi apakah request ini merupakan percobaan login yang GAGAL.
     * Login gagal biasanya ditandai dengan redirect kembali ke route 'login'.
     */
    private function isFailedLogin(Request $request, Response $response): bool
    {
        if (! $response->isRedirect()) {
            return false;
        }

        $location = $response->headers->get('Location', '');

        return str_contains($location, route('login', [], false));
    }
}
