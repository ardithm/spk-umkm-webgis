<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokumen;
use App\Models\Pengajuan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * DokumenController — Modul Verifikasi Dokumen Persyaratan UMKM
 *
 * Mengelola alur validasi berkas fisik digital (KTP, KK, NIB, SKU, Foto Usaha)
 * yang diunggah pemohon sesuai PRD Section 3 & PRD Section 4 (User Flow Admin Step 3).
 *
 * Fitur:
 * - Daftar antrean pengajuan berkas beserta progres kelengkapan
 * - Antarmuka review detail dokumen per kandidat
 * - Aksi Setujui & Tolak dengan catatan perbaikan
 * - Otomasi perubahan status pengajuan ke 'terverifikasi' atau 'revisi'
 * - Pengunduhan & pratinjau file dari direktori privat (non-public storage)
 */
class DokumenController extends Controller
{
    /**
     * Tampilkan daftar pengajuan UMKM yang memerlukan verifikasi berkas.
     */
    public function index(Request $request): View
    {
        $filterStatus = $request->query('status', 'semua');
        $search = $request->query('q');

        $query = Pengajuan::with(['umkm', 'dokumen'])
            ->where('status', '!=', Pengajuan::STATUS_DRAFT) // Hanya yang sudah disubmit
            ->latest('tanggal_pengajuan');

        // Filter status pengajuan
        if (in_array($filterStatus, [
            Pengajuan::STATUS_MENUNGGU,
            Pengajuan::STATUS_REVISI,
            Pengajuan::STATUS_TERVERIFIKASI,
            Pengajuan::STATUS_SELESAI,
        ])) {
            $query->where('status', $filterStatus);
        }

        // Pencarian nama usaha, nama pemilik, atau NIK
        if (!empty($search)) {
            $query->whereHas('umkm', function ($q) use ($search) {
                $q->where('nama_umkm', 'like', "%{$search}%")
                  ->orWhere('nama_pemilik', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('no_telepon', 'like', "%{$search}%");
            });
        }

        $pengajuanList = $query->paginate(15)->withQueryString();

        // Statistik ringkasan berkas
        $stats = [
            'total_berkas_masuk'  => Pengajuan::where('status', '!=', Pengajuan::STATUS_DRAFT)->count(),
            'menunggu_verifikasi' => Pengajuan::where('status', Pengajuan::STATUS_MENUNGGU)->count(),
            'perlu_revisi'        => Pengajuan::where('status', Pengajuan::STATUS_REVISI)->count(),
            'terverifikasi'       => Pengajuan::where('status', Pengajuan::STATUS_TERVERIFIKASI)->count(),
        ];

        return view('admin.dokumen.index', compact('pengajuanList', 'stats', 'filterStatus', 'search'));
    }

    /**
     * Tampilkan antarmuka peninjauan berkas lengkap untuk satu pengajuan UMKM.
     */
    public function show(int $id_pengajuan): View
    {
        $pengajuan = Pengajuan::with(['umkm', 'dokumen'])->findOrFail($id_pengajuan);

        // 5 jenis berkas persyaratan wajib (PRD Section 2 & 3)
        $jenisWajib = [
            'KTP'  => [
                'nama'      => 'KTP Pemilik Usaha',
                'deskripsi' => 'Identitas KTP elektronik 16 digit pemilik sah UMKM.',
                'icon'      => 'identification',
            ],
            'KK'   => [
                'nama'      => 'Kartu Keluarga (KK)',
                'deskripsi' => 'Bukti domisili dan susunan keluarga pendaftar.',
                'icon'      => 'user-group',
            ],
            'NIB'  => [
                'nama'      => 'Nomor Induk Berusaha (NIB)',
                'deskripsi' => 'Legalitas operasional dari sistem OSS (Online Single Submission).',
                'icon'      => 'badge-check',
            ],
            'SKU'  => [
                'nama'      => 'Surat Keterangan Usaha (SKU)',
                'deskripsi' => 'Surat rekomendasi usaha dari Kelurahan atau instansi setempat.',
                'icon'      => 'document-text',
            ],
            'FOTO' => [
                'nama'      => 'Foto Fisik Tempat Usaha',
                'deskripsi' => 'Dokumentasi visual gerai, toko, atau aktivitas produksi UMKM.',
                'icon'      => 'camera',
            ],
        ];

        // Kelompokkan dokumen yang ada berdasarkan jenis_dokumen
        $dokumenGrouped = $pengajuan->dokumen->keyBy('jenis_dokumen');

        // Perhitungan progres
        $totalWajib = count($jenisWajib);
        $totalUploaded = $pengajuan->dokumen->count();
        $totalDisetujui = $pengajuan->dokumen->where('status_verifikasi', Dokumen::STATUS_DISETUJUI)->count();
        $totalDitolak = $pengajuan->dokumen->where('status_verifikasi', Dokumen::STATUS_DITOLAK)->count();
        $totalMenunggu = $pengajuan->dokumen->where('status_verifikasi', Dokumen::STATUS_MENUNGGU)->count();

        $stats = [
            'total_wajib'     => $totalWajib,
            'total_uploaded'  => $totalUploaded,
            'total_disetujui' => $totalDisetujui,
            'total_ditolak'   => $totalDitolak,
            'total_menunggu'  => $totalMenunggu,
            'is_lengkap'      => ($totalDisetujui === $totalWajib),
        ];

        return view('admin.dokumen.show', compact(
            'pengajuan',
            'jenisWajib',
            'dokumenGrouped',
            'stats'
        ));
    }

    /**
     * Setujui satu berkas dokumen persyaratan.
     * Jika semua 5 berkas wajib telah disetujui, status pengajuan otomatis beralih ke 'terverifikasi'.
     */
    public function setujui(int $id): RedirectResponse
    {
        $dokumen = Dokumen::with('pengajuan.dokumen')->findOrFail($id);
        $pengajuan = $dokumen->pengajuan;

        $dokumen->status_verifikasi = Dokumen::STATUS_DISETUJUI;
        $dokumen->catatan_admin = null;
        $dokumen->save();

        // Refresh relasi untuk mengecek kelengkapan 5 dokumen wajib
        $pengajuan->load('dokumen');
        $jenisWajib = ['KTP', 'KK', 'NIB', 'SKU', 'FOTO'];
        $disetujuiCount = $pengajuan->dokumen
            ->whereIn('jenis_dokumen', $jenisWajib)
            ->where('status_verifikasi', Dokumen::STATUS_DISETUJUI)
            ->count();

        $semuaLengkap = ($disetujuiCount === count($jenisWajib));

        if ($semuaLengkap) {
            $pengajuan->status = Pengajuan::STATUS_TERVERIFIKASI;
            $pengajuan->save();

            return back()->with('success', 
                "✅ Berkas <strong>{$dokumen->jenis_dokumen}</strong> berhasil disetujui! " .
                "Seluruh 5 dokumen wajib telah terverifikasi, status pengajuan otomatis beralih menjadi <strong>TERVERIFIKASI</strong> (Siap diikutsertakan dalam SPK Profile Matching)."
            );
        }

        return back()->with('success', "✅ Berkas <strong>{$dokumen->jenis_dokumen}</strong> berhasil disetujui.");
    }

    /**
     * Tolak satu berkas dokumen dengan memberikan catatan perbaikan.
     * Status pengajuan otomatis beralih ke 'revisi'.
     */
    public function tolak(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'catatan_admin' => 'required|string|min:3|max:500',
        ], [
            'catatan_admin.required' => 'Catatan alasan penolakan berkas wajib diisi agar pendaftar mengetahui hal yang perlu diperbaiki.',
            'catatan_admin.min'      => 'Catatan alasan penolakan minimal 3 karakter.',
            'catatan_admin.max'      => 'Catatan alasan penolakan maksimal 500 karakter.',
        ]);

