@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto my-6 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-8 pt-8 pb-6 bg-gradient-to-b from-purple-50/50 to-white border-b border-gray-100 text-center">
        <div class="inline-flex items-center justify-center w-14 h-14 bg-brand-purple/10 text-brand-purple rounded-2xl mb-3 font-bold text-xl">
            📝
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Pendaftaran Akun UMKM Baru</h1>
        <p class="text-sm text-gray-500 mt-1">Buat akun mandiri untuk mengajukan bantuan modal usaha Kota Banjarmasin</p>
    </div>

    <form action="{{ route('register.post') }}" method="POST" class="p-8 space-y-6">
        @csrf

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded-xl">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div>
            <h3 class="text-sm font-bold text-brand-purple uppercase tracking-wider mb-3">1. Data Pemilik & Kredensial Akun</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="nama_lengkap" class="block text-xs font-semibold text-gray-700 mb-1">Nama Lengkap (sesuai KTP) *</label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-purple outline-none text-sm">
                </div>
                <div>
                    <label for="username" class="block text-xs font-semibold text-gray-700 mb-1">Username (huruf kecil & angka) *</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" required placeholder="contoh: budi_kopi" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-purple outline-none text-sm">
                </div>
                <div>
                    <label for="password" class="block text-xs font-semibold text-gray-700 mb-1">Kata Sandi (min. 8 karakter) *</label>
                    <input type="password" id="password" name="password" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-purple outline-none text-sm">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-gray-700 mb-1">Konfirmasi Kata Sandi *</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-purple outline-none text-sm">
                </div>
            </div>
        </div>

        <hr class="border-gray-100">

        <div>
            <h3 class="text-sm font-bold text-brand-purple uppercase tracking-wider mb-3">2. Identitas Usaha & Kependudukan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="nik" class="block text-xs font-semibold text-gray-700 mb-1">Nomor Induk Kependudukan (NIK 16 Digit) *</label>
                    <input type="text" id="nik" name="nik" value="{{ old('nik') }}" required maxlength="16" pattern="[0-9]{16}" placeholder="6371..." class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-purple outline-none text-sm font-mono">
                </div>
                <div>
                    <label for="no_telepon" class="block text-xs font-semibold text-gray-700 mb-1">No. WhatsApp / HP Aktif *</label>
                    <input type="tel" id="no_telepon" name="no_telepon" value="{{ old('no_telepon') }}" required placeholder="08..." class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-purple outline-none text-sm">
                </div>
                <div>
                    <label for="nama_umkm" class="block text-xs font-semibold text-gray-700 mb-1">Nama Entitas Usaha (Merk/Toko) *</label>
                    <input type="text" id="nama_umkm" name="nama_umkm" value="{{ old('nama_umkm') }}" required placeholder="Contoh: Sasirangan Berkah" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-purple outline-none text-sm">
                </div>
                <div>
                    <label for="nama_pemilik" class="block text-xs font-semibold text-gray-700 mb-1">Nama Penanggung Jawab Usaha *</label>
                    <input type="text" id="nama_pemilik" name="nama_pemilik" value="{{ old('nama_pemilik') }}" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-purple outline-none text-sm">
                </div>
                <div class="md:col-span-2">
                    <label for="alamat" class="block text-xs font-semibold text-gray-700 mb-1">Alamat Lengkap Usaha *</label>
                    <textarea id="alamat" name="alamat" required rows="2" placeholder="Jl. Ahmad Yani Km..., Kelurahan..., Kecamatan..." class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-purple outline-none text-sm">{{ old('alamat') }}</textarea>
                </div>
            </div>
        </div>

        <div class="pt-2">
            <button 
                type="submit" 
                class="w-full bg-brand-orange hover:bg-orange-500 text-white font-bold py-3 px-6 rounded-xl shadow-sm transition-all focus:ring-4 focus:ring-orange-200 text-sm"
            >
                Buat Akun & Lanjutkan Pendaftaran ➜
            </button>
        </div>

        <div class="text-center pt-2">
            <p class="text-sm text-gray-600">
                Sudah memiliki akun? 
                <a href="{{ route('login') }}" class="text-brand-purple font-bold hover:underline">
                    Masuk di sini
                </a>
            </p>
        </div>
    </form>
</div>
@endsection
