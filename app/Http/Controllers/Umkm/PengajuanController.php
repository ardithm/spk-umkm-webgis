<?php

namespace App\Http\Controllers\Umkm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Umkm\ReuploadDokumenRequest;
use App\Http\Requests\Umkm\StorePengajuanRequest;
use App\Http\Requests\Umkm\UpdatePengajuanRequest;
use App\Models\Dokumen;
use App\Models\Pengajuan;
use App\Models\Proses;
use App\Models\Umkm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * PengajuanController — Modul CRUD Data Pengajuan & Alur Perbaikan Berkas Pelaku UMKM
 *
 * Mengelola seluruh siklus hidup pengajuan bantuan modal usaha untuk peran Pelaku UMKM:
 * 1. Ownership Scope: Hanya mengizinkan akses data milik UMKM user yang login.
 * 2. Form Lock Mechanism: Data terkunci saat dalam proses seleksi (menunggu, terverifikasi, diproses, selesai).
 * 3. Re-upload Berkas: Mengunggah berkas pengganti per-dokumen yang ditolak admin dengan pembersihan berkas lama.
 * 4. Resubmit: Mengirimkan ulang pengajuan hasil revisi kembali ke status menunggu verifikasi.
 */
class PengajuanController extends Controller
{
    /**
     * Dapatkan profil UMKM milik user yang sedang terautentikasi.
     */
    protected function getAuthenticatedUmkm(): ?Umkm
    {
        $user = Auth::user();
        return Umkm::where('id_user', $user->id_user)->first();
    }

    /**
     * Tampilkan daftar riwayat dan status pengajuan bantuan UMKM.
     * [GET] /umkm/pengajuan
     */
    public function index(): View|RedirectResponse
    {
        $umkm = $this->getAuthenticatedUmkm();

        if (!$umkm || !$umkm->isProfileComplete()) {
            return redirect()
                ->route('umkm.akun.edit', ['tab' => 'profil'])
                ->with('warning', 'Harap lengkapi data profil usaha Anda terlebih dahulu sebelum mengakses menu pengajuan bantuan.');
        }

        $pengajuanList = Pengajuan::with(['dokumen'])
            ->where('id_umkm', $umkm->id_umkm)
            ->latest('tanggal_pengajuan')
            ->paginate(10);

        // Ambil pengajuan aktif jika ada
        $pengajuanAktif = Pengajuan::with(['dokumen'])
            ->where('id_umkm', $umkm->id_umkm)
            ->whereIn('status', [
                Pengajuan::STATUS_DRAFT,
                Pengajuan::STATUS_MENUNGGU,
                Pengajuan::STATUS_REVISI,
                Pengajuan::STATUS_TERVERIFIKASI,
                Pengajuan::STATUS_DIPROSES,
            ])
            ->first();

        $stats = [
            'total'         => Pengajuan::where('id_umkm', $umkm->id_umkm)->count(),
            'menunggu'      => Pengajuan::where('id_umkm', $umkm->id_umkm)->where('status', Pengajuan::STATUS_MENUNGGU)->count(),
            'revisi'        => Pengajuan::where('id_umkm', $umkm->id_umkm)->where('status', Pengajuan::STATUS_REVISI)->count(),
            'terverifikasi' => Pengajuan::where('id_umkm', $umkm->id_umkm)->where('status', Pengajuan::STATUS_TERVERIFIKASI)->count(),
        ];

        // Ambil info gelombang aktif untuk validasi UI
        $gelombangAktif = Proses::where('status', 'buka')
            ->whereDate('tgl_pendaftaran_mulai', '<=', now())
            ->whereDate('tgl_pendaftaran_selesai', '>=', now())
            ->first();

        return view('umkm.pengajuan.index', compact('umkm', 'pengajuanList', 'pengajuanAktif', 'stats', 'gelombangAktif'));
    }

