<?php

namespace App\Http\Controllers\Admin;

use App\Exports\RekapitulasiExport;
use App\Http\Controllers\Controller;
use App\Models\HasilPerhitungan;
use App\Models\Proses;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

/**
 * LaporanController — Cetak & Ekspor Rekapitulasi Hasil Seleksi SPK UMKM
 *
 * Mengelola antarmuka filter pelaporan serta ekspor dokumen:
 * 1. PDF Formal A4 Landscape (Kop Kedinasan, Tabel Nilai SPK & Rank, Titimangsa, TTD Kepala Dinas)
 * 2. Excel Spreadsheet .xlsx (Auto-sized, Text-bound NIK/Phone, Header Styling, Formula-friendly)
 *
 * Sesuai PRD Section 3 (Modul Komunikasi & Pelaporan) & Pedoman Desain.
 */
class LaporanController extends Controller
{
    /**
     * Tampilkan halaman filter laporan & live data preview
     */
    public function index(Request $request)
    {
        // 1. Ambil seluruh periode gelombang untuk pilihan filter
        $prosesList = Proses::orderByDesc('tanggal_proses')->get();

        // 2. Tentukan periode terpilih (default: periode pertama atau parameter request)
        $selectedProsesId = $request->query('id_proses', $prosesList->first()?->id_proses);
        $proses = $selectedProsesId ? Proses::find($selectedProsesId) : null;

        // 3. Tentukan filter status kelayakan
        $status = $request->query('status', 'semua');

        $hasilList = collect();
        $summary = [
            'total' => 0,
            'diterima' => 0,
            'cadangan' => 0,
            'tidak_diterima' => 0,
        ];

        if ($proses) {
            $query = HasilPerhitungan::where('id_proses', $proses->id_proses)
                ->with(['pengajuan.umkm'])
                ->orderBy('ranking');

            if ($status && $status !== 'semua' && in_array($status, [
                HasilPerhitungan::STATUS_DITERIMA,
                HasilPerhitungan::STATUS_CADANGAN,
                HasilPerhitungan::STATUS_TIDAK_DITERIMA,
            ])) {
                $query->where('status_seleksi', $status);
            }

            $hasilList = $query->paginate(20)->withQueryString();

            // Hitung ringkasan statistik
            $summary['total'] = HasilPerhitungan::where('id_proses', $proses->id_proses)->count();
            $summary['diterima'] = HasilPerhitungan::where('id_proses', $proses->id_proses)
                ->where('status_seleksi', HasilPerhitungan::STATUS_DITERIMA)
                ->count();
            $summary['cadangan'] = HasilPerhitungan::where('id_proses', $proses->id_proses)
                ->where('status_seleksi', HasilPerhitungan::STATUS_CADANGAN)
                ->count();
            $summary['tidak_diterima'] = HasilPerhitungan::where('id_proses', $proses->id_proses)
                ->where('status_seleksi', HasilPerhitungan::STATUS_TIDAK_DITERIMA)
                ->count();
        }

        return view('admin.laporan.index', compact(
            'prosesList',
            'proses',
            'selectedProsesId',
            'status',
            'hasilList',
            'summary'
        ));
    }

    /**
     * Ekspor Dokumen Resmi format PDF (A4 Landscape)
     */
    public function exportPdf(Request $request, $id)
    {
        $proses = Proses::findOrFail($id);
        $status = $request->query('status', 'semua');

        $query = HasilPerhitungan::where('id_proses', $proses->id_proses)
            ->with(['pengajuan.umkm'])
            ->orderBy('ranking');

        if ($status && $status !== 'semua' && in_array($status, [
            HasilPerhitungan::STATUS_DITERIMA,
            HasilPerhitungan::STATUS_CADANGAN,
            HasilPerhitungan::STATUS_TIDAK_DITERIMA,
        ])) {
            $query->where('status_seleksi', $status);
        }

        // Ambil seluruh data hasil (tanpa pagination) untuk dokumen cetak lengkap
        $hasilList = $query->get();

        // Tanggal pengesahan format Indonesia (contoh: 8 September 2026)
        Carbon::setLocale('id');
        $tanggalPengesahan = Carbon::now()->translatedFormat('d F Y');

        // Parameter judul & status
        $statusLabel = match ($status) {
            HasilPerhitungan::STATUS_DITERIMA => 'HANYA PENERIMA LOLOS (DISETUJUI)',
            HasilPerhitungan::STATUS_CADANGAN => 'DAFTAR CADANGAN (LUAR KUOTA)',
            HasilPerhitungan::STATUS_TIDAK_DITERIMA => 'TIDAK MEMENUHI SYARAT (DI BAWAH PASSING GRADE)',
            default => 'SEMUA PENDAFTAR DIEVALUASI',
        };

        // Render PDF
        $pdf = Pdf::loadView('admin.laporan.pdf', compact(
            'proses',
            'status',
            'statusLabel',
            'hasilList',
            'tanggalPengesahan'
        ));

        // Format A4 Landscape sesuai standar dokumen rekapitulasi kedinasan
        $pdf->setPaper('a4', 'landscape');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'sans-serif',
        ]);

        $safePeriode = Str::slug($proses->periode);
        $fileName = "Rekapitulasi-Penerima-Bantuan-UMKM-{$safePeriode}-{$status}.pdf";

        // Jika request meminta preview di browser tab baru
        if ($request->has('preview')) {
            return $pdf->stream($fileName);
        }

        return $pdf->download($fileName);
    }

    /**
     * Ekspor Data Analisis format Excel (.xlsx)
     */
    public function exportExcel(Request $request, $id)
    {
        $proses = Proses::findOrFail($id);
        $status = $request->query('status', 'semua');

        $safePeriode = Str::slug($proses->periode);
        $fileName = "Rekapitulasi-Penerima-Bantuan-UMKM-{$safePeriode}-{$status}.xlsx";

        $statusFilter = ($status === 'semua') ? null : $status;

        return Excel::download(new RekapitulasiExport((int) $id, $statusFilter), $fileName);
    }
}
