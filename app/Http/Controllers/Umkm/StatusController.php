<?php

namespace App\Http\Controllers\Umkm;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class StatusController extends Controller
{
    /**
     * Alihkan pemohon ke ringkasan status pengajuan aktif atau riwayat pengajuan.
     */
    public function index(): RedirectResponse
    {
        $user = Auth::user();
        $umkm = $user->umkm;
        $pengajuan = $umkm?->pengajuanAktif;

        if ($pengajuan) {
            return redirect()->route('umkm.pengajuan.show', $pengajuan->id_pengajuan);
        }

        return redirect()->route('umkm.pengajuan.index');
    }

    /**
     * Tampilkan hasil seleksi pengajuan pada proses tertentu.
     */
    public function hasil($id_proses): RedirectResponse
    {
        return redirect()->route('umkm.dashboard');
    }
}