        $dokumen = Dokumen::with('pengajuan')->findOrFail($id);
        $pengajuan = $dokumen->pengajuan;

        $dokumen->status_verifikasi = Dokumen::STATUS_DITOLAK;
        $dokumen->catatan_admin = $request->input('catatan_admin');
        $dokumen->save();

        // Pengajuan beralih ke status revisi
        $pengajuan->status = Pengajuan::STATUS_REVISI;
        $pengajuan->save();

        return back()->with('warning', 
            "⚠️ Berkas <strong>{$dokumen->jenis_dokumen}</strong> telah ditolak dengan catatan perbaikan. " .
            "Status pengajuan beralih menjadi <strong>REVISI</strong> agar pemohon dapat memperbarui berkas yang diminta."
        );
    }

    /**
     * Setujui seluruh berkas dokumen yang telah diunggah sekaligus (Batch Approve).
     */
    public function setujuiSemua(int $id_pengajuan): RedirectResponse
    {
        $pengajuan = Pengajuan::with('dokumen')->findOrFail($id_pengajuan);

        $dokumenList = $pengajuan->dokumen;

        if ($dokumenList->isEmpty()) {
            return back()->with('error', 'Tidak ada berkas yang diunggah untuk disetujui.');
        }

        foreach ($dokumenList as $dok) {
            $dok->status_verifikasi = Dokumen::STATUS_DISETUJUI;
            $dok->catatan_admin = null;
            $dok->save();
        }

        // Cek kelengkapan
        $pengajuan->load('dokumen');
        $jenisWajib = ['KTP', 'KK', 'NIB', 'SKU', 'FOTO'];
        $disetujuiCount = $pengajuan->dokumen
            ->whereIn('jenis_dokumen', $jenisWajib)
            ->where('status_verifikasi', Dokumen::STATUS_DISETUJUI)
            ->count();

        if ($disetujuiCount === count($jenisWajib)) {
            $pengajuan->status = Pengajuan::STATUS_TERVERIFIKASI;
            $pengajuan->save();

            return back()->with('success', 
                "✅ Seluruh ({$dokumenList->count()}) dokumen berhasil disetujui sekaligus! Status pengajuan kini <strong>TERVERIFIKASI</strong> (Siap masuk SPK)."
            );
        }

        return back()->with('success', 
            "✅ Berkas yang ada berhasil disetujui. Namun masih terdapat berkas wajib yang belum diunggah oleh pemohon."
        );
    }

    /**
     * Unduh atau pratinjau file dokumen dari storage privat (non-public).
     * Dilindungi middleware admin sesuai PRD Section 2 & 5.
     */
    public function download(int $id_dokumen)
    {
        $dokumen = Dokumen::with('pengajuan.umkm')->findOrFail($id_dokumen);

        // Path relatif di storage disk local (storage/app/private/)
        $filePath = $dokumen->file_dokumen;

        if (!Storage::disk('local')->exists($filePath)) {
            return back()->with('error', "Berkas fisik [{$dokumen->jenis_dokumen}] tidak ditemukan di server penyimpanan privat.");
        }

        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $cleanName = \Str::slug($dokumen->pengajuan?->umkm?->nama_umkm ?? 'umkm') . "_{$dokumen->jenis_dokumen}.{$extension}";

        return Storage::disk('local')->response($filePath, $cleanName);
    }
}
