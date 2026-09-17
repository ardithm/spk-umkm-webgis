@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <!-- Header / Breadcrumb Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 mb-1">
                <a href="{{ route('umkm.dashboard') }}" class="hover:text-brand-purple transition-colors flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Dasbor
                </a>
                <span>/</span>
                <span class="text-brand-purple">Pengaturan Profil & Akun</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Pengaturan Profil & Akun</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola informasi identitas, kontak usaha, foto profil, dan kata sandi keamanan Anda.</p>
        </div>

        <a href="{{ route('umkm.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-gray-200 bg-white hover:bg-gray-50 text-xs font-bold text-gray-700 shadow-sm transition-all hover:border-gray-300">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Dasbor
        </a>
    </div>

    <!-- Main Grid Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left Sidebar: Profile Summary Card & Tabs Navigation -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Profile Identity Card -->
            <div class="bg-white rounded-[24px] p-6 shadow-sm border border-gray-100 relative overflow-hidden group">
                <div class="absolute -top-12 -right-12 w-32 h-32 bg-brand-purple/5 rounded-full blur-2xl group-hover:bg-brand-purple/10 transition-all duration-500"></div>

                <div class="flex flex-col items-center text-center relative z-10">
                    <!-- Avatar with quick change overlay -->
                    <div class="relative group/avatar mb-4">
                        <div class="w-24 h-24 rounded-full p-1 bg-gradient-to-tr from-brand-purple to-brand-orange shadow-md">
                            @if($user->foto_url)
                                <img src="{{ $user->foto_url }}" alt="Foto {{ $user->nama_lengkap }}" class="w-full h-full object-cover rounded-full bg-white">
                            @else
                                <div class="w-full h-full rounded-full bg-purple-50 text-brand-purple flex items-center justify-center font-extrabold text-2xl">
                                    {{ strtoupper(substr($user->nama_lengkap ?? 'U', 0, 2)) }}
                                </div>
                            @endif
                        </div>
                        <button type="button" onclick="switchTab('avatar')" title="Ubah Foto Profil" class="absolute bottom-0 right-0 w-8 h-8 rounded-full bg-brand-purple hover:bg-purple-700 text-white flex items-center justify-center shadow-md transition-all hover:scale-110">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </button>
                    </div>

                    <h2 class="text-lg font-bold text-gray-900 leading-snug">{{ $user->nama_lengkap }}</h2>
                    <p class="text-xs text-gray-500 mt-0.5">{{ '@' . $user->username }}</p>
                    
                    <div class="mt-3 flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-brand-purple/10 text-brand-purple border border-brand-purple/20">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            Pelaku UMKM
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Aktif
                        </span>
                    </div>

                    <!-- Meta info summary -->
                    <div class="w-full mt-6 pt-5 border-t border-gray-100 text-left space-y-2.5 text-xs text-gray-600">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-400">Nama Usaha:</span>
                            <span class="font-bold text-gray-800 text-right truncate max-w-[170px]">{{ $umkm->nama_umkm ?? 'Belum diisi' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-400">NIK Terdaftar:</span>
                            <span class="font-bold text-gray-800 font-mono">{{ $umkm->nik ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-400">Terdaftar Sejak:</span>
                            <span class="font-medium text-gray-700">{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Navigation Menu -->
            <div class="bg-white rounded-[24px] p-3 shadow-sm border border-gray-100 space-y-1">
                <button type="button" 
                        onclick="switchTab('profil')" 
                        id="tab-btn-profil"
                        class="tab-button w-full flex items-center justify-between px-4 py-3.5 rounded-2xl text-xs font-bold transition-all duration-300 {{ $activeTab === 'profil' ? 'bg-brand-purple text-white shadow-md shadow-brand-purple/20' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <span>Informasi Pribadi & Kontak</span>
                    </div>
                    <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                </button>

                <button type="button" 
                        onclick="switchTab('keamanan')" 
                        id="tab-btn-keamanan"
                        class="tab-button w-full flex items-center justify-between px-4 py-3.5 rounded-2xl text-xs font-bold transition-all duration-300 {{ $activeTab === 'keamanan' ? 'bg-brand-purple text-white shadow-md shadow-brand-purple/20' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        <span>Keamanan & Password</span>
                    </div>
                    <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                </button>

                <button type="button" 
                        onclick="switchTab('avatar')" 
                        id="tab-btn-avatar"
                        class="tab-button w-full flex items-center justify-between px-4 py-3.5 rounded-2xl text-xs font-bold transition-all duration-300 {{ $activeTab === 'avatar' ? 'bg-brand-purple text-white shadow-md shadow-brand-purple/20' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span>Foto Profil / Avatar</span>
                    </div>
                    <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                </button>
            </div>
        </div>

        <!-- Right Content Area: Active Tab Panel -->
        <div class="lg:col-span-8">
            
            <!-- TAB 1: INFORMASI PRIBADI & KONTAK -->
            <div id="tab-panel-profil" class="tab-panel {{ $activeTab === 'profil' ? 'block' : 'hidden' }}">
                <div class="bg-white rounded-[24px] p-6 sm:p-8 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between pb-6 mb-6 border-b border-gray-100">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Informasi Pribadi & Kontak</h3>
                            <p class="text-xs text-gray-500 mt-1">Pastikan data pemilik dan identitas usaha valid untuk mempermudah proses seleksi bantuan.</p>
                        </div>
                        <span class="w-10 h-10 rounded-2xl bg-brand-purple/10 text-brand-purple flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </span>
                    </div>

                    <form action="{{ route('umkm.akun.profil.update') }}" method="POST" onsubmit="handleFormSubmit(this)">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">
                            <!-- Section: Data Akun & Login -->
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-brand-purple mb-4 flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Kredensial Akun
                                </h4>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <!-- Username (Readonly) -->
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 mb-2">Username Login</label>
                                        <div class="relative">
                                            <input type="text" value="{{ $user->username }}" disabled
                                                   class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-500 font-mono cursor-not-allowed">
                                            <span class="absolute right-3 top-3 text-[10px] font-semibold text-gray-400 bg-gray-200/60 px-2 py-0.5 rounded-full">Tetap</span>
                                        </div>
                                        <p class="text-[11px] text-gray-400 mt-1">Username digunakan saat login sistem dan tidak dapat diubah.</p>
                                    </div>

                                    <!-- Email -->
                                    <div>
                                        <label for="email" class="block text-xs font-bold text-gray-700 mb-2">
                                            Alamat Email <span class="text-brand-purple font-normal">(Untuk Notifikasi)</span>
                                        </label>
                                        <input type="email" id="email" name="email" 
                                               value="{{ old('email', $user->email) }}" 
                                               placeholder="contoh: umkm.juara@gmail.com"
                                               class="w-full px-4 py-3 bg-white border @error('email') border-red-500 ring-2 ring-red-100 @else border-gray-200 @enderror rounded-xl text-sm text-gray-900 focus:outline-none focus:border-brand-purple focus:ring-4 focus:ring-brand-purple/10 transition-all">
                                        @error('email')
                                            <p class="text-xs text-red-600 mt-1.5 flex items-center gap-1 font-medium">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-gray-100 pt-6"></div>

                            <!-- Section: Data Identitas Pemilik & UMKM -->
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-brand-purple mb-4 flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    Data Pemilik & Entitas Usaha
                                </h4>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <!-- Nama Lengkap Akun -->
                                    <div>
                                        <label for="nama_lengkap" class="block text-xs font-bold text-gray-700 mb-2">Nama Lengkap Akun <span class="text-red-500">*</span></label>
                                        <input type="text" id="nama_lengkap" name="nama_lengkap" 
                                               value="{{ old('nama_lengkap', $user->nama_lengkap) }}" required
                                               class="w-full px-4 py-3 bg-white border @error('nama_lengkap') border-red-500 ring-2 ring-red-100 @else border-gray-200 @enderror rounded-xl text-sm text-gray-900 focus:outline-none focus:border-brand-purple focus:ring-4 focus:ring-brand-purple/10 transition-all">
                                        @error('nama_lengkap')
                                            <p class="text-xs text-red-600 mt-1.5 flex items-center gap-1 font-medium">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                    <!-- Nama Pemilik Usaha -->
                                    <div>
                                        <label for="nama_pemilik" class="block text-xs font-bold text-gray-700 mb-2">Nama Pemilik Sesuai KTP <span class="text-red-500">*</span></label>
                                        <input type="text" id="nama_pemilik" name="nama_pemilik" 
                                               value="{{ old('nama_pemilik', $umkm->nama_pemilik) }}" required
                                               class="w-full px-4 py-3 bg-white border @error('nama_pemilik') border-red-500 ring-2 ring-red-100 @else border-gray-200 @enderror rounded-xl text-sm text-gray-900 focus:outline-none focus:border-brand-purple focus:ring-4 focus:ring-brand-purple/10 transition-all">
                                        @error('nama_pemilik')
                                            <p class="text-xs text-red-600 mt-1.5 flex items-center gap-1 font-medium">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                    <!-- Nama UMKM -->
                                    <div>
                                        <label for="nama_umkm" class="block text-xs font-bold text-gray-700 mb-2">Nama Usaha / Merek Dagang <span class="text-red-500">*</span></label>
                                        <input type="text" id="nama_umkm" name="nama_umkm" 
                                               value="{{ old('nama_umkm', $umkm->nama_umkm) }}" required
                                               class="w-full px-4 py-3 bg-white border @error('nama_umkm') border-red-500 ring-2 ring-red-100 @else border-gray-200 @enderror rounded-xl text-sm text-gray-900 focus:outline-none focus:border-brand-purple focus:ring-4 focus:ring-brand-purple/10 transition-all">
                                        @error('nama_umkm')
                                            <p class="text-xs text-red-600 mt-1.5 flex items-center gap-1 font-medium">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                    <!-- NIK (16 digit) -->
                                    <div>
                                        <label for="nik" class="block text-xs font-bold text-gray-700 mb-2">Nomor Induk Kependudukan (NIK) <span class="text-red-500">*</span></label>
                                        <input type="text" id="nik" name="nik" maxlength="16"
                                               value="{{ old('nik', $umkm->nik) }}" required
                                               placeholder="16 digit nomor NIK sesuai KTP"
                                               class="w-full px-4 py-3 bg-white border @error('nik') border-red-500 ring-2 ring-red-100 @else border-gray-200 @enderror rounded-xl text-sm font-mono text-gray-900 focus:outline-none focus:border-brand-purple focus:ring-4 focus:ring-brand-purple/10 transition-all">
                                        @error('nik')
                                            <p class="text-xs text-red-600 mt-1.5 flex items-center gap-1 font-medium">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                    <!-- Nomor Telepon / WhatsApp -->
                                    <div class="md:col-span-2">
                                        <label for="no_telepon" class="block text-xs font-bold text-gray-700 mb-2">
                                            Nomor Telepon / WhatsApp Aktif <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                            </div>
                                            <input type="text" id="no_telepon" name="no_telepon" 
                                                   value="{{ old('no_telepon', $umkm->no_telepon) }}" required
                                                   placeholder="contoh: 081234567890"
                                                   class="w-full pl-10 pr-4 py-3 bg-white border @error('no_telepon') border-red-500 ring-2 ring-red-100 @else border-gray-200 @enderror rounded-xl text-sm text-gray-900 focus:outline-none focus:border-brand-purple focus:ring-4 focus:ring-brand-purple/10 transition-all">
                                        </div>
                                        @error('no_telepon')
                                            <p class="text-xs text-red-600 mt-1.5 flex items-center gap-1 font-medium">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                    <!-- Alamat Usaha / Domisili -->
                                    <div class="md:col-span-2">
                                        <label for="alamat" class="block text-xs font-bold text-gray-700 mb-2">Alamat Lengkap Usaha / Domisili <span class="text-red-500">*</span></label>
                                        <textarea id="alamat" name="alamat" rows="3" required
                                                  placeholder="Tuliskan nama jalan, RT/RW, kelurahan, dan kecamatan di Kota Banjarmasin..."
                                                  class="w-full px-4 py-3 bg-white border @error('alamat') border-red-500 ring-2 ring-red-100 @else border-gray-200 @enderror rounded-xl text-sm text-gray-900 focus:outline-none focus:border-brand-purple focus:ring-4 focus:ring-brand-purple/10 transition-all">{{ old('alamat', $umkm->alamat) }}</textarea>
                                        @error('alamat')
                                            <p class="text-xs text-red-600 mt-1.5 flex items-center gap-1 font-medium">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
                                <a href="{{ route('umkm.dashboard') }}" class="px-5 py-2.5 rounded-full border border-gray-200 text-xs font-bold text-gray-600 hover:bg-gray-50 transition-colors">
                                    Batal
                                </a>
                                <button type="submit" class="submit-button inline-flex items-center justify-center gap-2 bg-brand-orange hover:bg-orange-500 text-white text-xs font-bold px-7 py-3 rounded-full shadow-md shadow-brand-orange/30 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 focus:ring-4 focus:ring-brand-orange/20">
                                    <span class="btn-text flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                        Simpan Perubahan Profil
                                    </span>
                                    <span class="btn-spinner hidden">
                                        <svg class="animate-spin h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        Menyimpan...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- TAB 2: KEAMANAN & GANTI PASSWORD -->
            <div id="tab-panel-keamanan" class="tab-panel {{ $activeTab === 'keamanan' ? 'block' : 'hidden' }}">
                <div class="bg-white rounded-[24px] p-6 sm:p-8 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between pb-6 mb-6 border-b border-gray-100">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Keamanan Akun</h3>
                            <p class="text-xs text-gray-500 mt-1">Perbarui kata sandi secara berkala untuk menjaga kerahasiaan dan keamanan data akun UMKM Anda.</p>
                        </div>
                        <span class="w-10 h-10 rounded-2xl bg-brand-orange/10 text-brand-orange flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </span>
                    </div>

                    <!-- Security Alert Callout -->
                    <div class="mb-6 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-start gap-3">
                        <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="space-y-1">
                            <p class="font-bold">Standar Keamanan Kata Sandi</p>
                            <p class="text-amber-800 leading-relaxed">
                                Gunakan minimal <strong>8 karakter</strong> yang mengombinasikan huruf besar, huruf kecil, dan angka. Hindari menggunakan informasi pribadi seperti tanggal lahir atau NIK.
                            </p>
                        </div>
                    </div>

                    <form action="{{ route('umkm.akun.password.update') }}" method="POST" onsubmit="handleFormSubmit(this)">
                        @csrf
                        @method('PUT')

                        <div class="space-y-5 max-w-xl">
                            <!-- Password Saat Ini -->
                            <div>
                                <label for="current_password" class="block text-xs font-bold text-gray-700 mb-2">
                                    Kata Sandi Saat Ini <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" id="current_password" name="current_password" required
                                           placeholder="Masukkan kata sandi lama Anda"
                                           class="w-full px-4 py-3 bg-white border @error('current_password') border-red-500 ring-2 ring-red-100 @else border-gray-200 @enderror rounded-xl text-sm text-gray-900 focus:outline-none focus:border-brand-purple focus:ring-4 focus:ring-brand-purple/10 transition-all">
                                    <button type="button" onclick="togglePasswordVisibility('current_password', this)" class="absolute right-3 top-3.5 text-gray-400 hover:text-gray-600">
                                        <svg class="w-4 h-4 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>
                                </div>
                                @error('current_password')
                                    <p class="text-xs text-red-600 mt-1.5 flex items-center gap-1 font-medium">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Password Baru -->
                            <div>
                                <label for="password" class="block text-xs font-bold text-gray-700 mb-2">
                                    Kata Sandi Baru <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" id="password" name="password" required
                                           placeholder="Minimal 8 karakter campuran huruf & angka"
                                           class="w-full px-4 py-3 bg-white border @error('password') border-red-500 ring-2 ring-red-100 @else border-gray-200 @enderror rounded-xl text-sm text-gray-900 focus:outline-none focus:border-brand-purple focus:ring-4 focus:ring-brand-purple/10 transition-all">
                                    <button type="button" onclick="togglePasswordVisibility('password', this)" class="absolute right-3 top-3.5 text-gray-400 hover:text-gray-600">
                                        <svg class="w-4 h-4 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="text-xs text-red-600 mt-1.5 flex items-center gap-1 font-medium">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Konfirmasi Password Baru -->
                            <div>
                                <label for="password_confirmation" class="block text-xs font-bold text-gray-700 mb-2">
                                    Konfirmasi Kata Sandi Baru <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" id="password_confirmation" name="password_confirmation" required
                                           placeholder="Ketik ulang kata sandi baru Anda"
                                           class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm text-gray-900 focus:outline-none focus:border-brand-purple focus:ring-4 focus:ring-brand-purple/10 transition-all">
                                    <button type="button" onclick="togglePasswordVisibility('password_confirmation', this)" class="absolute right-3 top-3.5 text-gray-400 hover:text-gray-600">
                                        <svg class="w-4 h-4 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-4 flex items-center gap-3">
                                <button type="submit" class="submit-button inline-flex items-center justify-center gap-2 bg-brand-purple hover:bg-purple-700 text-white text-xs font-bold px-7 py-3 rounded-full shadow-md shadow-brand-purple/30 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 focus:ring-4 focus:ring-brand-purple/20">
                                    <span class="btn-text flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                        Perbarui Kata Sandi
                                    </span>
                                    <span class="btn-spinner hidden">
                                        <svg class="animate-spin h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        Menyimpan...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- TAB 3: FOTO PROFIL / AVATAR -->
            <div id="tab-panel-avatar" class="tab-panel {{ $activeTab === 'avatar' ? 'block' : 'hidden' }}">
                <div class="bg-white rounded-[24px] p-6 sm:p-8 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between pb-6 mb-6 border-b border-gray-100">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Kelola Foto Profil</h3>
                            <p class="text-xs text-gray-500 mt-1">Unggah foto profil asli Anda atau logo usaha untuk mempermudah identifikasi pendaftar.</p>
                        </div>
                        <span class="w-10 h-10 rounded-2xl bg-brand-purple/10 text-brand-purple flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
                        <!-- Preview Column -->
                        <div class="md:col-span-4 flex flex-col items-center justify-center p-6 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                            <div class="w-32 h-32 rounded-full p-1.5 bg-white border-2 border-brand-purple shadow-sm overflow-hidden relative">
                                <img id="avatar-preview-img" 
                                     src="{{ $user->foto_url ?? 'data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'100\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%238A67AB\' stroke-width=\'1.5\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><path d=\'M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2\'></path><circle cx=\'12\' cy=\'7\' r=\'4\'></circle></svg>' }}" 
                                     alt="Preview Avatar" 
                                     class="w-full h-full object-cover rounded-full">
                            </div>
                            <span class="text-[11px] font-bold text-gray-500 mt-3" id="avatar-preview-label">
                                {{ $user->foto_url ? 'Foto Saat Ini' : 'Pratinjau Avatar' }}
                            </span>

                            @if($user->foto)
                                <form action="{{ route('umkm.akun.avatar.delete') }}" method="POST" class="mt-3 w-full" onsubmit="return confirm('Apakah Anda yakin ingin menghapus foto profil ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full text-center text-xs font-bold text-red-600 hover:text-red-700 hover:bg-red-50 py-1.5 rounded-full border border-red-200 transition-colors">
                                        Hapus Foto
                                    </button>
                                </form>
                            @endif
                        </div>

                        <!-- Upload Form Column -->
                        <div class="md:col-span-8">
                            <form action="{{ route('umkm.akun.avatar.update') }}" method="POST" enctype="multipart/form-data" onsubmit="handleFormSubmit(this)">
                                @csrf

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 mb-2">Pilih Berkas Foto Baru</label>
                                        <input type="file" id="foto-input" name="foto" accept="image/jpeg,image/png,image/jpg,image/webp" required
                                               onchange="previewAvatarImage(this)"
                                               class="block w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-brand-purple/10 file:text-brand-purple hover:file:bg-brand-purple hover:file:text-white file:transition-colors file:cursor-pointer border border-gray-200 rounded-xl p-2 bg-white cursor-pointer">
                                        @error('foto')
                                            <p class="text-xs text-red-600 mt-1.5 flex items-center gap-1 font-medium">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                    <div class="bg-purple-50/50 border border-brand-purple/10 rounded-xl p-4 text-xs text-gray-600 space-y-1.5">
                                        <p class="font-bold text-gray-800 flex items-center gap-1.5 text-brand-purple">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Ketentuan Berkas:
                                        </p>
                                        <ul class="list-disc list-inside space-y-1 text-[11px] text-gray-500">
                                            <li>Format gambar yang didukung: <strong>JPG, JPEG, PNG, atau WEBP</strong>.</li>
                                            <li>Ukuran maksimal file: <strong>2 Megabyte (2048 KB)</strong>.</li>
                                            <li>Gunakan foto rasio 1:1 (persegi) dengan pencahayaan jelas untuk hasil maksimal.</li>
                                        </ul>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="pt-2 flex items-center gap-3">
                                        <button type="submit" class="submit-button inline-flex items-center justify-center gap-2 bg-brand-orange hover:bg-orange-500 text-white text-xs font-bold px-7 py-3 rounded-full shadow-md shadow-brand-orange/30 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 focus:ring-4 focus:ring-brand-orange/20">
                                            <span class="btn-text flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                                Unggah & Simpan Foto
                                            </span>
                                            <span class="btn-spinner hidden">
                                                <svg class="animate-spin h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                Mengunggah...
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
    // Tab Switcher Handler
    function switchTab(tabName) {
        // Hide all panels
        document.querySelectorAll('.tab-panel').forEach(panel => {
            panel.classList.add('hidden');
            panel.classList.remove('block');
        });

        // Show target panel
        const targetPanel = document.getElementById('tab-panel-' + tabName);
        if (targetPanel) {
            targetPanel.classList.remove('hidden');
            targetPanel.classList.add('block');
        }

        // Update Tab button active styles
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('bg-brand-purple', 'text-white', 'shadow-md', 'shadow-brand-purple/20');
            btn.classList.add('text-gray-600', 'hover:bg-gray-50', 'hover:text-gray-900');
        });

        const activeBtn = document.getElementById('tab-btn-' + tabName);
        if (activeBtn) {
            activeBtn.classList.add('bg-brand-purple', 'text-white', 'shadow-md', 'shadow-brand-purple/20');
            activeBtn.classList.remove('text-gray-600', 'hover:bg-gray-50', 'hover:text-gray-900');
        }

        // Update URL parameter without full reload
        const url = new URL(window.location);
        url.searchParams.set('tab', tabName);
        window.history.replaceState({}, '', url);
    }

    // Toggle Password Visibility
    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        if (!input) return;

        if (input.type === 'password') {
            input.type = 'text';
            btn.innerHTML = `<svg class="w-4 h-4 text-brand-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>`;
        } else {
            input.type = 'password';
            btn.innerHTML = `<svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>`;
        }
    }

    // Instant Image Preview for Avatar
    function previewAvatarImage(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];

            // Basic client-side validation
            if (file.size > 2048 * 1024) {
                alert('Peringatan: Ukuran foto melebihi 2MB! Silakan pilih foto lain yang lebih kecil.');
                input.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('avatar-preview-img');
                const label = document.getElementById('avatar-preview-label');
                if (img) img.src = e.target.result;
                if (label) label.textContent = 'Pratinjau Terpilih: ' + file.name;
            }
            reader.readAsDataURL(file);
        }
    }

    // Double Submit Prevention & Loading Indicator
    function handleFormSubmit(form) {
        const submitBtn = form.querySelector('.submit-button');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-75', 'cursor-not-allowed');

            const textEl = submitBtn.querySelector('.btn-text');
            const spinnerEl = submitBtn.querySelector('.btn-spinner');

            if (textEl) textEl.classList.add('hidden');
            if (spinnerEl) spinnerEl.classList.remove('hidden');
        }
    }

    // Initialize Active Tab from URL on Load if Present
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        if (tab && ['profil', 'keamanan', 'avatar'].includes(tab)) {
            switchTab(tab);
        }
    });
</script>
@endpush
@endsection
