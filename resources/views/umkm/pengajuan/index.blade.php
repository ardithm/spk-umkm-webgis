@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 mb-1">
                <a href="{{ route('umkm.dashboard') }}" class="hover:text-brand-purple transition-colors flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Dasbor
                </a>
                <span>/</span>
                <span class="text-brand-purple">Pengajuan Bantuan</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Riwayat & Status Pengajuan</h1>
            <p class="text-sm text-gray-500 mt-1">Pantau status seleksi, kelengkapan berkas, dan catatan revisi bantuan modal usaha Anda.</p>
        </div>

        <div>
            @if(!$pengajuanAktif)
                @if($gelombangAktif)
                    <a href="{{ route('umkm.pengajuan.create') }}" class="inline-flex items-center gap-2.5 bg-brand-orange hover:bg-orange-500 text-white font-bold py-3 px-6 rounded-full shadow-md shadow-brand-orange/30 hover:shadow-lg hover:-translate-y-0.5 transition-all text-xs focus:ring-4 focus:ring-brand-orange/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                        <span>Buat Pengajuan Baru</span>
                    </a>
                @else
                    <button disabled class="inline-flex items-center gap-2.5 bg-gray-200 text-gray-400 font-bold py-3 px-6 rounded-full cursor-not-allowed shadow-sm text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        <span>Pendaftaran Ditutup</span>
                    </button>
                @endif
            @endif
        </div>
    </div>

    <!-- Peringatan Gelombang Tutup -->
    @if(!$gelombangAktif && !$pengajuanAktif)
        <div class="bg-rose-50 border border-rose-200 rounded-[24px] p-5 sm:p-6 shadow-sm flex items-start gap-4 text-rose-900 animate-in fade-in">
            <div class="w-10 h-10 rounded-full bg-rose-100 flex items-center justify-center shrink-0 text-rose-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </div>
            <div>
                <h3 class="text-base font-bold text-rose-950">Pendaftaran Pengajuan Bantuan Ditutup</h3>
                <p class="text-xs text-rose-800 mt-1 leading-relaxed">
                    Saat ini tidak ada Gelombang atau Periode Seleksi penerimaan bantuan modal usaha yang berstatus aktif. Anda tidak dapat mengajukan permohonan baru. Silakan pantau pengumuman dinas secara berkala.
                </p>
            </div>
        </div>
    @endif

    <!-- Active Application Highlight Banner (If Active) -->
    @if($pengajuanAktif)
        <div class="bg-white rounded-[24px] p-6 sm:p-8 shadow-sm border border-brand-purple/15 relative overflow-hidden group">
            <div class="absolute -top-12 -right-12 w-48 h-48 bg-gradient-to-br from-brand-purple/10 to-brand-orange/10 rounded-full blur-3xl -z-10 group-hover:scale-110 transition-transform duration-500"></div>

            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-brand-purple/10 text-brand-purple">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            Pengajuan Aktif Saat Ini
                        </span>

                        @php
                            $statusConfig = [
                                'draft'         => ['color' => 'bg-gray-100 text-gray-700 border-gray-200', 'label' => 'Draf Pengajuan'],
                                'menunggu'      => ['color' => 'bg-amber-50 text-amber-800 border-amber-200', 'label' => 'Menunggu Verifikasi Berkas'],
                                'revisi'        => ['color' => 'bg-rose-50 text-rose-800 border-rose-200 animate-pulse', 'label' => 'Perlu Perbaikan / Revisi Berkas'],
                                'terverifikasi' => ['color' => 'bg-emerald-50 text-emerald-800 border-emerald-200', 'label' => 'Berkas Terverifikasi'],
                                'diproses'      => ['color' => 'bg-blue-50 text-blue-800 border-blue-200', 'label' => 'Kalkulasi SPK Berjalan'],
                                'selesai'       => ['color' => 'bg-purple-50 text-purple-800 border-purple-200', 'label' => 'Seleksi Selesai'],
                            ];
                            $curr = $statusConfig[$pengajuanAktif->status] ?? ['color' => 'bg-gray-100 text-gray-700 border-gray-200', 'label' => ucfirst($pengajuanAktif->status)];
                        @endphp

                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase border {{ $curr['color'] }}">
                            {{ $curr['label'] }}
                        </span>
                    </div>

                    <h2 class="text-xl sm:text-2xl font-extrabold text-gray-900 tracking-tight">
                        Pengajuan Bantuan Modal — {{ $umkm->nama_umkm }}
                    </h2>

                    <div class="flex flex-wrap items-center gap-y-2 gap-x-6 text-xs text-gray-500">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-brand-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Diajukan: {{ $pengajuanAktif->tanggal_pengajuan ? $pengajuanAktif->tanggal_pengajuan->format('d M Y, H:i') : 'Belum disubmit' }} WITA
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-brand-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Berkas Terlampir: {{ $pengajuanAktif->dokumen->count() }} / 5 Dokumen
                        </span>
                    </div>

                    @if($pengajuanAktif->status === 'revisi')
                        <!-- Special Alert for Revision -->
                        <div class="mt-4 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs flex items-start gap-3">
                            <div class="w-7 h-7 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            <div class="space-y-1">
                                <p class="font-bold text-rose-950">Terdapat Berkas yang Ditolak oleh Verifikator!</p>
                                <p class="text-rose-800 leading-relaxed">
                                    {{ $pengajuanAktif->catatan_revisi ?? 'Mohon buka detail pengajuan untuk melihat berkas yang ditolak dan mengunggah dokumen pengganti.' }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="flex flex-wrap items-center gap-3 shrink-0">
                    @if($pengajuanAktif->isEditable())
                        <a href="{{ route('umkm.pengajuan.edit', $pengajuanAktif->id_pengajuan) }}" 
                           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full border border-gray-200 bg-white hover:bg-gray-50 text-xs font-bold text-gray-700 shadow-sm transition-all">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            <span>Ubah Data Usaha</span>
                        </a>
                    @endif

                    <a href="{{ route('umkm.pengajuan.show', $pengajuanAktif->id_pengajuan) }}" 
                       class="inline-flex items-center gap-2.5 px-6 py-2.5 rounded-full bg-brand-purple hover:bg-purple-700 text-white text-xs font-bold shadow-md shadow-brand-purple/20 transition-all hover:shadow-lg hover:-translate-y-0.5">
                        <span>Lihat Detail & Berkas</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- All Submissions History Table -->
    <div class="bg-white rounded-[24px] p-6 sm:p-8 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between pb-6 mb-6 border-b border-gray-100">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Daftar Pengajuan Anda</h3>
                <p class="text-xs text-gray-500 mt-1">Seluruh riwayat pendaftaran bantuan modal usaha yang pernah Anda ajukan ke Dinas.</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-600">
                Total: {{ $pengajuanList->total() }}
            </span>
        </div>

        @if($pengajuanList->isEmpty())
            <div class="text-center py-12 px-4">
                <div class="w-16 h-16 rounded-full bg-purple-50 text-brand-purple flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h4 class="text-base font-bold text-gray-900">Belum Ada Pengajuan Bantuan</h4>
                <p class="text-xs text-gray-500 max-w-md mx-auto mt-1 mb-6">
                    Anda belum pernah mengirimkan formulir permohonan bantuan modal usaha. Silakan mulai buat pengajuan baru sekarang.
                </p>
                @if($gelombangAktif)
                    <a href="{{ route('umkm.pengajuan.create') }}" class="inline-flex items-center gap-2 bg-brand-orange hover:bg-orange-500 text-white font-bold py-3 px-6 rounded-full text-xs shadow-md shadow-brand-orange/30 transition-all">
                        <span>+ Mulai Buat Pengajuan</span>
                    </a>
                @else
                    <span class="inline-flex items-center gap-2 bg-gray-100 text-gray-400 font-bold py-3 px-6 rounded-full text-xs cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        <span>Pendaftaran Ditutup</span>
                    </span>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-gray-600">
                    <thead class="bg-gray-50 text-gray-500 uppercase tracking-wider font-semibold border-b border-gray-100">
                        <tr>
                            <th class="py-3.5 px-4 rounded-l-xl">No</th>
                            <th class="py-3.5 px-4">Tanggal Pengajuan</th>
                            <th class="py-3.5 px-4">Omzet / Aset</th>
                            <th class="py-3.5 px-4">Kelengkapan Berkas</th>
                            <th class="py-3.5 px-4">Status Seleksi</th>
                            <th class="py-3.5 px-4 text-right rounded-r-xl">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($pengajuanList as $index => $item)
                            <tr class="hover:bg-gray-50/60 transition-colors">
                                <td class="py-4 px-4 font-bold text-gray-800">
                                    {{ $pengajuanList->firstItem() + $index }}
                                </td>
                                <td class="py-4 px-4 font-medium text-gray-900">
                                    {{ $item->tanggal_pengajuan ? $item->tanggal_pengajuan->format('d M Y, H:i') : 'Draf belum disubmit' }}
                                </td>
                                <td class="py-4 px-4">
                                    <div class="font-bold text-gray-900">Rp {{ number_format($item->omzet_tahunan, 0, ',', '.') }}</div>
                                    <div class="text-[11px] text-gray-400">Aset: Rp {{ number_format($item->aset, 0, ',', '.') }}</div>
                                </td>
                                <td class="py-4 px-4">
                                    @php
                                        $uploadedCount = $item->dokumen->count();
                                        $disetujuiCount = $item->dokumen->where('status_verifikasi', 'disetujui')->count();
                                    @endphp
                                    <div class="flex items-center gap-1.5 font-semibold">
                                        <span class="{{ $uploadedCount >= 5 ? 'text-emerald-700' : 'text-amber-700' }}">
                                            {{ $uploadedCount }}/5 Berkas
                                        </span>
                                        @if($disetujuiCount > 0)
                                            <span class="text-[10px] text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                                {{ $disetujuiCount }} Terverifikasi
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    @php
                                        $badgeClasses = [
                                            'draft'         => 'bg-gray-100 text-gray-700 border-gray-200',
                                            'menunggu'      => 'bg-amber-50 text-amber-800 border-amber-200',
                                            'revisi'        => 'bg-rose-50 text-rose-800 border-rose-200',
                                            'terverifikasi' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                            'diproses'      => 'bg-blue-50 text-blue-800 border-blue-200',
                                            'selesai'       => 'bg-purple-50 text-purple-800 border-purple-200',
                                        ];
                                        $badgeClass = $badgeClasses[$item->status] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold uppercase border {{ $badgeClass }}">
                                        {{ $item->status }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('umkm.pengajuan.show', $item->id_pengajuan) }}" 
                                           class="px-3.5 py-1.5 rounded-full bg-brand-purple/10 text-brand-purple hover:bg-brand-purple hover:text-white font-bold text-xs transition-colors">
                                            Detail
                                        </a>

                                        @if($item->status === 'draft')
                                            <form action="{{ route('umkm.pengajuan.destroy', $item->id_pengajuan) }}" method="POST" class="inline" onsubmit="return confirm('Batalkan dan hapus draf pengajuan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-gray-400 hover:text-rose-600 rounded-full transition-colors" title="Hapus Draf">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-100">
                {{ $pengajuanList->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
