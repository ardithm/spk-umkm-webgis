@extends('layouts.admin')

@section('content')
<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('admin.pengguna.index') }}" class="p-2 bg-white text-gray-500 hover:text-brand-purple border border-gray-200 hover:border-brand-purple hover:bg-purple-50 rounded-xl transition shadow-sm">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Tambah Pengguna Baru</h1>
        <p class="text-sm text-gray-500 mt-1">Lengkapi form berikut untuk mendaftarkan akun Admin atau UMKM baru.</p>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 max-w-3xl">
    <form action="{{ route('admin.pengguna.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Profil Akun -->
        <div>
            <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-brand-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Informasi Dasar
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                
                <!-- Nama Lengkap -->
                <div class="md:col-span-2">
                    <label for="nama_lengkap" class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required placeholder="Contoh: John Doe"
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-purple focus:border-brand-purple focus:bg-white transition text-sm @error('nama_lengkap') border-red-500 ring-1 ring-red-500 @enderror">
                    @error('nama_lengkap')
                        <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-semibold text-gray-700 mb-1.5">Username <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">@</div>
                        <input type="text" id="username" name="username" value="{{ old('username') }}" required placeholder="johndoe123"
                               class="w-full pl-9 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-purple focus:border-brand-purple focus:bg-white transition text-sm @error('username') border-red-500 ring-1 ring-red-500 @enderror">
                    </div>
                    @error('username')
                        <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p>
                    @else
                        <p class="mt-1.5 text-[11px] text-gray-500">Unik, tanpa spasi, untuk login.</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Alamat Email <span class="text-red-500">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="john@example.com"
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-purple focus:border-brand-purple focus:bg-white transition text-sm @error('email') border-red-500 ring-1 ring-red-500 @enderror">
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role -->
                <div class="md:col-span-2 mt-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Hak Akses (Role) <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="relative flex items-center p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition [&:has(input:checked)]:border-brand-purple [&:has(input:checked)]:bg-purple-50/50 [&:has(input:checked)]:ring-1 [&:has(input:checked)]:ring-brand-purple group">
                            <input type="radio" name="role" value="umkm" class="w-4 h-4 text-brand-purple bg-gray-100 border-gray-300 focus:ring-brand-purple focus:ring-2" {{ old('role', 'umkm') == 'umkm' ? 'checked' : '' }}>
                            <div class="ml-3 flex-1">
                                <span class="block text-sm font-bold text-gray-900 group-hover:text-brand-purple">Pelaku UMKM</span>
                                <span class="block text-xs text-gray-500 mt-0.5">Akun untuk pendaftar bantuan modal.</span>
                            </div>
                        </label>
                        
                        <label class="relative flex items-center p-4 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition [&:has(input:checked)]:border-brand-purple [&:has(input:checked)]:bg-purple-50/50 [&:has(input:checked)]:ring-1 [&:has(input:checked)]:ring-brand-purple group">
                            <input type="radio" name="role" value="admin" class="w-4 h-4 text-brand-purple bg-gray-100 border-gray-300 focus:ring-brand-purple focus:ring-2" {{ old('role') == 'admin' ? 'checked' : '' }}>
                            <div class="ml-3 flex-1">
                                <span class="block text-sm font-bold text-gray-900 group-hover:text-brand-purple">Administrator</span>
                                <span class="block text-xs text-gray-500 mt-0.5">Akses penuh ke panel dinas.</span>
                            </div>
                        </label>
                    </div>
                    @error('role')
                        <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <hr class="border-gray-100">

        <!-- Kredensial Keamanan -->
        <div>
            <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-brand-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Kredensial Keamanan
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Kata Sandi (Password) <span class="text-red-500">*</span></label>
                    <input type="password" id="password" name="password" required placeholder="Minimal 8 karakter"
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-purple focus:border-brand-purple focus:bg-white transition text-sm @error('password') border-red-500 ring-1 ring-red-500 @enderror">
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Konfirmasi Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1.5">Ulangi Kata Sandi <span class="text-red-500">*</span></label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Ketik ulang password"
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-purple focus:border-brand-purple focus:bg-white transition text-sm">
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
            <a href="{{ route('admin.pengguna.index') }}" class="px-5 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-brand-purple hover:bg-purple-700 text-white text-sm font-bold rounded-xl shadow-sm transition">
                Simpan Pengguna
            </button>
        </div>

    </form>
</div>
@endsection
