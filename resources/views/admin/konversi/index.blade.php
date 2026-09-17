@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                    📊 Modul Konversi Skor Kriteria
                </span>
                <span class="text-xs text-gray-400">PRD Section 3 & 6</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Kelola Aturan Konversi Nilai & Skor</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                Pengaturan interval rentang data aktual UMKM (omzet, aset, SDM, dll) menjadi skor standar 1–5 untuk perhitungan kesenjangan (GAP) Profile Matching.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.kriteria.index') }}" 
               class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition">
                &larr; Data Kriteria
            </a>
            <a href="{{ route('admin.konversi.create', ['id_kriteria' => $selectedKriteriaId]) }}" 
               class="inline-flex items-center gap-2 bg-gradient-to-r from-brand-purple to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white font-bold py-2.5 px-4 rounded-xl text-xs sm:text-sm shadow-md shadow-purple-900/20 transition-all transform hover:-translate-y-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Aturan Konversi
            </a>
        </div>
    </div>

    <!-- Summary Stats Bar -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
            <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total Aturan Konversi</span>
            <div class="text-2xl font-extrabold text-gray-900 mt-1">{{ $stats['total_aturan'] }}</div>
            <p class="text-[11px] text-gray-500 mt-0.5">Rentang skor terdaftar</p>
        </div>

        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
            <span class="text-[11px] font-bold text-blue-700 uppercase tracking-wider">Total Kriteria Acuan</span>
            <div class="text-2xl font-extrabold text-blue-600 mt-1">{{ $stats['total_kriteria'] }}</div>
            <p class="text-[11px] text-gray-500 mt-0.5">Parameter penilaian SPK</p>
        </div>

        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
            <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Kriteria Terkonfigurasi</span>
            <div class="text-2xl font-extrabold text-emerald-600 mt-1">{{ $stats['kriteria_terisi'] }}</div>
            <p class="text-[11px] text-gray-500 mt-0.5">Sudah memiliki rentang skor</p>
        </div>

        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
            <span class="text-[11px] font-bold text-amber-700 uppercase tracking-wider">Kriteria Belum Diatur</span>
            <div class="text-2xl font-extrabold text-amber-600 mt-1">{{ $stats['kriteria_kosong'] }}</div>
            <p class="text-[11px] text-gray-500 mt-0.5">Perlu konfigurasi skor</p>
        </div>
    </div>

    <!-- Filter by Kriteria Toolbar -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2 w-full sm:w-auto overflow-x-auto pb-2 sm:pb-0">
            <span class="text-xs font-bold text-gray-500 whitespace-nowrap">Filter Kriteria:</span>
            <a href="{{ route('admin.konversi.index') }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all {{ empty($selectedKriteriaId) || $selectedKriteriaId === 'semua' ? 'bg-brand-purple text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
                Semua Kriteria
            </a>
            @foreach($kriteriaList as $kr)
            <a href="{{ route('admin.konversi.index', ['kriteria' => $kr->id_kriteria]) }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all {{ $selectedKriteriaId == $kr->id_kriteria ? 'bg-brand-purple text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
                {{ $kr->kode_kriteria }} ({{ $kr->konversi_nilai_count }})
            </a>
            @endforeach
        </div>

        <div class="text-xs text-gray-400">
            Menampilkan <strong class="text-gray-700">{{ $konversiList->count() }}</strong> aturan
        </div>
    </div>

    <!-- Grouped Rules per Criterion -->
    <div class="space-y-6">
        @forelse($kriteriaList as $kriteria)
            @if(empty($selectedKriteriaId) || $selectedKriteriaId === 'semua' || $selectedKriteriaId == $kriteria->id_kriteria)
            @php
                $rules = $groupedKonversi->get($kriteria->id_kriteria, collect());
            @endphp
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <!-- Criterion Header -->
                <div class="p-5 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1.5 rounded-xl bg-brand-purple text-white font-extrabold font-mono text-xs shadow-xs">
                            {{ $kriteria->kode_kriteria }}
                        </span>
                        <div>
                            <h3 class="font-bold text-gray-900 text-sm sm:text-base flex items-center gap-2">
                                <span>{{ $kriteria->nama_kriteria }}</span>
                                @if($kriteria->jenis_faktor === 'core')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-700">Core Factor (60%)</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-orange-100 text-orange-700">Secondary Factor (40%)</span>
                                @endif
                            </h3>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Target Ideal Instansi: <strong class="text-gray-800">Skala {{ $kriteria->target_ideal }}</strong>
                                @if($kriteria->target_ideal == 3) (Cukup) @elseif($kriteria->target_ideal >= 4) (Tinggi) @endif
                            </p>
                        </div>
                    </div>

                    <a href="{{ route('admin.konversi.create', ['id_kriteria' => $kriteria->id_kriteria]) }}" 
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-gray-200 text-xs font-bold text-brand-purple hover:border-brand-purple hover:bg-purple-50 transition shadow-2xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Rentang {{ $kriteria->kode_kriteria }}
                    </a>
                </div>

                <!-- Rules Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                        <thead class="bg-gray-50/60">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider">Batas Bawah (Nilai Min)</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider">Batas Atas (Nilai Max)</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider">Rentang Nilai Aktual</th>
                                <th class="px-6 py-3 text-center font-semibold text-gray-500 uppercase tracking-wider">Skor Pemetaan</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider">GAP terhadap Target ({{ $kriteria->target_ideal }})</th>
                                <th class="px-6 py-3 text-right font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($rules as $r)
                            @php
                                $gap = $r->skor - $kriteria->target_ideal;
                            @endphp
                            <tr class="hover:bg-gray-50/60 transition-colors">
                                <td class="px-6 py-3.5 whitespace-nowrap font-mono font-medium text-gray-800">
                                    {{ number_format($r->nilai_min, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap font-mono font-medium text-gray-800">
                                    {{ number_format($r->nilai_max, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-gray-700">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-gray-50 font-mono text-[11px] border border-gray-200">
                                        {{ number_format($r->nilai_min, 0, ',', '.') }} s/d {{ number_format($r->nilai_max, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center justify-center gap-1 px-3 py-1 rounded-full font-bold text-xs
                                        @if($r->skor == 5) bg-emerald-100 text-emerald-800 border border-emerald-200
                                        @elseif($r->skor == 4) bg-blue-100 text-blue-800 border border-blue-200
                                        @elseif($r->skor == 3) bg-purple-100 text-purple-800 border border-purple-200
                                        @elseif($r->skor == 2) bg-amber-100 text-amber-800 border border-amber-200
                                        @else bg-rose-100 text-rose-800 border border-rose-200 @endif">
                                        ★ Skor {{ $r->skor }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap font-semibold">
                                    @if($gap === 0)
                                        <span class="text-emerald-600 font-bold">GAP 0 (Ideal Sesuai Target)</span>
                                    @elseif($gap > 0)
                                        <span class="text-blue-600">GAP +{{ $gap }} (Melebihi {{ $gap }} tingkat)</span>
                                    @else
                                        <span class="text-amber-600">GAP {{ $gap }} (Kurang {{ abs($gap) }} tingkat)</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.konversi.edit', $r->id_konversi) }}" 
                                           class="p-1.5 rounded-lg text-gray-400 hover:text-brand-purple hover:bg-purple-50 transition" 
                                           title="Ubah Aturan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        <form action="{{ route('admin.konversi.destroy', $r->id_konversi) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus aturan konversi rentang ini?')" 
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="p-1.5 rounded-lg text-gray-400 hover:text-rose-600 hover:bg-rose-50 transition" 
                                                    title="Hapus Aturan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                                    <p class="font-medium text-gray-600">Belum ada aturan konversi skor untuk {{ $kriteria->kode_kriteria }}.</p>
                                    <p class="text-[11px] text-gray-400 mt-1">Data aktual pemohon belum dapat dikonversi ke skor 1–5 secara otomatis.</p>
                                    <a href="{{ route('admin.konversi.create', ['id_kriteria' => $kriteria->id_kriteria]) }}" 
                                       class="inline-flex items-center gap-1 mt-3 text-brand-purple font-bold hover:underline">
                                        + Buat aturan skor untuk {{ $kriteria->kode_kriteria }} sekarang
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        @empty
        <div class="bg-white rounded-2xl p-12 text-center text-gray-400 shadow-sm border border-gray-100">
            <span class="text-3xl block mb-2">📊</span>
            <p class="font-semibold text-gray-700">Belum ada data kriteria acuan</p>
            <p class="text-xs text-gray-400 mt-1">Silakan tambahkan data kriteria SPK terlebih dahulu.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