    /**
     * Tampilkan form pembuatan pengajuan baru.
     * [GET] /umkm/pengajuan/create
     */
    public function create(): View|RedirectResponse
    {
        $umkm = $this->getAuthenticatedUmkm();

        if (!$umkm || !$umkm->isProfileComplete()) {
            return redirect()
                ->route('umkm.akun.edit', ['tab' => 'profil'])
                ->with('warning', 'Harap lengkapi data profil usaha Anda terlebih dahulu sebelum membuat pengajuan bantuan modal.');
        }

        // Validasi Gelombang Aktif
        $gelombangAktif = Proses::where('status', 'buka')
            ->whereDate('tgl_pendaftaran_mulai', '<=', now())
            ->whereDate('tgl_pendaftaran_selesai', '>=', now())
            ->first();

        if (!$gelombangAktif) {
            return redirect()
                ->route('umkm.pengajuan.index')
                ->with('error', 'Pendaftaran pengajuan bantuan saat ini ditutup. Belum ada Gelombang/Periode Seleksi yang aktif saat ini.');
        }

        // Cek apakah ada pengajuan yang masih aktif (belum selesai)
        $existing = Pengajuan::where('id_umkm', $umkm->id_umkm)
            ->whereIn('status', [
                Pengajuan::STATUS_DRAFT,
                Pengajuan::STATUS_MENUNGGU,
                Pengajuan::STATUS_REVISI,
                Pengajuan::STATUS_TERVERIFIKASI,
                Pengajuan::STATUS_DIPROSES,
            ])
            ->first();

        if ($existing) {
            return redirect()
                ->route('umkm.pengajuan.show', $existing->id_pengajuan)
                ->with('warning', 'Anda sudah memiliki pengajuan yang sedang berjalan. Silakan pantau atau kelola pengajuan aktif Anda.');
        }

        return view('umkm.pengajuan.create', compact('umkm'));
    }

