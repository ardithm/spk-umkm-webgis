<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KonversiNilai;
use App\Models\Kriteria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * KonversiController — Modul Kelola Konversi Skor Kriteria
 *
 * Mengelola pemetaan rentang nilai data aktual UMKM (omzet, aset, jumlah tenaga kerja, dll)
 * menjadi skor standar kualitatif (skala 1–5) yang digunakan dalam perhitungan SPK Profile Matching.
 *
 * Sesuai PRD Section 3 (Modul SPK) dan Section 6 (Database Schema: konversi_nilai).
 */
class KonversiController extends Controller
{
    /**
     * Tampilkan daftar seluruh aturan konversi nilai per kriteria.
     */
    public function index(Request $request): View
    {
        $selectedKriteriaId = $request->query('kriteria');

        $kriteriaList = Kriteria::withCount('konversiNilai')
            ->orderBy('kode_kriteria')
            ->get();

        $query = KonversiNilai::with('kriteria')
            ->orderBy('id_kriteria')
            ->orderBy('skor', 'asc');

        if (!empty($selectedKriteriaId) && $selectedKriteriaId !== 'semua') {
            $query->where('id_kriteria', $selectedKriteriaId);
        }

        $konversiList = $query->get();

        // Kelompokkan per kriteria agar tampilan lebih terstruktur
        $groupedKonversi = $konversiList->groupBy('id_kriteria');

        $stats = [
            'total_aturan'       => KonversiNilai::count(),
            'total_kriteria'     => $kriteriaList->count(),
            'kriteria_terisi'    => $kriteriaList->where('konversi_nilai_count', '>', 0)->count(),
            'kriteria_kosong'    => $kriteriaList->where('konversi_nilai_count', 0)->count(),
        ];

        return view('admin.konversi.index', compact(
            'kriteriaList',
            'konversiList',
            'groupedKonversi',
            'selectedKriteriaId',
            'stats'
        ));
    }

    /**
     * Tampilkan formulir penambahan aturan konversi nilai baru.
     */
    public function create(Request $request): View
    {
        $selectedKriteriaId = $request->query('id_kriteria');
        $kriteriaList = Kriteria::orderBy('kode_kriteria')->get();

        if ($kriteriaList->isEmpty()) {
            return redirect()->route('admin.kriteria.create')->with('error', 
                '⚠️ Harap tambahkan Kriteria SPK terlebih dahulu sebelum membuat aturan konversi nilai.'
            );
        }

        return view('admin.konversi.create', compact('kriteriaList', 'selectedKriteriaId'));
    }

    /**
     * Simpan aturan konversi baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_kriteria' => 'required|exists:kriteria,id_kriteria',
            'nilai_min'   => 'required|numeric|min:0',
            'nilai_max'   => 'required|numeric|gte:nilai_min',
            'skor'        => 'required|integer|min:1|max:5',
        ], [
            'id_kriteria.required' => 'Kriteria acuan wajib dipilih.',
            'id_kriteria.exists'   => 'Kriteria yang dipilih tidak valid.',
            'nilai_min.required'   => 'Batas bawah rentang nilai (nilai min) wajib diisi.',
            'nilai_min.numeric'    => 'Batas bawah rentang harus berupa angka numerik.',
            'nilai_min.min'        => 'Batas bawah tidak boleh bernilai negatif.',
            'nilai_max.required'   => 'Batas atas rentang nilai (nilai max) wajib diisi.',
            'nilai_max.numeric'    => 'Batas atas rentang harus berupa angka numerik.',
            'nilai_max.gte'        => 'Batas atas rentang (nilai max) harus lebih besar atau sama dengan batas bawah (nilai min).',
            'skor.required'        => 'Skor pemetaan (1–5) wajib ditentukan.',
            'skor.integer'         => 'Skor pemetaan harus berupa bilangan bulat.',
            'skor.min'             => 'Skor minimal bernilai 1.',
            'skor.max'             => 'Skor maksimal bernilai 5.',
        ]);

        $konversi = KonversiNilai::create($validated);
        $kriteria = Kriteria::find($validated['id_kriteria']);

        return redirect()->route('admin.konversi.index', ['kriteria' => $validated['id_kriteria']])->with('success', 
            "✅ Aturan konversi baru untuk <strong>{$kriteria->kode_kriteria} ({$kriteria->nama_kriteria})</strong> dengan skor <strong>{$konversi->skor}</strong> berhasil ditambahkan."
        );
    }

    /**
     * Tampilkan formulir pengubahan aturan konversi.
     */
    public function edit(int $id): View
    {
        $konversi = KonversiNilai::with('kriteria')->findOrFail($id);
        $kriteriaList = Kriteria::orderBy('kode_kriteria')->get();

        return view('admin.konversi.edit', compact('konversi', 'kriteriaList'));
    }

    /**
     * Perbarui aturan konversi di database.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $konversi = KonversiNilai::findOrFail($id);

        $validated = $request->validate([
            'id_kriteria' => 'required|exists:kriteria,id_kriteria',
            'nilai_min'   => 'required|numeric|min:0',
            'nilai_max'   => 'required|numeric|gte:nilai_min',
            'skor'        => 'required|integer|min:1|max:5',
        ], [
            'id_kriteria.required' => 'Kriteria acuan wajib dipilih.',
            'nilai_min.required'   => 'Batas bawah rentang nilai wajib diisi.',
            'nilai_min.gte'        => 'Batas bawah tidak boleh negatif.',
            'nilai_max.required'   => 'Batas atas rentang nilai wajib diisi.',
            'nilai_max.gte'        => 'Batas atas harus lebih besar atau sama dengan batas bawah.',
            'skor.required'        => 'Skor pemetaan wajib ditentukan.',
            'skor.min'             => 'Skor minimal 1.',
            'skor.max'             => 'Skor maksimal 5.',
        ]);

        $konversi->update($validated);

        return redirect()->route('admin.konversi.index', ['kriteria' => $validated['id_kriteria']])->with('success', 
            "✅ Aturan konversi untuk <strong>{$konversi->kriteria?->kode_kriteria}</strong> berhasil diperbarui."
        );
    }

    /**
     * Hapus aturan konversi dari database.
     */
    public function destroy(int $id): RedirectResponse
    {
        $konversi = KonversiNilai::with('kriteria')->findOrFail($id);
        $kodeKriteria = $konversi->kriteria?->kode_kriteria;
        $skor = $konversi->skor;

        $konversi->delete();

        return back()->with('success', 
            "🗑️ Aturan konversi untuk <strong>{$kodeKriteria}</strong> (Skor: {$skor}) berhasil dihapus."
        );
    }
}
