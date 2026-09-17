<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Umkm;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\EnsureUserIsUmkm;
use App\Http\Middleware\ForceHttps;
use App\Http\Middleware\LoginRateLimiter;
use App\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — SPK UMKM WebGIS Banjarmasin
|--------------------------------------------------------------------------
|
| Seluruh rute dikelompokkan menjadi 3 grup utama:
|
| 1. [PUBLIC]  Halaman guest (landing page, login, register)
| 2. [ADMIN]   Grup rute /admin — dilindungi Middleware EnsureUserIsAdmin
| 3. [UMKM]    Grup rute /umkm — dilindungi Middleware EnsureUserIsUmkm
|
| Keamanan yang diterapkan sesuai PRD Section 2:
| - ForceHttps     → Redirect HTTP → HTTPS + Security Headers
| - CSRF Token     → Aktif otomatis via grup middleware 'web'
| - LoginRateLimiter → Brute Force protection di endpoint /login
| - Bcrypt Password  → Hash::make() di RegisterController
|--------------------------------------------------------------------------
*/

// ═══════════════════════════════════════════════════════════════════════════
// GLOBAL: Aktifkan ForceHttps di seluruh rute (PRD Section 2 - HTTPS/SSL/TLS)
// ═══════════════════════════════════════════════════════════════════════════
Route::middleware([ForceHttps::class])->group(function () {

    // ── Landing Page (Terbuka untuk Umum & Terintegrasi Blade) ────────
    Route::get('/', [\App\Http\Controllers\LandingController::class, 'index'])->name('home');

    // ───────────────────────────────────────────────────────────────────────
    // GRUP 1: RUTE GUEST (Login & Register — dialihkan jika sudah login)
    // ───────────────────────────────────────────────────────────────────────
    Route::middleware([RedirectIfAuthenticated::class])->group(function () {

        // ── Login ─────────────────────────────────────────────────────────
        Route::get('/login', [LoginController::class, 'showLoginForm'])
            ->name('login');

        Route::post('/login', [LoginController::class, 'login'])
            ->middleware(LoginRateLimiter::class) // ← Brute Force Protection
            ->name('login.post');

        // ── Registrasi UMKM Mandiri ───────────────────────────────────────
        // Hanya tersedia untuk Pemohon UMKM (bukan Admin).
        // Admin dibuat oleh Super Admin via seeder.
        Route::get('/register', [RegisterController::class, 'showRegisterForm'])
            ->name('register');

        Route::post('/register', [RegisterController::class, 'register'])
            ->name('register.post');
    });

    // ── Logout (tidak perlu guest-only, tapi perlu auth) ──────────────────
    Route::post('/logout', [LoginController::class, 'logout'])
        ->middleware('auth')
        ->name('logout');


    // ═══════════════════════════════════════════════════════════════════════
    // GRUP 2: RUTE ADMIN — Dilindungi EnsureUserIsAdmin
    // Semua rute dalam grup ini mengharuskan login + role = 'admin'.
    // Sesuai PRD Section 3 & 4 (User Flow Admin).
    // ═══════════════════════════════════════════════════════════════════════
    Route::prefix('admin')
        ->name('admin.')
        ->middleware(['auth', EnsureUserIsAdmin::class])
        ->group(function () {

            // ── Dasbor Utama Admin ─────────────────────────────────────
            Route::get('/dashboard', [Admin\DashboardController::class, 'index'])
                ->name('dashboard');

            // ── Manajemen Periode & Gelombang ──────────────────────────
            // PRD Section 3: "Admin dapat membuka/menutup jadwal batch & kuota."
            Route::prefix('proses')->name('proses.')->group(function () {
                Route::get('/',        [Admin\ProsesController::class, 'index'])->name('index');
                Route::get('/create',  [Admin\ProsesController::class, 'create'])->name('create');
                Route::post('/',       [Admin\ProsesController::class, 'store'])->name('store');
                Route::get('/{id}',    [Admin\ProsesController::class, 'show'])->name('show');
                Route::get('/{id}/edit', [Admin\ProsesController::class, 'edit'])->name('edit');
                Route::put('/{id}',    [Admin\ProsesController::class, 'update'])->name('update');
                Route::delete('/{id}', [Admin\ProsesController::class, 'destroy'])->name('destroy');
            });

            // ── Manajemen Data UMKM Pendaftar ──────────────────────────
            Route::prefix('umkm')->name('umkm.')->group(function () {
                Route::get('/',     [Admin\UmkmController::class, 'index'])->name('index');
                Route::get('/{id}', [Admin\UmkmController::class, 'show'])->name('show');
            });

            // ── Manajemen Pengajuan ────────────────────────────────────
            Route::prefix('pengajuan')->name('pengajuan.')->group(function () {
                Route::get('/',     [Admin\PengajuanController::class, 'index'])->name('index');
                Route::get('/{id}', [Admin\PengajuanController::class, 'show'])->name('show');
            });

            // ── Verifikasi Dokumen Berkas ──────────────────────────────
            // PRD Section 3: "Antarmuka Admin memverifikasi dokumen (Setuju/Tolak)."
            Route::prefix('dokumen')->name('dokumen.')->group(function () {
                Route::get('/',                              [Admin\DokumenController::class, 'index'])->name('index');
                Route::get('/{id_pengajuan}',               [Admin\DokumenController::class, 'show'])->name('show');
                Route::patch('/{id}/setujui',               [Admin\DokumenController::class, 'setujui'])->name('setujui');
                Route::patch('/{id}/tolak',                 [Admin\DokumenController::class, 'tolak'])->name('tolak');
                Route::patch('/pengajuan/{id_pengajuan}/setujui-semua', [Admin\DokumenController::class, 'setujuiSemua'])->name('setujuiSemua');

                // Download file dokumen privat (hanya Admin) — PRD Section 2
                Route::get('/download/{id_dokumen}',        [Admin\DokumenController::class, 'download'])->name('download');
            });

            // ── Mesin SPK Profile Matching ─────────────────────────────
            // PRD Section 3: "Kalkulasi Profile Matching dilakukan secara massal."
            Route::prefix('spk')->name('spk.')->group(function () {
                Route::get('/',              [Admin\SpkController::class, 'index'])->name('index');
                Route::post('/hitung/{id_proses}', [Admin\SpkController::class, 'hitungMassal'])->name('hitung');
                Route::get('/hasil/{id_proses}',   [Admin\SpkController::class, 'hasil'])->name('hasil');
                Route::get('/ranking/{id_proses}', [Admin\SpkController::class, 'ranking'])->name('ranking');
            });

            // ── Manajemen Kriteria & Bobot SPK ────────────────────────
            // PRD Section 3: "Admin dapat memodifikasi bobot nilai target."
            Route::prefix('kriteria')->name('kriteria.')->group(function () {
                Route::get('/',         [Admin\KriteriaController::class, 'index'])->name('index');
                Route::get('/create',   [Admin\KriteriaController::class, 'create'])->name('create');
                Route::post('/',        [Admin\KriteriaController::class, 'store'])->name('store');
                Route::get('/{id}/edit',[Admin\KriteriaController::class, 'edit'])->name('edit');
                Route::put('/{id}',     [Admin\KriteriaController::class, 'update'])->name('update');
                Route::delete('/{id}',  [Admin\KriteriaController::class, 'destroy'])->name('destroy');
            });

            // ── Manajemen Konversi Nilai & Skor Kriteria ─────────────
            // PRD Section 3 & 6: "Tabel pemetaan jarak GAP / nilai menjadi skor kualitatif."
            Route::prefix('konversi')->name('konversi.')->group(function () {
                Route::get('/',         [Admin\KonversiController::class, 'index'])->name('index');
                Route::get('/create',   [Admin\KonversiController::class, 'create'])->name('create');
                Route::post('/',        [Admin\KonversiController::class, 'store'])->name('store');
                Route::get('/{id}/edit',[Admin\KonversiController::class, 'edit'])->name('edit');
                Route::put('/{id}',     [Admin\KonversiController::class, 'update'])->name('update');
                Route::delete('/{id}',  [Admin\KonversiController::class, 'destroy'])->name('destroy');
            });

            // ── Dasbor WebGIS & Rute Survei Lapangan ──────────────────
            // PRD Section 3: "Dasbor peta marker sebaran UMKM & mesin OSRM routing."
            Route::prefix('webgis')->name('webgis.')->group(function () {
                Route::get('/',        [Admin\WebgisController::class, 'index'])->name('index');
                Route::get('/data',    [Admin\WebgisController::class, 'data'])->name('data');   // JSON API peta
                Route::post('/rute',   [Admin\WebgisController::class, 'rute'])->name('rute');   // Kalkulasi OSRM
            });

            // ── Pelaporan & Ekspor ─────────────────────────────────────
            // PRD Section 3: "Admin dapat mencetak & mengekspor rekapitulasi ke PDF/Excel."
            Route::prefix('laporan')->name('laporan.')->group(function () {
                Route::get('/',              [Admin\LaporanController::class, 'index'])->name('index');
                Route::get('/pdf/{id}',      [Admin\LaporanController::class, 'exportPdf'])->name('pdf');
                Route::get('/excel/{id}',    [Admin\LaporanController::class, 'exportExcel'])->name('excel');
            });
            // ── Manajemen Data Pengguna (CRUD) ──────────────────────────
            // PRD Section 3: "Admin mengelola pengguna sistem (Admin & UMKM)."
            Route::prefix('pengguna')->name('pengguna.')->group(function () {
                Route::get('/',             [Admin\UserController::class, 'index'])->name('index');
                Route::get('/create',       [Admin\UserController::class, 'create'])->name('create');
                Route::post('/',            [Admin\UserController::class, 'store'])->name('store');
                Route::get('/{id}/edit',    [Admin\UserController::class, 'edit'])->name('edit');
                Route::put('/{id}',         [Admin\UserController::class, 'update'])->name('update');
                Route::delete('/{id}',      [Admin\UserController::class, 'destroy'])->name('destroy');
            });

            // ── Manajemen Akun Admin ───────────────────────────────────
            Route::prefix('akun')->name('akun.')->group(function () {
                Route::get('/',             [Admin\AkunController::class, 'index'])->name('index');
                Route::get('/profile',      [Admin\AkunController::class, 'profile'])->name('profile');
                Route::put('/profile',      [Admin\AkunController::class, 'updateProfile'])->name('profile.update');
                Route::put('/password',     [Admin\AkunController::class, 'updatePassword'])->name('password.update');
            });
        }); // ← End Admin Group


    // ═══════════════════════════════════════════════════════════════════════
    // GRUP 3: RUTE UMKM (Pemohon) — Dilindungi EnsureUserIsUmkm
    // Semua rute dalam grup ini mengharuskan login + role = 'umkm'.
    // Sesuai PRD Section 4 - User Flow Pelaku UMKM (Step 1–7).
    // ═══════════════════════════════════════════════════════════════════════
    Route::prefix('umkm')
        ->name('umkm.')
        ->middleware(['auth', EnsureUserIsUmkm::class])
        ->group(function () {

            // ── Dasbor UMKM ───────────────────────────────────────────
            // Step 2: "Pelaku UMKM login dan masuk ke dasbor akun."
            Route::get('/dashboard', [Umkm\DashboardController::class, 'index'])
                ->name('dashboard');

            // ── Profil & Titik Koordinat UMKM ─────────────────────────
            // Step 3: "Pemohon melengkapi profil dan menentukan titik koordinat."
            Route::prefix('profil')->name('profil.')->group(function () {
                Route::get('/',    [Umkm\ProfilController::class, 'show'])->name('show');
                Route::get('/edit',[Umkm\ProfilController::class, 'edit'])->name('edit');
                Route::put('/',    [Umkm\ProfilController::class, 'update'])->name('update');
            });

            // ── Formulir Pengajuan Bantuan Modal ──────────────────────
            // Step 4: "Pemohon menginput data usaha (Omzet, Aset, Tenaga Kerja, dll)."
            // Step 6: "Pemohon menekan konfirmasi pengajuan (status → Menunggu Verifikasi)."
            Route::prefix('pengajuan')->name('pengajuan.')->group(function () {
                Route::get('/',              [Umkm\PengajuanController::class, 'index'])->name('index');
                Route::get('/create',        [Umkm\PengajuanController::class, 'create'])->name('create');
                Route::post('/',             [Umkm\PengajuanController::class, 'store'])->name('store');
                Route::get('/{id}',          [Umkm\PengajuanController::class, 'show'])->name('show');
                Route::get('/{id}/edit',     [Umkm\PengajuanController::class, 'edit'])->name('edit');
                Route::put('/{id}',          [Umkm\PengajuanController::class, 'update'])->name('update');
                Route::delete('/{id}',       [Umkm\PengajuanController::class, 'destroy'])->name('destroy');

                // Konfirmasi submit awal pengajuan (status: draft -> menunggu)
                Route::post('/{id}/submit',  [Umkm\PengajuanController::class, 'submit'])->name('submit');

                // Kirim ulang berkas perbaikan (status: revisi -> menunggu)
                Route::post('/{id}/resubmit',[Umkm\PengajuanController::class, 'resubmit'])->name('resubmit');

                // Pratinjau & Upload Ulang Dokumen Spesifik
                Route::get('/{id}/dokumen/{id_dokumen}/preview',  [Umkm\PengajuanController::class, 'previewDokumen'])->name('dokumen.preview');
                Route::post('/{id}/dokumen/{id_dokumen}/reupload', [Umkm\PengajuanController::class, 'reuploadDokumen'])->name('dokumen.reupload');
            });

            // ── Unggah & Kelola Dokumen Berkas ────────────────────────
            // Step 5: "Pemohon mengunggah semua dokumen persyaratan fisik."
            Route::prefix('dokumen')->name('dokumen.')->group(function () {
                Route::get('/{id_pengajuan}',        [Umkm\DokumenController::class, 'index'])->name('index');
                Route::post('/{id_pengajuan}',       [Umkm\DokumenController::class, 'upload'])->name('upload');
                Route::delete('/{id_dokumen}',       [Umkm\DokumenController::class, 'destroy'])->name('destroy');
            });

            // ── Status & Hasil Seleksi SPK ─────────────────────────────
            // Step 7: "Pemohon memantau status persetujuan berkas atau kelulusan peringkat."
            Route::get('/status',          [Umkm\StatusController::class, 'index'])->name('status');
            Route::get('/hasil/{id_proses}',[Umkm\StatusController::class, 'hasil'])->name('hasil');

            // ── Akun & Profil Pelaku UMKM ───────────────────────────
            Route::prefix('akun')->name('akun.')->group(function () {
                Route::get('/',          [Umkm\AkunController::class, 'edit'])->name('show');
                Route::get('/edit',      [Umkm\AkunController::class, 'edit'])->name('edit');
                Route::put('/profil',    [Umkm\AkunController::class, 'updateProfil'])->name('profil.update');
                Route::put('/password',  [Umkm\AkunController::class, 'updatePassword'])->name('password.update');
                Route::post('/avatar',   [Umkm\AkunController::class, 'updateAvatar'])->name('avatar.update');
                Route::delete('/avatar', [Umkm\AkunController::class, 'deleteAvatar'])->name('avatar.delete');
            });
        }); // ← End UMKM Group

}); // ← End ForceHttps Global Group
