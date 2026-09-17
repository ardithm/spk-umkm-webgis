@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-orange-50 text-brand-orange border border-orange-100">
                 Modul Verifikasi Dokumen
                </span>
    
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Validasi Berkas Persyaratan UMKM</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                Pemeriksaan keabsahan dokumen wajib (KTP, KK, NIB, SKU, Foto Fisik) sebelum kandidat diikutsertakan dalam SPK Profile Matching.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.spk.index') }}" class="inline-flex items-center gap-2 bg-purple-50 hover:bg-purple-100 text-brand-purple font-semibold py-2.5 px-4 rounded-xl text-xs transition">
                Lanjut ke Mesin SPK &rarr;
            </a>
        </div>
    </div>

    <!-- Summary Stats Bar -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <a href="{{ route('admin.dokumen.index', ['status' => 'semua']) }}" 
           class="bg-white rounded-2xl p-4 border transition-all hover:shadow-sm {{ $filterStatus === 'semua' ? 'border-brand-purple ring-2 ring-brand-purple/20' : 'border-gray-100' }}">
            <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total Masuk</span>
            <div class="text-2xl font-extrabold text-gray-900 mt-1">{{ $stats['total_berkas_masuk'] }}</div>
            <p class="text-[11px] text-gray-500 mt-0.5">Semua permohonan</p>
        </a>

        <a href="{{ route('admin.dokumen.index', ['status' => 'menunggu']) }}" 
           class="bg-white rounded-2xl p-4 border transition-all hover:shadow-sm {{ $filterStatus === 'menunggu' ? 'border-amber-400 ring-2 ring-amber-400/20' : 'border-gray-100' }}">
            <span class="text-[11px] font-bold text-amber-600 uppercase tracking-wider flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                Menunggu Validasi
            </span>
            <div class="text-2xl font-extrabold text-amber-600 mt-1">{{ $stats['menunggu_verifikasi'] }}</div>
            <p class="text-[11px] text-gray-500 mt-0.5">Perlu ditinjau admin</p>
        </a>

        <a href="{{ route('admin.dokumen.index', ['status' => 'revisi']) }}" 
           class="bg-white rounded-2xl p-4 border transition-all hover:shadow-sm {{ $filterStatus === 'revisi' ? 'border-rose-400 ring-2 ring-rose-400/20' : 'border-gray-100' }}">
            <span class="text-[11px] font-bold text-rose-600 uppercase tracking-wider">Perlu Revisi</span>
            <div class="text-2xl font-extrabold text-rose-600 mt-1">{{ $stats['perlu_revisi'] }}</div>
            <p class="text-[11px] text-gray-500 mt-0.5">Ditolak dengan catatan</p>
        </a>

        <a href="{{ route('admin.dokumen.index', ['status' => 'terverifikasi']) }}" 
           class="bg-white rounded-2xl p-4 border transition-all hover:shadow-sm {{ $filterStatus === 'terverifikasi' ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-gray-100' }}">
            <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Terverifikasi</span>
            <div class="text-2xl font-extrabold text-emerald-600 mt-1">{{ $stats['terverifikasi'] }}</div>
            <p class="text-[11px] text-gray-500 mt-0.5">Lengkap & siap SPK</p>
        </a>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
        <!-- Status Filter Tabs -->
        <div class="flex items-center gap-1 overflow-x-auto w-full sm:w-auto pb-2 sm:pb-0">
            @foreach([
                'semua' => 'Semua Status',
                'menunggu' => 'Menunggu',
                'revisi' => 'Perlu Revisi',
                'terverifikasi' => 'Terverifikasi'
            ] as $key => $label)
            <a href="{{ route('admin.dokumen.index', array_merge(request()->query(), ['status' => $key])) }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all {{ $filterStatus === $key ? 'bg-brand-purple text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
                {{ $label }}
            </a>
            @endforeach
        </div>

        <!-- Search Form -->
        <form action="{{ route('admin.dokumen.index') }}" method="GET" class="w-full sm:w-80 flex items-center gap-2">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div class="relative flex-1">
                <input type="text" 
                       name="q" 
                       value="{{ $search }}" 
                       placeholder="Cari UMKM, pemilik, NIK..." 
                       class="w-full pl-9 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-purple/30 focus:border-brand-purple transition">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <button type="submit" class="bg-gray-900 text-white px-3 py-2 rounded-xl text-xs font-bold hover:bg-gray-800 transition">
                Cari
            </button>
            @if($search)
            <a href="{{ route('admin.dokumen.index', ['status' => $filterStatus]) }}" class="text-xs text-gray-400 hover:text-gray-600 p-2" title="Reset pencarian">
                ✕
            </a>
            @endif
        </form>
    </div>

    <!-- Table of Submissions -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/80">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kandidat UMKM</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pemilik / NIK</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Progress 5 Berkas Wajib</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal Masuk</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status Berkas</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100 text-xs">
                    @forelse($pengajuanList as $p)
                    @php
                        $dokumenByJenis = $p->dokumen->keyBy('jenis_dokumen');
                        $jenisWajib = ['KTP', 'KK', 'NIB', 'SKU', 'FOTO'];
                        $disetujuiCount = $p->dokumen->where('status_verifikasi', 'disetujui')->count();
                        $ditolakCount = $p->dokumen->where('status_verifikasi', 'ditolak')->count();
                        $menungguCount = $p->dokumen->where('status_verifikasi', 'menunggu')->count();
                    @endphp
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-bold text-gray-900 text-sm">{{ $p->umkm?->nama_umkm ?? 'UMKM #'.$p->id_pengajuan }}</div>
                            <div class="text-[11px] text-gray-500 line-clamp-1 max-w-xs mt-0.5">{{ $p->umkm?->alamat ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-semibold text-gray-800">{{ $p->umkm?->nama_pemilik ?? '-' }}</div>
                            <div class="text-[11px] text-gray-500 font-mono mt-0.5">
                                NIK: {{ $p->umkm?->nik ?? '-' }} | Telp: {{ $p->umkm?->no_telepon ?? '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <!-- Visual Icons of 5 Documents -->
                            <div class="flex items-center gap-1.5 mb-1.5">
                                @foreach($jenisWajib as $jw)
                                    @php $dok = $dokumenByJenis->get($jw); @endphp
                                    @if(!$dok)
                                        <span class="w-6 h-6 rounded flex items-center justify-center text-[10px] font-bold bg-gray-100 text-gray-400 border border-gray-200" title="{{ $jw }}: Belum diunggah">
                                            {{ substr($jw, 0, 1) }}
                                        </span>
                                    @elseif($dok->status_verifikasi === 'disetujui')
                                        <span class="w-6 h-6 rounded flex items-center justify-center text-[10px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-300" title="{{ $jw }}: Disetujui">
                                            ✓
                                        </span>
                                    @elseif($dok->status_verifikasi === 'ditolak')
                                        <span class="w-6 h-6 rounded flex items-center justify-center text-[10px] font-bold bg-rose-100 text-rose-700 border border-rose-300" title="{{ $jw }}: Ditolak (Perlu revisi)">
                                            ✗
                                        </span>
                                    @else
                                        <span class="w-6 h-6 rounded flex items-center justify-center text-[10px] font-bold bg-amber-100 text-amber-700 border border-amber-300" title="{{ $jw }}: Menunggu verifikasi">
                                            ⏳
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                            <div class="text-[10px] text-gray-500">
                                <span class="font-bold text-emerald-600">{{ $disetujuiCount }}/5 Disetujui</span>
                                @if($ditolakCount > 0)
                                    <span class="text-rose-600 font-medium">| {{ $ditolakCount }} ditolak</span>
                                @endif
                                @if($menungguCount > 0)
                                    <span class="text-amber-600 font-medium">| {{ $menungguCount }} pending</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                            {{ $p->tanggal_pengajuan ? $p->tanggal_pengajuan->format('d M Y H:i') : ($p->created_at ? $p->created_at->format('d M Y') : '-') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($p->status === 'terverifikasi')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Terverifikasi
                                </span>
                            @elseif($p->status === 'menunggu')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    Menunggu Verifikasi
                                </span>
                            @elseif($p->status === 'revisi')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                    Perlu Revisi
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-gray-100 text-gray-700">
                                    {{ ucfirst($p->status) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right font-medium">
                            <a href="{{ route('admin.dokumen.show', $p->id_pengajuan) }}" 
                               class="inline-flex items-center gap-1.5 bg-gradient-to-r from-brand-purple to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white px-3.5 py-1.5 rounded-xl text-xs font-bold shadow-sm transition-all transform hover:-translate-y-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                Tinjau Berkas &rarr;
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            <div class="max-w-sm mx-auto">
                                <span class="text-3xl block mb-2">📁</span>
                                <p class="font-semibold text-gray-700">Tidak ada data berkas pengajuan</p>
                                <p class="text-xs text-gray-400 mt-1">Belum ada pengajuan UMKM dengan kriteria pencarian atau status yang dipilih.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pengajuanList->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $pengajuanList->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