    /**
     * Simpan pengajuan baru beserta 5 berkas persyaratan fisik.
     * [POST] /umkm/pengajuan
     */
    public function store(StorePengajuanRequest $request): RedirectResponse
    {
        $umkm = $this->getAuthenticatedUmkm();

        if (!$umkm || !$umkm->isProfileComplete()) {
            return redirect()
                ->route('umkm.akun.edit', ['tab' => 'profil'])
                ->with('warning', 'Harap lengkapi data profil usaha Anda terlebih dahulu sebelum membuat pengajuan bantuan modal.');
        }

        $validated = $request->validated();

        // Validasi Gelombang Aktif
        $gelombangAktif = Proses::where('status', 'buka')
            ->whereDate('tgl_pendaftaran_mulai', '<=', now())
            ->whereDate('tgl_pendaftaran_selesai', '>=', now())
            ->first();

        if (!$gelombangAktif) {
            return redirect()
                ->route('umkm.pengajuan.index')
                ->with('error', 'Pendaftaran pengajuan bantuan saat ini ditutup.');
        }

        // Cek pengajuan aktif
        $existing = Pengajuan::where('id_umkm', $umkm->id_umkm)
            ->whereIn('status', [
                Pengajuan::STATUS_DRAFT,
                Pengajuan::STATUS_MENUNGGU,
                Pengajuan::STATUS_REVISI,
                Pengajuan::STATUS_TERVERIFIKASI,
                Pengajuan::STATUS_DIPROSES,
            ])
            ->first();

        if ($existing) {
            return redirect()
                ->route('umkm.pengajuan.show', $existing->id_pengajuan)
                ->with('error', 'Anda masih memiliki pengajuan aktif yang belum selesai.');
        }

        // Tentukan aksi submit: Kirim langsung atau Simpan Draft
        $isDraft = $request->input('action') === 'draft';
        $status = $isDraft ? Pengajuan::STATUS_DRAFT : Pengajuan::STATUS_MENUNGGU;

        try {
            DB::beginTransaction();

            // 1. Update koordinat spasial UMKM di tabel umkm
            $umkm->update([
                'latitude'  => $validated['latitude'],
                'longitude' => $validated['longitude'],
            ]);

            // 2. Buat record Pengajuan
            $pengajuan = Pengajuan::create([
                'id_umkm'             => $umkm->id_umkm,
                'omzet_tahunan'       => $validated['omzet_tahunan'],
                'aset'                => $validated['aset'],
                'jumlah_tenaga_kerja' => $validated['jumlah_tenaga_kerja'],
                'jangkauan_pemasaran' => $validated['jangkauan_pemasaran'],
                'status_perizinan'    => $validated['status_perizinan'],
                'tanggal_pengajuan'   => now(),
                'status'              => $status,
            ]);

            // 3. Unggah dan tautkan 5 Dokumen Persyaratan
            $dokumenMap = [
                'KTP'  => 'file_ktp',
                'KK'   => 'file_kk',
                'NIB'  => 'file_nib',
                'SKU'  => 'file_sku',
                'FOTO' => 'file_foto',
            ];

            foreach ($dokumenMap as $jenis => $inputKey) {
                if ($request->hasFile($inputKey)) {
                    $file = $request->file($inputKey);
                    // Simpan di direktori privat storage/app/dokumen_umkm/
                    $path = $file->store('dokumen_umkm');

                    Dokumen::create([
                        'id_pengajuan'      => $pengajuan->id_pengajuan,
                        'jenis_dokumen'     => $jenis,
                        'file_dokumen'      => $path,
                        'status_verifikasi' => Dokumen::STATUS_MENUNGGU,
                        'catatan_admin'     => null,
                    ]);
                }
            }

            DB::commit();

            $pesan = $isDraft
                ? 'Draf pengajuan bantuan berhasil disimpan. Anda dapat memperbaikinya sebelum dikirimkan.'
                : 'Pengajuan bantuan modal berhasil dikirimkan dan menunggu verifikasi dari Admin Dinas!';

            return redirect()
                ->route('umkm.pengajuan.show', $pengajuan->id_pengajuan)
                ->with('success', $pesan);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Terjadi kesalahan sistem saat menyimpan pengajuan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Tampilkan detail peninjauan pengajuan, koordinat WebGIS, dan status verifikasi per-berkas.
     * [GET] /umkm/pengajuan/{id}
     */
    public function show(int $id): View|RedirectResponse
    {
        $umkm = $this->getAuthenticatedUmkm();

        if (!$umkm) {
            return redirect()
                ->route('umkm.akun.edit', ['tab' => 'profil'])
                ->with('warning', 'Harap lengkapi data profil usaha Anda terlebih dahulu.');
        }

        // Strict ownership: hanya pengajuan milik UMKM yang bersangkutan
        $pengajuan = Pengajuan::with(['umkm', 'dokumen'])
            ->where('id_umkm', $umkm->id_umkm)
            ->findOrFail($id);

        $jenisWajib = [
            'KTP'  => [
                'nama'      => 'KTP Pemilik Usaha',
                'deskripsi' => 'Identitas resmi pemilik sesuai data Kependudukan.',
                'format'    => 'PDF, JPG, PNG',
            ],
            'KK'   => [
                'nama'      => 'Kartu Keluarga (KK)',
                'deskripsi' => 'Bukti susunan keluarga dan domisili.',
                'format'    => 'PDF, JPG, PNG',
            ],
            'NIB'  => [
                'nama'      => 'Nomor Induk Berusaha (NIB)',
                'deskripsi' => 'Legalitas OSS atau surat keterangan izin usaha.',
                'format'    => 'PDF, JPG, PNG',
            ],
            'SKU'  => [
                'nama'      => 'Surat Keterangan Usaha (SKU)',
                'deskripsi' => 'Surat rekomendasi dari Kelurahan / instansi setempat.',
                'format'    => 'PDF, JPG, PNG',
            ],
            'FOTO' => [
                'nama'      => 'Foto Fisik Tempat Usaha',
                'deskripsi' => 'Foto tempat usaha / gerai produksi fisik.',
                'format'    => 'JPG, JPEG, PNG',
            ],
        ];

        $dokumenGrouped = $pengajuan->dokumen->keyBy('jenis_dokumen');

        // Statistik kelengkapan dokumen
        $totalWajib = count($jenisWajib);
        $totalUploaded = $pengajuan->dokumen->count();
        $disetujuiCount = $pengajuan->dokumen->where('status_verifikasi', Dokumen::STATUS_DISETUJUI)->count();
        $ditolakCount = $pengajuan->dokumen->where('status_verifikasi', Dokumen::STATUS_DITOLAK)->count();
        $menungguCount = $pengajuan->dokumen->where('status_verifikasi', Dokumen::STATUS_MENUNGGU)->count();

        $stats = [
            'total_wajib'     => $totalWajib,
            'total_uploaded'  => $totalUploaded,
            'total_disetujui' => $disetujuiCount,
            'total_ditolak'   => $ditolakCount,
            'total_menunggu'  => $menungguCount,
            'siap_resubmit'   => ($pengajuan->status === Pengajuan::STATUS_REVISI && $ditolakCount === 0),
        ];

        return view('umkm.pengajuan.show', compact('pengajuan', 'umkm', 'jenisWajib', 'dokumenGrouped', 'stats'));
    }

    /**
     * Tampilkan form edit data pengajuan.
     * Menerapkan Form Lock Mechanism: hanya status 'draft' atau 'revisi' yang boleh diedit.
     * [GET] /umkm/pengajuan/{id}/edit
     */
    public function edit(int $id): View|RedirectResponse
    {
        $umkm = $this->getAuthenticatedUmkm();

        if (!$umkm) {
            return redirect()
                ->route('umkm.akun.edit', ['tab' => 'profil'])
                ->with('warning', 'Harap lengkapi data profil usaha Anda terlebih dahulu.');
        }

        $pengajuan = Pengajuan::with(['dokumen'])
            ->where('id_umkm', $umkm->id_umkm)
            ->findOrFail($id);

        // FORM LOCK MECHANISM
        if ($pengajuan->isLocked()) {
            return redirect()
                ->route('umkm.pengajuan.show', $pengajuan->id_pengajuan)
                ->with('error', 'Formulir pengajuan terkunci dan tidak dapat diubah karena sedang dalam proses seleksi administratif.');
        }

        $dokumenGrouped = $pengajuan->dokumen->keyBy('jenis_dokumen');

        return view('umkm.pengajuan.edit', compact('pengajuan', 'umkm', 'dokumenGrouped'));
    }

    /**
     * Perbarui data kriteria dan koordinat pengajuan.
     * [PUT] /umkm/pengajuan/{id}
     */
    public function update(UpdatePengajuanRequest $request, int $id): RedirectResponse
    {
        $umkm = $this->getAuthenticatedUmkm();

        if (!$umkm) {
            return redirect()
                ->route('umkm.akun.edit', ['tab' => 'profil'])
                ->with('warning', 'Harap lengkapi data profil usaha Anda terlebih dahulu.');
        }

        $pengajuan = Pengajuan::with(['dokumen'])
            ->where('id_umkm', $umkm->id_umkm)
            ->findOrFail($id);

        // FORM LOCK MECHANISM
        if ($pengajuan->isLocked()) {
            return redirect()
                ->route('umkm.pengajuan.show', $pengajuan->id_pengajuan)
                ->with('error', 'Pengajuan ini tidak dapat diedit karena sedang diproses oleh panitia seleksi.');
        }

        $validated = $request->validated();

        try {
            DB::beginTransaction();

            // 1. Perbarui koordinat fisik
            $umkm->update([
                'latitude'  => $validated['latitude'],
                'longitude' => $validated['longitude'],
            ]);

            // 2. Perbarui nilai kriteria
            $pengajuan->update([
                'omzet_tahunan'       => $validated['omzet_tahunan'],
                'aset'                => $validated['aset'],
                'jumlah_tenaga_kerja' => $validated['jumlah_tenaga_kerja'],
                'jangkauan_pemasaran' => $validated['jangkauan_pemasaran'],
                'status_perizinan'    => $validated['status_perizinan'],
            ]);

            // 3. Perbarui berkas fisik jika ada berkas baru yang diunggah
            $dokumenMap = [
                'KTP'  => 'file_ktp',
                'KK'   => 'file_kk',
                'NIB'  => 'file_nib',
                'SKU'  => 'file_sku',
                'FOTO' => 'file_foto',
            ];

            foreach ($dokumenMap as $jenis => $inputKey) {
                if ($request->hasFile($inputKey)) {
                    $file = $request->file($inputKey);
                    $existingDoc = $pengajuan->dokumen->firstWhere('jenis_dokumen', $jenis);

                    // Hapus berkas lama dari storage privat
                    if ($existingDoc && $existingDoc->file_dokumen && Storage::disk('local')->exists($existingDoc->file_dokumen)) {
                        Storage::disk('local')->delete($existingDoc->file_dokumen);
                    }

                    $path = $file->store('dokumen_umkm');

                    if ($existingDoc) {
                        $existingDoc->update([
                            'file_dokumen'      => $path,
                            'status_verifikasi' => Dokumen::STATUS_MENUNGGU,
                            'catatan_admin'     => null,
                        ]);
                    } else {
                        Dokumen::create([
                            'id_pengajuan'      => $pengajuan->id_pengajuan,
                            'jenis_dokumen'     => $jenis,
                            'file_dokumen'      => $path,
                            'status_verifikasi' => Dokumen::STATUS_MENUNGGU,
                            'catatan_admin'     => null,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('umkm.pengajuan.show', $pengajuan->id_pengajuan)
                ->with('success', 'Data pengajuan berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Gagal memperbarui data pengajuan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Submit pengajuan awal dari status 'draft' ke 'menunggu'.
     * [POST] /umkm/pengajuan/{id}/submit
     */
    public function submit(int $id): RedirectResponse
    {
        $umkm = $this->getAuthenticatedUmkm();

        if (!$umkm) {
            return redirect()
                ->route('umkm.akun.edit', ['tab' => 'profil'])
                ->with('warning', 'Harap lengkapi data profil usaha Anda terlebih dahulu.');
        }

        $pengajuan = Pengajuan::with('dokumen')
            ->where('id_umkm', $umkm->id_umkm)
            ->findOrFail($id);

        if ($pengajuan->status !== Pengajuan::STATUS_DRAFT) {
            return back()->with('error', 'Pengajuan sudah pernah dikirimkan sebelumnya.');
        }

        // Cek kelengkapan 5 dokumen
        $jenisWajib = ['KTP', 'KK', 'NIB', 'SKU', 'FOTO'];
        $uploadedCount = $pengajuan->dokumen->whereIn('jenis_dokumen', $jenisWajib)->count();

        if ($uploadedCount < count($jenisWajib)) {
            return back()->with('error', 'Harap lengkapi seluruh 5 dokumen persyaratan fisik sebelum mengirimkan pengajuan.');
        }

        $pengajuan->update([
            'status'            => Pengajuan::STATUS_MENUNGGU,
            'tanggal_pengajuan' => now(),
        ]);

        return redirect()
            ->route('umkm.pengajuan.show', $pengajuan->id_pengajuan)
            ->with('success', 'Pengajuan berhasil dikirimkan! Tim verifikator akan segera meninjau berkas Anda.');
    }

    /**
     * Upload ulang berkas spesifik yang ditolak oleh verifikator.
     * [POST] /umkm/pengajuan/{id}/dokumen/{id_dokumen}/reupload
     */
    public function reuploadDokumen(ReuploadDokumenRequest $request, int $id, int $id_dokumen): RedirectResponse
    {
        $umkm = $this->getAuthenticatedUmkm();

        if (!$umkm) {
            return redirect()
                ->route('umkm.akun.edit', ['tab' => 'profil'])
                ->with('warning', 'Harap lengkapi data profil usaha Anda terlebih dahulu.');
        }

        $pengajuan = Pengajuan::where('id_umkm', $umkm->id_umkm)->findOrFail($id);
        $dokumen = $pengajuan->dokumen()->findOrFail($id_dokumen);

        // Hanya boleh diunggah ulang jika status pengajuan dalam 'revisi' atau 'draft'
        if ($pengajuan->isLocked()) {
            return back()->with('error', 'Pengajuan sedang terkunci, tidak dapat mengunggah berkas.');
        }

        try {
            // Hapus berkas fisik lama dari storage privat
            if ($dokumen->file_dokumen && Storage::disk('local')->exists($dokumen->file_dokumen)) {
                Storage::disk('local')->delete($dokumen->file_dokumen);
            }

            // Simpan berkas pengganti
            $path = $request->file('file_dokumen')->store('dokumen_umkm');

            // Reset status dokumen menjadi 'menunggu' dan bersihkan catatan
            $dokumen->update([
                'file_dokumen'      => $path,
                'status_verifikasi' => Dokumen::STATUS_MENUNGGU,
                'catatan_admin'     => null,
            ]);

            return back()->with('success', "Berkas perbaikan [{$dokumen->nama_jenis}] berhasil diunggah! Status berkas telah diatur ulang ke Menunggu Verifikasi.");

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengunggah berkas perbaikan: ' . $e->getMessage());
        }
    }

    /**
     * Kirim ulang berkas pengajuan perbaikan (Resubmit) setelah berkas ditolak selesai diperbaiki.
     * [POST] /umkm/pengajuan/{id}/resubmit
     */
    public function resubmit(int $id): RedirectResponse
    {
        $umkm = $this->getAuthenticatedUmkm();

        if (!$umkm) {
            return redirect()
                ->route('umkm.akun.edit', ['tab' => 'profil'])
                ->with('warning', 'Harap lengkapi data profil usaha Anda terlebih dahulu.');
        }

        $pengajuan = Pengajuan::with('dokumen')
            ->where('id_umkm', $umkm->id_umkm)
            ->findOrFail($id);

        if ($pengajuan->status !== Pengajuan::STATUS_REVISI) {
            return back()->with('error', 'Hanya pengajuan berstatus revisi yang dapat dikirimkan ulang.');
        }

        // Pastikan tidak ada lagi dokumen yang masih berstatus 'ditolak'
        $masihDitolak = $pengajuan->dokumen->where('status_verifikasi', Dokumen::STATUS_DITOLAK);

        if ($masihDitolak->isNotEmpty()) {
            $daftarNama = $masihDitolak->pluck('nama_jenis')->join(', ');
            return back()->with('error', "Anda masih memiliki berkas yang berstatus ditolak dan belum diperbarui: {$daftarNama}. Silakan unggah berkas pengganti terlebih dahulu.");
        }

        // Ubah status pengajuan kembali ke 'menunggu' verifikasi
        $pengajuan->update([
            'status'            => Pengajuan::STATUS_MENUNGGU,
            'tanggal_pengajuan' => now(),
            'catatan_revisi'    => null, // Reset catatan revisi umum
        ]);

        return redirect()
            ->route('umkm.pengajuan.show', $pengajuan->id_pengajuan)
            ->with('success', 'Berkas perbaikan Anda berhasil dikirimkan ulang! Petugas verifikator akan segera meninjau kembali pengajuan Anda.');
    }

    /**
     * Pratinjau / unduh berkas dokumen privat milik UMKM sendiri.
     * [GET] /umkm/pengajuan/{id}/dokumen/{id_dokumen}/preview
     */
    public function previewDokumen(int $id, int $id_dokumen): Response
    {
        $umkm = $this->getAuthenticatedUmkm();

        if (!$umkm) {
            abort(404, 'Data profil UMKM tidak ditemukan.');
        }

        $pengajuan = Pengajuan::where('id_umkm', $umkm->id_umkm)->findOrFail($id);
        $dokumen = $pengajuan->dokumen()->findOrFail($id_dokumen);

        $filePath = $dokumen->file_dokumen;

        if (!Storage::disk('local')->exists($filePath)) {
            abort(404, 'Berkas fisik dokumen tidak ditemukan pada penyimpanan privat.');
        }

        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $fileName = \Str::slug($umkm->nama_umkm) . "_{$dokumen->jenis_dokumen}.{$extension}";

        return Storage::disk('local')->response($filePath, $fileName);
    }

    /**
     * Hapus pengajuan yang masih berstatus 'draft'.
     * [DELETE] /umkm/pengajuan/{id}
     */
    public function destroy(int $id): RedirectResponse
    {
        $umkm = $this->getAuthenticatedUmkm();

        if (!$umkm) {
            return redirect()
                ->route('umkm.akun.edit', ['tab' => 'profil'])
                ->with('warning', 'Harap lengkapi data profil usaha Anda terlebih dahulu.');
        }

        $pengajuan = Pengajuan::with('dokumen')
            ->where('id_umkm', $umkm->id_umkm)
            ->findOrFail($id);

        if ($pengajuan->status !== Pengajuan::STATUS_DRAFT) {
            return back()->with('error', 'Hanya pengajuan berstatus draf yang dapat dibatalkan atau dihapus.');
        }

        // Hapus seluruh file fisik
        foreach ($pengajuan->dokumen as $doc) {
            if ($doc->file_dokumen && Storage::disk('local')->exists($doc->file_dokumen)) {
                Storage::disk('local')->delete($doc->file_dokumen);
            }
        }

        $pengajuan->delete();

        return redirect()
            ->route('umkm.pengajuan.index')
            ->with('success', 'Draf pengajuan berhasil dibatalkan dan dihapus.');
    }
}
