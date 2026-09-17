@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumbs & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.dokumen.index') }}" 
               class="p-2 rounded-xl bg-white border border-gray-200 text-gray-600 hover:text-brand-purple hover:border-brand-purple transition-all shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Verifikasi Berkas Kandidat</span>
                    <span class="text-gray-300">/</span>
                    <span class="text-xs font-semibold text-brand-purple">ID #{{ $pengajuan->id_pengajuan }}</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mt-0.5">
                    {{ $pengajuan->umkm?->nama_umkm ?? 'Nama UMKM' }}
                </h1>
            </div>
        </div>

        <!-- Global Status & Batch Actions -->
        <div class="flex flex-wrap items-center gap-3">
            @if($stats['is_lengkap'])
                <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    5/5 Dokumen Lengkap & Terverifikasi
                </span>
            @elseif($stats['total_uploaded'] > 0 && $stats['total_disetujui'] < $stats['total_wajib'])
                <form action="{{ route('admin.dokumen.setujuiSemua', $pengajuan->id_pengajuan) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui seluruh dokumen yang diunggah pemohon ini?')">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold py-2.5 px-4 rounded-xl text-xs shadow-md shadow-emerald-700/20 transition-all transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Setujui Semua Berkas Sekaligus
                    </button>
                </form>
            @endif

            <a href="{{ route('admin.dokumen.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition">
                &larr; Kembali ke Daftar
            </a>
        </div>
    </div>

    <!-- Applicant Profile & Progress Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Col 1 & 2: Profil Usaha & Pemilik -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-brand-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                Data Pendaftar & Profil Usaha
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div class="p-3 bg-gray-50 rounded-xl">
                    <span class="text-gray-400 block mb-1">Nama Pemilik Sah</span>
                    <span class="font-bold text-gray-900 text-sm">{{ $pengajuan->umkm?->nama_pemilik ?? '-' }}</span>
                </div>
                <div class="p-3 bg-gray-50 rounded-xl">
                    <span class="text-gray-400 block mb-1">Nomor Induk Kependudukan (NIK)</span>
                    <span class="font-bold font-mono text-gray-900 text-sm">{{ $pengajuan->umkm?->nik ?? '-' }}</span>
                </div>
                <div class="p-3 bg-gray-50 rounded-xl">
                    <span class="text-gray-400 block mb-1">Nomor Telepon / WhatsApp</span>
                    <span class="font-bold font-mono text-gray-900">{{ $pengajuan->umkm?->no_telepon ?? '-' }}</span>
                </div>
                <div class="p-3 bg-gray-50 rounded-xl">
                    <span class="text-gray-400 block mb-1">Tanggal Pengajuan</span>
                    <span class="font-semibold text-gray-800">
                        {{ $pengajuan->tanggal_pengajuan ? $pengajuan->tanggal_pengajuan->format('d F Y, H:i') : '-' }} WITA
                    </span>
                </div>
                <div class="sm:col-span-2 p-3 bg-gray-50 rounded-xl">
                    <span class="text-gray-400 block mb-1">Alamat Fisik Operasional</span>
                    <span class="font-medium text-gray-800">{{ $pengajuan->umkm?->alamat ?? '-' }}</span>
                    @if($pengajuan->umkm?->latitude && $pengajuan->umkm?->longitude)
                    <div class="mt-2 flex items-center gap-3 text-[11px] text-gray-500 font-mono">
                        <span>Koordinat: {{ $pengajuan->umkm->latitude }}, {{ $pengajuan->umkm->longitude }}</span>
                        <a href="{{ route('admin.webgis.index') }}" class="text-brand-purple font-sans font-bold hover:underline">
                            Lihat di Peta &rarr;
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Param Kriteria Nilai Input -->
            <div class="mt-5 pt-4 border-t border-gray-100">
                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-3">Estimasi Parameter Usaha (Input Pemohon)</span>
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 text-center text-xs">
                    <div class="p-2.5 rounded-xl border border-gray-100 bg-white">
                        <span class="text-[10px] text-gray-400 block">K1. Omzet</span>
                        <span class="font-bold text-gray-900 mt-1 block">Rp {{ number_format($pengajuan->omzet_tahunan, 0, ',', '.') }}</span>
                    </div>
                    <div class="p-2.5 rounded-xl border border-gray-100 bg-white">
                        <span class="text-[10px] text-gray-400 block">K2. Aset</span>
                        <span class="font-bold text-gray-900 mt-1 block">Rp {{ number_format($pengajuan->aset, 0, ',', '.') }}</span>
                    </div>
                    <div class="p-2.5 rounded-xl border border-gray-100 bg-white">
                        <span class="text-[10px] text-gray-400 block">K3. SDM</span>
                        <span class="font-bold text-gray-900 mt-1 block">{{ $pengajuan->jumlah_tenaga_kerja ?? 0 }} Orang</span>
                    </div>
                    <div class="p-2.5 rounded-xl border border-gray-100 bg-white">
                        <span class="text-[10px] text-gray-400 block">K4. Pasar</span>
                        <span class="font-bold text-gray-900 mt-1 block uppercase text-[11px]">{{ $pengajuan->jangkauan_pemasaran ?? '-' }}</span>
                    </div>
                    <div class="p-2.5 rounded-xl border border-gray-100 bg-white">
                        <span class="text-[10px] text-gray-400 block">K5. Izin</span>
                        <span class="font-bold text-gray-900 mt-1 block uppercase text-[11px]">{{ $pengajuan->status_perizinan ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Col 3: Status & Progress Widget -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between">
            <div>
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">
                    Status Validasi Berkas
                </h2>

                <div class="p-4 rounded-xl mb-4 
                    @if($pengajuan->status === 'terverifikasi') bg-emerald-50 border border-emerald-200 text-emerald-800
                    @elseif($pengajuan->status === 'revisi') bg-rose-50 border border-rose-200 text-rose-800
                    @else bg-amber-50 border border-amber-200 text-amber-800 @endif">
                    <span class="text-xs uppercase font-bold tracking-wider block mb-1">Status Pengajuan:</span>
                    <span class="text-base font-extrabold flex items-center gap-2">
                        @if($pengajuan->status === 'terverifikasi')
                            <span>✅ TERVERIFIKASI</span>
                        @elseif($pengajuan->status === 'revisi')
                            <span>⚠️ PERLU REVISI</span>
                        @else
                            <span>⏳ MENUNGGU VERIFIKASI</span>
                        @endif
                    </span>
                    <p class="text-xs mt-1.5 opacity-90 leading-relaxed">
                        @if($pengajuan->status === 'terverifikasi')
                            Semua dokumen valid. Pengajuan ini telah masuk antrean proses seleksi Profile Matching.
                        @elseif($pengajuan->status === 'revisi')
                            Satu atau lebih berkas ditolak. Pemohon telah diberikan catatan perbaikan untuk unggah ulang.
                        @else
                            Silakan periksa keabsahan 5 berkas wajib di bawah ini dan beri keputusan.
                        @endif
                    </p>
                </div>

                <!-- Progress Bar -->
                <div class="space-y-2">
                    <div class="flex justify-between text-xs font-semibold">
                        <span class="text-gray-600">Kelengkapan Berkas Disetujui</span>
                        <span class="text-brand-purple font-bold">{{ $stats['total_disetujui'] }} / {{ $stats['total_wajib'] }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-gradient-to-r from-brand-purple to-emerald-500 h-2.5 rounded-full transition-all duration-500" 
                             style="width: {{ ($stats['total_disetujui'] / $stats['total_wajib']) * 100 }}%"></div>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-3 gap-2 text-center text-[11px] pt-3 border-t border-gray-100">
                    <div>
                        <span class="text-emerald-600 font-bold block text-sm">{{ $stats['total_disetujui'] }}</span>
                        <span class="text-gray-400">Disetujui</span>
                    </div>
                    <div>
                        <span class="text-rose-600 font-bold block text-sm">{{ $stats['total_ditolak'] }}</span>
                        <span class="text-gray-400">Ditolak</span>
                    </div>
                    <div>
                        <span class="text-amber-600 font-bold block text-sm">{{ $stats['total_menunggu'] }}</span>
                        <span class="text-gray-400">Pending</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-100 text-xs text-gray-500">
                <p>💡 <em>Catatan:</em> Sistem akan otomatis mengubah status pengajuan menjadi <strong>TERVERIFIKASI</strong> begitu ke-5 berkas persyaratan berstatus "Disetujui".</p>
            </div>
        </div>
    </div>

    <!-- 5 Mandatory Documents Grid -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-bold text-gray-900">Pemeriksaan 5 Berkas Persyaratan Wajib</h2>
                <p class="text-xs text-gray-500">Tinjau kesesuaian fisik dokumen, setujui, atau tolak dengan melampirkan catatan perbaikan</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($jenisWajib as $kode => $info)
            @php
                $dokumen = $dokumenGrouped->get($kode);
            @endphp
            <div class="bg-white rounded-2xl p-5 shadow-sm border transition-all flex flex-col justify-between
                @if(!$dokumen) border-gray-200 bg-gray-50/40
                @elseif($dokumen->status_verifikasi === 'disetujui') border-emerald-200 ring-1 ring-emerald-100
                @elseif($dokumen->status_verifikasi === 'ditolak') border-rose-200 ring-1 ring-rose-100
                @else border-amber-200 ring-1 ring-amber-100 @endif">
                
                <div>
                    <!-- Card Header: Jenis & Badge -->
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div>
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-gray-100 text-gray-700 mb-1 font-mono">
                                {{ $kode }}
                            </span>
                            <h3 class="font-bold text-gray-900 text-sm">{{ $info['nama'] }}</h3>
                        </div>

                        <!-- Status Badge -->
                        <div>
                            @if(!$dokumen)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500 border border-gray-200">
                                    Belum Diunggah
                                </span>
                            @elseif($dokumen->status_verifikasi === 'disetujui')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Disetujui
                                </span>
                            @elseif($dokumen->status_verifikasi === 'ditolak')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    Ditolak
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    Menunggu
                                </span>
                            @endif
                        </div>
                    </div>

                    <p class="text-xs text-gray-500 mb-4 leading-relaxed">
                        {{ $info['deskripsi'] }}
                    </p>

                    <!-- Document Details if Uploaded -->
                    @if($dokumen)
                        <div class="p-3 bg-gray-50 rounded-xl mb-4 text-xs space-y-1.5 border border-gray-100">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-400">Tanggal Unggah:</span>
                                <span class="font-medium text-gray-800">{{ $dokumen->created_at ? $dokumen->created_at->format('d M Y, H:i') : '-' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-400">Format File:</span>
                                <span class="font-mono uppercase font-bold text-gray-700 text-[10px] bg-white px-1.5 py-0.5 rounded border">
                                    {{ pathinfo($dokumen->file_dokumen, PATHINFO_EXTENSION) }}
                                </span>
                            </div>
                            <div class="pt-2 border-t border-gray-200/60">
                                <a href="{{ route('admin.dokumen.download', $dokumen->id_dokumen) }}" 
                                   target="_blank" 
                                   class="inline-flex items-center justify-center gap-1.5 w-full py-2 px-3 rounded-lg bg-white border border-gray-200 text-brand-purple font-bold text-xs hover:bg-purple-50 hover:border-brand-purple transition shadow-2xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Buka & Periksa File Asli
                                </a>
                            </div>
                        </div>

                        <!-- If Rejected: Show Rejection Notes -->
                        @if($dokumen->status_verifikasi === 'ditolak' && $dokumen->catatan_admin)
                        <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl mb-4 text-xs text-rose-800">
                            <span class="font-bold flex items-center gap-1 text-[11px] mb-1">
                                <span>⚠️ Catatan Perbaikan Admin:</span>
                            </span>
                            <p class="italic">"{{ $dokumen->catatan_admin }}"</p>
                        </div>
                        @endif
                    @else
                        <div class="p-6 text-center border-2 border-dashed border-gray-200 rounded-xl mb-4 text-xs text-gray-400">
                            <svg class="w-8 h-8 mx-auto text-gray-300 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Pemohon belum mengunggah dokumen ini.
                        </div>
                    @endif
                </div>

                <!-- Action Controls for this Document -->
                @if($dokumen)
                <div class="pt-3 border-t border-gray-100 flex flex-col gap-2">
                    <div class="flex items-center gap-2">
                        <!-- Setujui Form -->
                        @if($dokumen->status_verifikasi !== 'disetujui')
                        <form action="{{ route('admin.dokumen.setujui', $dokumen->id_dokumen) }}" method="POST" class="flex-1">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="w-full inline-flex items-center justify-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-3 rounded-xl text-xs shadow-sm transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Setujui
                            </button>
                        </form>
                        @endif

                        <!-- Tolak Toggle Button -->
                        <button type="button" 
                                onclick="toggleRejectForm('reject-box-{{ $dokumen->id_dokumen }}')"
                                class="flex-1 inline-flex items-center justify-center gap-1.5 {{ $dokumen->status_verifikasi === 'disetujui' ? 'w-full' : '' }} bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold py-2 px-3 rounded-xl text-xs transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            Tolak dengan Catatan
                        </button>
                    </div>

                    <!-- Hidden Rejection Form Container -->
                    <div id="reject-box-{{ $dokumen->id_dokumen }}" class="hidden mt-2 p-3 bg-gray-50 border border-rose-200 rounded-xl transition-all">
                        <form action="{{ route('admin.dokumen.tolak', $dokumen->id_dokumen) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <label class="block text-[11px] font-bold text-gray-700 mb-1">
                                Alasan Penolakan & Instruksi Perbaikan:
                            </label>
                            <textarea name="catatan_admin" 
                                      rows="3" 
                                      required
                                      placeholder="Contoh: Foto dokumen blur / tidak terbaca, silakan scan ulang dengan pencahayaan cukup..." 
                                      class="w-full p-2 text-xs border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 bg-white mb-2">{{ $dokumen->catatan_admin }}</textarea>
                            <div class="flex items-center justify-end gap-2">
                                <button type="button" 
                                        onclick="toggleRejectForm('reject-box-{{ $dokumen->id_dokumen }}')"
                                        class="px-2.5 py-1.5 rounded-lg text-xs font-semibold text-gray-500 hover:bg-gray-200">
                                    Batal
                                </button>
                                <button type="submit" 
                                        class="px-3 py-1.5 rounded-lg text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white shadow-sm">
                                    Kirim Penolakan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>

<script>
function toggleRejectForm(boxId) {
    const el = document.getElementById(boxId);
    if (el) {
        el.classList.toggle('hidden');
    }
}
</script>
@endsection
