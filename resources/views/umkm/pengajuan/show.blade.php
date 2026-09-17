@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <!-- Breadcrumb & Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 mb-1">
                <a href="{{ route('umkm.dashboard') }}" class="hover:text-brand-purple transition-colors flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Dasbor
                </a>
                <span>/</span>
                <a href="{{ route('umkm.pengajuan.index') }}" class="hover:text-brand-purple transition-colors">Pengajuan</a>
                <span>/</span>
                <span class="text-brand-purple">Detail #{{ $pengajuan->id_pengajuan }}</span>
            </div>
            <div class="flex items-center gap-3 mt-1">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">
                    Pengajuan Bantuan Modal #{{ $pengajuan->id_pengajuan }}
                </h1>

                @php
                    $statusStyles = [
                        'draft'         => 'bg-gray-100 text-gray-700 border-gray-200',
                        'menunggu'      => 'bg-amber-50 text-amber-800 border-amber-200',
                        'revisi'        => 'bg-rose-50 text-rose-800 border-rose-200 animate-pulse',
                        'terverifikasi' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                        'diproses'      => 'bg-blue-50 text-blue-800 border-blue-200',
                        'selesai'       => 'bg-purple-50 text-purple-800 border-purple-200',
                    ];
                    $badgeStyle = $statusStyles[$pengajuan->status] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                @endphp

                <span class="px-3.5 py-1 rounded-full text-xs font-bold uppercase border {{ $badgeStyle }} shadow-sm">
                    {{ $pengajuan->status }}
                </span>
            </div>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">
                Diajukan pada: {{ $pengajuan->tanggal_pengajuan ? $pengajuan->tanggal_pengajuan->format('d M Y, H:i') : 'Draf belum disubmit' }} WITA
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('umkm.pengajuan.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-gray-200 bg-white hover:bg-gray-50 text-xs font-bold text-gray-700 shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>

            @if($pengajuan->isEditable())
                <a href="{{ route('umkm.pengajuan.edit', $pengajuan->id_pengajuan) }}" class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-brand-purple hover:bg-purple-700 text-white text-xs font-bold shadow-md shadow-brand-purple/20 transition-all hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    <span>Ubah Formulir Data</span>
                </a>
            @endif
        </div>
    </div>

    <!-- LOCK BANNER (If Locked / In Review / Verified) -->
    @if($pengajuan->isLocked())
        <div class="bg-blue-50/80 border border-blue-200 rounded-[24px] p-5 text-blue-900 text-xs flex items-start gap-4">
            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </div>
            <div>
                <p class="font-bold text-sm text-blue-950">Formulir Pengajuan Sedang Terkunci</p>
                <p class="text-blue-800 mt-0.5 leading-relaxed">
                    Data pengajuan Anda saat ini berstatus <strong>{{ strtoupper($pengajuan->status) }}</strong> dan sedang dalam proses seleksi tim panitia. Data tidak dapat diubah agar menjamin keaslian dan objektivitas evaluasi SPK.
                </p>
            </div>
        </div>
    @endif

    <!-- REVISION CALLOUT / ALERT BOX (If Status Revisi) -->
    @if($pengajuan->status === 'revisi')
        <div class="bg-rose-50 border-2 border-rose-300 rounded-[24px] p-6 sm:p-7 shadow-sm text-rose-950 space-y-4">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-start gap-3.5">
                    <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div>
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-rose-200 text-rose-800 mb-1">
                            Perlu Tindakan Anda
                        </span>
                        <h2 class="text-lg sm:text-xl font-bold text-rose-950">Pengajuan Memerlukan Perbaikan Berkas!</h2>
                        <p class="text-xs sm:text-sm text-rose-800 mt-1 leading-relaxed">
                            Petugas verifikator menemukan ketidaksesuaian pada berkas yang diunggah. Mohon periksa catatan di bawah, unggah dokumen perbaikan pada bagian berkas yang ditolak, lalu tekan tombol <strong>"Kirim Ulang Berkas Perbaikan"</strong>.
                        </p>
                    </div>
                </div>

                @if($pengajuan->batas_revisi)
                    <div class="hidden sm:flex flex-col items-end shrink-0 bg-white/70 px-4 py-2 rounded-xl border border-rose-200">
                        <span class="text-[10px] uppercase font-bold text-rose-600">Batas Waktu Perbaikan</span>
                        <span class="text-xs font-bold text-rose-900 font-mono">{{ $pengajuan->batas_revisi->format('d M Y, H:i') }} WITA</span>
                    </div>
                @endif
            </div>

            <!-- Catatan Umum Revisi Admin -->
            @if($pengajuan->catatan_revisi)
                <div class="bg-white/80 rounded-xl p-4 border border-rose-200/80 text-xs">
                    <p class="font-bold text-rose-900 mb-1 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                        Catatan Verifikator:
                    </p>
                    <p class="text-rose-800 leading-relaxed italic">"{{ $pengajuan->catatan_revisi }}"</p>
                </div>
            @endif

            <!-- Resubmit Action Banner -->
            <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-rose-200">
                <div class="text-xs text-rose-800">
                    @if($stats['total_ditolak'] > 0)
                        <span class="font-semibold text-rose-700 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            Terdapat <strong>{{ $stats['total_ditolak'] }} berkas</strong> yang masih berstatus Ditolak dan harus diunggah ulang sebelum dikirim.
                        </span>
                    @else
                        <span class="font-semibold text-emerald-700 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Seluruh berkas yang sebelumnya ditolak telah Anda ganti! Anda sekarang dapat mengirimkan ulang permohonan.
                        </span>
                    @endif
                </div>

                @if($stats['siap_resubmit'])
                    <form action="{{ route('umkm.pengajuan.resubmit', $pengajuan->id_pengajuan) }}" method="POST" onsubmit="return confirm('Kirimkan ulang berkas perbaikan ini ke tim verifikator?')">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-brand-orange hover:bg-orange-500 text-white font-bold text-xs shadow-md shadow-brand-orange/30 hover:shadow-lg transition-all focus:ring-4 focus:ring-brand-orange/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            <span>Kirim Ulang Berkas Perbaikan</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    @endif

    <!-- SECTION: BERKAS PERSYARATAN & STATUS PER-DOKUMEN -->
    <div class="bg-white rounded-[24px] p-6 sm:p-8 shadow-sm border border-gray-100 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-6 border-b border-gray-100">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Kelengkapan Dokumen Persyaratan</h3>
                <p class="text-xs text-gray-500 mt-1">Periksa status verifikasi untuk setiap arsip berkas fisik Anda di bawah ini.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    {{ $stats['total_disetujui'] }} Valid
                </span>
                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $stats['total_ditolak'] > 0 ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-gray-100 text-gray-600' }}">
                    {{ $stats['total_ditolak'] }} Ditolak
                </span>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                    {{ $stats['total_menunggu'] }} Menunggu
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($jenisWajib as $kode => $info)
                @php
                    $doc = $dokumenGrouped[$kode] ?? null;
                    $status = $doc ? $doc->status_verifikasi : 'belum_ada';
                @endphp

                <div class="rounded-2xl p-5 border transition-all duration-300 flex flex-col justify-between
                            {{ $status === 'disetujui' ? 'bg-emerald-50/30 border-emerald-200 hover:border-emerald-300' : '' }}
                            {{ $status === 'ditolak' ? 'bg-rose-50/40 border-rose-300 hover:border-rose-400 ring-2 ring-rose-100' : '' }}
                            {{ $status === 'menunggu' ? 'bg-white border-gray-200 hover:border-brand-purple/30 shadow-sm' : '' }}
                            {{ $status === 'belum_ada' ? 'bg-gray-50 border-gray-200' : '' }}">
                    
                    <div>
                        <!-- Header Dokumen: Icon + Badge Status -->
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-sm
                                        {{ $status === 'disetujui' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                        {{ $status === 'ditolak' ? 'bg-rose-100 text-rose-700' : '' }}
                                        {{ $status === 'menunggu' ? 'bg-brand-purple/10 text-brand-purple' : '' }}
                                        {{ $status === 'belum_ada' ? 'bg-gray-200 text-gray-500' : '' }}">
                                {{ $kode }}
                            </div>

                            @if($status === 'disetujui')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                    Valid / Terverifikasi
                                </span>
                            @elseif($status === 'ditolak')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-rose-100 text-rose-800 border border-rose-300">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    Ditolak / Perlu Revisi
                                </span>
                            @elseif($status === 'menunggu')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-amber-50 text-amber-800 border border-amber-200">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Menunggu Verifikasi
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-gray-100 text-gray-600">
                                    Belum Diunggah
                                </span>
                            @endif
                        </div>

                        <!-- Info Dokumen -->
                        <h4 class="font-bold text-gray-900 text-sm leading-snug">{{ $info['nama'] }}</h4>
                        <p class="text-[11px] text-gray-500 mt-1 leading-normal">{{ $info['deskripsi'] }}</p>

                        <!-- Catatan Penolakan Spesifik dari Admin (Jika ada) -->
                        @if($doc && $doc->catatan_admin)
                            <div class="mt-3 p-3 rounded-xl bg-rose-100/70 border border-rose-200 text-rose-900 text-xs">
                                <p class="font-bold text-[11px] text-rose-950 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Alasan Penolakan:
                                </p>
                                <p class="text-[11px] text-rose-800 mt-0.5 leading-relaxed font-medium">
                                    "{{ $doc->catatan_admin }}"
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Actions Area -->
                    <div class="mt-5 pt-3 border-t border-gray-100/80 space-y-2">
                        @if($doc)
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-[10px] text-gray-400">Diunggah: {{ $doc->created_at->format('d/m/Y') }}</span>
                                <a href="{{ route('umkm.pengajuan.dokumen.preview', [$pengajuan->id_pengajuan, $doc->id_dokumen]) }}" 
                                   target="_blank"
                                   class="inline-flex items-center gap-1 text-xs font-bold text-brand-purple hover:text-purple-700 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Lihat Berkas
                                </a>
                            </div>
                        @endif

                        <!-- Inline Re-upload Form jika berkas berstatus DITOLAK dan pengajuan berstatus REVISI -->
                        @if($doc && $doc->status_verifikasi === 'ditolak' && $pengajuan->status === 'revisi')
                            <div class="mt-2 pt-2">
                                <button type="button" 
                                        onclick="toggleReuploadForm('reupload-form-{{ $doc->id_dokumen }}')"
                                        class="w-full py-2 px-3 rounded-full bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition-all shadow-sm flex items-center justify-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                    Unggah Berkas Pengganti
                                </button>

                                <!-- Hidden form container toggled by button -->
                                <div id="reupload-form-{{ $doc->id_dokumen }}" class="hidden mt-3 p-3 bg-white rounded-xl border border-rose-200 shadow-sm animate-fadeIn">
                                    <form action="{{ route('umkm.pengajuan.dokumen.reupload', [$pengajuan->id_pengajuan, $doc->id_dokumen]) }}" 
                                          method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <label class="block text-[11px] font-bold text-gray-700 mb-1.5">Pilih Berkas Baru (PDF/JPG/PNG, Maks 2MB):</label>
                                        <input type="file" name="file_dokumen" required
                                               accept=".pdf,.jpg,.jpeg,.png"
                                               class="w-full text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:text-[11px] file:font-bold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100 cursor-pointer mb-2">
                                        <button type="submit" class="w-full py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-colors">
                                            Simpan Pengganti
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- SECTION: DATA KRITERIA & LOKASI SPASIAL -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Kriteria Profile Matching Summary -->
        <div class="lg:col-span-6 bg-white rounded-[24px] p-6 sm:p-8 shadow-sm border border-gray-100 space-y-4">
            <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-brand-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                5 Kriteria Usaha (Profile Matching)
            </h3>

            <div class="space-y-3 text-xs">
                <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50">
                    <span class="text-gray-500 font-medium">Estimasi Omzet Tahunan:</span>
                    <span class="font-bold text-gray-900 text-sm">Rp {{ number_format($pengajuan->omzet_tahunan, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50">
                    <span class="text-gray-500 font-medium">Nilai Total Aset Usaha:</span>
                    <span class="font-bold text-gray-900 text-sm">Rp {{ number_format($pengajuan->aset, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50">
                    <span class="text-gray-500 font-medium">Jumlah Tenaga Kerja:</span>
                    <span class="font-bold text-gray-900 text-sm">{{ $pengajuan->jumlah_tenaga_kerja }} Orang</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50">
                    <span class="text-gray-500 font-medium">Skala Jangkauan Pemasaran:</span>
                    <span class="font-bold text-brand-purple text-xs uppercase px-2.5 py-1 bg-purple-50 rounded-full border border-purple-200">
                        {{ ucfirst($pengajuan->jangkauan_pemasaran) }}
                    </span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50">
                    <span class="text-gray-500 font-medium">Status Kelengkapan Perizinan:</span>
                    <span class="font-bold text-brand-orange text-xs uppercase px-2.5 py-1 bg-orange-50 rounded-full border border-orange-200">
                        {{ str_replace('_', ' ', $pengajuan->status_perizinan) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- WebGIS Location Pin View -->
        <div class="lg:col-span-6 bg-white rounded-[24px] p-6 sm:p-8 shadow-sm border border-gray-100 space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-brand-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Titik Lokasi Usaha (WebGIS)
                </h3>
                <span class="text-[11px] font-mono text-gray-500">
                    {{ $umkm->latitude ? number_format($umkm->latitude, 5) . ', ' . number_format($umkm->longitude, 5) : 'Belum ditentukan' }}
                </span>
            </div>

            <div id="preview-map" class="w-full h-56 rounded-2xl border border-gray-200 shadow-inner z-10"></div>
            
            <p class="text-[11px] text-gray-500">
                Alamat Fisik: <strong class="text-gray-700">{{ $umkm->alamat ?? '-' }}</strong>
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Toggle Reupload Form
    function toggleReuploadForm(id) {
        const form = document.getElementById(id);
        if (form) {
            form.classList.toggle('hidden');
        }
    }

    // Leaflet Readonly Preview Map
    document.addEventListener('DOMContentLoaded', function() {
        const lat = {{ $umkm->latitude ?? -3.316694 }};
        const lng = {{ $umkm->longitude ?? 114.590111 }};

        const map = L.map('preview-map', {
            center: [lat, lng],
            zoom: 15,
            scrollWheelZoom: false
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        L.marker([lat, lng])
            .addTo(map)
            .bindPopup("<b>{{ $umkm->nama_umkm }}</b><br>{{ $umkm->nama_pemilik }}")
            .openPopup();
    });
</script>
@endpush
@endsection
