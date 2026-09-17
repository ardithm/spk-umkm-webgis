<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HasilPerhitungan;
use App\Models\Proses;
use App\Services\ProfileMatchingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * SpkController — Thin Controller untuk Modul SPK Profile Matching
 *
 * Mengelola alur HTTP untuk Mesin SPK di sisi Admin, sesuai PRD Section 3
 * (Modul Sistem Pendukung Keputusan) dan PRD Section 4 (User Flow Admin, Step 4–5).
 *
 * Seluruh logika kalkulasi didelegasikan ke ProfileMatchingService agar controller
 * tetap ramping (thin controller — fat service) dan mudah diuji secara terpisah.
 *
 * Rute yang dilayani (prefix: /admin/spk):
 *  GET  /              → index()        : Daftar sesi proses & summary hasil
 *  POST /hitung/{id}   → hitungMassal() : Eksekusi kalkulasi Profile Matching
 *  GET  /hasil/{id}    → hasil()        : Detail penilaian per kriteria per kandidat
 *  GET  /ranking/{id}  → ranking()      : Tabel perankingan akhir
 *
 * @see ProfileMatchingService Mesin kalkulasi Profile Matching
 * @see PRD Section 3 — Modul Sistem Pendukung Keputusan
 */
class SpkController extends Controller
{
    public function __construct(
        private readonly ProfileMatchingService $spkService
    ) {}

    // =========================================================================
    // index() — Daftar Sesi Proses + Summary
    // =========================================================================

    /**
     * Tampilkan daftar semua sesi proses seleksi beserta ringkasan hasil kalkulasi.
     *
     * Mengambil data Proses beserta count hasil_perhitungan yang sudah dihitung,
     * agar Admin dapat melihat status setiap gelombang sebelum memutuskan re-run.
     *
     * PRD Section 4 Admin Step 4: "Admin masuk ke menu 'Proses Perhitungan'."
     */
    public function index(): View
    {
        $prosesList = Proses::withCount([
                'hasilPerhitungan',
                'hasilPerhitungan as total_diterima' => function ($q) {
                    $q->where('status_seleksi', HasilPerhitungan::STATUS_DITERIMA);
                },
                'hasilPerhitungan as total_cadangan' => function ($q) {
                    $q->where('status_seleksi', HasilPerhitungan::STATUS_CADANGAN);
                },
            ])
            ->orderByDesc('tanggal_proses')
            ->paginate(10);

        return view('admin.spk.index', compact('prosesList'));
    }

    // =========================================================================
    // hitungMassal() — Eksekusi Kalkulasi Profile Matching
    // =========================================================================

