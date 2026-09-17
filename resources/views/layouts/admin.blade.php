<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SiBantuan UMKM - Admin Dashboard</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            purple: '#8A67AB',
                            orange: '#FFB347',
                            dark: '#1F1F1F',
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8f9fa; }
        
        /* Custom Scrollbar for Sidebar */
        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background-color: rgba(138, 103, 171, 0.2);
            border-radius: 20px;
        }
    </style>
</head>
<body class="text-brand-dark antialiased flex h-screen overflow-hidden bg-gray-50/50">
    
    <!-- Left Sidebar -->
    <aside class="w-64 bg-white border-r border-gray-100 flex flex-col hidden md:flex z-20 shrink-0 shadow-sm relative">
        <!-- Decorative Sparkle -->
        <div class="absolute top-6 left-4 text-brand-purple/20">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0l2.5 9.5L24 12l-9.5 2.5L12 24l-2.5-9.5L0 12l9.5-2.5z"/></svg>
        </div>

        <!-- Logo & Brand -->
        <div class="h-20 flex items-center px-6 border-b border-gray-50">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-[14px] bg-gradient-to-br from-brand-purple to-purple-600 flex items-center justify-center text-white shadow-md shadow-brand-purple/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div>
                    <span class="text-base font-extrabold text-brand-dark tracking-tight leading-none block">
                        SiBantuan <span class="text-brand-purple">UMKM</span>
                    </span>
                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-0.5 block">Admin Panel</span>
                </div>
            </a>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 overflow-y-auto sidebar-scroll py-6 px-4 space-y-1.5">
            <div class="px-3 text-[10px] font-bold uppercase text-gray-400 tracking-wider mb-2 mt-4">Main Menu</div>
            
            <a href="{{ route('admin.dashboard') }}" 
               class="px-4 py-3 rounded-2xl text-sm font-semibold transition-all flex items-center gap-3 {{ request()->routeIs('admin.dashboard*') ? 'bg-brand-purple text-white shadow-md shadow-brand-purple/20' : 'text-gray-500 hover:bg-purple-50 hover:text-brand-purple' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.dashboard*') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Dashboard
            </a>

            <a href="{{ route('admin.pengguna.index') }}" 
               class="px-4 py-3 rounded-2xl text-sm font-semibold transition-all flex items-center gap-3 {{ request()->routeIs('admin.pengguna*') ? 'bg-brand-purple text-white shadow-md shadow-brand-purple/20' : 'text-gray-500 hover:bg-purple-50 hover:text-brand-purple' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.pengguna*') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                Kelola Pengguna
            </a>
            
            <a href="{{ route('admin.dokumen.index') }}" 
               class="px-4 py-3 rounded-2xl text-sm font-semibold transition-all flex items-center gap-3 {{ request()->routeIs('admin.dokumen*') ? 'bg-brand-purple text-white shadow-md shadow-brand-purple/20' : 'text-gray-500 hover:bg-purple-50 hover:text-brand-purple' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.dokumen*') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Verifikasi Berkas
            </a>

            <div class="px-3 text-[10px] font-bold uppercase text-gray-400 tracking-wider mb-2 mt-6">Sistem Keputusan</div>

            <a href="{{ route('admin.proses.index') }}" 
               class="px-4 py-3 rounded-2xl text-sm font-semibold transition-all flex items-center gap-3 {{ request()->routeIs('admin.proses*') ? 'bg-brand-purple text-white shadow-md shadow-brand-purple/20' : 'text-gray-500 hover:bg-purple-50 hover:text-brand-purple' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.proses*') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Manajemen Periode
            </a>

            <a href="{{ route('admin.spk.index') }}" 
               class="px-4 py-3 rounded-2xl text-sm font-semibold transition-all flex items-center gap-3 {{ request()->routeIs('admin.spk*') ? 'bg-brand-purple text-white shadow-md shadow-brand-purple/20' : 'text-gray-500 hover:bg-purple-50 hover:text-brand-purple' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.spk*') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                SPK Profile Matching
            </a>
            
            <a href="{{ route('admin.kriteria.index') }}" 
               class="px-4 py-3 rounded-2xl text-sm font-semibold transition-all flex items-center gap-3 {{ request()->routeIs('admin.kriteria*') ? 'bg-brand-purple text-white shadow-md shadow-brand-purple/20' : 'text-gray-500 hover:bg-purple-50 hover:text-brand-purple' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.kriteria*') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                Kriteria SPK
            </a>
            
            <a href="{{ route('admin.konversi.index') }}" 
               class="px-4 py-3 rounded-2xl text-sm font-semibold transition-all flex items-center gap-3 {{ request()->routeIs('admin.konversi*') ? 'bg-brand-purple text-white shadow-md shadow-brand-purple/20' : 'text-gray-500 hover:bg-purple-50 hover:text-brand-purple' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.konversi*') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                Konversi Skor
            </a>

            <div class="px-3 text-[10px] font-bold uppercase text-gray-400 tracking-wider mb-2 mt-6">Pemetaan Spasial</div>

            <a href="{{ route('admin.webgis.index') }}" 
               class="px-4 py-3 rounded-2xl text-sm font-semibold transition-all flex items-center gap-3 {{ request()->routeIs('admin.webgis*') ? 'bg-brand-purple text-white shadow-md shadow-brand-purple/20' : 'text-gray-500 hover:bg-purple-50 hover:text-brand-purple' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.webgis*') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                WebGIS & Rute
            </a>

            <div class="px-3 text-[10px] font-bold uppercase text-gray-400 tracking-wider mb-2 mt-6">Pelaporan & SK</div>

            <a href="{{ route('admin.laporan.index') }}" 
               class="px-4 py-3 rounded-2xl text-sm font-semibold transition-all flex items-center gap-3 {{ request()->routeIs('admin.laporan*') ? 'bg-brand-purple text-white shadow-md shadow-brand-purple/20' : 'text-gray-500 hover:bg-purple-50 hover:text-brand-purple' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.laporan*') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Laporan & Rekapitulasi
            </a>
        </nav>

        <!-- User Profile Area (Bottom of Sidebar) -->
        <div class="p-4 border-t border-gray-50">
            <div class="bg-gray-50 rounded-2xl p-3 flex items-center justify-between border border-gray-100/50">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-purple-100 text-brand-purple flex items-center justify-center font-bold text-xs border border-purple-200">
                        {{ strtoupper(substr(Auth::user()->nama_lengkap ?? 'A', 0, 2)) }}
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xs font-bold text-brand-dark line-clamp-1 w-24">{{ Auth::user()->nama_lengkap ?? Auth::user()->username }}</span>
                        <span class="text-[9px] font-bold text-brand-purple uppercase tracking-wider">Admin</span>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="Keluar" class="w-8 h-8 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-400 hover:text-red-500 hover:border-red-200 hover:bg-red-50 transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col overflow-hidden relative">
        <!-- Top Mobile Header (only visible on small screens) -->
        <header class="md:hidden h-16 bg-white border-b border-gray-100 flex items-center justify-between px-4 z-10 shrink-0">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-brand-purple to-purple-600 flex items-center justify-center text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <span class="text-sm font-bold text-brand-dark">SiBantuan</span>
            </div>
            <!-- Mobile Menu Button (Optional to implement full mobile nav later) -->
            <button class="p-2 text-gray-500 bg-gray-50 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
        </header>

        <!-- Main Scrollable Area -->
        <main class="flex-1 overflow-y-auto p-4 md:p-8 bg-gray-50/50">
            <div class="max-w-6xl mx-auto">
                @if(session('success'))
                    <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl flex items-center gap-3 shadow-sm">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <span class="font-medium text-sm">{{ session('success') }}</span>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-2xl flex items-center gap-3 shadow-sm">
                        <div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </div>
                        <span class="font-medium text-sm">{{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    @stack('scripts')
</body>
</html>
