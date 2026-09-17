<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SiBantuan UMKM — Portal Pelaku UMKM</title>
    
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
<body class="text-gray-800 antialiased bg-gray-50/50 min-h-screen">

    @auth
        <!-- Mobile Sidebar Backdrop Overlay -->
        <div id="sidebar-backdrop" 
             class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-40 hidden md:hidden transition-opacity duration-300 opacity-0"
             onclick="closeSidebarDrawer()"></div>

        <!-- Sidebar Samping Vertikal (Persistent on Desktop, Drawer on Mobile) -->
        <aside id="sidebar-drawer" 
               class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-100 flex flex-col shadow-lg md:shadow-sm transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">
            
            <!-- Logo & Brand Header -->
            <div class="h-20 flex items-center justify-between px-6 border-b border-gray-50 shrink-0">
                <a href="{{ route('umkm.dashboard') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[14px] bg-gradient-to-br from-brand-purple to-purple-600 flex items-center justify-center text-white shadow-md shadow-brand-purple/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div>
                        <span class="text-base font-extrabold text-gray-900 tracking-tight leading-none block">
                            SiBantuan <span class="text-brand-purple">UMKM</span>
                        </span>
                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1 block">Portal Pemohon</span>
                    </div>
                </a>

                <!-- Mobile Close Button (X) -->
                <button type="button" 
                        onclick="closeSidebarDrawer()" 
                        class="md:hidden p-1.5 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Vertical Navigation Menu -->
            <nav class="flex-1 overflow-y-auto sidebar-scroll py-6 px-4 space-y-1.5">
                <div class="px-3 text-[10px] font-bold uppercase text-gray-400 tracking-wider mb-2">Menu Utama</div>
                
                <!-- 1. Dasbor -->
                <a href="{{ route('umkm.dashboard') }}" 
                   class="px-4 py-3 rounded-2xl text-xs font-bold transition-all flex items-center gap-3 {{ request()->routeIs('umkm.dashboard*') ? 'bg-brand-purple text-white shadow-md shadow-brand-purple/20' : 'text-gray-600 hover:bg-purple-50 hover:text-brand-purple' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('umkm.dashboard*') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Dasbor</span>
                </a>

                <!-- 2. Pengajuan Bantuan -->
                <a href="{{ route('umkm.pengajuan.index') }}" 
                   class="px-4 py-3 rounded-2xl text-xs font-bold transition-all flex items-center gap-3 {{ request()->routeIs('umkm.pengajuan*') ? 'bg-brand-purple text-white shadow-md shadow-brand-purple/20' : 'text-gray-600 hover:bg-purple-50 hover:text-brand-purple' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('umkm.pengajuan*') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Pengajuan Bantuan</span>
                </a>

                <!-- 3. Profil & Akun Usaha -->
                <a href="{{ route('umkm.akun.edit') }}" 
                   class="px-4 py-3 rounded-2xl text-xs font-bold transition-all flex items-center gap-3 {{ request()->routeIs('umkm.akun*') ? 'bg-brand-purple text-white shadow-md shadow-brand-purple/20' : 'text-gray-600 hover:bg-purple-50 hover:text-brand-purple' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('umkm.akun*') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>Profil & Akun</span>
                </a>

                <div class="px-3 text-[10px] font-bold uppercase text-gray-400 tracking-wider mb-2 mt-6">Tautan Luar</div>

                <!-- Halaman Depan -->
                <a href="{{ url('/') }}" 
                   class="px-4 py-2.5 rounded-2xl text-xs font-semibold text-gray-500 hover:bg-gray-50 hover:text-gray-900 transition-all flex items-center gap-3">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                    <span>Halaman Utama</span>
                </a>
            </nav>

            <!-- User Profile Area & Logout Button (Bottom Footer of Sidebar) -->
            <div class="p-4 border-t border-gray-100 shrink-0">
                <div class="bg-gray-50 rounded-2xl p-3 flex items-center justify-between border border-gray-100">
                    <a href="{{ route('umkm.akun.edit') }}" class="flex items-center gap-2.5 group flex-1 min-w-0 mr-2">
                        @if(Auth::user()->foto_url)
                            <img src="{{ Auth::user()->foto_url }}" alt="Avatar" class="w-9 h-9 rounded-full object-cover border-2 border-brand-purple/20 group-hover:border-brand-purple transition-all shrink-0 shadow-sm">
                        @else
                            <div class="w-9 h-9 rounded-full bg-brand-purple/10 text-brand-purple flex items-center justify-center font-bold text-xs border border-brand-purple/20 group-hover:bg-brand-purple group-hover:text-white transition-all shrink-0 shadow-sm">
                                {{ strtoupper(substr(Auth::user()->nama_lengkap ?? 'U', 0, 2)) }}
                            </div>
                        @endif
                        <div class="flex flex-col min-w-0">
                            <span class="text-xs font-bold text-gray-900 truncate group-hover:text-brand-purple transition-colors">
                                {{ Auth::user()->nama_lengkap ?? Auth::user()->username }}
                            </span>
                            <span class="text-[9px] font-bold text-brand-purple uppercase tracking-wider">
                                Pelaku UMKM
                            </span>
                        </div>
                    </a>

                    <form action="{{ route('logout') }}" method="POST" class="shrink-0">
                        @csrf
                        <button type="submit" 
                                title="Keluar Sistem"
                                class="w-8 h-8 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-400 hover:text-red-500 hover:border-red-200 hover:bg-red-50 transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Layout Container (Offset by Sidebar on Desktop) -->
        <div class="md:pl-64 flex flex-col min-h-screen">
            
            <!-- Mobile Topbar (Hidden on Desktop) -->
            <header class="md:hidden h-16 bg-white/95 backdrop-blur-md border-b border-gray-100 flex items-center justify-between px-4 sticky top-0 z-30 shrink-0">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-brand-purple to-purple-600 flex items-center justify-center text-white shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-extrabold text-gray-900 tracking-tight">
                        SiBantuan <span class="text-brand-purple">UMKM</span>
                    </span>
                </div>

                <!-- Hamburger Button -->
                <button type="button" 
                        onclick="openSidebarDrawer()" 
                        class="p-2 rounded-xl text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-colors focus:outline-none"
                        aria-label="Buka Menu Navigasi">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-[20px] shadow-sm flex items-start gap-3.5 transition-all" role="alert">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-bold text-sm text-emerald-950">Berhasil!</p>
                            <p class="text-xs text-emerald-700 mt-0.5">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-[20px] shadow-sm flex items-start gap-3.5 transition-all" role="alert">
                        <div class="w-8 h-8 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-bold text-sm text-rose-950">Perhatian!</p>
                            <p class="text-xs text-rose-700 mt-0.5">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="mb-6 bg-amber-50 border border-amber-200 text-amber-800 px-5 py-4 rounded-[20px] shadow-sm flex items-start gap-3.5 transition-all" role="alert">
                        <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-bold text-sm text-amber-950">Peringatan!</p>
                            <p class="text-xs text-amber-800 mt-0.5">{{ session('warning') }}</p>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    @else
        <!-- Guest View (Login, Register, etc.) -->
        <header class="bg-white border-b border-gray-100 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <a href="{{ url('/') }}" class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-purple to-purple-500 flex items-center justify-center text-white shadow-md shadow-brand-purple/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="text-lg font-bold text-gray-900 tracking-tight flex items-center gap-1.5">
                                SiBantuan <span class="text-brand-purple">UMKM</span>
                            </span>
                            <span class="text-[10px] font-medium text-gray-400 block -mt-1">Dinas Koperasi & Tenaga Kerja</span>
                        </div>
                    </a>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('login') }}" class="text-xs font-bold text-brand-purple hover:underline px-3 py-2">Masuk</a>
                        <a href="{{ route('register') }}" class="text-xs font-bold text-white bg-brand-purple hover:bg-purple-700 px-4 py-2 rounded-full shadow-sm transition-all">Daftar</a>
                    </div>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @yield('content')
        </main>
    @endauth

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <!-- Mobile Drawer Script -->
    <script>
        function openSidebarDrawer() {
            const drawer = document.getElementById('sidebar-drawer');
            const backdrop = document.getElementById('sidebar-backdrop');
            if (drawer && backdrop) {
                backdrop.classList.remove('hidden');
                setTimeout(() => {
                    backdrop.classList.remove('opacity-0');
                    backdrop.classList.add('opacity-100');
                    drawer.classList.remove('-translate-x-full');
                }, 10);
            }
        }

        function closeSidebarDrawer() {
            const drawer = document.getElementById('sidebar-drawer');
            const backdrop = document.getElementById('sidebar-backdrop');
            if (drawer && backdrop) {
                drawer.classList.add('-translate-x-full');
                backdrop.classList.remove('opacity-100');
                backdrop.classList.add('opacity-0');
                setTimeout(() => {
                    backdrop.classList.add('hidden');
                }, 300);
            }
        }

        // Close on ESC key press
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSidebarDrawer();
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
