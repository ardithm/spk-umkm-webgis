<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * KriteriaController — Modul Kelola Kriteria SPK Profile Matching
 *
 * Mengelola parameter penilaian standar Dinas Koperasi & UMKM Kota Banjarmasin:
 * - Menampilkan daftar kriteria beserta target ideal dan jenis faktor (Core / Secondary)
 * - Menambah kriteria penilaian baru
 * - Mengubah bobot nilai target dan klasifikasi faktor
 * - Menghapus data kriteria
 *
 * Sesuai PRD Section 3 (Modul Sistem Pendukung Keputusan) & Section 6 (Database Schema: kriteria).
 */
class KriteriaController extends Controller
{
    /**
     * Tampilkan daftar seluruh kriteria penilaian SPK Profile Matching.
     */
    public function index(): View
    {
        $kriteriaList = Kriteria::withCount('konversiNilai')
            ->orderBy('kode_kriteria')
            ->get();

        $stats = [
            'total_kriteria'    => $kriteriaList->count(),
            'core_factor'       => $kriteriaList->where('jenis_faktor', Kriteria::FAKTOR_CORE)->count(),
            'secondary_factor'  => $kriteriaList->where('jenis_faktor', Kriteria::FAKTOR_SECONDARY)->count(),
            'avg_target'        => $kriteriaList->count() > 0 ? round($kriteriaList->avg('target_ideal'), 1) : 0,
        ];

        return view('admin.kriteria.index', compact('kriteriaList', 'stats'));
    }

    /**
     * Tampilkan formulir penambahan kriteria baru.
     */
    public function create(): View
    {
        // Prediksi kode kriteria berikutnya (contoh: K1, K2... K6)
        $total = Kriteria::count();
        $nextKode = 'K' . ($total + 1);

        return view('admin.kriteria.create', compact('nextKode'));
    }

    /**
     * Simpan kriteria baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_kriteria' => 'required|string|max:10|unique:kriteria,kode_kriteria',
            'nama_kriteria' => 'required|string|max:150',
            'target_ideal'  => 'required|integer|min:1|max:5',
            'jenis_faktor'  => 'required|in:core,secondary',
        ], [
            'kode_kriteria.required' => 'Kode kriteria wajib diisi (contoh: K1, K2, dst).',
            'kode_kriteria.unique'   => 'Kode kriteria ini sudah terdaftar di sistem. Gunakan kode lain.',
            'nama_kriteria.required' => 'Nama kriteria wajib diisi.',
            'target_ideal.required'  => 'Nilai target ideal instansi wajib ditentukan.',
            'target_ideal.min'       => 'Nilai target ideal minimal bernilai 1 (skala 1–5).',
            'target_ideal.max'       => 'Nilai target ideal maksimal bernilai 5 (skala 1–5).',
            'jenis_faktor.required'  => 'Jenis faktor Profile Matching (Core / Secondary) wajib dipilih.',
            'jenis_faktor.in'        => 'Pilihan jenis faktor tidak valid.',
        ]);

        // Format kode menjadi uppercase (contoh: k1 -> K1)
        $validated['kode_kriteria'] = strtoupper(trim($validated['kode_kriteria']));

        Kriteria::create($validated);

        return redirect()->route('admin.kriteria.index')->with('success', 
            "✅ Kriteria baru <strong>{$validated['kode_kriteria']} - {$validated['nama_kriteria']}</strong> berhasil ditambahkan."
        );
    }

    /**
     * Tampilkan formulir pengubahan kriteria.
     */
    public function edit(int $id): View
    {
        $kriteria = Kriteria::with('konversiNilai')->findOrFail($id);

        return view('admin.kriteria.edit', compact('kriteria'));
    }

    /**
     * Perbarui data kriteria di database.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $kriteria = Kriteria::findOrFail($id);

        $validated = $request->validate([
            'kode_kriteria' => 'required|string|max:10|unique:kriteria,kode_kriteria,' . $id . ',id_kriteria',
            'nama_kriteria' => 'required|string|max:150',
            'target_ideal'  => 'required|integer|min:1|max:5',
            'jenis_faktor'  => 'required|in:core,secondary',
        ], [
            'kode_kriteria.required' => 'Kode kriteria wajib diisi.',
            'kode_kriteria.unique'   => 'Kode kriteria ini sudah terdaftar di sistem.',
            'nama_kriteria.required' => 'Nama kriteria wajib diisi.',
            'target_ideal.required'  => 'Nilai target ideal wajib ditentukan.',
            'target_ideal.min'       => 'Nilai target ideal minimal bernilai 1.',
            'target_ideal.max'       => 'Nilai target ideal maksimal bernilai 5.',
            'jenis_faktor.required'  => 'Jenis faktor wajib dipilih.',
        ]);

        $validated['kode_kriteria'] = strtoupper(trim($validated['kode_kriteria']));

        $kriteria->update($validated);

        return redirect()->route('admin.kriteria.index')->with('success', 
            "✅ Data kriteria <strong>{$kriteria->kode_kriteria}</strong> berhasil diperbarui."
        );
    }

    /**
     * Hapus data kriteria dari sistem.
     */
    public function destroy(int $id): RedirectResponse
    {
        $kriteria = Kriteria::withCount(['konversiNilai', 'detailPenilaian'])->findOrFail($id);
        $kode = $kriteria->kode_kriteria;
        $nama = $kriteria->nama_kriteria;

        // Peringatan jika kriteria sudah digunakan dalam kalkulasi penilaian
        if ($kriteria->detail_penilaian_count > 0) {
            return back()->with('error', 
                "⚠️ Kriteria <strong>{$kode}</strong> tidak dapat dihapus karena telah terhubung dengan {$kriteria->detail_penilaian_count} data penilaian pengajuan UMKM."
            );
        }

        $kriteria->delete();

        return redirect()->route('admin.kriteria.index')->with('success', 
            "🗑️ Kriteria <strong>{$kode} - {$nama}</strong> berhasil dihapus dari sistem."
        );
    }
}
