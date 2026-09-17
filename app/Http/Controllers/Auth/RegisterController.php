<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Umkm;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Controller: RegisterController
 *
 * Menangani pendaftaran akun bagi Pelaku UMKM (Pemohon) secara mandiri.
 *
 * Sesuai PRD Section 2:
 * - Validasi NIK 16 digit numerik (unik).
 * - Cegah duplikasi Email & WhatsApp dalam satu gelombang.
 * - Password di-hash dengan Bcrypt (via Hash::make).
 *
 * Sesuai PRD Section 4 - User Flow UMKM Step 1:
 * "Pelaku UMKM membuka halaman registrasi, mengisi data diri,
 * dan sistem melakukan validasi NIK/Email."
 */
class RegisterController extends Controller
{
    /**
     * Tampilkan halaman form registrasi UMKM.
     * [GET] /register
     */
    public function showRegisterForm(): View
    {
        return view('auth.register');
    }

    /**
     * Proses pendaftaran akun UMKM baru.
     * [POST] /register
     *
     * Urutan proses:
     * 1. Validasi semua field termasuk NIK 16 digit.
     * 2. Cek duplikasi NIK, username, dan email/WA.
     * 3. Buat User + Profil Umkm dalam satu DB Transaction.
     * 4. Auto-login setelah registrasi berhasil.
     * 5. Redirect ke dashboard UMKM.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // ── Kredensial Akun ──────────────────────────────────────
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'username'     => ['required', 'string', 'max:50', 'unique:users,username', 'regex:/^[a-z0-9_]+$/'],

            // Password: min 8 karakter, wajib ada huruf & angka (PRD Section 2 - Bcrypt)
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],

            // ── Data Profil UMKM ─────────────────────────────────────
            'nama_umkm'   => ['required', 'string', 'max:150'],
            'nama_pemilik'=> ['required', 'string', 'max:100'],

            // NIK 16 digit numerik, unik di tabel umkm (PRD Section 2)
            'nik'         => ['required', 'digits:16', 'unique:umkm,nik'],

            'no_telepon'  => ['required', 'string', 'max:20', 'unique:umkm,no_telepon'],
            'alamat'      => ['required', 'string', 'max:500'],
        ], [
            'username.regex'     => 'Username hanya boleh mengandung huruf kecil, angka, dan underscore.',
            'nik.digits'         => 'NIK harus terdiri dari tepat 16 digit angka.',
            'nik.unique'         => 'NIK ini sudah terdaftar dalam sistem. Periksa kembali atau hubungi Admin.',
            'no_telepon.unique'  => 'Nomor telepon/WhatsApp ini sudah digunakan pada akun lain.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // Buat akun dalam satu DB Transaction untuk menjaga integritas data
        $user = DB::transaction(function () use ($validated) {
            // Step 1: Buat record User dengan password ter-hash Bcrypt
            $user = User::create([
                'nama_lengkap' => $validated['nama_lengkap'],
                'username'     => $validated['username'],
                'password'     => Hash::make($validated['password']), // ← Bcrypt hash
                'role'         => 'umkm', // Pendaftar mandiri selalu role UMKM
            ]);

            // Step 2: Buat profil UMKM yang terhubung dengan User
            Umkm::create([
                'id_user'      => $user->id_user,
                'nama_umkm'    => $validated['nama_umkm'],
                'nama_pemilik' => $validated['nama_pemilik'],
                'nik'          => $validated['nik'],
                'no_telepon'   => $validated['no_telepon'],
                'alamat'       => $validated['alamat'],
                // latitude & longitude diisi nanti di step profil (PRD Section 4, Step 3)
            ]);

            return $user;
        });

        // Auto-login setelah registrasi berhasil (PRD Section 4, Step 2)
        Auth::login($user);

        $request->session()->regenerate();

        return redirect()
            ->route('umkm.dashboard')
            ->with('success', 'Akun berhasil dibuat! Silakan lengkapi profil UMKM dan titik koordinat usaha Anda.');
    }
}
