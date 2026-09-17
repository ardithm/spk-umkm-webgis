@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Perankingan Hasil SPK</h1>
            <p class="text-sm text-gray-500 mt-1">
                Periode: <span class="font-semibold text-gray-700">{{ $proses->periode }}</span> | 
                Passing Grade: <span class="font-semibold text-brand-purple">{{ number_format($proses->passing_grade, 2) }}</span> |
                Kuota: <span class="font-semibold text-brand-purple">{{ $proses->kuota ?? 'Tidak Dibatasi' }}</span>
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.laporan.pdf', ['id' => $proses->id_proses, 'status' => $filterStatus ?? 'semua', 'preview' => 1]) }}" 
               target="_blank" 
               class="text-xs font-bold text-gray-700 hover:text-brand-purple border border-gray-300 px-3.5 py-2 rounded-xl bg-white shadow-sm flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Cetak PDF
            </a>
            <a href="{{ route('admin.laporan.excel', ['id' => $proses->id_proses, 'status' => $filterStatus ?? 'semua']) }}" 
               class="text-xs font-bold text-emerald-700 hover:bg-emerald-50 border border-emerald-300 px-3.5 py-2 rounded-xl bg-white shadow-sm flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Ekspor Excel
            </a>
            <a href="{{ route('admin.spk.hasil', $proses->id_proses) }}" class="text-xs font-bold text-brand-purple hover:bg-brand-purple/10 border border-brand-purple px-3.5 py-2 rounded-xl bg-white">Detail Per Kriteria</a>
            <a href="{{ route('admin.spk.index') }}" class="text-xs font-bold text-gray-600 hover:text-gray-900 border border-gray-300 px-3.5 py-2 rounded-xl bg-white">&larr; Kembali</a>
        </div>
    </div>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <a href="{{ route('admin.spk.ranking', $proses->id_proses) }}" class="bg-white p-4 rounded-xl border {{ !$filterStatus ? 'border-brand-purple ring-1 ring-brand-purple' : 'border-gray-200' }} hover:shadow-md transition-shadow">
        <div class="text-sm text-gray-500 font-medium mb-1">Total Dievaluasi</div>
        <div class="text-2xl font-bold text-gray-900">{{ $summary->sum() }}</div>
    </a>
    <a href="{{ route('admin.spk.ranking', ['id_proses' => $proses->id_proses, 'filter' => 'diterima']) }}" class="bg-white p-4 rounded-xl border {{ $filterStatus == 'diterima' ? 'border-green-500 ring-1 ring-green-500' : 'border-gray-200' }} hover:shadow-md transition-shadow">
        <div class="text-sm text-green-600 font-medium mb-1">Diterima (Lolos)</div>
        <div class="text-2xl font-bold text-gray-900">{{ $summary->get('diterima', 0) }}</div>
    </a>
    <a href="{{ route('admin.spk.ranking', ['id_proses' => $proses->id_proses, 'filter' => 'cadangan']) }}" class="bg-white p-4 rounded-xl border {{ $filterStatus == 'cadangan' ? 'border-yellow-500 ring-1 ring-yellow-500' : 'border-gray-200' }} hover:shadow-md transition-shadow">
        <div class="text-sm text-yellow-600 font-medium mb-1">Cadangan (Luar Kuota)</div>
        <div class="text-2xl font-bold text-gray-900">{{ $summary->get('cadangan', 0) }}</div>
    </a>
    <a href="{{ route('admin.spk.ranking', ['id_proses' => $proses->id_proses, 'filter' => 'tidak_diterima']) }}" class="bg-white p-4 rounded-xl border {{ $filterStatus == 'tidak_diterima' ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' }} hover:shadow-md transition-shadow">
        <div class="text-sm text-red-600 font-medium mb-1">Tidak Diterima (< PG)</div>
        <div class="text-2xl font-bold text-gray-900">{{ $summary->get('tidak_diterima', 0) }}</div>
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-16">Rank</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Informasi UMKM</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">NCF (60%)</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">NSF (40%)</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-800 uppercase tracking-wider">Nilai Akhir</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Status Seleksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($hasilList as $hasil)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @if($hasil->ranking == 1)
                            <div class="w-8 h-8 mx-auto bg-yellow-100 text-yellow-700 rounded-full flex items-center justify-center font-bold shadow-sm">1</div>
                        @elseif($hasil->ranking == 2)
                            <div class="w-8 h-8 mx-auto bg-gray-200 text-gray-600 rounded-full flex items-center justify-center font-bold shadow-sm">2</div>
                        @elseif($hasil->ranking == 3)
                            <div class="w-8 h-8 mx-auto bg-brand-orange/20 text-brand-orange rounded-full flex items-center justify-center font-bold shadow-sm">3</div>
                        @else
                            <div class="font-semibold text-gray-500">{{ $hasil->ranking }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="font-bold text-gray-900 text-base">{{ $hasil->pengajuan->umkm->nama_umkm }}</div>
                        <div class="text-sm text-gray-500 mt-0.5">{{ $hasil->pengajuan->umkm->nama_pemilik }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-600">
                        {{ number_format($hasil->nilai_ncf, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-600">
                        {{ number_format($hasil->nilai_nsf, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="text-lg font-extrabold text-brand-purple">
                            {{ number_format($hasil->nilai_akhir, 3) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        @if($hasil->status_seleksi == 'diterima')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                ✓ Lolos Seleksi
                            </span>
                        @elseif($hasil->status_seleksi == 'cadangan')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800">
                                ⚠ Cadangan
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                ✕ Tidak Lolos
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        Belum ada data perankingan untuk status ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($hasilList->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $hasilList->links() }}
    </div>
    @endif
</div>
@endsection
