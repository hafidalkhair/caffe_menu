@extends('layouts.admin')

@section('title', 'Manajemen Menu & Produk')

@section('content')

    {{-- HEADER SECTION --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4 fade-in-up">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Daftar Menu & Produk</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola semua hidangan dan produk yang tersedia di cafe.</p>
        </div>

        {{-- Tombol Tambah --}}
        <a href="{{ route('admin.menus.create') }}"
           class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl font-bold hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200 flex items-center gap-2 transform hover:-translate-y-0.5">
            <i class="fas fa-plus"></i>
            <span>Menu Baru</span>
        </a>
    </div>

    {{-- FILTER CARD --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6 fade-in-up" style="animation-delay: 0.1s;">
        <form action="{{ route('admin.menus.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center">

            <div class="flex items-center gap-2 text-gray-600 w-full md:w-auto">
                <i class="fas fa-filter text-indigo-500"></i>
                <span class="font-bold text-sm">Filter Kategori:</span>
            </div>

            <div class="flex-1 w-full md:w-auto">
                <select name="category_id" onchange="this.form.submit()"
                    class="w-full md:w-64 pl-4 pr-10 py-2.5 rounded-xl border-gray-200 bg-gray-50 focus:bg-white text-gray-800 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm cursor-pointer transition-all">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            @if(request('category_id'))
                <a href="{{ route('admin.menus.index') }}" class="text-xs text-red-500 hover:text-red-700 font-semibold underline">
                    Reset Filter
                </a>
            @endif
        </form>
    </div>

    {{-- NOTIFIKASI --}}
    @if(session('success'))
    <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-3 fade-in-up shadow-sm">
        <i class="fas fa-check-circle text-xl"></i>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
    @endif

    {{-- TABEL MENU --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col fade-in-up" style="animation-delay: 0.2s;">

        {{-- Card Header --}}
        <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/30 rounded-t-2xl">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                    <i class="fas fa-utensils"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800">Menu Saat Ini</h3>
            </div>
            <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-lg text-xs font-bold border border-gray-200">
                Total: {{ $menus->total() }} Item
            </span>
        </div>

        {{-- Scrollable Table --}}
        <div class="overflow-x-auto overflow-y-auto max-h-[600px] custom-scrollbar">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50 sticky top-0 z-10 shadow-sm">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider bg-gray-50 w-24">Gambar</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider bg-gray-50">Nama & Deskripsi</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider bg-gray-50">Kategori</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider bg-gray-50">Harga</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-wider bg-gray-50">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 bg-white">
                    @forelse($menus as $item)
                        <tr class="group hover:bg-indigo-50/30 transition-colors duration-200">
                            {{-- Gambar --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="w-16 h-16 rounded-xl overflow-hidden shadow-sm border border-gray-100 group-hover:shadow-md transition-all">
                                    <img src="{{ str_contains($item->image, 'images/menus/') ? asset($item->image) : asset('storage/' . $item->image) }}"
                                         class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500"
                                         alt="{{ $item->name }}">
                                </div>
                            </td>

                            {{-- Nama & Deskripsi --}}
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-gray-800 group-hover:text-indigo-600 transition-colors">{{ $item->name }}</p>
                                <p class="text-xs text-gray-500 line-clamp-2 mt-1 leading-relaxed max-w-xs">{{ $item->description }}</p>
                            </td>

                            {{-- Kategori --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-50 text-purple-700 border border-purple-100">
                                    {{ $item->category->name ?? 'Tanpa Kategori' }}
                                </span>
                            </td>

                            {{-- Harga --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg border border-emerald-100">
                                    Rp{{ number_format($item->price, 0, ',', '.') }}
                                </span>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2 opacity-80 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('admin.menus.edit', $item->id) }}"
                                       class="w-8 h-8 flex items-center justify-center rounded-lg text-indigo-600 bg-indigo-50 hover:bg-indigo-600 hover:text-white transition-all shadow-sm"
                                       title="Edit">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>

                                    <form action="{{ route('admin.menus.destroy', $item->id) }}" method="POST" class="inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete(this, '{{ $item->name }}')"
                                                class="w-8 h-8 flex items-center justify-center rounded-lg text-rose-500 bg-rose-50 hover:bg-rose-600 hover:text-white transition-all shadow-sm"
                                                title="Hapus">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-12">
                                <div class="inline-block p-4 rounded-full bg-gray-50 text-gray-300 mb-3">
                                    <i class="fas fa-hamburger text-3xl"></i>
                                </div>
                                <h3 class="text-gray-900 font-medium">Tidak ada menu ditemukan</h3>
                                <p class="text-gray-500 text-sm mt-1">
                                    @if(request('category_id'))
                                        Coba pilih kategori lain atau reset filter.
                                    @else
                                        Mulai dengan menambahkan menu baru.
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($menus->hasPages())
            <div class="p-4 border-t border-gray-100 bg-gray-50/50 rounded-b-2xl">
                {{ $menus->links() }}
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
                title: 'Hapus Menu?',
                text: `Menu "${name}" akan dihapus permanen!`,
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
