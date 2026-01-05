@extends('layouts.admin')

@section('title', 'Manajemen Menu & Produk')

@section('content')

    {{-- HEADER SECTION --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4 fade-in-up">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Daftar Menu & Produk</h2>
            <p class="text-sm text-slate-500 mt-1">Kelola semua hidangan dan produk yang tersedia.</p>
        </div>

        {{-- Tombol Tambah --}}
        <a href="{{ route('admin.menus.create') }}"
           class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl font-bold hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200 flex items-center gap-2 transform hover:-translate-y-0.5">
            <i class="fas fa-plus"></i>
            <span>Menu Baru</span>
        </a>
    </div>

    {{-- FILTER CARD --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 mb-6 fade-in-up" style="animation-delay: 0.1s;">
        <form action="{{ route('admin.menus.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center">

            <div class="flex items-center gap-2 text-slate-600 w-full md:w-auto min-w-max">
                <div class="p-2 bg-slate-100 rounded-lg">
                    <i class="fas fa-filter text-slate-500"></i>
                </div>
                <span class="font-bold text-sm">Filter:</span>
            </div>

            <div class="flex-1 w-full md:w-auto relative">
                <i class="fas fa-tags absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <select name="category_id" onchange="this.form.submit()"
                    class="w-full pl-9 pr-10 py-2.5 rounded-xl border-slate-200 bg-slate-50 focus:bg-white text-slate-700 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm cursor-pointer transition-all outline-none appearance-none font-medium">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
            </div>

            @if(request('category_id'))
                <a href="{{ route('admin.menus.index') }}" class="px-4 py-2.5 bg-red-50 text-red-600 rounded-xl text-xs font-bold hover:bg-red-100 transition-colors flex items-center gap-2">
                    <i class="fas fa-times"></i> Reset
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
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 flex flex-col fade-in-up overflow-hidden" style="animation-delay: 0.2s;">

        {{-- Card Header --}}
        <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                    <i class="fas fa-utensils"></i>
                </div>
                <h3 class="text-base font-bold text-slate-800">Daftar Menu</h3>
            </div>
            <span class="px-3 py-1 bg-white text-slate-600 rounded-lg text-xs font-bold border border-slate-200 shadow-sm">
                Total: {{ $menus->total() }} Item
            </span>
        </div>

        {{-- Scrollable Table --}}
        <div class="overflow-x-auto overflow-y-auto max-h-[600px] custom-scrollbar">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50 sticky top-0 z-10 shadow-sm">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider w-24">Foto</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Nama & Deskripsi</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Harga</th>
                        {{-- Kolom Gerai (Hanya Admin) --}}
                        @if(auth()->user()->role === 'admin')
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Gerai</th>
                        @endif
                        <th class="px-6 py-4 text-right text-xs font-bold text-slate-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 bg-white">
                    @forelse($menus as $item)
                        <tr class="group hover:bg-slate-50/80 transition-colors duration-200">
                            {{-- Gambar --}}
                            <td class="px-6 py-4 whitespace-nowrap align-top">
                                <div class="w-16 h-16 rounded-xl overflow-hidden shadow-sm border border-slate-100 group-hover:shadow-md transition-all bg-slate-100">
                                    <img src="{{ str_contains($item->image, 'images/menus/') ? asset($item->image) : asset('storage/' . $item->image) }}"
                                         class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500"
                                         onerror="this.src='https://placehold.co/100x100?text=No+Image'"
                                         alt="{{ $item->name }}">
                                </div>
                            </td>

                            {{-- Nama & Deskripsi --}}
                            <td class="px-6 py-4 align-top">
                                <p class="text-sm font-bold text-slate-800 group-hover:text-indigo-600 transition-colors">{{ $item->name }}</p>
                                <p class="text-xs text-slate-500 line-clamp-2 mt-1 leading-relaxed max-w-xs">{{ $item->description }}</p>
                            </td>

                            {{-- Kategori --}}
                            <td class="px-6 py-4 whitespace-nowrap align-top">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                    {{ $item->category->name ?? 'Tanpa Kategori' }}
                                </span>
                            </td>

                            {{-- Harga --}}
                            <td class="px-6 py-4 whitespace-nowrap align-top">
                                <span class="text-sm font-bold text-slate-700 font-mono">
                                    Rp{{ number_format($item->price, 0, ',', '.') }}
                                </span>
                            </td>

                            {{-- Gerai (Khusus Admin) --}}
                            @if(auth()->user()->role === 'admin')
                            <td class="px-6 py-4 whitespace-nowrap align-top">
                                @if($item->store)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        <i class="fas fa-store mr-1"></i> {{ $item->store->name }}
                                    </span>
                                @else
                                    <span class="text-[10px] text-gray-400 italic">Global</span>
                                @endif
                            </td>
                            @endif

                            {{-- Aksi --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium align-top">
                                <div class="flex justify-end gap-2 opacity-60 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('admin.menus.edit', $item->id) }}"
                                       class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-600 bg-white border border-slate-200 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition-all shadow-sm"
                                       title="Edit">
                                        <i class="fas fa-pencil-alt text-xs"></i>
                                    </a>

                                    <form action="{{ route('admin.menus.destroy', $item->id) }}" method="POST" class="inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete(this, '{{ $item->name }}')"
                                                class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-600 bg-white border border-slate-200 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition-all shadow-sm"
                                                title="Hapus">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->role === 'admin' ? 6 : 5 }}" class="text-center py-16">
                                <div class="inline-block p-4 rounded-full bg-slate-50 text-slate-300 mb-4">
                                    <i class="fas fa-hamburger text-4xl"></i>
                                </div>
                                <h3 class="text-slate-800 font-bold text-lg">Tidak ada menu ditemukan</h3>
                                <p class="text-slate-500 text-sm mt-2 max-w-sm mx-auto">
                                    @if(request('category_id'))
                                        Tidak ada menu di kategori ini. Coba reset filter.
                                    @else
                                        Mulai dengan menambahkan hidangan baru ke daftar menu Anda.
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
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $menus->links() }}
            </div>
        @endif
    </div>

    {{-- Script & Styles Tambahan --}}
    @push('scripts')
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: #94a3b8; }
    </style>
    <script>
        function confirmDelete(button, name) {
            Swal.fire({
                title: 'Hapus Menu?',
                text: `Menu "${name}" akan dihapus permanen!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444', // Red-500
                cancelButtonColor: '#64748b',  // Slate-500
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                background: '#ffffff',
                color: '#1e293b'
            }).then((result) => {
                if (result.isConfirmed) {
                    button.closest('form').submit();
                }
            })
        }
    </script>
    @endpush

@endsection
