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
                <a href="{{ route('umkm.pengajuan.show', $pengajuan->id_pengajuan) }}" class="hover:text-brand-purple transition-colors">Detail Pengajuan</a>
                <span>/</span>
                <span class="text-brand-purple">Ubah Data</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Perbarui Data Pengajuan Bantuan</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Perbaiki estimasi kriteria usaha, titik koordinat, atau berkas lampiran yang diminta.</p>
        </div>

        <a href="{{ route('umkm.pengajuan.show', $pengajuan->id_pengajuan) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-gray-200 bg-white hover:bg-gray-50 text-xs font-bold text-gray-700 shadow-sm transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <!-- Alert status jika revisi -->
    @if($pengajuan->status === 'revisi')
        <div class="p-5 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-900 flex items-start gap-3">
            <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <div>
                <p class="font-bold text-rose-950">Pengajuan dalam Status Perbaikan / Revisi</p>
                <p class="text-rose-800 mt-0.5 leading-relaxed">
                    {{ $pengajuan->catatan_revisi ?? 'Silakan periksa dan perbarui data atau berkas yang belum sesuai dengan ketentuan panitia seleksi.' }}
                </p>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-[24px] p-6 sm:p-10 shadow-sm border border-gray-100">
        <form action="{{ route('umkm.pengajuan.update', $pengajuan->id_pengajuan) }}" method="POST" enctype="multipart/form-data" onsubmit="handleFormSubmit(this)">
            @csrf
            @method('PUT')

            <!-- BAGIAN 1: Kriteria Profil Usaha -->
            <div class="mb-10">
                <div class="flex items-center gap-2 mb-4 pb-2 border-b border-gray-100">
                    <span class="w-8 h-8 rounded-full bg-brand-purple/10 text-brand-purple flex items-center justify-center font-bold text-xs">1</span>
                    <h2 class="text-lg font-bold text-gray-900">Kriteria Profil Usaha (Profile Matching)</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Omzet -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Omzet Tahunan (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="omzet_tahunan" value="{{ old('omzet_tahunan', $pengajuan->omzet_tahunan) }}" required min="0" 
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:border-brand-purple focus:ring-4 focus:ring-brand-purple/10 outline-none transition-all">
                        @error('omzet_tahunan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Aset -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Nilai Total Aset (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="aset" value="{{ old('aset', $pengajuan->aset) }}" required min="0" 
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:border-brand-purple focus:ring-4 focus:ring-brand-purple/10 outline-none transition-all">
                        @error('aset') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Tenaga Kerja -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Jumlah Tenaga Kerja (Orang) <span class="text-red-500">*</span></label>
                        <input type="number" name="jumlah_tenaga_kerja" value="{{ old('jumlah_tenaga_kerja', $pengajuan->jumlah_tenaga_kerja) }}" required min="1" 
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:border-brand-purple focus:ring-4 focus:ring-brand-purple/10 outline-none transition-all">
                        @error('jumlah_tenaga_kerja') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Jangkauan Pemasaran -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Jangkauan Pemasaran <span class="text-red-500">*</span></label>
                        <select name="jangkauan_pemasaran" required class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:border-brand-purple focus:ring-4 focus:ring-brand-purple/10 outline-none bg-white transition-all">
                            <option value="kelurahan" {{ old('jangkauan_pemasaran', $pengajuan->jangkauan_pemasaran) == 'kelurahan' ? 'selected' : '' }}>Kelurahan</option>
                            <option value="kecamatan" {{ old('jangkauan_pemasaran', $pengajuan->jangkauan_pemasaran) == 'kecamatan' ? 'selected' : '' }}>Kecamatan</option>
                            <option value="kota" {{ old('jangkauan_pemasaran', $pengajuan->jangkauan_pemasaran) == 'kota' ? 'selected' : '' }}>Kota / Kabupaten</option>
                            <option value="provinsi" {{ old('jangkauan_pemasaran', $pengajuan->jangkauan_pemasaran) == 'provinsi' ? 'selected' : '' }}>Provinsi</option>
                            <option value="nasional" {{ old('jangkauan_pemasaran', $pengajuan->jangkauan_pemasaran) == 'nasional' ? 'selected' : '' }}>Nasional / Internasional</option>
                        </select>
                        @error('jangkauan_pemasaran') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Status Perizinan -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 mb-2">Status Perizinan & Legalitas <span class="text-red-500">*</span></label>
                        <select name="status_perizinan" required class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:border-brand-purple focus:ring-4 focus:ring-brand-purple/10 outline-none bg-white transition-all">
                            <option value="belum_ada" {{ old('status_perizinan', $pengajuan->status_perizinan) == 'belum_ada' ? 'selected' : '' }}>Belum Ada</option>
                            <option value="sku_rt" {{ old('status_perizinan', $pengajuan->status_perizinan) == 'sku_rt' ? 'selected' : '' }}>SKU RT/RW</option>
                            <option value="sku_kelurahan" {{ old('status_perizinan', $pengajuan->status_perizinan) == 'sku_kelurahan' ? 'selected' : '' }}>SKU Kelurahan</option>
                            <option value="nib" {{ old('status_perizinan', $pengajuan->status_perizinan) == 'nib' ? 'selected' : '' }}>NIB (Nomor Induk Berusaha)</option>
                            <option value="nib_lengkap" {{ old('status_perizinan', $pengajuan->status_perizinan) == 'nib_lengkap' ? 'selected' : '' }}>NIB + Sertifikasi Lengkap (Halal / PIRT / BPOM)</option>
                        </select>
                        @error('status_perizinan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- BAGIAN 2: Titik Koordinat Peta WebGIS -->
            <div class="mb-10">
                <div class="flex items-center gap-2 mb-4 pb-2 border-b border-gray-100">
                    <span class="w-8 h-8 rounded-full bg-brand-orange/10 text-brand-orange flex items-center justify-center font-bold text-xs">2</span>
                    <h2 class="text-lg font-bold text-gray-900">Titik Koordinat Lokasi Fisik Usaha (WebGIS)</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2">
                        <div id="map" class="h-80 w-full rounded-2xl border border-gray-200 shadow-inner z-10"></div>
                        <p class="text-[11px] text-gray-400 mt-2">Geser penanda merah atau klik pada peta untuk memperbarui titik koordinat operasional usaha Anda.</p>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Latitude <span class="text-red-500">*</span></label>
                            <input type="text" id="latitude" name="latitude" value="{{ old('latitude', $umkm->latitude) }}" required readonly 
                                   class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-mono text-gray-700 outline-none">
                            @error('latitude') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Longitude <span class="text-red-500">*</span></label>
                            <input type="text" id="longitude" name="longitude" value="{{ old('longitude', $umkm->longitude) }}" required readonly 
                                   class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-mono text-gray-700 outline-none">
                            @error('longitude') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <button type="button" id="btn-current-location" class="w-full py-2.5 px-4 border border-brand-purple text-brand-purple text-xs font-bold rounded-xl hover:bg-brand-purple hover:text-white transition-all flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                            Gunakan Lokasi Saat Ini
                        </button>
                    </div>
                </div>
            </div>

            <!-- BAGIAN 3: Perbarui Lampiran Dokumen (Opsional) -->
            <div class="mb-10">
                <div class="flex items-center gap-2 mb-4 pb-2 border-b border-gray-100">
                    <span class="w-8 h-8 rounded-full bg-brand-purple/10 text-brand-purple flex items-center justify-center font-bold text-xs">3</span>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Perbarui Lampiran Berkas (Opsional)</h2>
                        <p class="text-[11px] text-gray-400">Pilih berkas baru hanya jika Anda ingin mengganti berkas yang sudah tersimpan sebelumnya.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @php
                        $inputFiles = [
                            'KTP'  => ['key' => 'file_ktp', 'label' => 'Scan KTP Pemilik Usaha', 'accept' => '.pdf,.jpg,.jpeg,.png'],
                            'KK'   => ['key' => 'file_kk', 'label' => 'Scan Kartu Keluarga (KK)', 'accept' => '.pdf,.jpg,.jpeg,.png'],
                            'NIB'  => ['key' => 'file_nib', 'label' => 'Dokumen NIB / Izin Usaha', 'accept' => '.pdf,.jpg,.jpeg,.png'],
                            'SKU'  => ['key' => 'file_sku', 'label' => 'Dokumen Surat Keterangan Usaha (SKU)', 'accept' => '.pdf,.jpg,.jpeg,.png'],
                            'FOTO' => ['key' => 'file_foto', 'label' => 'Foto Fisik Tempat Usaha (JPG/PNG)', 'accept' => '.jpg,.jpeg,.png'],
                        ];
                    @endphp

                    @foreach($inputFiles as $kode => $cfg)
                        @php $doc = $dokumenGrouped[$kode] ?? null; @endphp
                        <div class="border {{ $doc && $doc->status_verifikasi === 'ditolak' ? 'border-rose-300 bg-rose-50/20' : 'border-gray-200 bg-gray-50/40' }} rounded-2xl p-4">
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-bold text-gray-800">{{ $cfg['label'] }}</label>
                                @if($doc)
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full uppercase
                                                {{ $doc->status_verifikasi === 'disetujui' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                                {{ $doc->status_verifikasi === 'ditolak' ? 'bg-rose-100 text-rose-700' : '' }}
                                                {{ $doc->status_verifikasi === 'menunggu' ? 'bg-amber-100 text-amber-700' : '' }}">
                                        {{ $doc->status_verifikasi }}
                                    </span>
                                @endif
                            </div>

                            @if($doc && $doc->catatan_admin)
                                <p class="text-[11px] text-rose-700 mb-2 italic">Catatan: "{{ $doc->catatan_admin }}"</p>
                            @endif

                            <input type="file" name="{{ $cfg['key'] }}" accept="{{ $cfg['accept'] }}" 
                                   class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:text-[11px] file:font-bold file:bg-brand-purple/10 file:text-brand-purple hover:file:bg-brand-purple/20 cursor-pointer">
                            @error($cfg['key']) <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Submit Action -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="{{ route('umkm.pengajuan.show', $pengajuan->id_pengajuan) }}" class="px-6 py-2.5 rounded-full border border-gray-200 text-xs font-bold text-gray-600 hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="submit-button inline-flex items-center gap-2 bg-brand-orange hover:bg-orange-500 text-white font-bold text-xs px-8 py-3 rounded-full shadow-md shadow-brand-orange/30 hover:shadow-lg transition-all focus:ring-4 focus:ring-brand-orange/20">
                    <span class="btn-text flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                        Simpan Perubahan Pengajuan
                    </span>
                    <span class="btn-spinner hidden">
                        <svg class="animate-spin h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Menyimpan...
                    </span>
                </button>
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
                            document.getElementById('btn-current-location').innerHTML = 'Gunakan Lokasi Saat Ini';
                        }, 2000);
                    },
                    function(err) {
                        alert('Gagal mendapatkan lokasi browser.');
                        document.getElementById('btn-current-location').innerHTML = 'Gunakan Lokasi Saat Ini';
                    }
                );
            }
        });
    });
</script>
@endpush
@endsection
