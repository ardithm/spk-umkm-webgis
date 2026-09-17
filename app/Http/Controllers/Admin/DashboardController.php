<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HasilPerhitungan;
use App\Models\Kriteria;
use App\Models\Pengajuan;
use App\Models\Proses;
use App\Models\Umkm;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller: Admin\DashboardController
 *
 * Dasbor utama Admin (Petugas Dinas Koperasi & UMKM Banjarmasin).
 * Menampilkan ringkasan statistik komprehensif, pusat kontrol seluruh fitur SPK,
 * sebaran koordinat WebGIS, audit kriteria, dan pengajuan terbaru.
 *
 * Sesuai PRD Section 3 (Core Features) & Section 4 (User Flow Admin).
 */
class DashboardController extends Controller
{
    public function index(): View
    {
        $proses_aktif = Proses::withCount('hasilPerhitungan')->latest('tanggal_proses')->first();

        $stats = [
            'total_umkm'           => Umkm::count(),
            'umkm_berkoordinat'    => Umkm::whereNotNull('latitude')->whereNotNull('longitude')->count(),
            'total_pengajuan'      => Pengajuan::count(),
            'menunggu_verifikasi'  => Pengajuan::where('status', 'menunggu')->count(),
            'terverifikasi'        => Pengajuan::where('status', 'terverifikasi')->count(),
            'proses_aktif'         => $proses_aktif,
            'total_diterima'       => HasilPerhitungan::where('status_seleksi', 'diterima')->count(),
            'total_cadangan'       => HasilPerhitungan::where('status_seleksi', 'cadangan')->count(),
            'total_tidak_diterima' => HasilPerhitungan::where('status_seleksi', 'tidak_diterima')->count(),
            'total_kriteria'       => Kriteria::count(),
            'kriteria_core'        => Kriteria::where('jenis_faktor', 'core')->count(),
            'kriteria_secondary'   => Kriteria::where('jenis_faktor', 'secondary')->count(),
        ];

        $pengajuan_terbaru = Pengajuan::with(['umkm', 'dokumen'])
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        $kriteria_list = Kriteria::orderBy('kode_kriteria')->get();

        $top_ranking = collect();
        if ($proses_aktif) {
            $top_ranking = HasilPerhitungan::where('id_proses', $proses_aktif->id_proses)
                ->with('pengajuan.umkm')
                ->orderBy('ranking')
                ->limit(5)
                ->get();
        }

        $umkm_map_data = Umkm::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get(['id_umkm', 'nama_umkm', 'nama_pemilik', 'alamat', 'latitude', 'longitude', 'no_telepon']);

        return view('admin.dashboard', compact(
            'stats',
            'pengajuan_terbaru',
            'kriteria_list',
            'top_ranking',
            'umkm_map_data'
        ));
    }
}
