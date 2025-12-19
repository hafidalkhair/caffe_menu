@extends('layouts.admin')

@section('title', 'Manajemen Kategori')

@section('content')

    {{-- HEADER SECTION --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4 fade-in-up">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Kategori Menu</h2>
            <p class="text-sm text-gray-500 mt-1">Atur pengelompokan menu agar mudah ditemukan pelanggan.</p>
        </div>

        {{-- Tombol Tambah --}}
        <a href="{{ route('admin.categories.create') }}"
           class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl font-bold hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200 flex items-center gap-2 transform hover:-translate-y-0.5">
            <i class="fas fa-plus"></i>
            <span>Kategori Baru</span>
        </a>
    </div>

    {{-- NOTIFIKASI --}}
    @if(session('success'))
    <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-3 fade-in-up shadow-sm">
        <i class="fas fa-check-circle text-xl"></i>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
    @endif

    {{-- CONTENT CARD --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col fade-in-up" style="animation-delay: 0.1s;">

        {{-- Card Header --}}
        <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/30 rounded-t-2xl">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                    <i class="fas fa-tags"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800">Daftar Kategori</h3>
            </div>

            {{-- Badge Total --}}
            <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-lg text-xs font-bold border border-gray-200">
                Total: {{ $categories instanceof \Illuminate\Pagination\LengthAwarePaginator ? $categories->total() : $categories->count() }}
            </span>
        </div>

        {{-- Scrollable Table Container --}}
        <div class="overflow-x-auto overflow-y-auto max-h-[600px] custom-scrollbar">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50 sticky top-0 z-10 shadow-sm">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider bg-gray-50">Nama Kategori</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider bg-gray-50">Deskripsi</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-wider bg-gray-50">Total Menu</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-wider bg-gray-50">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 bg-white">
                    @forelse($categories as $item)
                        <tr class="group hover:bg-indigo-50/30 transition-colors duration-200">
                            {{-- Nama --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-bold text-gray-800 group-hover:text-indigo-600 transition-colors">
                                    {{ $item->name }}
                                </span>
                            </td>

                            {{-- Deskripsi --}}
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-500 line-clamp-1 max-w-xs">
                                    {{ $item->description ?? '-' }}
                                </p>
                            </td>

                            {{-- Jumlah Menu --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700 border border-indigo-200">
                                    {{ $item->menus_count }} Item
                                </span>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2 opacity-80 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('admin.categories.edit', $item->id) }}"
                                       class="w-8 h-8 flex items-center justify-center rounded-lg text-indigo-600 bg-indigo-50 hover:bg-indigo-600 hover:text-white transition-all shadow-sm"
                                       title="Edit">
                                        <i class="fas fa-pen text-xs"></i>
                                    </a>

                                    <form action="{{ route('admin.categories.destroy', $item->id) }}" method="POST" class="inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete(this, '{{ $item->name }}')"
                                                class="w-8 h-8 flex items-center justify-center rounded-lg text-rose-500 bg-rose-50 hover:bg-rose-600 hover:text-white transition-all shadow-sm"
                                                title="Hapus">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-12">
                                <div class="inline-block p-4 rounded-full bg-gray-50 text-gray-300 mb-3">
                                    <i class="fas fa-folder-open text-3xl"></i>
                                </div>
                                <h3 class="text-gray-900 font-medium">Belum ada kategori</h3>
                                <p class="text-gray-500 text-sm mt-1">Mulai dengan menambahkan kategori baru.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION LINKS (Ditambahkan) --}}
        @if($categories instanceof \Illuminate\Pagination\LengthAwarePaginator && $categories->hasPages())
            <div class="p-4 border-t border-gray-100 bg-gray-50/50 rounded-b-2xl">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

    {{-- Styles untuk Scrollbar Halus --}}
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f9fafb; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #e5e7eb; border-radius: 20px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: #d1d5db; }
    </style>

    {{-- Script Konfirmasi Hapus --}}
    @push('scripts')
    <script>
        function confirmDelete(button, name) {
            Swal.fire({
                title: 'Hapus Kategori?',
                text: `Kategori "${name}" akan dihapus. Menu di dalamnya akan kehilangan kategori.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    button.closest('form').submit();
                }
            })
        }
    </script>
    @endpush

@endsection
