@extends('layouts.app')

@section('content')
<div class="space-y-8 max-w-5xl mx-auto">
    <!-- Header & Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 mb-1">
                <a href="{{ route('umkm.dashboard') }}" class="hover:text-brand-purple transition-colors flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Dasbor
                </a>
                <span>/</span>
                <span class="text-brand-purple">Formulir Pengajuan Baru</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Formulir Pengajuan Bantuan Modal</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Lengkapi estimasi data kriteria, tentukan titik lokasi usaha, dan unggah berkas persyaratan fisik digital.</p>
        </div>

        <a href="{{ route('umkm.pengajuan.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-gray-200 bg-white hover:bg-gray-50 text-xs font-bold text-gray-700 shadow-sm transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <!-- Instructions Info Card -->
    <div class="bg-gradient-to-r from-purple-50/70 to-orange-50/70 border border-brand-purple/15 rounded-[24px] p-6 shadow-sm flex items-start gap-4">
        <div class="w-10 h-10 rounded-2xl bg-brand-purple/10 text-brand-purple flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div class="text-xs text-gray-600 space-y-1">
            <p class="font-bold text-sm text-gray-900">Petunjuk Pengisian Formulir Bantuan</p>
            <p class="leading-relaxed">
                Pastikan data omzet, aset, dan jumlah tenaga kerja yang Anda isi mendekati fakta lapangan yang sesungguhnya. Titik koordinat peta akan digunakan oleh petugas Dinas untuk memvalidasi lokasi dan menentukan rute survei fisik lapangan (WebGIS).
            </p>
        </div>
    </div>

    <div class="bg-white rounded-[24px] p-6 sm:p-10 shadow-sm border border-gray-100">
        <form action="{{ route('umkm.pengajuan.store') }}" method="POST" enctype="multipart/form-data" onsubmit="handleFormSubmit(this)">
            @csrf

            <!-- BAGIAN 1: Kriteria Profil Usaha -->
            <div class="mb-10">
                <div class="flex items-center gap-2.5 mb-6 pb-3 border-b border-gray-100">
                    <span class="w-8 h-8 rounded-full bg-brand-purple/10 text-brand-purple flex items-center justify-center font-bold text-xs">1</span>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Kriteria Profil Usaha (SPK Profile Matching)</h2>
                        <p class="text-[11px] text-gray-400">Variabel penilaian matematis Core Factor & Secondary Factor instansi</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Omzet -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Omzet Finansial Tahunan (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="omzet_tahunan" value="{{ old('omzet_tahunan') }}" required min="0" placeholder="contoh: 50000000"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm text-gray-900 focus:border-brand-purple focus:ring-4 focus:ring-brand-purple/10 outline-none transition-all">
                        @error('omzet_tahunan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Aset -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Nilai Total Aset Usaha (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="aset" value="{{ old('aset') }}" required min="0" placeholder="contoh: 25000000"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm text-gray-900 focus:border-brand-purple focus:ring-4 focus:ring-brand-purple/10 outline-none transition-all">
                        @error('aset') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Tenaga Kerja -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Jumlah Tenaga Kerja (Orang) <span class="text-red-500">*</span></label>
                        <input type="number" name="jumlah_tenaga_kerja" value="{{ old('jumlah_tenaga_kerja') }}" required min="1" placeholder="contoh: 3"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm text-gray-900 focus:border-brand-purple focus:ring-4 focus:ring-brand-purple/10 outline-none transition-all">
                        @error('jumlah_tenaga_kerja') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Jangkauan Pemasaran -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Skala Jangkauan Pemasaran <span class="text-red-500">*</span></label>
                        <select name="jangkauan_pemasaran" required class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:border-brand-purple focus:ring-4 focus:ring-brand-purple/10 outline-none bg-white transition-all">
                            <option value="" disabled {{ old('jangkauan_pemasaran') ? '' : 'selected' }}>Pilih Jangkauan Pemasaran...</option>
                            <option value="kelurahan" {{ old('jangkauan_pemasaran') == 'kelurahan' ? 'selected' : '' }}>Kelurahan / Sekitar Lingkungan</option>
                            <option value="kecamatan" {{ old('jangkauan_pemasaran') == 'kecamatan' ? 'selected' : '' }}>Kecamatan</option>
                            <option value="kota" {{ old('jangkauan_pemasaran') == 'kota' ? 'selected' : '' }}>Kota Banjarmasin</option>
                            <option value="provinsi" {{ old('jangkauan_pemasaran') == 'provinsi' ? 'selected' : '' }}>Kalimantan Selatan (Provinsi)</option>
                            <option value="nasional" {{ old('jangkauan_pemasaran') == 'nasional' ? 'selected' : '' }}>Nasional / Ekspor</option>
                        </select>
                        @error('jangkauan_pemasaran') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Status Perizinan -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 mb-2">Status Legalitas & Perizinan Usaha <span class="text-red-500">*</span></label>
                        <select name="status_perizinan" required class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:border-brand-purple focus:ring-4 focus:ring-brand-purple/10 outline-none bg-white transition-all">
                            <option value="" disabled {{ old('status_perizinan') ? '' : 'selected' }}>Pilih Legalitas yang Dimiliki...</option>
                            <option value="belum_ada" {{ old('status_perizinan') == 'belum_ada' ? 'selected' : '' }}>Belum Memiliki Izin Resmi</option>
                            <option value="sku_rt" {{ old('status_perizinan') == 'sku_rt' ? 'selected' : '' }}>Surat Keterangan Usaha (SKU) RT/RW</option>
                            <option value="sku_kelurahan" {{ old('status_perizinan') == 'sku_kelurahan' ? 'selected' : '' }}>SKU Kelurahan / Kecamatan</option>
                            <option value="nib" {{ old('status_perizinan') == 'nib' ? 'selected' : '' }}>NIB (Nomor Induk Berusaha OSS)</option>
                            <option value="nib_lengkap" {{ old('status_perizinan') == 'nib_lengkap' ? 'selected' : '' }}>NIB + Sertifikasi Tambahan (Halal / PIRT / BPOM)</option>
                        </select>
                        @error('status_perizinan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- BAGIAN 2: Titik Koordinat Peta WebGIS -->
            <div class="mb-10">
                <div class="flex items-center gap-2.5 mb-6 pb-3 border-b border-gray-100">
                    <span class="w-8 h-8 rounded-full bg-brand-orange/10 text-brand-orange flex items-center justify-center font-bold text-xs">2</span>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Titik Spasial Lokasi Operasional (WebGIS)</h2>
                        <p class="text-[11px] text-gray-400">Tentukan letak presisi tempat usaha pada peta interaktif</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2">
                        <div id="map" class="h-80 w-full rounded-2xl border border-gray-200 shadow-inner z-10"></div>
                        <p class="text-[11px] text-gray-400 mt-2 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-brand-purple shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Klik pada peta atau geser penanda merah untuk menyesuaikan titik lokasi fisik gerai usaha Anda.
                        </p>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Latitude <span class="text-red-500">*</span></label>
                            <input type="text" id="latitude" name="latitude" value="{{ old('latitude', $umkm->latitude ?? -3.316694) }}" required readonly 
                                   class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-mono text-gray-700 outline-none">
                            @error('latitude') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Longitude <span class="text-red-500">*</span></label>
                            <input type="text" id="longitude" name="longitude" value="{{ old('longitude', $umkm->longitude ?? 114.590111) }}" required readonly 
                                   class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-mono text-gray-700 outline-none">
                            @error('longitude') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <button type="button" id="btn-current-location" class="w-full py-2.5 px-4 border border-brand-purple text-brand-purple text-xs font-bold rounded-xl hover:bg-brand-purple hover:text-white transition-all flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                            Gunakan Lokasi Saat Ini (GPS)
                        </button>
                    </div>
                </div>
            </div>

            <!-- BAGIAN 3: Unggah 5 Dokumen Persyaratan Wajib -->
            <div class="mb-10">
                <div class="flex items-center gap-2.5 mb-6 pb-3 border-b border-gray-100">
                    <span class="w-8 h-8 rounded-full bg-brand-purple/10 text-brand-purple flex items-center justify-center font-bold text-xs">3</span>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Unggah 5 Dokumen Persyaratan Fisik Wajib</h2>
                        <p class="text-[11px] text-gray-400">Format: PDF, JPG, PNG (Maksimal 2MB per berkas). Disimpan di sistem penyimpanan privat aman.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- KTP -->
                    <div class="border border-gray-200 bg-gray-50/40 rounded-2xl p-4">
                        <label class="block text-xs font-bold text-gray-800 mb-1">Scan / Foto KTP Pemilik <span class="text-red-500">*</span></label>
                        <p class="text-[11px] text-gray-500 mb-3">Identitas KTP elektronik 16 digit sesuai data pemilik sah.</p>
                        <input type="file" name="file_ktp" accept=".pdf,.jpg,.jpeg,.png" required 
                               class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3.5 file:rounded-full file:border-0 file:text-[11px] file:font-bold file:bg-brand-purple/10 file:text-brand-purple hover:file:bg-brand-purple hover:file:text-white cursor-pointer transition-colors">
                        @error('file_ktp') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- KK -->
                    <div class="border border-gray-200 bg-gray-50/40 rounded-2xl p-4">
                        <label class="block text-xs font-bold text-gray-800 mb-1">Scan / Foto Kartu Keluarga (KK) <span class="text-red-500">*</span></label>
                        <p class="text-[11px] text-gray-500 mb-3">Bukti sah domisili dan susunan keluarga pendaftar.</p>
                        <input type="file" name="file_kk" accept=".pdf,.jpg,.jpeg,.png" required 
                               class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3.5 file:rounded-full file:border-0 file:text-[11px] file:font-bold file:bg-brand-purple/10 file:text-brand-purple hover:file:bg-brand-purple hover:file:text-white cursor-pointer transition-colors">
                        @error('file_kk') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- NIB -->
                    <div class="border border-gray-200 bg-gray-50/40 rounded-2xl p-4">
                        <label class="block text-xs font-bold text-gray-800 mb-1">Dokumen NIB / Surat Keterangan <span class="text-red-500">*</span></label>
                        <p class="text-[11px] text-gray-500 mb-3">Nomor Induk Berusaha atau dokumen legalitas dari OSS.</p>
                        <input type="file" name="file_nib" accept=".pdf,.jpg,.jpeg,.png" required 
                               class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3.5 file:rounded-full file:border-0 file:text-[11px] file:font-bold file:bg-brand-purple/10 file:text-brand-purple hover:file:bg-brand-purple hover:file:text-white cursor-pointer transition-colors">
                        @error('file_nib') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- SKU -->
                    <div class="border border-gray-200 bg-gray-50/40 rounded-2xl p-4">
                        <label class="block text-xs font-bold text-gray-800 mb-1">Surat Keterangan Usaha (SKU) <span class="text-red-500">*</span></label>
                        <p class="text-[11px] text-gray-500 mb-3">Rekomendasi tertulis dari Kelurahan atau instansi RT/RW.</p>
                        <input type="file" name="file_sku" accept=".pdf,.jpg,.jpeg,.png" required 
                               class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3.5 file:rounded-full file:border-0 file:text-[11px] file:font-bold file:bg-brand-purple/10 file:text-brand-purple hover:file:bg-brand-purple hover:file:text-white cursor-pointer transition-colors">
                        @error('file_sku') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- FOTO -->
                    <div class="border border-gray-200 bg-gray-50/40 rounded-2xl p-4 md:col-span-2">
                        <label class="block text-xs font-bold text-gray-800 mb-1">Foto Fisik Tempat Usaha <span class="text-red-500">*</span></label>
                        <p class="text-[11px] text-gray-500 mb-3">Foto gerai, toko, etalase, atau aktivitas produksi fisik (Khusus format JPG / JPEG / PNG).</p>
                        <input type="file" name="file_foto" accept=".jpg,.jpeg,.png" required 
                               class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3.5 file:rounded-full file:border-0 file:text-[11px] file:font-bold file:bg-brand-purple/10 file:text-brand-purple hover:file:bg-brand-purple hover:file:text-white cursor-pointer transition-colors">
                        @error('file_foto') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Submit Action Bar -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-6 border-t border-gray-100">
                <a href="{{ route('umkm.pengajuan.index') }}" class="text-xs font-bold text-gray-500 hover:text-gray-800 transition-colors">
                    Batal & Kembali
                </a>

                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <button type="submit" name="action" value="draft" 
                            class="w-1/2 sm:w-auto px-6 py-3 rounded-full border border-gray-200 bg-white hover:bg-gray-50 text-xs font-bold text-gray-700 shadow-sm transition-all">
                        Simpan sebagai Draf
                    </button>

                    <button type="submit" name="action" value="submit" 
                            class="submit-button w-1/2 sm:w-auto inline-flex items-center justify-center gap-2 bg-brand-orange hover:bg-orange-500 text-white font-bold text-xs px-8 py-3 rounded-full shadow-md shadow-brand-orange/30 hover:shadow-lg transition-all focus:ring-4 focus:ring-brand-orange/20">
                        <span class="btn-text flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                            Kirim Pengajuan
                        </span>
                        <span class="btn-spinner hidden">
                            <svg class="animate-spin h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Memproses...
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function handleFormSubmit(form) {
        const btn = form.querySelector('.submit-button');
        if (btn) {
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            const txt = btn.querySelector('.btn-text');
            const spn = btn.querySelector('.btn-spinner');
            if (txt) txt.classList.add('hidden');
            if (spn) spn.classList.remove('hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        let lat = parseFloat("{{ old('latitude', $umkm->latitude ?? -3.316694) }}");
        let lng = parseFloat("{{ old('longitude', $umkm->longitude ?? 114.590111) }}");

        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');

        const map = L.map('map').setView([lat, lng], 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        let marker = L.marker([lat, lng], {draggable: true}).addTo(map);

        marker.on('dragend', function(e) {
            const pos = marker.getLatLng();
            latInput.value = pos.lat.toFixed(6);
            lngInput.value = pos.lng.toFixed(6);
        });

        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            latInput.value = e.latlng.lat.toFixed(6);
            lngInput.value = e.latlng.lng.toFixed(6);
        });

        document.getElementById('btn-current-location').addEventListener('click', function() {
            if (navigator.geolocation) {
                this.innerHTML = 'Mencari lokasi...';
                navigator.geolocation.getCurrentPosition(
                    function(pos) {
                        const curLat = pos.coords.latitude;
                        const curLng = pos.coords.longitude;
                        map.setView([curLat, curLng], 16);
                        marker.setLatLng([curLat, curLng]);
                        latInput.value = curLat.toFixed(6);
                        lngInput.value = curLng.toFixed(6);
                        document.getElementById('btn-current-location').innerHTML = 'Lokasi Terdeteksi';
                        setTimeout(() => {
                            document.getElementById('btn-current-location').innerHTML = 'Gunakan Lokasi Saat Ini (GPS)';
                        }, 2000);
                    },
                    function(err) {
                        alert('Gagal mendapatkan lokasi browser.');
                        document.getElementById('btn-current-location').innerHTML = 'Gunakan Lokasi Saat Ini (GPS)';
                    }
                );
            }
        });
    });
</script>
@endpush
@endsection
