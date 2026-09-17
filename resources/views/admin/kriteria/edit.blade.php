@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Breadcrumbs -->
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.kriteria.index') }}" 
           class="p-2 rounded-xl bg-white border border-gray-200 text-gray-600 hover:text-brand-purple hover:border-brand-purple transition-all shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.kriteria.index') }}" class="text-xs font-bold text-gray-400 hover:text-gray-600 uppercase tracking-wider">
                    Kelola Kriteria SPK
                </a>
                <span class="text-gray-300">/</span>
                <span class="text-xs font-semibold text-brand-purple">Ubah Kriteria {{ $kriteria->kode_kriteria }}</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mt-0.5">Ubah Kriteria Penilaian</h1>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100">
        <form action="{{ route('admin.kriteria.update', $kriteria->id_kriteria) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Kode Kriteria -->
            <div>
                <label for="kode_kriteria" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                    Kode Kriteria <span class="text-rose-500">*</span>
                </label>
                <div class="relative max-w-xs">
                    <input type="text" 
                           name="kode_kriteria" 
                           id="kode_kriteria" 
                           value="{{ old('kode_kriteria', $kriteria->kode_kriteria) }}" 
                           required 
                           placeholder="Contoh: K1, K2"
                           class="w-full px-4 py-2.5 bg-gray-50 border rounded-xl text-sm font-bold font-mono focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-purple/30 focus:border-brand-purple transition @error('kode_kriteria') border-rose-400 ring-1 ring-rose-400 @else border-gray-200 @enderror">
                </div>
                <p class="text-xs text-gray-400 mt-1">Identitas parameter yang unik (biasanya diawali huruf 'K' diikuti angka urutan).</p>
                @error('kode_kriteria')
                    <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nama Kriteria -->
            <div>
                <label for="nama_kriteria" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                    Nama Parameter Kriteria <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       name="nama_kriteria" 
                       id="nama_kriteria" 
                       value="{{ old('nama_kriteria', $kriteria->nama_kriteria) }}" 
                       required 
                       placeholder="Contoh: Omzet Penjualan Tahunan"
                       class="w-full px-4 py-2.5 bg-gray-50 border rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-purple/30 focus:border-brand-purple transition @error('nama_kriteria') border-rose-400 ring-1 ring-rose-400 @else border-gray-200 @enderror">
                <p class="text-xs text-gray-400 mt-1">Deskripsi lengkap dari aspek penilaian yang diukur.</p>
                @error('nama_kriteria')
                    <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Target Ideal Instansi (1-5) -->
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                    Nilai Target Ideal Instansi (Skala 1–5) <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-5 gap-2 sm:gap-3">
                    @for($val = 1; $val <= 5; $val++)
                    <label class="relative flex flex-col items-center justify-center p-3 rounded-xl border cursor-pointer transition-all hover:bg-gray-50 has-[:checked]:border-brand-purple has-[:checked]:bg-purple-50/50 has-[:checked]:ring-2 has-[:checked]:ring-brand-purple/20">
                        <input type="radio" 
                               name="target_ideal" 
                               value="{{ $val }}" 
                               {{ old('target_ideal', $kriteria->target_ideal) == $val ? 'checked' : '' }}
                               class="sr-only">
                        <span class="text-lg font-extrabold text-gray-900">{{ $val }}</span>
                        <span class="text-[10px] text-gray-500 mt-0.5 text-center">
                            @if($val == 1) Sangat Rendah
                            @elseif($val == 2) Rendah
                            @elseif($val == 3) Cukup
                            @elseif($val == 4) Tinggi
                            @else Sangat Tinggi @endif
                        </span>
                    </label>
                    @endfor
                </div>
                <p class="text-xs text-gray-400 mt-1.5">Nilai standar yang diharapkan oleh Dinas Koperasi sebagai titik acuan selisih GAP = 0.</p>
                @error('target_ideal')
                    <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Jenis Faktor Profile Matching -->
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                    Klasifikasi Jenis Faktor Profile Matching <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Core Factor -->
                    <label class="relative flex items-start gap-3 p-4 rounded-xl border cursor-pointer transition-all hover:bg-gray-50 has-[:checked]:border-brand-purple has-[:checked]:bg-purple-50/40 has-[:checked]:ring-2 has-[:checked]:ring-brand-purple/20">
                        <input type="radio" 
                               name="jenis_faktor" 
                               value="core" 
                               {{ old('jenis_faktor', $kriteria->jenis_faktor) === 'core' ? 'checked' : '' }}
                               class="mt-1 text-brand-purple focus:ring-brand-purple">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-gray-900 text-sm">Core Factor (CF)</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-700">Bobot 60%</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                                Aspek utama yang paling menonjol dan mutlak dibutuhkan dalam menentukan kelayakan penerima bantuan modal.
                            </p>
                        </div>
                    </label>

                    <!-- Secondary Factor -->
                    <label class="relative flex items-start gap-3 p-4 rounded-xl border cursor-pointer transition-all hover:bg-gray-50 has-[:checked]:border-brand-orange has-[:checked]:bg-orange-50/40 has-[:checked]:ring-2 has-[:checked]:ring-brand-orange/20">
                        <input type="radio" 
                               name="jenis_faktor" 
                               value="secondary" 
                               {{ old('jenis_faktor', $kriteria->jenis_faktor) === 'secondary' ? 'checked' : '' }}
                               class="mt-1 text-brand-orange focus:ring-brand-orange">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-gray-900 text-sm">Secondary Factor (SF)</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-orange-100 text-orange-700">Bobot 40%</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                                Aspek pendukung atau penunjang yang melengkapi penilaian profil UMKM secara menyeluruh.
                            </p>
                        </div>
                    </label>
                </div>
                @error('jenis_faktor')
                    <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Rentang Konversi Nilai Terhubung (Jika Ada) -->
            @if($kriteria->konversiNilai->isNotEmpty())
            <div class="pt-4 border-t border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <span class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                        Rentang Konversi Nilai Aktual &rarr; Skor
                    </span>
                    <a href="{{ route('admin.konversi.index', ['kriteria' => $kriteria->id_kriteria]) }}" class="text-xs font-bold text-brand-purple hover:underline">
                        Kelola Rentang Skor &rarr;
                    </a>
                </div>
                <div class="overflow-x-auto rounded-xl border border-gray-100">
                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-gray-500 font-semibold">Batas Bawah (Min)</th>
                                <th class="px-4 py-2 text-left text-gray-500 font-semibold">Batas Atas (Max)</th>
                                <th class="px-4 py-2 text-center text-gray-500 font-semibold">Skor Pemetaan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach($kriteria->konversiNilai as $kv)
                            <tr>
                                <td class="px-4 py-2 text-gray-800">{{ number_format($kv->nilai_min, 0, ',', '.') }}</td>
                                <td class="px-4 py-2 text-gray-800">{{ number_format($kv->nilai_max, 0, ',', '.') }}</td>
                                <td class="px-4 py-2 text-center font-bold text-brand-purple">Skor {{ $kv->skor }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.kriteria.index') }}" 
                   class="px-5 py-2.5 rounded-xl border border-gray-200 text-xs font-bold text-gray-600 hover:bg-gray-100 transition">
                    Batal
                </a>
                <button type="submit" 
                        class="bg-gradient-to-r from-brand-purple to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white font-bold py-2.5 px-6 rounded-xl text-xs sm:text-sm shadow-md shadow-purple-900/20 transition-all transform hover:-translate-y-0.5">
                    Perbarui Kriteria
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
