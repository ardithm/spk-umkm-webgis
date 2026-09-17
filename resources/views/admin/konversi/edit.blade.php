@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Breadcrumbs -->
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.konversi.index', ['kriteria' => $konversi->id_kriteria]) }}" 
           class="p-2 rounded-xl bg-white border border-gray-200 text-gray-600 hover:text-brand-purple hover:border-brand-purple transition-all shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.konversi.index', ['kriteria' => $konversi->id_kriteria]) }}" class="text-xs font-bold text-gray-400 hover:text-gray-600 uppercase tracking-wider">
                    Konversi Skor {{ $konversi->kriteria?->kode_kriteria }}
                </a>
                <span class="text-gray-300">/</span>
                <span class="text-xs font-semibold text-brand-purple">Ubah Aturan #{{ $konversi->id_konversi }}</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mt-0.5">Ubah Rentang Konversi Skor</h1>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100">
        <form action="{{ route('admin.konversi.update', $konversi->id_konversi) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Pilih Kriteria -->
            <div>
                <label for="id_kriteria" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                    Kriteria Penilaian <span class="text-rose-500">*</span>
                </label>
                <select name="id_kriteria" 
                        id="id_kriteria" 
                        required
                        class="w-full px-4 py-2.5 bg-gray-50 border rounded-xl text-sm font-semibold focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-purple/30 focus:border-brand-purple transition @error('id_kriteria') border-rose-400 ring-1 ring-rose-400 @else border-gray-200 @enderror">
                    @foreach($kriteriaList as $kr)
                    <option value="{{ $kr->id_kriteria }}" 
                            {{ old('id_kriteria', $konversi->id_kriteria) == $kr->id_kriteria ? 'selected' : '' }}>
                        {{ $kr->kode_kriteria }} — {{ $kr->nama_kriteria }} (Target: {{ $kr->target_ideal }} | {{ strtoupper($kr->jenis_faktor) }})
                    </option>
                    @endforeach
                </select>
                @error('id_kriteria')
                    <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Rentang Nilai Aktual (Min & Max) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="nilai_min" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                        Batas Bawah Rentang (Nilai Min) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" 
                           step="any" 
                           name="nilai_min" 
                           id="nilai_min" 
                           value="{{ old('nilai_min', $konversi->nilai_min) }}" 
                           required 
                           class="w-full px-4 py-2.5 bg-gray-50 border rounded-xl text-sm font-mono focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-purple/30 focus:border-brand-purple transition @error('nilai_min') border-rose-400 ring-1 ring-rose-400 @else border-gray-200 @enderror">
                    @error('nilai_min')
                        <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nilai_max" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                        Batas Atas Rentang (Nilai Max) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" 
                           step="any" 
                           name="nilai_max" 
                           id="nilai_max" 
                           value="{{ old('nilai_max', $konversi->nilai_max) }}" 
                           required 
                           class="w-full px-4 py-2.5 bg-gray-50 border rounded-xl text-sm font-mono focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-purple/30 focus:border-brand-purple transition @error('nilai_max') border-rose-400 ring-1 ring-rose-400 @else border-gray-200 @enderror">
                    @error('nilai_max')
                        <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Skor Pemetaan (1-5) -->
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                    Skor Pemetaan Hasil Konversi (Skala 1–5) <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-5 gap-2 sm:gap-3">
                    @for($s = 1; $s <= 5; $s++)
                    <label class="relative flex flex-col items-center justify-center p-3 rounded-xl border cursor-pointer transition-all hover:bg-gray-50 has-[:checked]:border-brand-purple has-[:checked]:bg-purple-50/50 has-[:checked]:ring-2 has-[:checked]:ring-brand-purple/20">
                        <input type="radio" 
                               name="skor" 
                               value="{{ $s }}" 
                               {{ old('skor', $konversi->skor) == $s ? 'checked' : '' }}
                               class="sr-only">
                        <span class="text-lg font-extrabold text-gray-900">Skor {{ $s }}</span>
                        <span class="text-[10px] text-gray-500 mt-0.5 text-center">
                            @if($s == 1) Sangat Rendah
                            @elseif($s == 2) Rendah
                            @elseif($s == 3) Cukup
                            @elseif($s == 4) Baik / Tinggi
                            @else Sangat Baik @endif
                        </span>
                    </label>
                    @endfor
                </div>
                @error('skor')
                    <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.konversi.index', ['kriteria' => $konversi->id_kriteria]) }}" 
                   class="px-5 py-2.5 rounded-xl border border-gray-200 text-xs font-bold text-gray-600 hover:bg-gray-100 transition">
                    Batal
                </a>
                <button type="submit" 
                        class="bg-gradient-to-r from-brand-purple to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white font-bold py-2.5 px-6 rounded-xl text-xs sm:text-sm shadow-md shadow-purple-900/20 transition-all transform hover:-translate-y-0.5">
                    Perbarui Aturan Konversi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
