@extends('layouts.admin')

@section('content')
<!-- Leaflet & Routing Machine Styles -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />

<style>
    /* Styling Peta & Marker */
    #map {
        height: 720px;
        width: 100%;
        border-radius: 0.75rem;
        z-index: 1;
    }
    .custom-div-icon {
        background: transparent;
        border: none;
    }
    .marker-pin {
        width: 36px;
        height: 36px;
        border-radius: 50% 50% 50% 0;
        position: absolute;
        transform: rotate(-45deg);
        left: 50%;
        top: 50%;
        margin: -20px 0 0 -18px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 10px rgba(0,0,0,0.25);
        border: 2px solid #ffffff;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .marker-pin:hover {
        transform: rotate(-45deg) scale(1.15);
        box-shadow: 0 6px 16px rgba(0,0,0,0.35);
    }
    .marker-pin i, .marker-pin svg {
        transform: rotate(45deg);
        color: white;
    }
    /* Sembunyikan panel bawaan leaflet-routing jika ingin UI clean di sidebar */
    .leaflet-routing-container {
        display: none !important;
    }
    /* Custom Scrollbar pada Sidebar */
    .custom-scroll::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scroll::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    .custom-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .custom-scroll::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>

<!-- Header & Metrik Cepat -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-brand-purple/10 text-brand-purple">
                    Sistem Informasi Geografis (WebGIS)
                </span>
                <span class="text-xs text-gray-400">• Kota Banjarmasin</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mt-1">Persebaran UMKM & Optimasi Rute Survei</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                Monitoring titik lokasi seluruh pendaftar bantuan modal, analisis spasial status verifikasi, dan simulasi rute tercepat OSRM.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.resetMapView()" class="inline-flex items-center gap-1.5 px-3 py-2 bg-white border border-gray-200 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-50 transition shadow-sm">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                Reset Tampilan Peta
            </button>
            <button onclick="window.getUserLocation()" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-brand-purple text-white text-xs font-semibold rounded-lg hover:bg-purple-700 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Lokasi Saya
            </button>
        </div>
    </div>

    <!-- Ringkasan Statistik Spasial -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mt-4">
        <div class="bg-white rounded-xl p-3.5 border border-gray-100 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-purple-50 text-brand-purple flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <div>
                <span class="text-xs text-gray-500 font-medium block">Total Terpetakan</span>
                <span id="stat-total" class="text-lg font-bold text-gray-900">{{ $totalUmkm }}</span>
            </div>
        </div>

        <div class="bg-white rounded-xl p-3.5 border border-gray-100 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <span class="text-xs text-gray-500 font-medium block">Lolos Bantuan (SPK)</span>
                <span id="stat-lolos" class="text-lg font-bold text-emerald-600">{{ $lolosBantuan }}</span>
            </div>
        </div>

        <div class="bg-white rounded-xl p-3.5 border border-gray-100 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
            <div>
                <span class="text-xs text-gray-500 font-medium block">Terverifikasi</span>
                <span id="stat-terverifikasi" class="text-lg font-bold text-blue-600">{{ $terverifikasi }}</span>
            </div>
        </div>

        <div class="bg-white rounded-xl p-3.5 border border-gray-100 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <span class="text-xs text-gray-500 font-medium block">Menunggu Verifikasi</span>
                <span id="stat-menunggu" class="text-lg font-bold text-amber-600">{{ $menunggu }}</span>
            </div>
        </div>

        <div class="bg-white rounded-xl p-3.5 border border-gray-100 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <span class="text-xs text-gray-500 font-medium block">Perlu Revisi</span>
                <span id="stat-revisi" class="text-lg font-bold text-rose-600">{{ $revisi }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Main WebGIS Container: Filter Toolbar + Split Panel (Sidebar & Leaflet Map) -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 space-y-4">
    
    <!-- Filter Toolbar -->
    <div class="bg-gray-50/80 rounded-xl p-3.5 border border-gray-200/80">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
            
            <!-- 1. Search Input -->
            <div class="md:col-span-4 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" id="filter-search" placeholder="Cari nama usaha, pemilik, alamat..." 
                       class="w-full pl-9 pr-3 py-2 text-xs bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-purple focus:border-brand-purple text-gray-900 transition" />
            </div>

            <!-- 2. Filter Status Verifikasi -->
            <div class="md:col-span-3">
                <label for="filter-status-verifikasi" class="sr-only">Status Verifikasi</label>
                <select id="filter-status-verifikasi" class="w-full py-2 px-3 text-xs bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-purple focus:border-brand-purple text-gray-700 transition">
                    <option value="semua">Semua Status Verifikasi</option>
                    <option value="terverifikasi">Terverifikasi (Lengkap)</option>
                    <option value="menunggu">Menunggu Verifikasi</option>
                    <option value="revisi">Perlu Revisi Berkas</option>
                    <option value="draft">Draf Pengajuan</option>
                </select>
            </div>

            <!-- 3. Filter Status Penerima Bantuan -->
            <div class="md:col-span-3">
                <label for="filter-status-bantuan" class="sr-only">Status Penerima Bantuan</label>
                <select id="filter-status-bantuan" class="w-full py-2 px-3 text-xs bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-purple focus:border-brand-purple text-gray-700 transition">
                    <option value="semua">Semua Status Bantuan</option>
                    <option value="diterima">Lolos Bantuan (Layak SPK)</option>
                    <option value="tidak_diterima">Tidak Lolos Bantuan</option>
                    <option value="cadangan">Cadangan</option>
                    <option value="belum_dinilai">Belum Dinilai / Menunggu SPK</option>
                </select>
            </div>

            <!-- 4. Tombol Reset Filter & Layer Selector -->
            <div class="md:col-span-2 flex items-center justify-end gap-2">
                <button id="btn-reset-filters" class="w-full py-2 px-3 text-xs font-semibold text-gray-600 bg-white hover:bg-gray-100 border border-gray-300 rounded-lg transition text-center">
                    Reset Filter
                </button>
            </div>

        </div>
    </div>

    <!-- Split Screen: Sidebar (Daftar UMKM / Panel Rute) & Canvas Peta Leaflet -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
        
        <!-- SIDEBAR (4 Kolom pada Layar Besar) -->
        <div class="lg:col-span-4 flex flex-col h-[720px] bg-gray-50/50 rounded-xl border border-gray-200 overflow-hidden">
            
            <!-- Tab Switcher Sidebar: [Daftar UMKM] & [Rute Navigasi OSRM] -->
            <div class="flex border-b border-gray-200 bg-white p-1 gap-1">
                <button id="tab-btn-list" onclick="window.switchSidebarTab('list')" class="flex-1 py-2 text-xs font-bold rounded-lg text-brand-purple bg-purple-50 transition flex items-center justify-center gap-1.5 shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    Daftar UMKM (<span id="umkm-count-badge">0</span>)
                </button>
                <button id="tab-btn-route" onclick="window.switchSidebarTab('route')" class="flex-1 py-2 text-xs font-semibold rounded-lg text-gray-600 hover:text-gray-900 transition flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    Rute Survei (OSRM)
                </button>
            </div>

            <!-- CONTENT 1: DAFTAR LIST UMKM -->
            <div id="sidebar-tab-list" class="flex-1 overflow-y-auto p-3 space-y-2.5 custom-scroll">
                <!-- Diisi via JavaScript -->
                <div class="text-center py-12 text-gray-400 text-xs">
                    <svg class="w-8 h-8 mx-auto animate-spin text-brand-purple mb-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    Memuat data spasial UMKM...
                </div>
            </div>

            <!-- CONTENT 2: PANEL KALKULASI RUTE OSRM -->
            <div id="sidebar-tab-route" class="hidden flex-1 overflow-y-auto p-3.5 space-y-3.5 custom-scroll bg-white">
                
                <div class="bg-purple-50/70 p-3 rounded-xl border border-purple-100 text-xs text-purple-900 leading-relaxed">
                    <strong class="font-bold flex items-center gap-1 text-brand-purple">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Mesin Navigasi Lapangan
                    </strong>
                    Kalkulasi rute tercepat dari Kantor Dinas atau lokasi petugas menuju lokasi fisik UMKM yang akan disurvei.
                </div>

                <!-- Input Titik Awal (Origin) -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1 flex items-center justify-between">
                        <span>Titik Awal (Origin)</span>
                        <span class="text-[10px] text-brand-purple font-semibold cursor-pointer hover:underline" onclick="window.setOriginToCurrentGps()">
                            Gunakan GPS Saya
                        </span>
                    </label>
                    <select id="route-origin-select" class="w-full py-2 px-3 text-xs bg-gray-50 border border-gray-300 rounded-lg text-gray-800 font-medium">
                        <option value="dinas" selected>🏛️ Kantor Dinas Koperasi Kota Banjarmasin</option>
                        <option value="gps" id="opt-origin-gps" disabled>📍 Lokasi GPS Saya (Belum Aktif)</option>
                    </select>
                    <p class="text-[10px] text-gray-400 mt-1" id="origin-desc">Titik: Jl. Pramuka, Banjarmasin (-3.32832, 114.59124)</p>
                </div>

                <!-- Input Titik Tujuan (Destination) -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">
                        Titik Tujuan Survei (Destination)
                    </label>
                    <select id="route-dest-select" class="w-full py-2 px-3 text-xs bg-gray-50 border border-gray-300 rounded-lg text-gray-800 font-medium">
                        <option value="">-- Pilih UMKM Tujuan --</option>
                    </select>
                    <p class="text-[10px] text-gray-400 mt-1">Atau klik langsung pada marker UMKM di peta lalu klik tombol "Rute ke Sini".</p>
                </div>

                <!-- Action Buttons: Hitung & Reset -->
                <div class="flex gap-2 pt-1">
                    <button id="btn-do-calculate" onclick="window.executeRouteCalculation()" class="flex-1 py-2.5 px-3 bg-brand-purple hover:bg-purple-700 text-white rounded-lg text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Kalkulasi Rute (OSRM)
                    </button>
                    <button id="btn-do-clear-route" onclick="window.clearActiveRoute()" class="py-2.5 px-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold transition">
                        Hapus
                    </button>
                </div>

                <!-- Ringkasan Hasil Kalkulasi Rute -->
                <div id="route-result-box" class="hidden space-y-3 pt-2">
                    <div class="grid grid-cols-2 gap-2">
                        <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 text-center">
                            <span class="text-[10px] uppercase font-bold tracking-wider text-gray-400 block">Jarak Tempuh</span>
                            <span id="route-dist-val" class="text-xl font-extrabold text-brand-purple">0.0 km</span>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 text-center">
                            <span class="text-[10px] uppercase font-bold tracking-wider text-gray-400 block">Estimasi Waktu</span>
                            <span id="route-time-val" class="text-xl font-extrabold text-gray-800">0 mnt</span>
                        </div>
                    </div>

                    <!-- Tombol Navigasi Langsung Google Maps -->
                    <a id="btn-google-maps-nav" href="#" target="_blank" class="w-full py-2 px-3 bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 rounded-lg text-xs font-bold flex items-center justify-center gap-2 transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                        Buka Panduan Arah di Google Maps
                    </a>

                    <!-- Turn by Turn Direction Instructions -->
                    <div class="border-t border-gray-100 pt-2">
                        <h4 class="text-xs font-bold text-gray-700 mb-2 flex items-center justify-between">
                            <span>Petunjuk Belokan (Turn-by-Turn):</span>
                            <span id="steps-count" class="text-[10px] font-normal text-gray-400"></span>
                        </h4>
                        <div id="route-steps-container" class="space-y-1 max-h-48 overflow-y-auto custom-scroll pr-1 text-xs text-gray-600">
                            <!-- Diisi via JS -->
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <!-- CANVAS PETA DIGITAL (8 Kolom pada Layar Besar) -->
        <div class="lg:col-span-8 relative">
            <div id="map" class="shadow-inner border border-gray-200"></div>

            <!-- Layer Switcher Floating Button (Peta Jalan vs Satelit) -->
            <div class="absolute top-4 right-4 z-[999] bg-white/95 backdrop-blur-sm rounded-lg shadow-md border border-gray-200 p-1 flex gap-1 text-xs">
                <button id="layer-btn-street" onclick="window.switchTileLayer('street')" class="px-2.5 py-1 rounded font-bold bg-brand-purple text-white transition">
                    Jalan
                </button>
                <button id="layer-btn-satellite" onclick="window.switchTileLayer('satellite')" class="px-2.5 py-1 rounded font-medium text-gray-700 hover:bg-gray-100 transition">
                    Satelit
                </button>
            </div>

            <!-- Legenda Marker Floating (Pojok Kiri Bawah Peta) -->
            <div class="absolute bottom-4 left-4 z-[999] bg-white/95 backdrop-blur-md rounded-xl shadow-lg border border-gray-200 p-3 max-w-xs text-xs">
                <div class="flex items-center justify-between font-bold text-gray-800 pb-1.5 mb-1.5 border-b border-gray-100">
                    <span>Legenda Status Marker</span>
                    <span class="text-[10px] text-gray-400 font-normal">Klik pin untuk info</span>
                </div>
                <div class="grid grid-cols-2 gap-x-3 gap-y-1.5 text-[11px]">
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-emerald-500 border-2 border-white shadow-sm flex-shrink-0"></span>
                        <span class="text-gray-700 font-medium">Lolos Bantuan</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-blue-500 border-2 border-white shadow-sm flex-shrink-0"></span>
                        <span class="text-gray-700 font-medium">Terverifikasi</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-amber-500 border-2 border-white shadow-sm flex-shrink-0"></span>
                        <span class="text-gray-700 font-medium">Menunggu Verif</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-rose-500 border-2 border-white shadow-sm flex-shrink-0"></span>
                        <span class="text-gray-700 font-medium">Perlu Revisi</span>
                    </div>
                    <div class="flex items-center gap-1.5 col-span-2 pt-1 border-t border-gray-100">
                        <span class="w-3 h-3 rounded-full bg-purple-700 border-2 border-white shadow-sm flex-shrink-0"></span>
                        <span class="text-purple-900 font-bold">🏛️ Kantor Dinas Koperasi Kota</span>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

<!-- Scripts Leaflet & WebGIS Logic -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // ── 1. Data Kantor Dinas Bawaan ──────────────────────────────────────────
    const KANTOR_DINAS = {
        nama: "{{ $kantorDinas['nama'] }}",
        alamat: "{{ $kantorDinas['alamat'] }}",
        lat: {{ $kantorDinas['latitude'] }},
        lng: {{ $kantorDinas['longitude'] }}
    };

    // ── 2. Inisialisasi Peta Leaflet ─────────────────────────────────────────
    const map = L.map('map', {
        zoomControl: true,
        attributionControl: true
    }).setView([KANTOR_DINAS.lat, KANTOR_DINAS.lng], 13);

    // Layer OpenStreetMap
    const streetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Layer Satelit Esri
    const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        maxZoom: 19,
        attribution: '&copy; Esri &mdash; Earthstar Geographics'
    });

    // Layer Switcher
    window.switchTileLayer = function(type) {
        const btnStreet = document.getElementById('layer-btn-street');
        const btnSatellite = document.getElementById('layer-btn-satellite');

        if (type === 'satellite') {
            map.removeLayer(streetLayer);
            satelliteLayer.addTo(map);
            btnSatellite.className = "px-2.5 py-1 rounded font-bold bg-brand-purple text-white transition";
            btnStreet.className = "px-2.5 py-1 rounded font-medium text-gray-700 hover:bg-gray-100 transition";
        } else {
            map.removeLayer(satelliteLayer);
            streetLayer.addTo(map);
            btnStreet.className = "px-2.5 py-1 rounded font-bold bg-brand-purple text-white transition";
            btnSatellite.className = "px-2.5 py-1 rounded font-medium text-gray-700 hover:bg-gray-100 transition";
        }
    };

    // ── 3. Marker Khusus Kantor Dinas ────────────────────────────────────────
    const dinasIcon = L.divIcon({
        className: 'custom-div-icon',
        html: `
            <div class="marker-pin" style="background-color: #6b21a8; border-color: #f3e8ff;">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
            </div>
        `,
        iconSize: [36, 36],
        iconAnchor: [18, 36],
        popupAnchor: [0, -32]
    });

    const dinasMarker = L.marker([KANTOR_DINAS.lat, KANTOR_DINAS.lng], { icon: dinasIcon })
        .addTo(map)
        .bindPopup(`
            <div class="p-2 text-xs max-w-xs">
                <div class="flex items-center gap-1.5 font-bold text-purple-900 text-sm mb-1">
                    <span>🏛️</span>
                    <span>${KANTOR_DINAS.nama}</span>
                </div>
                <p class="text-gray-500 mb-2">${KANTOR_DINAS.alamat}</p>
                <div class="bg-purple-50 p-2 rounded border border-purple-100 text-purple-800 text-[11px] font-medium">
                    Titik Markas Default untuk memulai kalkulasi rute survei lapangan.
                </div>
            </div>
        `);

    // ── 4. State Variabel Sistem WebGIS ──────────────────────────────────────
    let allUmkmData = [];
    let markerInstances = {}; // Map id_umkm -> L.Marker
    let activeRouteControl = null;
    let currentUserCoords = null; // { lat, lng }

    // Helper Pembuat Custom Pin Marker berdasarkan tipe status
    function getMarkerIcon(type) {
        let color = '#64748b'; // default draft (abu-abu)
        let iconSvg = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>`;

        if (type === 'lolos') {
            color = '#10b981'; // Emerald (Lolos Bantuan)
            iconSvg = `<svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>`;
        } else if (type === 'terverifikasi') {
            color = '#3b82f6'; // Blue
            iconSvg = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`;
        } else if (type === 'menunggu') {
            color = '#f59e0b'; // Amber
            iconSvg = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`;
        } else if (type === 'revisi') {
            color = '#f43f5e'; // Rose
            iconSvg = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`;
        }

        return L.divIcon({
            className: 'custom-div-icon',
            html: `
                <div class="marker-pin" style="background-color: ${color};">
                    ${iconSvg}
                </div>
            `,
            iconSize: [36, 36],
            iconAnchor: [18, 36],
            popupAnchor: [0, -32]
        });
    }

    // ── 5. Fetch Data UMKM dari API Backend ───────────────────────────────────
    function fetchUmkmData() {
        fetch("{{ route('admin.webgis.data') }}")
            .then(res => res.json())
            .then(res => {
                if (res.success && res.umkm) {
                    allUmkmData = res.umkm;
                    populateDestinationDropdown(allUmkmData);
                    applyFiltersAndRender();
                }
            })
            .catch(err => {
                console.error("Gagal memuat data UMKM:", err);
                document.getElementById('sidebar-tab-list').innerHTML = `
                    <div class="p-4 bg-red-50 text-red-600 rounded-lg text-xs">
                        Gagal memuat data sebaran titik UMKM dari server. Silakan muat ulang halaman.
                    </div>
                `;
            });
    }

    // Isi dropdown tujuan kalkulasi rute
    function populateDestinationDropdown(list) {
        const select = document.getElementById('route-dest-select');
        select.innerHTML = '<option value="">-- Pilih UMKM Tujuan --</option>';
        list.forEach(u => {
            const opt = document.createElement('option');
            opt.value = u.id_umkm;
            opt.textContent = `${u.nama_umkm} (${u.nama_pemilik})`;
            select.appendChild(opt);
        });
    }

    // ── 6. Logika Filter Dinamis & Rendering ──────────────────────────────────
    const filterSearch = document.getElementById('filter-search');
    const filterStatusVerif = document.getElementById('filter-status-verifikasi');
    const filterStatusBantuan = document.getElementById('filter-status-bantuan');
    const btnResetFilters = document.getElementById('btn-reset-filters');

    function applyFiltersAndRender() {
        const query = filterSearch.value.toLowerCase().trim();
        const verifVal = filterStatusVerif.value;
        const bantuanVal = filterStatusBantuan.value;

        // Bersihkan seluruh marker lama dari peta
        Object.values(markerInstances).forEach(marker => map.removeLayer(marker));
        markerInstances = {};

        const filtered = allUmkmData.filter(u => {
            // Pencarian Keyword
            const matchQuery = !query || 
                u.nama_umkm.toLowerCase().includes(query) ||
                u.nama_pemilik.toLowerCase().includes(query) ||
                u.alamat.toLowerCase().includes(query) ||
                (u.no_telepon && u.no_telepon.includes(query));

            // Status Verifikasi
            let matchVerif = true;
            if (verifVal !== 'semua') {
                matchVerif = (u.status_verifikasi === verifVal);
            }

            // Status Bantuan
            let matchBantuan = true;
            if (bantuanVal !== 'semua') {
                matchBantuan = (u.status_bantuan === bantuanVal);
            }

            return matchQuery && matchVerif && matchBantuan;
        });

        // Update badge jumlah list
        document.getElementById('umkm-count-badge').textContent = filtered.length;

        // Render List di Sidebar & Marker di Peta
        renderSidebarList(filtered);
        renderMapMarkers(filtered);
    }

    // Pasang Event Listeners Filter
    filterSearch.addEventListener('input', applyFiltersAndRender);
    filterStatusVerif.addEventListener('change', applyFiltersAndRender);
    filterStatusBantuan.addEventListener('change', applyFiltersAndRender);

    btnResetFilters.addEventListener('click', function() {
        filterSearch.value = '';
        filterStatusVerif.value = 'semua';
        filterStatusBantuan.value = 'semua';
        applyFiltersAndRender();
    });

    // ── 7. Render Card List di Sidebar ───────────────────────────────────────
    function renderSidebarList(list) {
        const container = document.getElementById('sidebar-tab-list');

        if (list.length === 0) {
            container.innerHTML = `
                <div class="text-center py-12 text-gray-400">
                    <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-xs font-medium text-gray-500">Tidak ada UMKM yang cocok dengan filter.</p>
                    <button onclick="document.getElementById('btn-reset-filters').click()" class="mt-2 text-brand-purple font-semibold text-xs hover:underline">
                        Reset Filter
                    </button>
                </div>
            `;
            return;
        }

        let html = '';
        list.forEach(u => {
            // Badge Status Verifikasi
            let badgeVerifClass = 'bg-gray-100 text-gray-700';
            if (u.status_verifikasi === 'terverifikasi') badgeVerifClass = 'bg-blue-50 text-blue-700 border border-blue-200';
            else if (u.status_verifikasi === 'menunggu') badgeVerifClass = 'bg-amber-50 text-amber-700 border border-amber-200';
            else if (u.status_verifikasi === 'revisi') badgeVerifClass = 'bg-rose-50 text-rose-700 border border-rose-200';

            // Badge Status Bantuan
            let badgeBantuan = '';
            if (u.status_bantuan === 'diterima') {
                badgeBantuan = `<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 flex items-center gap-1">
                    Rank #${u.ranking ?? '-'} (Lolos)
                </span>`;
            } else if (u.status_bantuan === 'tidak_diterima') {
                badgeBantuan = `<span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-red-100 text-red-700">Tidak Lolos</span>`;
            }

            html += `
                <div class="bg-white p-3 rounded-xl border border-gray-200 hover:border-brand-purple hover:shadow-md transition-all cursor-pointer group"
                     onclick="window.focusUmkmMarker(${u.id_umkm})">
                    
                    <div class="flex items-start justify-between gap-2 mb-1.5">
                        <div>
                            <h3 class="font-bold text-xs text-gray-900 group-hover:text-brand-purple transition leading-snug">
                                ${u.nama_umkm}
                            </h3>
                            <p class="text-[11px] text-gray-500">${u.nama_pemilik}</p>
                        </div>
                        <div class="flex flex-col items-end gap-1 flex-shrink-0">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold ${badgeVerifClass}">
                                ${u.status_verifikasi_label}
                            </span>
                            ${badgeBantuan}
                        </div>
                    </div>

                    <p class="text-[11px] text-gray-500 line-clamp-2 mb-2">
                         ${u.alamat}
                    </p>

                    <div class="flex items-center justify-between pt-2 border-t border-gray-100 text-[11px]">
                        <span class="text-gray-400 font-mono text-[10px]">
                            ${u.latitude.toFixed(4)}, ${u.longitude.toFixed(4)}
                        </span>
                        <div class="flex items-center gap-1">
                            <button onclick="event.stopPropagation(); window.quickRouteTo(${u.id_umkm})" 
                                    class="px-2 py-1 bg-purple-50 hover:bg-purple-100 text-brand-purple rounded text-[10px] font-bold transition flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                Rute
                            </button>
                            <a href="https://www.google.com/maps?q=${u.latitude},${u.longitude}" target="_blank" onclick="event.stopPropagation()"
                               class="px-2 py-1 bg-gray-50 hover:bg-gray-100 text-gray-600 rounded text-[10px] font-medium transition" title="Buka di Google Maps">
                                ↗ G-Maps
                            </a>
                        </div>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    // ── 8. Render Marker di Peta Leaflet ──────────────────────────────────────
    function renderMapMarkers(list) {
        const bounds = [];
        bounds.push([KANTOR_DINAS.lat, KANTOR_DINAS.lng]);

        list.forEach(u => {
            const icon = getMarkerIcon(u.marker_type);
            const marker = L.marker([u.latitude, u.longitude], { icon: icon }).addTo(map);

            // Pop-up Card Kaya Informasi
            const popupHtml = `
                <div class="p-2 text-xs max-w-sm">
                    <div class="flex items-center justify-between gap-2 pb-2 mb-2 border-b border-gray-100">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider ${
                            u.marker_type === 'lolos' ? 'bg-emerald-100 text-emerald-800' :
                            (u.marker_type === 'terverifikasi' ? 'bg-blue-100 text-blue-800' :
                            (u.marker_type === 'menunggu' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800'))
                        }">
                            ${u.status_verifikasi_label}
                        </span>
                        ${u.ranking ? `<span class="text-xs font-extrabold text-emerald-700">🏆 Rank #${u.ranking} (Skor: ${u.nilai_akhir})</span>` : ''}
                    </div>

                    <h3 class="font-bold text-sm text-gray-900 mb-0.5 leading-snug">${u.nama_umkm}</h3>
                    <p class="text-gray-600 mb-2">Pemilik: <strong>${u.nama_pemilik}</strong> | NIK: ${u.nik}</p>
                    
                    <div class="bg-gray-50 p-2 rounded-lg border border-gray-100 space-y-1 mb-2 text-[11px]">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Omzet Tahunan:</span>
                            <span class="font-bold text-gray-800">${u.omzet_formatted}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Nilai Aset:</span>
                            <span class="font-bold text-gray-800">${u.aset_formatted}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">SDM / Tenaga Kerja:</span>
                            <span class="font-bold text-gray-800">${u.jumlah_tenaga_kerja} Orang</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Legalitas:</span>
                            <span class="font-bold text-gray-800">${u.status_perizinan}</span>
                        </div>
                    </div>

                    <p class="text-gray-500 text-[11px] mb-3">
                        <strong>Alamat:</strong> ${u.alamat}
                    </p>

                    <div class="grid grid-cols-2 gap-1.5 pt-1 border-t border-gray-100">
                        <button onclick="window.quickRouteTo(${u.id_umkm})" 
                                class="w-full py-1.5 px-2 bg-brand-purple text-white rounded text-[11px] font-bold hover:bg-purple-700 transition flex items-center justify-center gap-1">
                            🚗 Rute dari Dinas
                        </button>
                        ${u.no_telepon ? `
                            <a href="https://wa.me/62${u.no_telepon.replace(/^0/, '')}" target="_blank"
                               class="w-full py-1.5 px-2 bg-emerald-600 text-white rounded text-[11px] font-semibold hover:bg-emerald-700 transition flex items-center justify-center gap-1">
                                💬 WhatsApp
                            </a>
                        ` : `
                            <a href="https://www.google.com/maps?q=${u.latitude},${u.longitude}" target="_blank"
                               class="w-full py-1.5 px-2 bg-gray-100 text-gray-700 rounded text-[11px] font-semibold hover:bg-gray-200 transition text-center">
                                🗺️ G-Maps
                            </a>
                        `}
                    </div>
                </div>
            `;

            marker.bindPopup(popupHtml);
            bounds.push([u.latitude, u.longitude]);
            markerInstances[u.id_umkm] = marker;
        });

        if (bounds.length > 1) {
            map.fitBounds(bounds, { padding: [40, 40] });
        }
    }

    // ── 9. Fungsi Navigasi & Fokus Marker ────────────────────────────────────
    window.focusUmkmMarker = function(idUmkm) {
        const marker = markerInstances[idUmkm];
        if (marker) {
            map.flyTo(marker.getLatLng(), 16, { animate: true, duration: 1 });
            marker.openPopup();
        }
    };

    window.resetMapView = function() {
        const bounds = [[KANTOR_DINAS.lat, KANTOR_DINAS.lng]];
        Object.values(markerInstances).forEach(m => bounds.push(m.getLatLng()));
        map.fitBounds(bounds, { padding: [40, 40] });
    };

    // ── 10. Switcher Tab Sidebar ─────────────────────────────────────────────
    window.switchSidebarTab = function(tab) {
        const tabList = document.getElementById('sidebar-tab-list');
        const tabRoute = document.getElementById('sidebar-tab-route');
        const btnList = document.getElementById('tab-btn-list');
        const btnRoute = document.getElementById('tab-btn-route');

        if (tab === 'route') {
            tabList.classList.add('hidden');
            tabRoute.classList.remove('hidden');
            btnRoute.className = "flex-1 py-2 text-xs font-bold rounded-lg text-brand-purple bg-purple-50 transition flex items-center justify-center gap-1.5 shadow-sm";
            btnList.className = "flex-1 py-2 text-xs font-semibold rounded-lg text-gray-600 hover:text-gray-900 transition flex items-center justify-center gap-1.5";
        } else {
            tabRoute.classList.add('hidden');
            tabList.classList.remove('hidden');
            btnList.className = "flex-1 py-2 text-xs font-bold rounded-lg text-brand-purple bg-purple-50 transition flex items-center justify-center gap-1.5 shadow-sm";
            btnRoute.className = "flex-1 py-2 text-xs font-semibold rounded-lg text-gray-600 hover:text-gray-900 transition flex items-center justify-center gap-1.5";
        }
    };

    // ── 11. Geolocation Browser Petugas ──────────────────────────────────────
    window.getUserLocation = function() {
        if (!navigator.geolocation) {
            alert('Browser Anda tidak mendukung fitur Geolocation.');
            return;
        }

        navigator.geolocation.getCurrentPosition(
            pos => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                currentUserCoords = { lat, lng };

                // Tambahkan marker posisi petugas
                L.circleMarker([lat, lng], {
                    radius: 8,
                    fillColor: '#8A67AB',
                    color: '#ffffff',
                    weight: 3,
                    opacity: 1,
                    fillOpacity: 0.9
                }).addTo(map).bindPopup('<strong>Lokasi Anda Saat Ini</strong>').openPopup();

                map.flyTo([lat, lng], 15);

                // Aktifkan pilihan GPS di dropdown rute
                const optGps = document.getElementById('opt-origin-gps');
                optGps.removeAttribute('disabled');
                optGps.textContent = `📍 Lokasi GPS Saya (${lat.toFixed(4)}, ${lng.toFixed(4)})`;
            },
            err => {
                console.warn("Gagal deteksi GPS:", err);
                alert('Gagal mengambil koordinat lokasi perangkat Anda: ' + err.message);
            }
        );
    };

    window.setOriginToCurrentGps = function() {
        if (!currentUserCoords) {
            window.getUserLocation();
        } else {
            document.getElementById('route-origin-select').value = 'gps';
            document.getElementById('origin-desc').textContent = `Titik GPS: ${currentUserCoords.lat.toFixed(5)}, ${currentUserCoords.lng.toFixed(5)}`;
        }
    };

    // ── 12. Eksekusi Kalkulasi Rute OSRM ─────────────────────────────────────
    window.quickRouteTo = function(idUmkm) {
        window.switchSidebarTab('route');
        document.getElementById('route-dest-select').value = idUmkm;
        window.executeRouteCalculation();
    };

    window.executeRouteCalculation = function() {
        const originType = document.getElementById('route-origin-select').value;
        const destId = document.getElementById('route-dest-select').value;

        if (!destId) {
            alert('Silakan pilih UMKM tujuan terlebih dahulu.');
            return;
        }

        const targetUmkm = allUmkmData.find(u => u.id_umkm == destId);
        if (!targetUmkm) {
            alert('Data UMKM tujuan tidak ditemukan.');
            return;
        }

        let originLat = KANTOR_DINAS.lat;
        let originLng = KANTOR_DINAS.lng;

        if (originType === 'gps') {
            if (!currentUserCoords) {
                alert('Lokasi GPS belum terdeteksi. Silakan klik "Gunakan GPS Saya" terlebih dahulu.');
                return;
            }
            originLat = currentUserCoords.lat;
            originLng = currentUserCoords.lng;
        }

        const destLat = targetUmkm.latitude;
        const destLng = targetUmkm.longitude;

        // Bersihkan rute sebelumnya jika ada
        window.clearActiveRoute();

        // 1. Eksekusi Routing Menggunakan Leaflet Routing Machine
        activeRouteControl = L.Routing.control({
            waypoints: [
                L.latLng(originLat, originLng),
                L.latLng(destLat, destLng)
            ],
            router: L.Routing.osrmv1({
                serviceUrl: 'https://router.project-osrm.org/route/v1',
                language: 'id',
                profile: 'driving'
            }),
            lineOptions: {
                styles: [{ color: '#8A67AB', weight: 6, opacity: 0.9 }]
            },
            showAlternatives: false,
            addWaypoints: false,
            fitSelectedRoutes: true
        }).addTo(map);

        activeRouteControl.on('routesfound', function(e) {
            const routes = e.routes;
            const summary = routes[0].summary;
            const distanceKm = (summary.totalDistance / 1000).toFixed(2);
            const durationMin = Math.round(summary.totalTime / 60);

            // Tampilkan Ringkasan di Box Hasil
            document.getElementById('route-result-box').classList.remove('hidden');
            document.getElementById('route-dist-val').textContent = distanceKm + ' km';
            document.getElementById('route-time-val').textContent = durationMin + ' menit';

            // Google Maps Navigation URL
            const gmapsUrl = `https://www.google.com/maps/dir/?api=1&origin=${originLat},${originLng}&destination=${destLat},${destLng}&travelmode=driving`;
            document.getElementById('btn-google-maps-nav').setAttribute('href', gmapsUrl);

            // Tampilkan Instruksi Belokan
            const stepsContainer = document.getElementById('route-steps-container');
            const instructions = routes[0].instructions || [];
            document.getElementById('steps-count').textContent = instructions.length + ' langkah arah';

            let stepsHtml = '';
            instructions.forEach((ins, idx) => {
                stepsHtml += `
                    <div class="py-1 border-b border-gray-100 flex items-start gap-2">
                        <span class="font-mono text-brand-purple font-bold text-[10px] w-4 flex-shrink-0">${idx + 1}.</span>
                        <div class="flex-1">
                            <p class="font-medium text-gray-800 leading-tight">${ins.text}</p>
                            <span class="text-[10px] text-gray-400 font-mono">${(ins.distance).toFixed(0)} m</span>
                        </div>
                    </div>
                `;
            });
            stepsContainer.innerHTML = stepsHtml || '<p class="text-gray-400 text-xs">Petunjuk rute siap.</p>';
        });

        activeRouteControl.on('routingerror', function(err) {
            console.warn("OSRM client routing issue, mencoba backend fallback...", err);
            // Fallback: Call backend proxy API
            fetch("{{ route('admin.webgis.rute') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    start_lat: originLat,
                    start_lng: originLng,
                    end_lat: destLat,
                    end_lng: destLng
                })
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    document.getElementById('route-result-box').classList.remove('hidden');
                    document.getElementById('route-dist-val').textContent = res.distance_km + ' km';
                    document.getElementById('route-time-val').textContent = res.duration_min + ' menit';
                    
                    const gmapsUrl = `https://www.google.com/maps/dir/?api=1&origin=${originLat},${originLng}&destination=${destLat},${destLng}&travelmode=driving`;
                    document.getElementById('btn-google-maps-nav').setAttribute('href', gmapsUrl);
                }
            });
        });
    };

    window.clearActiveRoute = function() {
        if (activeRouteControl) {
            map.removeControl(activeRouteControl);
            activeRouteControl = null;
        }
        document.getElementById('route-result-box').classList.add('hidden');
    };

    // ── 13. Muat Data Pertama Kali ───────────────────────────────────────────
    fetchUmkmData();

});
</script>
@endsection
