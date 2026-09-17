<?php

namespace App\Http\Controllers\Umkm;

use App\Http\Controllers\Controller;
use App\Models\Dokumen;
use App\Models\Pengajuan;
use App\Models\Umkm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DokumenController extends Controller
{
    /**
     * Alihkan ke halaman peninjauan pengajuan & dokumen.
     */
    public function index(int $id_pengajuan): RedirectResponse
    {
        return redirect()->route('umkm.pengajuan.show', $id_pengajuan);
    }

    /**
     * Upload dokumen mandiri untuk pengajuan.
     */
    public function upload(Request $request, int $id_pengajuan): RedirectResponse
    {
        $user = Auth::user();
        $umkm = Umkm::where('id_user', $user->id_user)->first();

        if (!$umkm) {
            return redirect()->route('umkm.akun.edit', ['tab' => 'profil'])->with('warning', 'Harap lengkapi profil usaha Anda terlebih dahulu.');
        }

        $pengajuan = Pengajuan::where('id_umkm', $umkm->id_umkm)->findOrFail($id_pengajuan);

        if ($pengajuan->isLocked()) {
            return back()->with('error', 'Pengajuan sedang terkunci dan tidak dapat menerima unggahan berkas baru.');
        }

        $request->validate([
            'jenis_dokumen' => 'required|in:KTP,KK,NIB,SKU,FOTO',
            'file_dokumen'  => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $path = $request->file('file_dokumen')->store('dokumen_umkm');

        // Hapus file lama jika ada
        $existing = $pengajuan->dokumen()->where('jenis_dokumen', $request->jenis_dokumen)->first();
        if ($existing && $existing->file_dokumen && Storage::disk('local')->exists($existing->file_dokumen)) {
            Storage::disk('local')->delete($existing->file_dokumen);
            $existing->update([
                'file_dokumen'      => $path,
                'status_verifikasi' => Dokumen::STATUS_MENUNGGU,
                'catatan_admin'     => null,
            ]);
        } else {
            Dokumen::create([
                'id_pengajuan'      => $pengajuan->id_pengajuan,
                'jenis_dokumen'     => $request->jenis_dokumen,
                'file_dokumen'      => $path,
                'status_verifikasi' => Dokumen::STATUS_MENUNGGU,
            ]);
        }

        return back()->with('success', "Berkas {$request->jenis_dokumen} berhasil diunggah.");
    }

    /**
     * Hapus lampiran berkas dokumen (hanya jika pengajuan masih draft).
     */
    public function destroy(int $id_dokumen): RedirectResponse
    {
        $user = Auth::user();
        $umkm = Umkm::where('id_user', $user->id_user)->first();

        if (!$umkm) {
            return redirect()->route('umkm.akun.edit', ['tab' => 'profil'])->with('warning', 'Harap lengkapi profil usaha Anda terlebih dahulu.');
        }
        
        $dokumen = Dokumen::whereHas('pengajuan', function ($q) use ($umkm) {
            $q->where('id_umkm', $umkm->id_umkm);
        })->findOrFail($id_dokumen);

        if ($dokumen->pengajuan->isLocked()) {
            return back()->with('error', 'Tidak dapat menghapus berkas pada pengajuan yang terkunci.');
        }

        if ($dokumen->file_dokumen && Storage::disk('local')->exists($dokumen->file_dokumen)) {
            Storage::disk('local')->delete($dokumen->file_dokumen);
        }

        $dokumen->delete();

        return back()->with('success', 'Berkas berhasil dihapus.');
    }
}
