@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <!-- Hero/Welcome Banner -->
    <div class="relative bg-gradient-to-r from-purple-50 via-white to-orange-50 rounded-[24px] p-8 sm:p-10 shadow-sm border border-brand-purple/10 overflow-hidden group hover:shadow-md transition-all duration-300">
        <!-- Decorative elements -->
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-brand-orange/5 rounded-full blur-3xl group-hover:bg-brand-orange/10 transition-all duration-500"></div>
        <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-brand-purple/5 rounded-full blur-3xl group-hover:bg-brand-purple/10 transition-all duration-500"></div>

        <div class="relative flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold bg-white border border-brand-purple/20 text-brand-purple shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    Portal Pelaku UMKM
                </span>
                <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight mt-2">
                    Selamat Datang, <span class="text-brand-purple">{{ $user->nama_lengkap }}</span>!
                </h1>
                <p class="text-sm md:text-base text-gray-600 max-w-2xl">
                    Kelola data usaha, pantau status permohonan bantuan modal, dan tingkatkan performa bisnis Anda bersama kami.
                </p>
            </div>

            <div class="shrink-0">
                <a href="{{ route('umkm.pengajuan.create') }}" class="inline-flex items-center justify-center gap-3 bg-brand-orange hover:bg-orange-500 text-white font-bold py-3.5 px-8 rounded-full shadow-lg shadow-brand-orange/30 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 focus:ring-4 focus:ring-brand-orange/20">
                    <span>Buat Pengajuan Bantuan</span>
                    <span class="bg-white/20 p-1.5 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </span>
                </a>
            </div>
        </div>
    </div>

    <!-- Alert Kelengkapan Profil (Jika belum lengkap) -->
    @if(!$umkm || !$umkm->isProfileComplete())
        <div class="bg-amber-50 border border-amber-200 rounded-[24px] p-5 sm:p-6 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 text-amber-900 animate-in fade-in">
            <div class="flex items-start gap-3.5">
                <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-amber-950">Lengkapi Data Profil Usaha Anda Terlebih Dahulu</h3>
                    <p class="text-xs text-amber-800 mt-0.5 leading-relaxed">
                        Akun Anda belum memiliki data profil usaha yang lengkap (Nama Usaha, NIK, No. Telepon, dan Alamat). Lengkapi data terlebih dahulu untuk dapat mengajukan permohonan bantuan modal.
                    </p>
                </div>
            </div>
            <a href="{{ route('umkm.akun.edit', ['tab' => 'profil']) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-sm shadow-amber-600/20 hover:shadow transition-all shrink-0">
                <span>Lengkapi Sekarang</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    @endif

    <!-- Section Title -->
    <div class="flex items-center gap-3 px-2">
        <h2 class="text-xl font-bold text-gray-900">Ringkasan Dasbor</h2>
        <div class="h-px bg-gray-200 flex-1"></div>
    </div>

    <!-- Status Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <!-- Card 1: Profil UMKM -->
        <div class="group bg-white rounded-[24px] p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:border-brand-purple/20 transition-all duration-300 flex flex-col h-full relative overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-brand-purple/5 rounded-bl-[100px] -z-10 transition-transform duration-500 group-hover:scale-110"></div>
            
            <div class="flex items-start gap-4 mb-5">
                <div class="w-12 h-12 shrink-0 rounded-[16px] bg-brand-purple/10 text-brand-purple flex items-center justify-center transition-transform duration-300 group-hover:scale-110 group-hover:bg-brand-purple group-hover:text-white shadow-inner">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9h18v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9Z"/><path d="m3 9 2.45-4.9A2 2 0 0 1 7.24 3h9.52a2 2 0 0 1 1.8 1.1L21 9"/><path d="M12 3v6"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 text-lg leading-tight">{{ $umkm->nama_umkm ?? 'Usaha Belum Diatur' }}</h3>
                    <p class="text-xs font-medium text-gray-500 mt-1 flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                        NIK: {{ $umkm->nik ?? 'Belum ada' }}
                    </p>
                </div>
            </div>

            <div class="space-y-3 text-sm text-gray-600 flex-1 flex flex-col justify-end border-t border-gray-100 pt-5">
                <div class="flex items-start gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-brand-purple shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <span class="truncate">{{ $umkm->nama_pemilik ?? 'Belum ada pemilik' }}</span>
                </div>
                <div class="flex items-start gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-brand-purple shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    <span>{{ $umkm->no_telepon ?? 'Belum ada kontak' }}</span>
                </div>
                <div class="flex items-start gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-brand-purple shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                    <span class="line-clamp-2 leading-relaxed">{{ $umkm->alamat ?? 'Belum ada alamat' }}</span>
                </div>
            </div>

            <div class="border-t border-gray-100 mt-4 pt-3 flex items-center justify-between">
                <span class="text-[11px] text-gray-400">Data Terakhir</span>
                <a href="{{ route('umkm.akun.edit') }}" class="inline-flex items-center gap-1 text-xs font-bold text-brand-purple hover:text-purple-700 transition-colors">
                    <span>Edit Profil Akun</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
        </div>

        <!-- Card 2: Status Pengajuan Terkini -->
        <div class="group bg-white rounded-[24px] p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:border-brand-orange/20 transition-all duration-300 flex flex-col h-full relative overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-brand-orange/5 rounded-bl-[100px] -z-10 transition-transform duration-500 group-hover:scale-110"></div>
            
            <div class="flex items-start gap-4 mb-5">
                <div class="w-12 h-12 shrink-0 rounded-[16px] bg-brand-orange/10 text-brand-orange flex items-center justify-center transition-transform duration-300 group-hover:scale-110 group-hover:bg-brand-orange group-hover:text-white shadow-inner">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/><path d="M10 9H8"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 text-lg leading-tight">Status Pengajuan</h3>
                    <p class="text-xs font-medium text-gray-500 mt-1">Periode 2025/2026</p>
                </div>
            </div>
            
            <div class="flex-1 flex flex-col justify-end border-t border-gray-100 pt-5">
                @if($pengajuan)
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Status Saat Ini</span>
                            @php
                                $statusColors = [
                                    'terverifikasi' => 'bg-green-100 text-green-700 border-green-200',
                                    'ditolak' => 'bg-red-100 text-red-700 border-red-200',
                                    'menunggu' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                ];
                                $colorClass = $statusColors[$pengajuan->status] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                            @endphp
                            <span class="px-3 py-1.5 rounded-full text-xs font-bold uppercase border {{ $colorClass }} shadow-sm transition-colors">
                                {{ $pengajuan->status }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-gray-500 mt-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            Diajukan: {{ $pengajuan->created_at?->format('d M Y, H:i') ?? '-' }}
                        </div>
                    </div>
                @else
                    <div class="text-center py-4 bg-gray-50 rounded-xl border border-gray-100 border-dashed h-full flex flex-col items-center justify-center gap-3 group-hover:bg-brand-orange/5 transition-colors">
                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 mb-1 group-hover:text-brand-orange group-hover:bg-brand-orange/10 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                        </div>
                        <p class="text-sm text-gray-500 px-4">Belum ada formulir pengajuan bantuan.</p>
                        <a href="{{ route('umkm.pengajuan.create') }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-brand-orange hover:text-orange-500 hover:underline underline-offset-4 decoration-2">
                            Isi Formulir Sekarang
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Card 3: Hasil SPK -->
        <div class="group bg-white rounded-[24px] p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:border-blue-200 transition-all duration-300 flex flex-col h-full relative overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50/50 rounded-bl-[100px] -z-10 transition-transform duration-500 group-hover:scale-110"></div>
            
            <div class="flex items-start gap-4 mb-5">
                <div class="w-12 h-12 shrink-0 rounded-[16px] bg-blue-50 text-blue-600 flex items-center justify-center transition-transform duration-300 group-hover:scale-110 group-hover:bg-blue-600 group-hover:text-white shadow-inner">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 16 4 4 4-4"/><path d="M7 20V4"/><path d="m21 8-4-4-4 4"/><path d="M17 4v16"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 text-lg leading-tight">Hasil SPK</h3>
                    <p class="text-xs font-medium text-gray-500 mt-1">Profile Matching</p>
                </div>
            </div>

            <div class="flex-1 flex flex-col justify-center border-t border-gray-100 pt-5">
                @if($hasil)
                    <div class="text-center space-y-3">
                        <div class="inline-flex flex-col items-center justify-center w-24 h-24 rounded-full bg-blue-50 border-4 border-blue-100/50 shadow-inner group-hover:border-blue-200 transition-colors">
                            <span class="text-2xl font-extrabold text-blue-700 leading-none mb-1">{{ number_format($hasil->nilai_akhir, 2) }}</span>
                            <span class="text-[10px] font-bold text-blue-500 uppercase tracking-widest">Skor</span>
                        </div>
                        
                        <div class="flex flex-col items-center gap-2 pt-2">
                            <div class="flex items-center gap-1.5 text-sm font-semibold text-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-brand-orange" viewBox="0 0 24 24" fill="currentColor" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                Peringkat: #{{ $hasil->ranking }}
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $hasil->status_seleksi === 'diterima' ? 'bg-green-100 text-green-700 border-green-200' : 'bg-gray-100 text-gray-700 border-gray-200' }}">
                                @if($hasil->status_seleksi === 'diterima')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                @endif
                                {{ ucfirst($hasil->status_seleksi) }}
                            </span>
                        </div>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center h-full text-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v4l3 3"/><circle cx="12" cy="12" r="10"/></svg>
                        </div>
                        <p class="text-sm text-gray-500 max-w-[200px]">Hasil akan muncul setelah kalkulasi massal oleh Dinas.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
