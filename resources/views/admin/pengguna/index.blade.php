@extends('layouts.admin')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Kelola Data Pengguna</h1>
        <p class="text-sm text-gray-500 mt-1">Manajemen akun Administrator dan Pelaku UMKM terdaftar.</p>
    </div>
    <a href="{{ route('admin.pengguna.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-brand-purple text-white text-sm font-semibold rounded-full hover:bg-purple-700 transition shadow-sm">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Pengguna
    </a>
</div>

<!-- Card Utama -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    
    <!-- Filter Toolbar -->
    <div class="p-4 border-b border-gray-100 bg-gray-50/50">
        <form action="{{ route('admin.pengguna.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
            <!-- Search -->
            <div class="md:col-span-5 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, username, atau email..." 
                       class="w-full pl-10 pr-3 py-2.5 text-sm bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-purple focus:border-brand-purple transition" />
            </div>

            <!-- Filter Role -->
            <div class="md:col-span-3">
                <select name="role" class="w-full py-2.5 px-3 text-sm bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-purple focus:border-brand-purple transition">
                    <option value="">Semua Role</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="umkm" {{ request('role') == 'umkm' ? 'selected' : '' }}>UMKM</option>
                </select>
            </div>

            <!-- Tombol Aksi Filter -->
            <div class="md:col-span-4 flex items-center gap-2">
                <button type="submit" class="w-full md:w-auto px-5 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded-xl hover:bg-gray-800 transition">
                    Terapkan
                </button>
                @if(request()->hasAny(['search', 'role']))
                    <a href="{{ route('admin.pengguna.index') }}" class="w-full md:w-auto px-5 py-2.5 bg-white text-gray-600 border border-gray-200 text-sm font-semibold rounded-xl hover:bg-gray-50 transition text-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Data -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-white border-b border-gray-100 text-xs uppercase tracking-wider text-gray-500">
                    <th class="px-6 py-4 font-bold">No</th>
                    <th class="px-6 py-4 font-bold">Nama Lengkap</th>
                    <th class="px-6 py-4 font-bold">Kontak / Kredensial</th>
                    <th class="px-6 py-4 font-bold text-center">Role</th>
                    <th class="px-6 py-4 font-bold">Didaftarkan</th>
                    <th class="px-6 py-4 font-bold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pengguna as $key => $user)
                    <tr class="hover:bg-purple-50/30 transition group">
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $pengguna->firstItem() + $key }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($user->foto_url)
                                    <img src="{{ $user->foto_url }}" alt="Avatar" class="w-10 h-10 rounded-full object-cover border border-gray-200">
                                @else
                                    <div class="w-10 h-10 rounded-full {{ $user->role === 'admin' ? 'bg-purple-100 text-brand-purple' : 'bg-orange-100 text-brand-orange' }} flex items-center justify-center font-bold text-sm shrink-0">
                                        {{ strtoupper(substr($user->nama_lengkap, 0, 2)) }}
                                    </div>
                                @endif
                                <div>
                                    <p class="text-sm font-bold text-gray-900">{{ $user->nama_lengkap }}</p>
                                    <p class="text-xs text-gray-500">@ {{ $user->username }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-700 font-medium">{{ $user->email }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($user->role === 'admin')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-purple-100 text-purple-800 border border-purple-200">
                                    Admin
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-orange-100 text-orange-800 border border-orange-200">
                                    UMKM
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $user->created_at->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.pengguna.edit', $user->id_user) }}" class="p-2 text-gray-400 hover:text-brand-purple hover:bg-purple-50 rounded-lg transition" title="Edit Data">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </a>
                                
                                @if(Auth::id() !== $user->id_user)
                                <button type="button" onclick="confirmDelete({{ $user->id_user }}, '{{ $user->nama_lengkap }}')" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition" title="Hapus Data">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </div>
                            <h3 class="text-base font-bold text-gray-900 mb-1">Data Pengguna Kosong</h3>
                            <p class="text-sm text-gray-500">Tidak ada pengguna yang sesuai dengan kriteria pencarian.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($pengguna->hasPages())
    <div class="p-4 border-t border-gray-100 bg-gray-50/30">
        {{ $pengguna->links('pagination::tailwind') }}
    </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-md w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                            Hapus Pengguna
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Apakah Anda yakin ingin menghapus akun <strong id="deleteUserName" class="text-gray-900"></strong>? Tindakan ini tidak dapat dibatalkan.
                            </p>
                            <p class="text-[11px] text-red-500 mt-2 bg-red-50 p-2 rounded-lg border border-red-100">
                                Catatan: Jika ini adalah akun UMKM yang sudah memiliki data profil usaha atau riwayat pengajuan, penghapusan akan ditolak oleh sistem untuk menjaga integritas data.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition">
                        Ya, Hapus Akun
                    </button>
                </form>
                <button type="button" onclick="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-purple sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function confirmDelete(id, name) {
        document.getElementById('deleteUserName').textContent = name;
        document.getElementById('deleteForm').action = '/admin/pengguna/' + id;
        
        const modal = document.getElementById('deleteModal');
        modal.classList.remove('hidden');
    }

    function closeModal() {
        const modal = document.getElementById('deleteModal');
        modal.classList.add('hidden');
    }
</script>
@endpush
