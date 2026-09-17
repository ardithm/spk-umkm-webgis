<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proses;
use Illuminate\Http\Request;

class ProsesController extends Controller
{
    public function index()
    {
        $prosesList = Proses::orderByDesc('tanggal_proses')->paginate(10);
        return view('admin.proses.index', compact('prosesList'));
    }

    public function create()
    {
        return view('admin.proses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'periode' => 'required|string|max:255',
            'status' => 'required|in:Draft,Buka,Tutup',
            'passing_grade' => 'required|numeric|min:0|max:100',
            'kuota' => 'nullable|integer|min:1',
            'tanggal_proses' => 'required|date',
            'keterangan' => 'nullable|string',
            'deskripsi_alur' => 'nullable|string',
            'tgl_pendaftaran_mulai' => 'nullable|date',
            'tgl_pendaftaran_selesai' => 'nullable|date',
            'tgl_verifikasi_mulai' => 'nullable|date',
            'tgl_verifikasi_selesai' => 'nullable|date',
            'tgl_spk_mulai' => 'nullable|date',
            'tgl_spk_selesai' => 'nullable|date',
            'tgl_survei_mulai' => 'nullable|date',
            'tgl_survei_selesai' => 'nullable|date',
            'tgl_pengumuman' => 'nullable|date',
        ]);

        if ($validated['status'] === 'Buka') {
            Proses::where('status', 'Buka')->update(['status' => 'Tutup']);
        }

        Proses::create($validated);

        return redirect()->route('admin.proses.index')->with('success', 'Periode Bantuan Modal berhasil ditambahkan.');
    }

    public function show($id)
    {
        // For now, redirect to index or show details if needed.
        return redirect()->route('admin.proses.index');
    }

    public function edit($id)
    {
        $proses = Proses::findOrFail($id);
        return view('admin.proses.edit', compact('proses'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'periode' => 'required|string|max:255',
            'status' => 'required|in:Draft,Buka,Tutup',
            'passing_grade' => 'required|numeric|min:0|max:100',
            'kuota' => 'nullable|integer|min:1',
            'tanggal_proses' => 'required|date',
            'keterangan' => 'nullable|string',
            'deskripsi_alur' => 'nullable|string',
            'tgl_pendaftaran_mulai' => 'nullable|date',
            'tgl_pendaftaran_selesai' => 'nullable|date',
            'tgl_verifikasi_mulai' => 'nullable|date',
            'tgl_verifikasi_selesai' => 'nullable|date',
            'tgl_spk_mulai' => 'nullable|date',
            'tgl_spk_selesai' => 'nullable|date',
            'tgl_survei_mulai' => 'nullable|date',
            'tgl_survei_selesai' => 'nullable|date',
            'tgl_pengumuman' => 'nullable|date',
        ]);

        $proses = Proses::findOrFail($id);

        if ($validated['status'] === 'Buka') {
            Proses::where('id_proses', '!=', $proses->id_proses)->where('status', 'Buka')->update(['status' => 'Tutup']);
        }

        $proses->update($validated);

        return redirect()->route('admin.proses.index')->with('success', 'Periode Bantuan Modal berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $proses = Proses::findOrFail($id);
        // Delete only if no related calculations exist or we cascade delete.
        // For safety, let's just delete the record directly. If foreign keys fail, it will throw an exception.
        try {
            $proses->delete();
            return redirect()->route('admin.proses.index')->with('success', 'Periode Bantuan Modal berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.proses.index')->with('error', 'Gagal menghapus! Pastikan tidak ada data yang terikat dengan periode ini.');
        }
    }
}
