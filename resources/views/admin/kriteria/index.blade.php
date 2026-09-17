@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                    ⚙️ Modul Kriteria SPK
                </span>
                <span class="text-xs text-gray-400">PRD Section 3 & 6</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Kelola Kriteria Profile Matching</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                Konfigurasi standar parameter penilaian, target ideal instansi, dan pembagian kelas Core Factor (60%) serta Secondary Factor (40%).
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.konversi.index') }}" 
               class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition">
                <svg class="w-4 h-4 text-brand-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                Aturan Konversi Skor
            </a>
            <a href="{{ route('admin.kriteria.create') }}" 
               class="inline-flex items-center gap-2 bg-gradient-to-r from-brand-purple to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white font-bold py-2.5 px-4 rounded-xl text-xs sm:text-sm shadow-md shadow-purple-900/20 transition-all transform hover:-translate-y-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Kriteria Baru
            </a>
        </div>
    </div>

    <!-- Summary Stats Bar -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
            <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total Kriteria</span>
            <div class="text-2xl font-extrabold text-gray-900 mt-1">{{ $stats['total_kriteria'] }}</div>
            <p class="text-[11px] text-gray-500 mt-0.5">Parameter evaluasi aktif</p>
        </div>

        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
            <span class="text-[11px] font-bold text-purple-700 uppercase tracking-wider">Core Factor (60%)</span>
            <div class="text-2xl font-extrabold text-brand-purple mt-1">{{ $stats['core_factor'] }}</div>
            <p class="text-[11px] text-gray-500 mt-0.5">Aspek utama kompetensi</p>
        </div>

        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
            <span class="text-[11px] font-bold text-orange-700 uppercase tracking-wider">Secondary Factor (40%)</span>
            <div class="text-2xl font-extrabold text-brand-orange mt-1">{{ $stats['secondary_factor'] }}</div>
            <p class="text-[11px] text-gray-500 mt-0.5">Aspek penunjang</p>
        </div>

        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
            <span class="text-[11px] font-bold text-blue-700 uppercase tracking-wider">Rata-rata Target</span>
            <div class="text-2xl font-extrabold text-blue-600 mt-1">Skala {{ $stats['avg_target'] }}</div>
            <p class="text-[11px] text-gray-500 mt-0.5">Standar ideal instansi</p>
        </div>
    </div>

    <!-- Table of Criteria -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h3 class="font-bold text-gray-900 text-base">Daftar Parameter Acuan Seleksi</h3>
                <p class="text-xs text-gray-500 mt-0.5">Setiap nilai aktual kandidat akan dikonversi ke skor 1–5 lalu dihitung kesenjangannya (GAP) terhadap target ideal.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-400 font-medium">Formula Akhir:</span>
                <code class="px-2.5 py-1 rounded-lg bg-gray-100 text-[11px] font-bold text-gray-800">
                    Nilai = (60% × NCF) + (40% × NSF)
                </code>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/80">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Parameter Kriteria</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Target Ideal Dinas</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Jenis Faktor</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Bobot Pengaruh</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100 text-xs">
                    @forelse($kriteriaList as $k)
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 rounded-lg bg-purple-50 text-brand-purple font-extrabold font-mono text-xs border border-purple-100">
                                {{ $k->kode_kriteria }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-bold text-gray-900 text-sm">{{ $k->nama_kriteria }}</div>
                            <a href="{{ route('admin.konversi.index', ['kriteria' => $k->id_kriteria]) }}" 
                               class="text-[11px] text-brand-purple hover:underline font-medium inline-flex items-center gap-1 mt-0.5">
                                <span>{{ $k->konversi_nilai_count > 0 ? $k->konversi_nilai_count . ' rentang konversi skor' : 'Belum diatur rentang nilai (+)' }}</span>
                                <span>&rarr;</span>
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <span class="font-extrabold text-sm text-gray-800">Skala {{ $k->target_ideal }}</span>
                                <div class="flex gap-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="w-2 h-2 rounded-full {{ $i <= $k->target_ideal ? 'bg-brand-purple' : 'bg-gray-200' }}"></span>
                                    @endfor
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($k->jenis_faktor === 'core')
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-purple-50 text-brand-purple border border-purple-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-brand-purple"></span>
                                    Core Factor (CF)
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-orange-50 text-brand-orange border border-orange-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-brand-orange"></span>
                                    Secondary Factor (SF)
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-700">
                            {{ $k->jenis_faktor === 'core' ? '60% Komputasi' : '40% Komputasi' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.konversi.index', ['kriteria' => $k->id_kriteria]) }}" 
                                   class="p-2 rounded-lg text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 transition"
                                   title="Kelola Konversi Skor {{ $k->kode_kriteria }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                </a>
                                <a href="{{ route('admin.kriteria.edit', $k->id_kriteria) }}" 
                                   class="p-2 rounded-lg text-gray-500 hover:text-brand-purple hover:bg-purple-50 transition"
                                   title="Ubah Kriteria">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <form action="{{ route('admin.kriteria.destroy', $k->id_kriteria) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus kriteria {{ $k->kode_kriteria }} ({{ $k->nama_kriteria }})? Tindakan ini tidak dapat dibatalkan.')" 
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="p-2 rounded-lg text-gray-400 hover:text-rose-600 hover:bg-rose-50 transition"
                                            title="Hapus Kriteria">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            <span class="text-3xl block mb-2">📋</span>
                            <p class="font-semibold text-gray-700">Belum ada kriteria penilaian</p>
                            <p class="text-xs text-gray-400 mt-1">Silakan klik tombol "Tambah Kriteria Baru" di atas untuk menambahkan parameter seleksi.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Table of GAP Weights Reference -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center gap-2 mb-3">
            <span class="text-base">📐</span>
            <h3 class="font-bold text-gray-900 text-sm">Tabel Standar Pembobotan Nilai GAP (Profile Matching)</h3>
        </div>
        <p class="text-xs text-gray-500 mb-4">
            Tingkat kesenjangan (GAP) dihitung dengan rumus: <strong>GAP = Skor Aktual − Nilai Target Ideal</strong>. Selisih tersebut kemudian dikonversikan ke bobot interpolasi berikut:
        </p>

        <div class="grid grid-cols-3 sm:grid-cols-5 lg:grid-cols-9 gap-2 text-center text-xs">
            <div class="p-2.5 rounded-xl bg-purple-50 border border-purple-200">
                <span class="text-gray-500 block text-[10px]">Selisih GAP</span>
                <span class="font-extrabold text-sm text-brand-purple">0</span>
                <span class="text-[11px] font-bold text-gray-800 block mt-1">Bobot: 5.0</span>
                <span class="text-[9px] text-gray-400">Sesuai Profil</span>
            </div>
            <div class="p-2.5 rounded-xl bg-gray-50 border border-gray-200">
                <span class="text-gray-500 block text-[10px]">Selisih GAP</span>
                <span class="font-extrabold text-sm text-gray-800">+1</span>
                <span class="text-[11px] font-bold text-gray-800 block mt-1">Bobot: 4.5</span>
                <span class="text-[9px] text-gray-400">Lebih 1 tingkat</span>
            </div>
            <div class="p-2.5 rounded-xl bg-gray-50 border border-gray-200">
                <span class="text-gray-500 block text-[10px]">Selisih GAP</span>
                <span class="font-extrabold text-sm text-gray-800">-1</span>
                <span class="text-[11px] font-bold text-gray-800 block mt-1">Bobot: 4.0</span>
                <span class="text-[9px] text-gray-400">Kurang 1 tingkat</span>
            </div>
            <div class="p-2.5 rounded-xl bg-gray-50 border border-gray-200">
                <span class="text-gray-500 block text-[10px]">Selisih GAP</span>
                <span class="font-extrabold text-sm text-gray-800">+2</span>
                <span class="text-[11px] font-bold text-gray-800 block mt-1">Bobot: 3.5</span>
                <span class="text-[9px] text-gray-400">Lebih 2 tingkat</span>
            </div>
            <div class="p-2.5 rounded-xl bg-gray-50 border border-gray-200">
                <span class="text-gray-500 block text-[10px]">Selisih GAP</span>
                <span class="font-extrabold text-sm text-gray-800">-2</span>
                <span class="text-[11px] font-bold text-gray-800 block mt-1">Bobot: 3.0</span>
                <span class="text-[9px] text-gray-400">Kurang 2 tingkat</span>
            </div>
            <div class="p-2.5 rounded-xl bg-gray-50 border border-gray-200">
                <span class="text-gray-500 block text-[10px]">Selisih GAP</span>
                <span class="font-extrabold text-sm text-gray-800">+3</span>
                <span class="text-[11px] font-bold text-gray-800 block mt-1">Bobot: 2.5</span>
                <span class="text-[9px] text-gray-400">Lebih 3 tingkat</span>
            </div>
            <div class="p-2.5 rounded-xl bg-gray-50 border border-gray-200">
                <span class="text-gray-500 block text-[10px]">Selisih GAP</span>
                <span class="font-extrabold text-sm text-gray-800">-3</span>
                <span class="text-[11px] font-bold text-gray-800 block mt-1">Bobot: 2.0</span>
                <span class="text-[9px] text-gray-400">Kurang 3 tingkat</span>
            </div>
            <div class="p-2.5 rounded-xl bg-gray-50 border border-gray-200">
                <span class="text-gray-500 block text-[10px]">Selisih GAP</span>
                <span class="font-extrabold text-sm text-gray-800">+4</span>
                <span class="text-[11px] font-bold text-gray-800 block mt-1">Bobot: 1.5</span>
                <span class="text-[9px] text-gray-400">Lebih 4 tingkat</span>
            </div>
            <div class="p-2.5 rounded-xl bg-gray-50 border border-gray-200">
                <span class="text-gray-500 block text-[10px]">Selisih GAP</span>
                <span class="font-extrabold text-sm text-gray-800">-4</span>
                <span class="text-[11px] font-bold text-gray-800 block mt-1">Bobot: 1.0</span>
                <span class="text-[9px] text-gray-400">Kurang 4 tingkat</span>
            </div>
        </div>
    </div>
</div>
@endsection
