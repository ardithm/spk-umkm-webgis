<?php

namespace App\Http\Controllers\Umkm;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Controller: Umkm\DashboardController
 *
 * Dasbor utama Pelaku UMKM setelah login.
 * Menampilkan status pengajuan terkini, panduan tahapan seleksi,
 * dan notifikasi dari Admin Dinas.
 *
 * Sesuai PRD Section 4 - User Flow Pemohon, Step 2 & 7:
 * "Pemohon menerima notifikasi dan memantau status persetujuan via sistem."
 */
class DashboardController extends Controller
{
    public function index(): View
    {
        $user   = Auth::user();
        $umkm   = $user->umkm;
        $pengajuan = $umkm?->pengajuanAktif;
        $hasil  = $pengajuan?->hasilPerhitungan;

        return view('umkm.dashboard', compact('user', 'umkm', 'pengajuan', 'hasil'));
    }
}
