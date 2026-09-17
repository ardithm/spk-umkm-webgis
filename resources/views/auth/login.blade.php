@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto my-8 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-8 pt-8 pb-6 bg-gradient-to-b from-purple-50/50 to-white border-b border-gray-100 text-center">
        <div class="inline-flex items-center justify-center w-14 h-14 bg-brand-purple/10 text-brand-purple rounded-2xl mb-4 font-bold text-xl">
            🏛️
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Masuk ke SiBantuan</h1>
        <p class="text-sm text-gray-500 mt-1">Portal SPK & WebGIS Bantuan Modal UMKM Banjarmasin</p>
    </div>

    <form action="{{ route('login.post') }}" method="POST" class="p-8 space-y-5">
        @csrf

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded-xl">
                {{ $errors->first() }}
            </div>
        @endif

        <div>
            <label for="username" class="block text-sm font-semibold text-gray-700 mb-1.5">Username</label>
            <input 
                type="text" 
                id="username" 
                name="username" 
                value="{{ old('username') }}" 
                required 
                autofocus
                placeholder="Masukkan username Anda"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-purple focus:border-brand-purple outline-none transition text-sm"
            />
        </div>

        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-sm font-semibold text-gray-700">Kata Sandi</label>
            </div>
            <input 
                type="password" 
                id="password" 
                name="password" 
                required 
                placeholder="Masukkan kata sandi"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-purple focus:border-brand-purple outline-none transition text-sm"
            />
        </div>

        <div class="pt-2">
            <button 
                type="submit" 
                class="w-full bg-brand-orange hover:bg-orange-500 text-white font-bold py-3 px-6 rounded-xl shadow-sm transition-all focus:ring-4 focus:ring-orange-200"
            >
                Masuk Sekarang ➜
            </button>
        </div>

        <div class="text-center pt-4 border-t border-gray-100">
            <p class="text-sm text-gray-600">
                Belum memiliki akun UMKM? 
                <a href="{{ route('register') }}" class="text-brand-purple font-bold hover:underline">
                    Daftar di sini
                </a>
            </p>

        </div>
    </form>
</div>
@endsection
