@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    <!-- Page Header & Title -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-100 text-brand-purple border border-purple-200">
                    Modul Pelaporan & Landasan SK
                </span>
            </div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-brand-dark tracking-tight mt-1">
                Rekapitulasi & Ekspor Data Seleksi
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Pusat cetak dokumen resmi penerima bantuan modal usaha dan ekspor spreadsheet untuk penetapan Surat Keputusan (SK).
            </p>
        </div>

    </div>

    <!-- Filter Card Container -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <form method="GET" action="{{ route('admin.laporan.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <!-- Dropdown Periode (Required) -->
                <div class="md:col-span-6">
                    <label for="id_proses" class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5">
                        Gelombang / Periode Bantuan <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select name="id_proses" id="id_proses" onchange="this.form.submit()"
                                class="w-full pl-4 pr-10 py-3 rounded-2xl bg-gray-50 border border-gray-200 text-sm font-medium text-brand-dark focus:bg-white focus:border-brand-purple focus:ring-2 focus:ring-brand-purple/20 transition-all appearance-none cursor-pointer">
                            @forelse($prosesList as $p)
                                <option value="{{ $p->id_proses }}" {{ $selectedProsesId == $p->id_proses ? 'selected' : '' }}>
                                    {{ $p->periode }} ({{ $p->tanggal_proses ? \Carbon\Carbon::parse($p->tanggal_proses)->format('d/m/Y') : 'Belum dihitung' }})
                                </option>
                            @empty
                                <option value="">Belum ada periode proses seleksi</option>
                            @endforelse
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Dropdown Status Kelayakan -->
                <div class="md:col-span-4">
                    <label for="status" class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5">
                        Status Kelayakan / Penerima
                    </label>
                    <div class="relative">
                        <select name="status" id="status" onchange="this.form.submit()"
                                class="w-full pl-4 pr-10 py-3 rounded-2xl bg-gray-50 border border-gray-200 text-sm font-medium text-brand-dark focus:bg-white focus:border-brand-purple focus:ring-2 focus:ring-brand-purple/20 transition-all appearance-none cursor-pointer">
                            <option value="semua" {{ $status == 'semua' ? 'selected' : '' }}>Semua Pendaftar (Seluruh Hasil)</option>
                            <option value="diterima" {{ $status == 'diterima' ? 'selected' : '' }}>Hanya Lolos / Rekomendasi Penerima</option>
                            <option value="cadangan" {{ $status == 'cadangan' ? 'selected' : '' }}>Hanya Cadangan (Luar Kuota)</option>
                            <option value="tidak_diterima" {{ $status == 'tidak_diterima' ? 'selected' : '' }}>Tidak Memenuhi Syarat (&lt; Passing Grade)</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Reset / Submit Button -->
                <div class="md:col-span-2 flex gap-2">
                    <button type="submit" 
                            class="w-full py-3 px-4 rounded-2xl bg-gray-900 text-white font-bold text-sm hover:bg-brand-purple transition-colors shadow-sm flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                        Terapkan
                    </button>
                    @if($status !== 'semua')
                    <a href="{{ route('admin.laporan.index', ['id_proses' => $selectedProsesId]) }}" 
                       title="Reset Filter Status"
                       class="py-3 px-3 rounded-2xl bg-gray-100 text-gray-500 hover:bg-gray-200 hover:text-gray-700 transition-colors flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    @if($proses)
    <!-- Summary Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card Total -->
        <a href="{{ route('admin.laporan.index', ['id_proses' => $proses->id_proses, 'status' => 'semua']) }}"
           class="bg-white p-5 rounded-2xl border transition-all hover:shadow-md {{ $status == 'semua' ? 'border-brand-purple ring-2 ring-brand-purple/20' : 'border-gray-100' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Peserta</span>
                <span class="w-8 h-8 rounded-xl bg-purple-50 text-brand-purple flex items-center justify-center font-bold text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </span>
            </div>
            <div class="text-3xl font-extrabold text-brand-dark mt-2">{{ $summary['total'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Dievaluasi pada gelombang ini</div>
        </a>

        <!-- Card Lolos / Diterima -->
        <a href="{{ route('admin.laporan.index', ['id_proses' => $proses->id_proses, 'status' => 'diterima']) }}"
           class="bg-white p-5 rounded-2xl border transition-all hover:shadow-md {{ $status == 'diterima' ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-gray-100' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-600">Lolos / Rekomendasi</span>
                <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </span>
            </div>
            <div class="text-3xl font-extrabold text-brand-dark mt-2">{{ $summary['diterima'] }}</div>
            <div class="text-xs text-emerald-600 font-medium mt-1">Kuota: {{ $proses->kuota ?? 'Bebas' }} UMKM</div>
        </a>

        <!-- Card Cadangan -->
        <a href="{{ route('admin.laporan.index', ['id_proses' => $proses->id_proses, 'status' => 'cadangan']) }}"
           class="bg-white p-5 rounded-2xl border transition-all hover:shadow-md {{ $status == 'cadangan' ? 'border-amber-500 ring-2 ring-amber-500/20' : 'border-gray-100' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-600">Daftar Cadangan</span>
                <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </span>
            </div>
            <div class="text-3xl font-extrabold text-brand-dark mt-2">{{ $summary['cadangan'] }}</div>
            <div class="text-xs text-amber-600 font-medium mt-1">Lulus PG, Luar Kuota</div>
        </a>

        <!-- Card Tidak Diterima -->
        <a href="{{ route('admin.laporan.index', ['id_proses' => $proses->id_proses, 'status' => 'tidak_diterima']) }}"
           class="bg-white p-5 rounded-2xl border transition-all hover:shadow-md {{ $status == 'tidak_diterima' ? 'border-rose-500 ring-2 ring-rose-500/20' : 'border-gray-100' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-rose-600">Tidak Lolos</span>
                <span class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </span>
            </div>
            <div class="text-3xl font-extrabold text-brand-dark mt-2">{{ $summary['tidak_diterima'] }}</div>
            <div class="text-xs text-rose-600 font-medium mt-1">&lt; PG ({{ number_format($proses->passing_grade, 2) }})</div>
        </a>
    </div>

    <!-- Live Preview Table Section -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <!-- Table Top Header -->
        <div class="p-6 border-b border-gray-50 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h3 class="text-base font-extrabold text-brand-dark">Pratinjau Data Rekapitulasi</h3>
                <p class="text-xs text-gray-500 mt-0.5">
                    Menampilkan data yang akan dicetak/diekspor sesuai filter yang sedang aktif ({{ $hasilList->total() }} data ditemukan).
                </p>
            </div>
            
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.laporan.pdf', ['id' => $proses->id_proses, 'status' => $status]) }}" 
                   title="Unduh Dokumen PDF Rekapitulasi"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-purple-200 bg-purple-50/70 text-xs font-bold text-brand-purple hover:bg-brand-purple hover:text-white transition-all shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    <span>Cetak PDF</span>
                </a>

                <a href="{{ route('admin.laporan.excel', ['id' => $proses->id_proses, 'status' => $status]) }}" 
                   title="Unduh Spreadsheet Excel (.xlsx)"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-emerald-200 bg-emerald-50/70 text-xs font-bold text-emerald-700 hover:bg-emerald-600 hover:text-white transition-all shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span>Ekspor Excel</span>
                </a>

                <a href="{{ route('admin.spk.ranking', $proses->id_proses) }}" 
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-gray-200 text-xs font-semibold text-gray-600 hover:text-brand-purple hover:border-brand-purple transition-colors">
                    Lihat Ranking SPK &rarr;
                </a>
            </div>
        </div>

        <!-- Table Responsive Container -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600 divide-y divide-gray-100">
                <thead class="bg-gray-50/75 text-[11px] font-bold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="py-3.5 px-4 text-center w-14">Rank</th>
                        <th class="py-3.5 px-4">Nama Pemilik / NIK</th>
                        <th class="py-3.5 px-4">Nama Usaha (UMKM)</th>
                        <th class="py-3.5 px-4 text-center">Legalitas</th>
                        <th class="py-3.5 px-4">Alamat Domisili</th>
                        <th class="py-3.5 px-4 text-center">NCF (60%)</th>
                        <th class="py-3.5 px-4 text-center">NSF (40%)</th>
                        <th class="py-3.5 px-4 text-center">Nilai Akhir</th>
                        <th class="py-3.5 px-4 text-center">Status Kelayakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 bg-white font-medium">
                    @forelse($hasilList as $hasil)
                        @php
                            $pengajuan = $hasil->pengajuan;
                            $umkm = $pengajuan?->umkm;
                        @endphp
                        <tr class="hover:bg-purple-50/30 transition-colors">
                            <td class="py-4 px-4 text-center">
                                @if($hasil->ranking == 1)
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-xl bg-amber-100 text-amber-700 font-extrabold text-xs shadow-sm">1</span>
                                @elseif($hasil->ranking == 2)
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-xl bg-slate-100 text-slate-700 font-extrabold text-xs shadow-sm">2</span>
                                @elseif($hasil->ranking == 3)
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-xl bg-amber-50 text-amber-800 font-extrabold text-xs shadow-sm">3</span>
                                @else
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-xl bg-gray-100 text-gray-600 font-semibold text-xs">{{ $hasil->ranking ?? '-' }}</span>
                                @endif
                            </td>

                            <td class="py-4 px-4">
                                <div class="font-bold text-brand-dark">{{ $umkm?->nama_pemilik ?? '-' }}</div>
                                <div class="text-xs font-mono text-gray-400 mt-0.5">{{ $umkm?->nik ?? '-' }}</div>
                            </td>

                            <td class="py-4 px-4">
                                <div class="font-semibold text-brand-dark">{{ $umkm?->nama_umkm ?? '-' }}</div>
                                <div class="text-xs text-gray-400">{{ $umkm?->no_telepon ?? '-' }}</div>
                            </td>

                            <td class="py-4 px-4 text-center">
                                @php
                                    $izin = $pengajuan?->status_perizinan;
                                @endphp
                                @if(in_array($izin, ['nib', 'nib_lengkap']))
                                    <span class="inline-flex px-2 py-0.5 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                        {{ strtoupper(str_replace('_', ' ', $izin)) }}
                                    </span>
                                @elseif(str_contains($izin ?? '', 'sku'))
                                    <span class="inline-flex px-2 py-0.5 rounded-lg text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                        {{ strtoupper(str_replace('_', ' ', $izin)) }}
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded-lg text-xs font-semibold bg-gray-50 text-gray-400 border border-gray-100">
                                        {{ $izin ? strtoupper($izin) : '-' }}
                                    </span>
                                @endif
                            </td>

                            <td class="py-4 px-4 max-w-xs">
                                <p class="text-xs text-gray-500 line-clamp-2" title="{{ $umkm?->alamat }}">
                                    {{ $umkm?->alamat ?? '-' }}
                                </p>
                            </td>

                            <td class="py-4 px-4 text-center font-mono text-xs">
                                {{ number_format($hasil->nilai_ncf, 4) }}
                            </td>

                            <td class="py-4 px-4 text-center font-mono text-xs">
                                {{ number_format($hasil->nilai_nsf, 4) }}
                            </td>

                            <td class="py-4 px-4 text-center">
                                <span class="font-extrabold text-sm text-brand-purple font-mono">
                                    {{ number_format($hasil->nilai_akhir, 4) }}
                                </span>
                            </td>

                            <td class="py-4 px-4 text-center whitespace-nowrap">
                                @if($hasil->status_seleksi === 'diterima')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Lolos / Diterima
                                    </span>
                                @elseif($hasil->status_seleksi === 'cadangan')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Cadangan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        Tidak Lolos
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 px-4 text-center">
                                <div class="w-12 h-12 rounded-2xl bg-gray-50 text-gray-300 flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <div class="text-sm font-bold text-gray-700">Tidak ada data ditemukan</div>
                                <p class="text-xs text-gray-400 mt-1">Silakan pilih periode gelombang lain atau sesuaikan filter status kelayakan di atas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($hasilList->hasPages())
        <!-- Pagination Area -->
        <div class="p-4 border-t border-gray-50">
            {{ $hasilList->links() }}
        </div>
        @endif
    </div>
    @else
    <!-- Empty State jika belum ada periode -->
    <div class="bg-white rounded-2xl p-12 text-center border border-gray-100 shadow-sm">
        <div class="w-16 h-16 rounded-2xl bg-purple-50 text-brand-purple flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        </div>
        <h3 class="text-lg font-bold text-brand-dark">Belum Ada Sesi Seleksi</h3>
        <p class="text-sm text-gray-500 mt-1 max-w-md mx-auto">
            Silakan buat sesi periode pendaftaran dan jalankan kalkulasi Profile Matching terlebih dahulu sebelum mengekspor data laporan.
        </p>
        <div class="mt-6">
            <a href="{{ route('admin.proses.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-brand-purple text-white text-sm font-bold shadow-md shadow-brand-purple/20 hover:bg-purple-700 transition-all">
                Buat Periode Baru
            </a>
        </div>
    </div>
    @endif

</div>
@endsection
