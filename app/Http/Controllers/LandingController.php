<?php

namespace App\Http\Controllers;

use App\Models\Proses;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingController extends Controller
{
    /**
     * Tampilkan halaman landing page dengan data periode yang aktif.
     */
    public function index(): View
    {
        // Ambil periode yang statusnya 'Buka'. Jika tidak ada, kembalikan null.
        $prosesAktif = Proses::where('status', 'Buka')->first();

        return view('landing', compact('prosesAktif'));
    }
}
