@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center gap-3 mb-2">
        <a href="{{ route('admin.proses.index') }}" class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:text-brand-purple hover:border-purple-200 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <h2 class="text-2xl font-extrabold text-brand-dark">Tambah Periode Baru</h2>
    </div>

    <div class="bg-white rounded-[24px] shadow-sm border border-gray-100 p-8">
        <form action="{{ route('admin.proses.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Periode -->
                <div class="md:col-span-2">
                    <label for="periode" class="block text-sm font-bold text-gray-700 mb-2">Nama Periode / Gelombang <span class="text-red-500">*</span></label>
                    <input type="text" id="periode" name="periode" value="{{ old('periode') }}" required placeholder="Contoh: Gelombang 1 Tahun 2024"
                           class="w-full rounded-xl border-gray-200 shadow-sm focus:border-brand-purple focus:ring-brand-purple text-sm px-4 py-3 bg-gray-50/50">
                    @error('periode') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-bold text-gray-700 mb-2">Status Periode <span class="text-red-500">*</span></label>
                    <select id="status" name="status" required class="w-full rounded-xl border-gray-200 shadow-sm focus:border-brand-purple focus:ring-brand-purple text-sm px-4 py-3 bg-gray-50/50">
                        <option value="Draft" {{ old('status') == 'Draft' ? 'selected' : '' }}>Draft (Sembunyikan)</option>
                        <option value="Buka" {{ old('status') == 'Buka' ? 'selected' : '' }}>Buka (Aktifkan di Landing Page)</option>
                        <option value="Tutup" {{ old('status') == 'Tutup' ? 'selected' : '' }}>Tutup (Arsipkan)</option>
                    </select>
                    @error('status') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- Passing Grade -->
                <div>
                    <label for="passing_grade" class="block text-sm font-bold text-gray-700 mb-2">Passing Grade (Batas Kelulusan) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" min="0" max="100" id="passing_grade" name="passing_grade" value="{{ old('passing_grade') }}" required placeholder="Contoh: 70.00"
                           class="w-full rounded-xl border-gray-200 shadow-sm focus:border-brand-purple focus:ring-brand-purple text-sm px-4 py-3 bg-gray-50/50">
                    @error('passing_grade') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- Kuota -->
                <div>
                    <label for="kuota" class="block text-sm font-bold text-gray-700 mb-2">Kuota Penerima (Opsional)</label>
                    <input type="number" min="1" id="kuota" name="kuota" value="{{ old('kuota') }}" placeholder="Contoh: 50"
                           class="w-full rounded-xl border-gray-200 shadow-sm focus:border-brand-purple focus:ring-brand-purple text-sm px-4 py-3 bg-gray-50/50">
                    <p class="mt-1 text-[11px] text-gray-400">Kosongkan jika tidak ada batasan kuota.</p>
                    @error('kuota') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- Tanggal Proses (SPK) -->
                <div>
                    <label for="tanggal_proses" class="block text-sm font-bold text-gray-700 mb-2">Tanggal Pelaksanaan SPK <span class="text-red-500">*</span></label>
                    <input type="datetime-local" id="tanggal_proses" name="tanggal_proses" value="{{ old('tanggal_proses', now()->format('Y-m-d\TH:i')) }}" required
                           class="w-full rounded-xl border-gray-200 shadow-sm focus:border-brand-purple focus:ring-brand-purple text-sm px-4 py-3 bg-gray-50/50">
                    @error('tanggal_proses') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- Keterangan -->
                <div class="md:col-span-2">
                    <label for="keterangan" class="block text-sm font-bold text-gray-700 mb-2">Keterangan Tambahan (Opsional)</label>
                    <textarea id="keterangan" name="keterangan" rows="3" placeholder="Deskripsi singkat mengenai gelombang ini..."
                              class="w-full rounded-xl border-gray-200 shadow-sm focus:border-brand-purple focus:ring-brand-purple text-sm px-4 py-3 bg-gray-50/50">{{ old('keterangan') }}</textarea>
                    @error('keterangan') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- Pembatas Timeline -->
                <div class="md:col-span-2 mt-6 pt-6 border-t border-gray-100">
                    <h3 class="text-lg font-bold text-brand-dark mb-1">Timeline Program (Ditampilkan di Landing Page)</h3>
                    <p class="text-sm text-gray-500 mb-4">Lengkapi tanggal mulai dan selesai untuk masing-masing tahapan.</p>
                </div>

                <!-- Deskripsi Alur -->
                <div class="md:col-span-2">
                    <label for="deskripsi_alur" class="block text-sm font-bold text-gray-700 mb-2">Deskripsi Alur Seleksi</label>
                    <textarea id="deskripsi_alur" name="deskripsi_alur" rows="2" placeholder="Sesuai Kerangka Acuan PRD Dinas Koperasi..."
                              class="w-full rounded-xl border-gray-200 shadow-sm focus:border-brand-purple focus:ring-brand-purple text-sm px-4 py-3 bg-gray-50/50">{{ old('deskripsi_alur') }}</textarea>
                    @error('deskripsi_alur') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- Timeline Dates -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">1. Pendaftaran Mandiri (Mulai - Selesai)</label>
                    <div class="flex gap-2">
                        <input type="date" name="tgl_pendaftaran_mulai" value="{{ old('tgl_pendaftaran_mulai') }}" class="w-1/2 rounded-xl border-gray-200 shadow-sm focus:border-brand-purple focus:ring-brand-purple text-sm px-4 py-3 bg-gray-50/50">
                        <input type="date" name="tgl_pendaftaran_selesai" value="{{ old('tgl_pendaftaran_selesai') }}" class="w-1/2 rounded-xl border-gray-200 shadow-sm focus:border-brand-purple focus:ring-brand-purple text-sm px-4 py-3 bg-gray-50/50">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">2. Verifikasi Dokumen (Mulai - Selesai)</label>
                    <div class="flex gap-2">
                        <input type="date" name="tgl_verifikasi_mulai" value="{{ old('tgl_verifikasi_mulai') }}" class="w-1/2 rounded-xl border-gray-200 shadow-sm focus:border-brand-purple focus:ring-brand-purple text-sm px-4 py-3 bg-gray-50/50">
                        <input type="date" name="tgl_verifikasi_selesai" value="{{ old('tgl_verifikasi_selesai') }}" class="w-1/2 rounded-xl border-gray-200 shadow-sm focus:border-brand-purple focus:ring-brand-purple text-sm px-4 py-3 bg-gray-50/50">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">3. Kalkulasi SPK Massal (Mulai - Selesai)</label>
                    <div class="flex gap-2">
                        <input type="date" name="tgl_spk_mulai" value="{{ old('tgl_spk_mulai') }}" class="w-1/2 rounded-xl border-gray-200 shadow-sm focus:border-brand-purple focus:ring-brand-purple text-sm px-4 py-3 bg-gray-50/50">
                        <input type="date" name="tgl_spk_selesai" value="{{ old('tgl_spk_selesai') }}" class="w-1/2 rounded-xl border-gray-200 shadow-sm focus:border-brand-purple focus:ring-brand-purple text-sm px-4 py-3 bg-gray-50/50">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">4. Survei Lapangan (Mulai - Selesai)</label>
                    <div class="flex gap-2">
                        <input type="date" name="tgl_survei_mulai" value="{{ old('tgl_survei_mulai') }}" class="w-1/2 rounded-xl border-gray-200 shadow-sm focus:border-brand-purple focus:ring-brand-purple text-sm px-4 py-3 bg-gray-50/50">
                        <input type="date" name="tgl_survei_selesai" value="{{ old('tgl_survei_selesai') }}" class="w-1/2 rounded-xl border-gray-200 shadow-sm focus:border-brand-purple focus:ring-brand-purple text-sm px-4 py-3 bg-gray-50/50">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">5. Pengumuman & SK</label>
                    <input type="date" name="tgl_pengumuman" value="{{ old('tgl_pengumuman') }}" class="w-full rounded-xl border-gray-200 shadow-sm focus:border-brand-purple focus:ring-brand-purple text-sm px-4 py-3 bg-gray-50/50">
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end border-t border-gray-100">
                <button type="submit" class="px-6 py-3 rounded-full bg-brand-purple hover:bg-purple-700 text-white font-bold text-sm transition-all shadow-md shadow-brand-purple/20">
                    Simpan Periode Baru
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
