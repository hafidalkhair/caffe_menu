@extends('layouts.admin')

@section('title', 'Manajemen Meja')

@section('content')

    {{-- HEADER SECTION --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4 fade-in-up">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Daftar Meja</h2>
            <p class="text-sm text-gray-500 mt-1">Pantau dan kelola ketersediaan meja cafe.</p>
        </div>
    </div>

    {{-- ALERTS --}}
    @if (session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-3 fade-in-up shadow-sm">
        <i class="fas fa-check-circle text-xl"></i>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
    @endif

    @if ($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl fade-in-up shadow-sm">
        <div class="flex items-center gap-2 mb-1">
            <i class="fas fa-exclamation-triangle"></i>
            <span class="font-bold">Periksa Kembali Inputan:</span>
        </div>
        <ul class="list-disc list-inside text-sm ml-6">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- MAIN LAYOUT GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 fade-in-up" style="animation-delay: 0.1s;">

        {{-- KOLOM KIRI: FORMULIR (Static Height) --}}
        <div class="lg:col-span-1">
            @php
                $isEdit = isset($table);
                $route = $isEdit ? route('admin.tables.update', $table->id) : route('admin.tables.store');
                $method = $isEdit ? 'PUT' : 'POST';
                $title = $isEdit ? 'Edit Data Meja' : 'Tambah Meja';
                $desc = $isEdit ? 'Ubah detail meja yang dipilih.' : 'Buat meja baru untuk pelanggan.';
            @endphp

            <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6 h-full">

                {{-- Header Form --}}
                <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-100">
                    <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center shadow-sm">
                        <i class="fas {{ $isEdit ? 'fa-pen-to-square' : 'fa-plus' }} text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">{{ $title }}</h3>
                        <p class="text-xs text-gray-500 font-medium">{{ $desc }}</p>
                    </div>
                </div>

                <form action="{{ $route }}" method="POST" class="space-y-5">
                    @csrf
                    @method($method)

                    {{-- Input Kode Meja --}}
                    <div>
                        <label for="name" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Kode Meja</label>
                        <div class="relative group">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                                <i class="fas fa-tag"></i>
                            </span>
                            <input type="text" name="name" id="name" placeholder="Contoh: M01" value="{{ old('name', $table->name ?? '') }}" required
                                class="pl-10 block w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white text-gray-800 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 sm:text-sm py-3 transition-all">
                        </div>
                    </div>

                    {{-- Input Status (Hanya Edit) --}}
                    @if($isEdit)
                    <div>
                        <label for="status" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Status</label>
                        <div class="relative group">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                                <i class="fas fa-info-circle"></i>
                            </span>
                            <select name="status" id="status" required
                                class="pl-10 block w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white text-gray-800 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 sm:text-sm py-3 cursor-pointer transition-all appearance-none">
                                <option value="available" {{ old('status', $table->status ?? '') == 'available' ? 'selected' : '' }}>Available (Kosong)</option>
                                <option value="occupied" {{ old('status', $table->status ?? '') == 'occupied' ? 'selected' : '' }}>Occupied (Terisi)</option>
                            </select>
                            <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-chevron-down text-xs"></i>
                            </span>
                        </div>
                    </div>
                    @else
                        <input type="hidden" name="status" value="available">
                    @endif

                    {{-- Tombol Aksi --}}
                    <div class="pt-4 flex gap-3">
                        <button type="submit" class="flex-1 bg-indigo-600 text-white px-4 py-3 rounded-xl hover:bg-indigo-700 font-bold shadow-lg shadow-indigo-200 transition-all transform active:scale-95 flex items-center justify-center gap-2">
                            <i class="fas fa-save"></i>
                            {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Baru' }}
                        </button>

                        @if($isEdit)
                            <a href="{{ route('admin.tables.index') }}" class="px-4 py-3 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 font-semibold transition-colors">
                                Batal
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- KOLOM KANAN: TABEL DATA (Scrollable Internal) --}}
        <div class="lg:col-span-2 h-full">
            {{-- Container Card dengan Fixed Height --}}
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 flex flex-col h-[600px]"> {{-- Fixed Height disini --}}

                {{-- Header Tabel --}}
                <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/30 rounded-t-2xl">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Database Meja</h3>
                        <p class="text-xs text-gray-400">Geser untuk melihat data lainnya</p>
                    </div>
                    <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-lg text-xs font-bold border border-indigo-100">
                        Total: {{ $tables->total() }} Unit
                    </span>
                </div>

                @if($tables->count() > 0)

                {{-- Scrollable Table Area --}}
                <div class="flex-1 overflow-y-auto custom-scrollbar relative">
                    <table class="min-w-full divide-y divide-gray-100">
                        {{-- Sticky Header --}}
                        <thead class="bg-white sticky top-0 z-10 shadow-sm">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider bg-gray-50/80 backdrop-blur-sm">Info Meja</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-wider bg-gray-50/80 backdrop-blur-sm">Status</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-wider bg-gray-50/80 backdrop-blur-sm">Dibuat</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-wider bg-gray-50/80 backdrop-blur-sm">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 bg-white">
                            @foreach($tables as $item)
                                <tr class="group hover:bg-indigo-50/30 transition-colors {{ isset($table) && $table->id == $item->id ? 'bg-indigo-50 border-l-4 border-indigo-500' : '' }}">

                                    {{-- Kolom Info --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 flex-shrink-0 bg-gray-100 rounded-xl flex items-center justify-center text-gray-400 group-hover:bg-white group-hover:text-indigo-600 group-hover:shadow-md transition-all duration-300">
                                                <i class="fas fa-chair"></i>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-gray-800">{{ $item->name }}</div>
                                                <div class="text-[10px] text-gray-400 font-mono">ID: #{{ $item->id }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Kolom Status --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($item->status == 'available')
                                            <span class="px-3 py-1.5 inline-flex items-center text-xs font-bold rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
                                                Tersedia
                                            </span>
                                        @else
                                            <span class="px-3 py-1.5 inline-flex items-center text-xs font-bold rounded-lg bg-rose-50 text-rose-600 border border-rose-100">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-2"></span>
                                                Terisi
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Kolom Tanggal --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-xs font-medium text-gray-500">
                                        {{ $item->created_at ? $item->created_at->format('d/m/Y') : '-' }}
                                    </td>

                                    {{-- Kolom Aksi --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end gap-2 opacity-60 group-hover:opacity-100 transition-opacity">
                                            <a href="{{ route('admin.tables.edit', $item->id) }}"
                                               class="w-8 h-8 flex items-center justify-center rounded-lg text-indigo-600 bg-indigo-50 hover:bg-indigo-600 hover:text-white transition-all shadow-sm"
                                               title="Edit">
                                                <i class="fas fa-pen text-xs"></i>
                                            </a>

                                            <form action="{{ route('admin.tables.destroy', $item->id) }}" method="POST" class="inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="confirmDelete(this)"
                                                        class="w-8 h-8 flex items-center justify-center rounded-lg text-rose-500 bg-rose-50 hover:bg-rose-600 hover:text-white transition-all shadow-sm"
                                                        title="Hapus">
                                                    <i class="fas fa-trash text-xs"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Footer Pagination --}}
                <div class="p-4 border-t border-gray-100 bg-white rounded-b-2xl">
                    {{ $tables->links() }}
                </div>

                @else
                {{-- Empty State --}}
                <div class="flex-1 flex flex-col items-center justify-center p-12 text-center text-gray-400">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4 shadow-inner">
                        <i class="fas fa-chair text-3xl text-gray-300"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-700">Data Kosong</h3>
                    <p class="text-sm mt-1 max-w-xs">Belum ada meja yang terdaftar. Gunakan formulir di sebelah kiri untuk menambah data.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Styles Tambahan untuk Scrollbar Cantik --}}
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f9fafb;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #e5e7eb;
            border-radius: 20px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: #d1d5db;
        }
    </style>

    {{-- Script untuk Konfirmasi Delete --}}
    @push('scripts')
    <script>
        function confirmDelete(button) {
            Swal.fire({
                title: 'Hapus Meja?',
                text: "Data yang dihapus tidak dapat dikembalikan.",
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
