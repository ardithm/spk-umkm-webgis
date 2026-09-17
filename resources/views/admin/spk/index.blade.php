@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Proses Perhitungan SPK</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola sesi kalkulasi kelayakan bantuan UMKM metode Profile Matching.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="text-sm font-medium text-brand-purple hover:underline">&larr; Kembali ke Dashboard</a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Periode Proses</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Passing Grade</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kuota</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status Hasil</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($prosesList as $p)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="font-semibold text-gray-900">{{ $p->periode }}</div>
                        <div class="text-xs text-gray-500 mt-1">{{ $p->tanggal_proses ? $p->tanggal_proses->format('d M Y H:i') : '-' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ number_format($p->passing_grade, 2) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $p->kuota ?? 'Tidak Dibatasi' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($p->hasil_perhitungan_count > 0)
                            <div class="flex gap-2">
                                <span class="px-2 py-1 rounded bg-green-50 text-green-700 text-xs font-bold" title="Diterima">{{ $p->total_diterima }}</span>
                                <span class="px-2 py-1 rounded bg-yellow-50 text-yellow-700 text-xs font-bold" title="Cadangan">{{ $p->total_cadangan }}</span>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-1">Total Dihitung: {{ $p->hasil_perhitungan_count }}</p>
                        @else
                            <span class="text-xs text-gray-400 italic">Belum dihitung</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end gap-3 items-center">
                            @if($p->hasil_perhitungan_count > 0)
                                <a href="{{ route('admin.spk.hasil', $p->id_proses) }}" class="text-blue-600 hover:text-blue-900 hover:underline">Detail</a>
                                <a href="{{ route('admin.spk.ranking', $p->id_proses) }}" class="text-indigo-600 hover:text-indigo-900 font-bold hover:underline">Ranking</a>
                            @endif
                            <form action="{{ route('admin.spk.hitung', $p->id_proses) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menjalankan ulang perhitungan SPK untuk periode ini? Data sebelumnya akan ditimpa.')">
                                @csrf
                                <button type="submit" class="bg-brand-purple text-white px-3 py-1.5 rounded-md hover:bg-opacity-90 transition-opacity text-xs font-bold flex items-center gap-1 shadow-sm">
                                    ▶ Hitung Ulang
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <p>Belum ada data periode proses seleksi.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($prosesList->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $prosesList->links() }}
    </div>
    @endif
</div>
@endsection
