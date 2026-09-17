<?php

namespace App\Http\Controllers\Umkm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Umkm\UpdateAvatarRequest;
use App\Http\Requests\Umkm\UpdatePasswordRequest;
use App\Http\Requests\Umkm\UpdateProfilRequest;
use App\Models\Umkm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AkunController extends Controller
{
    /**
     * Tampilkan halaman formulir Edit Profil & Pengaturan Akun UMKM.
     * Mengelompokkan data ke dalam tab: Profil & Kontak, Keamanan, dan Avatar.
     */
    public function edit(Request $request): View
    {
        $user = Auth::user();
        
        // Ambil data profil UMKM milik user saat ini (jika belum ada, inisiasi instance kosong)
        $umkm = $user->umkm ?? new Umkm(['id_user' => $user->id_user]);

        // Tentukan tab yang aktif (default: 'profil')
        $activeTab = $request->query('tab', 'profil');
        if (!in_array($activeTab, ['profil', 'keamanan', 'avatar'])) {
            $activeTab = 'profil';
        }

        return view('umkm.akun.edit', compact('user', 'umkm', 'activeTab'));
    }

    /**
     * Alias untuk view akun (bisa diakses via route umkm.akun.show).
     */
    public function show(Request $request): View
    {
        return $this->edit($request);
    }

    /**
     * Perbarui data profil pribadi dan kontak UMKM.
     * Menggunakan Database Transaction untuk menjamin integritas relasi User dan Umkm.
     */
    public function updateProfil(UpdateProfilRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $validated = $request->validated();

        DB::transaction(function () use ($user, $validated) {
            // 1. Perbarui data User
            $user->update([
                'nama_lengkap' => $validated['nama_lengkap'],
                'email'        => $validated['email'],
            ]);

            // 2. Perbarui atau buat profil data UMKM
            $user->umkm()->updateOrCreate(
                ['id_user' => $user->id_user],
                [
                    'nama_pemilik' => $validated['nama_pemilik'],
                    'nama_umkm'    => $validated['nama_umkm'],
                    'nik'          => $validated['nik'],
                    'no_telepon'   => $validated['no_telepon'],
                    'alamat'       => $validated['alamat'],
                ]
            );
        });

        return redirect()
            ->route('umkm.akun.edit', ['tab' => 'profil'])
            ->with('success', 'Data profil pribadi dan usaha berhasil diperbarui!');
    }

    /**
     * Perbarui kata sandi akun UMKM.
     * Validasi telah memastikan password lama sesuai dan password baru memenuhi standar keamanan.
     */
    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $user = Auth::user();

        $user->update([
            'password' => Hash::make($request->validated('password')),
        ]);

        return redirect()
            ->route('umkm.akun.edit', ['tab' => 'keamanan'])
            ->with('success', 'Kata sandi akun Anda berhasil diperbarui dengan aman!');
    }

    /**
     * Unggah dan perbarui foto profil / avatar UMKM.
     * Menghapus file avatar lama jika ada untuk mencegah penumpukan berkas sampah.
     */
    public function updateAvatar(UpdateAvatarRequest $request): RedirectResponse
    {
        $user = Auth::user();

        if ($request->hasFile('foto')) {
            // Hapus file avatar lama jika ada di storage publik
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }

            // Simpan foto baru ke folder avatars di disk public
            $path = $request->file('foto')->store('avatars', 'public');

            $user->update([
                'foto' => $path,
            ]);
        }

        return redirect()
            ->route('umkm.akun.edit', ['tab' => 'avatar'])
            ->with('success', 'Foto profil berhasil diperbarui!');
    }

    /**
     * Hapus foto profil UMKM dan kembalikan ke avatar monogram inisial standar.
     */
    public function deleteAvatar(): RedirectResponse
    {
        $user = Auth::user();

        if ($user->foto) {
            if (Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }

            $user->update([
                'foto' => null,
            ]);
        }

        return redirect()
            ->route('umkm.akun.edit', ['tab' => 'avatar'])
            ->with('success', 'Foto profil berhasil dihapus dan dikembalikan ke avatar standar.');
    }
}
