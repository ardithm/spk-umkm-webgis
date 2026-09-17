@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Detail Hasil Perhitungan</h1>
            <p class="text-sm text-gray-500 mt-1">
                Periode: <span class="font-semibold text-gray-700">{{ $proses->periode }}</span> | 
                Passing Grade: <span class="font-semibold text-brand-purple">{{ number_format($proses->passing_grade, 2) }}</span>
            </p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.spk.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 border border-gray-300 px-4 py-2 rounded-lg bg-white">&larr; Kembali</a>
            <a href="{{ route('admin.spk.ranking', $proses->id_proses) }}" class="text-sm font-medium text-white bg-brand-purple hover:bg-opacity-90 px-4 py-2 rounded-lg shadow-sm">Lihat Ranking</a>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-500 border-r border-gray-200" rowspan="2">Rank</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-500 border-r border-gray-200" rowspan="2">Nama UMKM</th>
                    
                    {{-- Kriteria Headers --}}
                    @foreach($kriteria as $k)
                    <th scope="col" class="px-4 py-2 text-center font-semibold text-gray-500 border-r border-gray-200 border-b" colspan="4">
                        {{ $k->kode_kriteria }} ({{ $k->jenis_faktor == 'core' ? 'CF' : 'SF' }})
                    </th>
                    @endforeach

                    <th scope="col" class="px-4 py-3 text-center font-semibold text-gray-500 border-r border-gray-200" rowspan="2">NCF<br><span class="text-[10px] font-normal">(60%)</span></th>
                    <th scope="col" class="px-4 py-3 text-center font-semibold text-gray-500 border-r border-gray-200" rowspan="2">NSF<br><span class="text-[10px] font-normal">(40%)</span></th>
                    <th scope="col" class="px-4 py-3 text-center font-bold text-gray-800" rowspan="2">Nilai Akhir</th>
                </tr>
                <tr>
                    @foreach($kriteria as $k)
                    <th scope="col" class="px-2 py-1 text-center text-xs text-gray-400 bg-gray-50/50">Akt</th>
                    <th scope="col" class="px-2 py-1 text-center text-xs text-gray-400 bg-gray-50/50">Skor</th>
                    <th scope="col" class="px-2 py-1 text-center text-xs text-gray-400 bg-gray-50/50">GAP</th>
                    <th scope="col" class="px-2 py-1 text-center text-xs text-gray-400 bg-gray-50/50 border-r border-gray-200">Bbt</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($hasilList as $hasil)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-4 py-3 whitespace-nowrap border-r border-gray-200 text-center font-bold {{ $hasil->ranking <= 3 ? 'text-brand-orange' : 'text-gray-500' }}">
                        {{ $hasil->ranking }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap border-r border-gray-200">
                        <div class="font-semibold text-gray-900">{{ $hasil->pengajuan->umkm->nama_umkm }}</div>
                        <div class="text-[10px] text-gray-500">{{ $hasil->pengajuan->umkm->nama_pemilik }}</div>
                    </td>
                    
                    {{-- Detail Nilai --}}
                    @php
                        // Buat dictionary (keyBy id_kriteria) agar urutannya pasti pas dengan header tabel
                        $details = $hasil->pengajuan->detailPenilaian->keyBy('id_kriteria');
                    @endphp

                    @foreach($kriteria as $k)
                        @php $d = $details->get($k->id_kriteria); @endphp
                        @if($d)
                            <td class="px-2 py-2 text-center text-gray-600 bg-gray-50/20" title="Nilai Aktual">{{ is_numeric($d->nilai_aktual) && floor($d->nilai_aktual) != $d->nilai_aktual ? number_format($d->nilai_aktual, 2) : $d->nilai_aktual }}</td>
                            <td class="px-2 py-2 text-center text-gray-900 font-medium" title="Skor">{{ $d->skor }}</td>
                            <td class="px-2 py-2 text-center {{ $d->gap < 0 ? 'text-red-500' : ($d->gap > 0 ? 'text-blue-500' : 'text-gray-400') }}" title="GAP">{{ $d->gap > 0 ? '+'.$d->gap : $d->gap }}</td>
                            <td class="px-2 py-2 text-center font-semibold text-brand-purple border-r border-gray-200 bg-gray-50/20" title="Bobot GAP">{{ number_format($d->bobot_gap, 1) }}</td>
                        @else
                            <td colspan="4" class="px-2 py-2 text-center text-gray-300 border-r border-gray-200">-</td>
                        @endif
                    @endforeach

                    <td class="px-4 py-3 text-center border-r border-gray-200 font-medium text-gray-700">{{ number_format($hasil->nilai_ncf, 2) }}</td>
                    <td class="px-4 py-3 text-center border-r border-gray-200 font-medium text-gray-700">{{ number_format($hasil->nilai_nsf, 2) }}</td>
                    <td class="px-4 py-3 text-center font-bold text-lg {{ $hasil->status_seleksi == 'diterima' ? 'text-green-600' : 'text-gray-800' }}">
                        {{ number_format($hasil->nilai_akhir, 3) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ 5 + (count($kriteria) * 4) }}" class="px-6 py-12 text-center text-gray-500">
                        Belum ada data perhitungan. Silakan jalankan kalkulasi SPK.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