    /**
     * Eksekusi kalkulasi Profile Matching secara massal untuk satu sesi proses.
     *
     * Alur:
     *  1. Validasi bahwa Proses dengan id yang dimaksud ada.
     *  2. Delegasikan ke ProfileMatchingService::jalankanKalkulasi().
     *  3. Flash ringkasan sukses atau pesan error ke session.
     *  4. Redirect ke halaman ranking untuk menampilkan hasil.
     *
     * Mendukung re-run: jika proses pernah dihitung sebelumnya, data lama
     * akan dibersihkan otomatis sebelum kalkulasi baru dijalankan (oleh Service).
     *
     * PRD Section 3: "Kalkulasi Profile Matching dilakukan secara massal dan otomatis."
     * PRD Section 4 Admin Step 4: "sistem akan menarik seluruh UMKM tervalidasi
     *                              lalu menjalankan algoritma Profile Matching secara otomatis."
     *
     * @param  int $id_proses Primary key sesi proses yang akan dihitung.
     */
    public function hitungMassal(int $id_proses): RedirectResponse
    {
        $proses = Proses::findOrFail($id_proses);

        try {
            $ringkasan = $this->spkService->jalankanKalkulasi($proses);

            session()->flash('success',
                "✅ Kalkulasi Profile Matching untuk periode <strong>{$proses->periode}</strong> berhasil dijalankan. " .
                "<br>Total dihitung: <strong>{$ringkasan['total_dihitung']}</strong> kandidat | " .
                "Diterima: <strong>{$ringkasan['total_diterima']}</strong> | " .
                "Cadangan: <strong>{$ringkasan['total_cadangan']}</strong> | " .
                "Tidak Diterima: <strong>{$ringkasan['total_tidak_diterima']}</strong>."
            );

            return redirect()->route('admin.spk.ranking', $id_proses);

        } catch (\RuntimeException $e) {
            // Error yang sudah diantisipasi (contoh: tidak ada kriteria, tidak ada pengajuan)
            session()->flash('error', "⚠️ Kalkulasi gagal: {$e->getMessage()}");
            return redirect()->route('admin.spk.index');

        } catch (\Throwable $e) {
            // Error tidak terduga — log detail untuk debugging
            \Log::error("[SPK] Kalkulasi gagal untuk Proses #{$id_proses}.", [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            session()->flash('error',
                '❌ Terjadi kesalahan sistem saat menjalankan kalkulasi. ' .
                'Silakan periksa log aplikasi atau hubungi developer.'
            );
            return redirect()->route('admin.spk.index');
        }
    }

    // =========================================================================
    // hasil() — Detail Penilaian Per Kriteria Per Kandidat
    // =========================================================================

    /**
     * Tampilkan detail penilaian (breakdown per kriteria) untuk semua kandidat
     * dalam satu sesi proses.
     *
     * Data yang ditampilkan:
     *  - Per pengajuan: nilai_aktual, skor konversi, GAP, bobot_gap per kriteria.
     *  - NCF, NSF, dan nilai_akhir dari hasil_perhitungan.
     *
     * Berguna bagi Admin untuk mengaudit transparansi perhitungan sebelum
     * mencetak laporan final kelulusan.
     *
     * PRD Section 4 Admin Step 5: "Admin dapat meninjau rekomendasi perankingan
     *                              dari total nilai akhir kelayakan."
     *
     * @param  int $id_proses Primary key sesi proses.
     */
    public function hasil(int $id_proses): View
    {
        $proses = Proses::findOrFail($id_proses);

        // Ambil hasil beserta detail penilaian per kriteria (untuk breakdown tabel)
        $hasilList = HasilPerhitungan::where('id_proses', $id_proses)
            ->with([
                'pengajuan.umkm',
                'pengajuan.detailPenilaian.kriteria',
            ])
            ->orderBy('ranking')
            ->get();

        // Ambil daftar kriteria untuk header kolom tabel
        $kriteria = \App\Models\Kriteria::orderBy('kode_kriteria')->get();

        return view('admin.spk.hasil', compact('proses', 'hasilList', 'kriteria'));
    }

    // =========================================================================
    // ranking() — Tabel Perankingan Akhir
    // =========================================================================

    /**
     * Tampilkan tabel perankingan akhir hasil seleksi SPK Profile Matching.
     *
     * Menampilkan daftar kandidat diurut berdasarkan ranking (1 = terbaik),
     * dengan kolom: ranking, nama UMKM, nilai_akhir, NCF, NSF, dan status_seleksi.
     * Admin dapat memfilter berdasarkan status seleksi (diterima/cadangan/tidak_diterima).
     *
     * PRD Section 4 Admin Step 5: "Admin dapat meninjau rekomendasi perankingan."
     *
     * @param  Request $request   HTTP request (opsional: ?filter=diterima|cadangan|tidak_diterima).
     * @param  int     $id_proses Primary key sesi proses.
     */
    public function ranking(Request $request, int $id_proses): View
    {
        $proses = Proses::findOrFail($id_proses);

        $query = HasilPerhitungan::where('id_proses', $id_proses)
            ->with('pengajuan.umkm')
            ->orderBy('ranking');

        // Filter opsional berdasarkan status seleksi
        $filterStatus = $request->query('filter');
        if (in_array($filterStatus, [
            HasilPerhitungan::STATUS_DITERIMA,
            HasilPerhitungan::STATUS_CADANGAN,
            HasilPerhitungan::STATUS_TIDAK_DITERIMA,
        ])) {
            $query->where('status_seleksi', $filterStatus);
        }

        $hasilList = $query->paginate(25)->withQueryString();

        // Hitung ringkasan status untuk badge/card summary di view
        $summary = HasilPerhitungan::where('id_proses', $id_proses)
            ->selectRaw('status_seleksi, COUNT(*) as total')
            ->groupBy('status_seleksi')
            ->pluck('total', 'status_seleksi');

        return view('admin.spk.ranking', compact('proses', 'hasilList', 'summary', 'filterStatus'));
    }
}
