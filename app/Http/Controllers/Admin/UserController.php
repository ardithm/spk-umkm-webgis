<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Menampilkan daftar pengguna (Admin & UMKM).
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Pencarian (Search)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter Role
        if ($request->filled('role') && in_array($request->role, ['admin', 'umkm'])) {
            $query->where('role', $request->role);
        }

        // Urutkan berdasarkan yang terbaru
        $pengguna = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('admin.pengguna.index', compact('pengguna'));
    }

    /**
     * Menampilkan form tambah pengguna baru.
     */
    public function create()
    {
        return view('admin.pengguna.create');
    }

    /**
     * Menyimpan pengguna baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'username'     => ['required', 'string', 'max:255', 'unique:users,username'],
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'     => ['required', 'string', 'min:8', 'confirmed'],
            'role'         => ['required', 'in:admin,umkm'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('admin.pengguna.index')
            ->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit pengguna.
     */
    public function edit($id)
    {
        $pengguna = User::findOrFail($id);
        return view('admin.pengguna.edit', compact('pengguna'));
    }

    /**
     * Memperbarui data pengguna.
     */
    public function update(Request $request, $id)
    {
        $pengguna = User::findOrFail($id);

        $rules = [
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'username'     => ['required', 'string', 'max:255', Rule::unique('users')->ignore($pengguna->id_user, 'id_user')],
            'email'        => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($pengguna->id_user, 'id_user')],
            'role'         => ['required', 'in:admin,umkm'],
        ];

        // Validasi password opsional (hanya jika diisi)
        if ($request->filled('password')) {
            $rules['password'] = ['string', 'min:8', 'confirmed'];
        }

        $validated = $request->validate($rules);

        // Proteksi: Cegah Admin menurunkan/mengubah role akunnya sendiri
        if (Auth::id() == $pengguna->id_user && $validated['role'] !== 'admin') {
            return back()->with('error', 'Anda tidak dapat mengubah role akun Anda sendiri menjadi UMKM.');
        }

        // Update password jika diisi
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $pengguna->update($validated);

        return redirect()->route('admin.pengguna.index')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Menghapus pengguna.
     */
    public function destroy($id)
    {
        $pengguna = User::findOrFail($id);

        // Proteksi 1: Jangan biarkan admin menghapus dirinya sendiri
        if (Auth::id() == $pengguna->id_user) {
            return redirect()->route('admin.pengguna.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        }

        // Proteksi 2: Cegah hapus UMKM jika sudah punya profil UMKM atau Pengajuan
        // (Soft delete bisa saja, tapi sesuai kesepakatan Hard Delete dengan pencegahan relasi)
        if ($pengguna->role === 'umkm') {
            if ($pengguna->umkm()->exists()) {
                return redirect()->route('admin.pengguna.index')
                    ->with('error', 'Tidak dapat menghapus akun ini karena UMKM terkait telah memiliki profil data/pengajuan terdaftar.');
            }
        }

        $pengguna->delete();

        return redirect()->route('admin.pengguna.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}
