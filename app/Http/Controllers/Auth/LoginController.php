<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Controller: LoginController
 *
 * Menangani alur autentikasi login dan logout untuk KEDUA role (admin & umkm).
 * Menggunakan satu endpoint login terpadu dengan pemisahan redirect berbasis role.
 *
 * Sesuai PRD Section 2 & 4:
 * - Kredensial dilindungi hashing Bcrypt.
 * - Rate Limiting Brute Force via Middleware LoginRateLimiter.
 * - CSRF Token aktif via middleware web (otomatis dari grup rute).
 */
class LoginController extends Controller
{
    /**
     * Tampilkan halaman form login.
     * [GET] /login
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Proses request login dari form.
     * [POST] /login
     *
     * Urutan validasi & proses:
     * 1. Validasi format field (required, max length).
     * 2. Autentikasi via Auth::attempt() — Laravel membandingkan password
     *    input dengan hash Bcrypt di database secara aman.
     * 3. Regenerate session ID setelah login untuk mencegah Session Fixation.
     * 4. Redirect ke dashboard sesuai role pengguna.
     */
    public function login(Request $request): RedirectResponse
    {
        // Validasi input form
        $credentials = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:8'],
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 8 karakter.',
        ]);

        // Coba autentikasi: Laravel akan otomatis memverifikasi Hash::check()
        // terhadap password Bcrypt yang tersimpan di kolom 'password'
        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            // Login gagal: LoginRateLimiter middleware akan mencatat percobaan ini
            throw ValidationException::withMessages([
                'username' => 'Username atau password yang Anda masukkan tidak cocok dengan data kami.',
            ]);
        }

        // Regenerasi Session ID untuk mencegah Session Fixation Attack
        $request->session()->regenerate();

        $user = Auth::user();

        // Redirect berdasarkan role (sesuai PRD Section 2 - Dua Peran Utama)
        return match ($user->role) {
            'admin' => redirect()
                ->intended(route('admin.dashboard'))
                ->with('success', "Selamat datang kembali, {$user->nama_lengkap}!"),

            'umkm' => redirect()
                ->intended(route('umkm.dashboard'))
                ->with('success', "Selamat datang, {$user->nama_lengkap}!"),

            default => $this->handleUnknownRole($request),
        };
    }

    /**
     * Proses Logout pengguna.
     * [POST] /logout
     */
    public function logout(Request $request): RedirectResponse
    {
        $nama = Auth::user()?->nama_lengkap ?? 'Pengguna';

        Auth::logout();

        // Invalidasi session dan regenerasi CSRF token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', "Sampai jumpa, {$nama}! Anda telah keluar dari sistem.");
    }

    /**
     * Tangani role yang tidak dikenal (failsafe).
     */
    private function handleUnknownRole(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('error', 'Role akun tidak dikenali. Hubungi Administrator Sistem.');
    }
}
