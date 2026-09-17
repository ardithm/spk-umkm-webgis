@extends('layouts.admin')

@section('content')
<div class="space-y-8">
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- 1. HERO HEADER: Greeting, Periode Aktif, & Quick Action Hub          -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <div class="bg-gradient-to-r from-gray-900 via-purple-950 to-gray-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-purple-950/20 border border-purple-900/40 relative overflow-hidden">
        <!-- Ambient background glows -->
        <div class="absolute -right-16 -top-16 w-64 h-64 bg-brand-purple/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-1/3 -bottom-16 w-48 h-48 bg-brand-orange/15 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-2.5 mb-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-brand-purple/30 text-purple-200 border border-brand-purple/40 backdrop-blur-md">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                        Portal Utama Administrator Dinas
                    </span>
                    <span class="text-xs text-purple-300/80 font-medium">
                        {{ now()->translatedFormat('l, d F Y') }}
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                    Pusat Komando & SPK Bantuan Modal UMKM
                </h1>
                <p class="text-sm text-purple-200/80 mt-1 max-w-2xl">
                    Dinas Koperasi, Usaha Mikro dan Tenaga Kerja Kota Banjarmasin — Monitoring pendaftaran, validasi berkas, simulasi Profile Matching, dan WebGIS spasial.
                </p>

                <!-- Active Process Indicator -->
                @if($stats['proses_aktif'])
                <div class="mt-4 inline-flex items-center gap-3 px-3.5 py-1.5 rounded-xl bg-white/10 border border-white/10 backdrop-blur-md text-xs">
                    <span class="text-purple-300">Sesi Seleksi Aktif:</span>
                    <span class="font-bold text-white">{{ $stats['proses_aktif']->periode }}</span>
                    <span class="w-1 h-1 rounded-full bg-purple-400"></span>
                    <span class="text-purple-300">Passing Grade: <strong class="text-brand-orange">{{ number_format($stats['proses_aktif']->passing_grade, 2) }}</strong></span>
                    <span class="w-1 h-1 rounded-full bg-purple-400"></span>
                    <span class="text-purple-300">Kuota: <strong class="text-emerald-400">{{ $stats['proses_aktif']->kuota ?? 'Tak Terbatas' }}</strong></span>
                </div>
                @else
                <div class="mt-4 inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-amber-500/20 border border-amber-500/30 text-xs text-amber-200">
                    ⚠️ Belum ada sesi gelombang seleksi aktif. Buat sesi di menu Proses SPK.
                </div>
                @endif
            </div>

            <!-- Quick Action Shortcut Buttons -->
            <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                <a href="{{ route('admin.spk.index') }}" 
                   class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 bg-gradient-to-r from-brand-purple to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white font-bold py-2.5 px-4 rounded-xl text-xs sm:text-sm shadow-lg shadow-purple-900/40 transition-all transform hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Kalkulasi SPK
                </a>
                <a href="{{ route('admin.webgis.index') }}" 
                   class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 bg-white/10 hover:bg-white/20 text-white font-semibold py-2.5 px-4 rounded-xl text-xs sm:text-sm border border-white/20 backdrop-blur-md transition-all transform hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                    Peta WebGIS
                </a>
                <a href="{{ route('home') }}" target="_blank"
                   class="inline-flex items-center justify-center p-2.5 rounded-xl bg-white/5 hover:bg-white/10 text-purple-200 border border-white/10 transition"
                   title="Lihat Landing Page Publik">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- 2. KPI METRICS (6 Stat Cards Grid)                                    -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        <!-- Metric 1: Total UMKM -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-100 hover:border-purple-200 hover:shadow-md transition-all group">
            <div class="flex items-center justify-between text-gray-400 group-hover:text-brand-purple transition-colors mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider">Total UMKM</span>
                <div class="w-8 h-8 rounded-lg bg-purple-50 text-brand-purple flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-gray-900">{{ $stats['total_umkm'] }}</div>
            <p class="text-[11px] text-gray-500 mt-1 flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                <span>{{ $stats['umkm_berkoordinat'] }} terpetakan</span>
            </p>
        </div>

        <!-- Metric 2: Total Pengajuan -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-100 hover:border-orange-200 hover:shadow-md transition-all group">
            <div class="flex items-center justify-between text-gray-400 group-hover:text-brand-orange transition-colors mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider">Pengajuan</span>
                <div class="w-8 h-8 rounded-lg bg-orange-50 text-brand-orange flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-gray-900">{{ $stats['total_pengajuan'] }}</div>
            <p class="text-[11px] text-amber-600 font-medium mt-1">
                {{ $stats['menunggu_verifikasi'] }} menunggu verif
            </p>
        </div>

        <!-- Metric 3: Pengajuan Terverifikasi -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-100 hover:border-emerald-200 hover:shadow-md transition-all group">
            <div class="flex items-center justify-between text-gray-400 group-hover:text-emerald-600 transition-colors mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider">Siap SPK</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-emerald-600">{{ $stats['terverifikasi'] }}</div>
            <p class="text-[11px] text-gray-500 mt-1">Berkas tervalidasi</p>
        </div>

        <!-- Metric 4: Hasil Lolos Seleksi -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-100 hover:border-blue-200 hover:shadow-md transition-all group">
            <div class="flex items-center justify-between text-gray-400 group-hover:text-blue-600 transition-colors mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider">Lolos Diterima</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-blue-600">{{ $stats['total_diterima'] }}</div>
            <p class="text-[11px] text-gray-500 mt-1">Penerima bantuan</p>
        </div>

        <!-- Metric 5: Cadangan & Gugur -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-100 hover:border-yellow-200 hover:shadow-md transition-all group">
            <div class="flex items-center justify-between text-gray-400 group-hover:text-yellow-600 transition-colors mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider">Cadangan / Gugur</span>
                <div class="w-8 h-8 rounded-lg bg-yellow-50 text-yellow-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-gray-800">
                {{ $stats['total_cadangan'] }} <span class="text-xs text-gray-400 font-normal">/ {{ $stats['total_tidak_diterima'] }}</span>
            </div>
            <p class="text-[11px] text-gray-500 mt-1">Cadangan & diskualifikasi</p>
        </div>

        <!-- Metric 6: Kriteria Penilaian -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-100 hover:border-indigo-200 hover:shadow-md transition-all group">
            <div class="flex items-center justify-between text-gray-400 group-hover:text-indigo-600 transition-colors mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider">Kriteria SPK</span>
                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-indigo-600">{{ $stats['total_kriteria'] }}</div>
            <p class="text-[11px] text-gray-500 mt-1">
                {{ $stats['kriteria_core'] }} CF (60%) | {{ $stats['kriteria_secondary'] }} SF (40%)
            </p>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- 3. PUSAT MODUL & FITUR TERPADU (Core Feature Command Center)          -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-bold text-gray-900">Modul & Layanan Seleksi</h2>
                <p class="text-xs text-gray-500">Akses cepat ke seluruh fitur administratif dan algoritma sistem</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Modul 1: SPK Profile Matching -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-all group">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-purple-50 text-brand-purple flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </div>
                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-700 uppercase tracking-wider mb-1">
                        Metode SPK
                    </span>
                    <h3 class="text-base font-bold text-gray-900">Profile Matching</h3>
                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                        Hitung GAP kesenjangan kompetensi, bobot Core & Secondary Factor, serta kalkulasi nilai akhir perankingan secara otomatis.
                    </p>
                </div>
                <div class="mt-5 pt-4 border-t border-gray-50 flex items-center justify-between">
                    <span class="text-[11px] font-semibold text-gray-500">Rumus: 60% CF + 40% SF</span>
                    <a href="{{ route('admin.spk.index') }}" class="text-xs font-bold text-brand-purple hover:text-purple-800 flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                        Kelola & Hitung &rarr;
                    </a>
                </div>
            </div>

            <!-- Modul 2: WebGIS & OSRM -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-all group">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700 uppercase tracking-wider mb-1">
                        Spasial WebGIS
                    </span>
                    <h3 class="text-base font-bold text-gray-900">Pemetaan & Rute OSRM</h3>
                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                        Visualisasi sebaran marker geografis UMKM Kota Banjarmasin dan kalkulasi rute navigasi kunjungan survei fisik di lapangan.
                    </p>
                </div>
                <div class="mt-5 pt-4 border-t border-gray-50 flex items-center justify-between">
                    <span class="text-[11px] font-semibold text-gray-500">{{ $stats['umkm_berkoordinat'] }} Titik Terdaftar</span>
                    <a href="{{ route('admin.webgis.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-800 flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                        Buka Peta &rarr;
                    </a>
                </div>
            </div>

            <!-- Modul 3: Manajemen Dokumen -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-all group">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-orange-50 text-brand-orange flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-orange-100 text-orange-700 uppercase tracking-wider mb-1">
                        Validasi Berkas
                    </span>
                    <h3 class="text-base font-bold text-gray-900">Verifikasi Persyaratan</h3>
                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                        Validasi lampiran legalitas wajib pendaftar (KTP, Kartu Keluarga, NIB, SKU, dan foto usaha) sebelum diikutsertakan dalam SPK.
                    </p>
                </div>
                <div class="mt-5 pt-4 border-t border-gray-50 flex items-center justify-between">
                    <span class="text-[11px] font-semibold text-amber-600">{{ $stats['menunggu_verifikasi'] }} Menunggu</span>
                    <a href="{{ route('admin.dokumen.index') }}" class="text-xs font-bold text-brand-orange hover:text-orange-700 flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                        Periksa Berkas &rarr;
                    </a>
                </div>
            </div>

            <!-- Modul 4: Kriteria & Standar Bobot -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-all group">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                    </div>
                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700 uppercase tracking-wider mb-1">
                        Standarisasi
                    </span>
                    <h3 class="text-base font-bold text-gray-900">Kriteria & Target Ideal</h3>
                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                        Pengaturan 5 kriteria acuan Dinas (Omzet, Aset, SDM, Pemasaran, Perizinan) beserta target ideal dan tabel bobot interpolasi GAP.
                    </p>
                </div>
                <div class="mt-5 pt-4 border-t border-gray-50 flex items-center justify-between">
                    <span class="text-[11px] font-semibold text-gray-500">{{ $stats['total_kriteria'] }} Parameter Aktif</span>
                    <a href="{{ route('admin.kriteria.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                        Kelola Kriteria &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- 4. DUAL COLUMN: Live WebGIS Mini Preview & Top SPK Leaderboard       -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left: Interactive Spatial Map Preview (7 Cols) -->
        <div class="lg:col-span-7 bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <h3 class="font-bold text-gray-900 text-base">Sebaran Koordinat Lapangan (WebGIS)</h3>
                </div>
                <a href="{{ route('admin.webgis.index') }}" class="text-xs font-bold text-brand-purple hover:underline flex items-center gap-1">
                    Buka Mode Penuh & Rute &rarr;
                </a>
            </div>
            <p class="text-xs text-gray-500 mb-4">
                Peta interaktif menampilkan titik operasional fisik UMKM di Kota Banjarmasin. Klik marker untuk melihat data pemilik dan alamat.
            </p>

            <div id="mini-dashboard-map" class="w-full h-80 rounded-xl border border-gray-200 overflow-hidden shadow-inner relative z-0"></div>

            <div class="mt-4 flex items-center justify-between text-xs text-gray-500 bg-gray-50 p-3 rounded-xl border border-gray-100">
                <div class="flex items-center gap-4">
                    <span class="flex items-center gap-1.5 font-medium text-gray-700">
                        <span class="w-3 h-3 rounded-full bg-brand-purple"></span>
                        Marker UMKM
                    </span>
                    <span>Total terpetakan: <strong class="text-gray-800">{{ $umkm_map_data->count() }}</strong> titik</span>
                </div>
                <a href="{{ route('admin.webgis.index') }}" class="text-brand-purple font-semibold hover:underline">
                    Kalkulasi Rute OSRM &rarr;
                </a>
            </div>
        </div>

        <!-- Right: Top 5 SPK Ranking Leaderboard (5 Cols) -->
        <div class="lg:col-span-5 bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-brand-purple"></span>
                        <h3 class="font-bold text-gray-900 text-base">Top Rekomendasi Seleksi SPK</h3>
                    </div>
                    @if($stats['proses_aktif'])
                    <a href="{{ route('admin.spk.ranking', $stats['proses_aktif']->id_proses) }}" class="text-xs font-bold text-brand-purple hover:underline">
                        Lihat Semua &rarr;
                    </a>
                    @endif
                </div>

                @if($stats['proses_aktif'])
                <p class="text-xs text-gray-500 mb-4">
                    Hasil perankingan tertinggi sesi <strong class="text-gray-800">{{ $stats['proses_aktif']->periode }}</strong> berdasarkan nilai Profile Matching.
                </p>

                <div class="space-y-3">
                    @forelse($top_ranking as $item)
                    <div class="p-3 rounded-xl bg-gray-50/70 border border-gray-100 hover:border-purple-200 hover:bg-purple-50/30 transition-all flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center font-extrabold text-xs
                                @if($item->ranking == 1) bg-amber-400 text-white shadow-md shadow-amber-400/30
                                @elseif($item->ranking == 2) bg-slate-400 text-white
                                @elseif($item->ranking == 3) bg-amber-700 text-white
                                @else bg-gray-200 text-gray-700 @endif">
                                #{{ $item->ranking }}
                            </div>
                            <div>
                                <h4 class="font-bold text-xs text-gray-900 line-clamp-1">
                                    {{ $item->pengajuan?->umkm?->nama_umkm ?? 'UMKM #'.$item->id_pengajuan }}
                                </h4>
                                <p class="text-[10px] text-gray-500">
                                    {{ $item->pengajuan?->umkm?->nama_pemilik ?? '-' }} | NCF: {{ number_format($item->nilai_ncf, 2) }} | NSF: {{ number_format($item->nilai_nsf, 2) }}
                                </p>
                            </div>
                        </div>

                        <div class="text-right">
                            <div class="text-sm font-extrabold text-brand-purple">
                                {{ number_format($item->nilai_akhir, 4) }}
                            </div>
                            <span class="inline-block px-2 py-0.5 rounded-full text-[9px] font-bold uppercase
                                @if($item->status_seleksi === 'diterima') bg-emerald-100 text-emerald-800
                                @elseif($item->status_seleksi === 'cadangan') bg-amber-100 text-amber-800
                                @else bg-rose-100 text-rose-800 @endif">
                                {{ $item->status_seleksi }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="py-12 text-center text-gray-400 text-xs">
                        <p>Belum ada data kalkulasi untuk sesi ini.</p>
                        <form action="{{ route('admin.spk.hitung', $stats['proses_aktif']->id_proses) }}" method="POST" class="mt-3">
                            @csrf
                            <button type="submit" class="bg-brand-purple text-white px-4 py-1.5 rounded-lg text-xs font-bold hover:bg-opacity-90 shadow-sm">
                                ▶ Jalankan Hitung Sekarang
                            </button>
                        </form>
                    </div>
                    @endforelse
                </div>
                @else
                <div class="py-16 text-center text-gray-400 text-xs">
                    <p class="mb-2">Belum ada sesi seleksi SPK yang tercatat.</p>
                    <a href="{{ route('admin.spk.index') }}" class="text-brand-purple font-bold hover:underline">
                        Buka Manajemen SPK &rarr;
                    </a>
                </div>
                @endif
            </div>

            @if($stats['proses_aktif'])
            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs">
                <a href="{{ route('admin.spk.hasil', $stats['proses_aktif']->id_proses) }}" class="text-gray-500 hover:text-gray-800 font-medium">
                    🔍 Detail Nilai GAP Kriteria
                </a>
                <a href="{{ route('admin.spk.ranking', $stats['proses_aktif']->id_proses) }}" class="text-brand-purple font-bold hover:underline">
                    Tabel Ranking Lengkap &rarr;
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- 5. TABEL STANDAR KRITERIA PROFILE MATCHING                            -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <div id="tabel-kriteria" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h3 class="font-bold text-gray-900 text-base">Standar Kriteria & Pembobotan Profile Matching</h3>
                <p class="text-xs text-gray-500 mt-0.5">Konfigurasi bobot faktor dan nilai target ideal acuan Dinas Koperasi Kota Banjarmasin</p>
            </div>
            <div class="flex items-center gap-3 text-xs">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-purple-50 text-brand-purple font-bold border border-purple-100">
                    Core Factor: 60%
                </span>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-orange-50 text-brand-orange font-bold border border-orange-100">
                    Secondary Factor: 40%
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Kriteria</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Target Ideal Dinas</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kelompok Faktor</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Bobot Pengaruh</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100 text-xs">
                    @forelse($kriteria_list as $k)
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        <td class="px-6 py-3.5 whitespace-nowrap font-bold text-brand-purple">
                            {{ $k->kode_kriteria }}
                        </td>
                        <td class="px-6 py-3.5 whitespace-nowrap font-semibold text-gray-900">
                            {{ $k->nama_kriteria }}
                        </td>
                        <td class="px-6 py-3.5 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-gray-100 text-gray-800">
                                Skala {{ $k->target_ideal }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5 whitespace-nowrap">
                            @if($k->jenis_faktor === 'core')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full font-bold text-purple-700 bg-purple-50 border border-purple-100">
                                Core Factor (CF)
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full font-bold text-orange-700 bg-orange-50 border border-orange-100">
                                Secondary Factor (SF)
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-3.5 whitespace-nowrap font-semibold text-gray-600">
                            {{ $k->jenis_faktor === 'core' ? '60% Komputasi' : '40% Komputasi' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                            Belum ada data kriteria SPK.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- 6. TABEL PENGAJUAN TERBARU (Recent Submissions Feed)                 -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h3 class="font-bold text-gray-900 text-base">Pengajuan Bantuan Terbaru</h3>
                <p class="text-xs text-gray-500 mt-0.5">Daftar berkas permohonan modal usaha yang masuk ke dalam sistem</p>
            </div>
            <a href="{{ route('admin.spk.index') }}" class="text-xs font-bold text-brand-purple hover:underline">
                Kelola Semua di SPK &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Entitas Usaha</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pemilik / Kontak</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Omzet & Aset</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal Pengajuan</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status Berkas</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi Cepat</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100 text-xs">
                    @forelse($pengajuan_terbaru as $p)
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-bold text-gray-900">{{ $p->umkm?->nama_umkm ?? 'UMKM #'.$p->id_pengajuan }}</div>
                            <div class="text-[11px] text-gray-500 line-clamp-1 max-w-xs">{{ $p->umkm?->alamat ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-semibold text-gray-800">{{ $p->umkm?->nama_pemilik ?? '-' }}</div>
                            <div class="text-[11px] text-gray-500 font-mono">{{ $p->umkm?->no_telepon ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-gray-800">Rp {{ number_format($p->omzet_tahunan, 0, ',', '.') }}</div>
                            <div class="text-[10px] text-gray-500">Aset: Rp {{ number_format($p->aset, 0, ',', '.') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                            {{ $p->tanggal_pengajuan ? $p->tanggal_pengajuan->format('d M Y H:i') : ($p->created_at ? $p->created_at->format('d M Y') : '-') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($p->status === 'terverifikasi')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Terverifikasi
                            </span>
                            @elseif($p->status === 'menunggu')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                Menunggu Validasi
                            </span>
                            @elseif($p->status === 'revisi')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-100">
                                Perlu Revisi
                            </span>
                            @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-gray-100 text-gray-700">
                                {{ ucfirst($p->status) }}
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right font-medium">
                            <a href="{{ route('admin.dokumen.show', $p->id_pengajuan) }}" class="text-brand-purple hover:underline font-bold text-xs flex items-center justify-end gap-1">
                                Tinjau Berkas &rarr;
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-400">
                            Belum ada pengajuan UMKM yang masuk ke dalam sistem.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Inisialisasi Mini Map Sebaran Spasial di Dasbor
    const mapElement = document.getElementById('mini-dashboard-map');
    if (!mapElement) return;

    // Default koordinat Kota Banjarmasin
    const map = L.map('mini-dashboard-map', {
        zoomControl: true,
        scrollWheelZoom: false
    }).setView([-3.316694, 114.590111], 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap',
        maxZoom: 18
    }).addTo(map);

    const umkmData = @json($umkm_map_data);
    const bounds = [];

    if (Array.isArray(umkmData) && umkmData.length > 0) {
        umkmData.forEach(item => {
            if (item.latitude && item.longitude) {
                const latLng = [parseFloat(item.latitude), parseFloat(item.longitude)];
                bounds.push(latLng);

                const marker = L.marker(latLng).addTo(map);
                marker.bindPopup(`
                    <div style="font-family: inherit; font-size: 12px; min-width: 160px;">
                        <strong style="font-size: 13px; color: #8A67AB;">${item.nama_umkm}</strong><br>
                        <span style="color: #666;">${item.nama_pemilik || ''}</span><br>
                        <div style="margin-top: 4px; color: #888; font-size: 11px;">${item.alamat || ''}</div>
                        <a href="{{ route('admin.webgis.index') }}" style="display: block; margin-top: 6px; color: #8A67AB; font-weight: bold; text-decoration: underline;">
                            Buka di WebGIS &rarr;
                        </a>
                    </div>
                `);
            }
        });

        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [30, 30] });
        }
    }
});
</script>
@endpush
