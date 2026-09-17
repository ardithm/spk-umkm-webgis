@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-[24px] shadow-sm border border-gray-100 overflow-hidden relative">
        <!-- Decorative Sparkles -->
        <div class="absolute top-4 right-6 text-brand-purple/20">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0l2.5 9.5L24 12l-9.5 2.5L12 24l-2.5-9.5L0 12l9.5-2.5z"/></svg>
        </div>

        <div class="p-6 sm:p-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-gray-50">
            <div>
                <h2 class="text-2xl font-extrabold text-brand-dark">Manajemen Periode Bantuan</h2>
                <p class="text-sm text-gray-500 mt-1">Kelola gelombang seleksi, jadwal, dan batas passing grade profil</p>
            </div>
            <a href="{{ route('admin.proses.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-brand-orange hover:bg-orange-400 text-white font-bold text-sm transition-all shadow-md shadow-brand-orange/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Periode Baru
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-8 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Periode</th>
                        <th class="px-8 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-8 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Tgl Pelaksanaan</th>
                        <th class="px-8 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Passing Grade</th>
                        <th class="px-8 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Kuota</th>
                        <th class="px-8 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-50 text-sm">
                    @forelse($prosesList as $p)
                    <tr class="hover:bg-purple-50/30 transition-colors">
                        <td class="px-8 py-5 whitespace-nowrap">
                            <div class="font-bold text-brand-dark">{{ $p->periode }}</div>
                            <div class="text-xs text-gray-400 mt-0.5 line-clamp-1 w-48">{{ $p->keterangan ?? '-' }}</div>
                        </td>
                        <td class="px-8 py-5 whitespace-nowrap">
                            @if($p->status === 'Buka')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>Buka
                                </span>
                            @elseif($p->status === 'Tutup')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-600 border border-rose-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5"></span>Tutup
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-gray-50 text-gray-600 border border-gray-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mr-1.5"></span>Draft
                                </span>
                            @endif
                        </td>
                        <td class="px-8 py-5 whitespace-nowrap font-semibold text-gray-600">
                            {{ $p->tanggal_proses ? $p->tanggal_proses->format('d M Y H:i') : '-' }}
                        </td>
                        <td class="px-8 py-5 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-purple-100 text-brand-purple">
                                {{ number_format($p->passing_grade, 2) }}
                            </span>
                        </td>
                        <td class="px-8 py-5 whitespace-nowrap font-medium text-gray-600">
                            {{ $p->kuota ? $p->kuota . ' UMKM' : 'Tidak Terbatas' }}
                        </td>
                        <td class="px-8 py-5 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.proses.edit', $p->id_proses) }}" class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-100 transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </a>
                                <form action="{{ route('admin.proses.destroy', $p->id_proses) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus periode ini? Data perhitungan terkait mungkin akan ikut terpengaruh.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded-full bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-100 transition-colors" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-12 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-3 text-gray-400">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            </div>
                            <p class="text-gray-500 font-medium">Belum ada data periode pendaftaran.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($prosesList->hasPages())
        <div class="p-6 border-t border-gray-50">
            {{ $prosesList->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
