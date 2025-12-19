@extends('layouts.admin')

@section('title', 'Tambah Kategori')

@section('content')

    {{-- HEADER SECTION --}}
    <div class="max-w-3xl mx-auto mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4 fade-in-up">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Tambah Kategori</h2>
            <p class="text-sm text-gray-500 mt-1">Buat kelompok menu baru untuk memudahkan pelanggan.</p>
        </div>

        <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50 hover:text-gray-900 font-medium transition-colors shadow-sm flex items-center gap-2">
            <i class="fas fa-arrow-left text-xs"></i> Kembali
        </a>
    </div>

    {{-- FORM CARD --}}
    <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 p-8 fade-in-up" style="animation-delay: 0.1s;">

        <div class="flex items-center gap-4 mb-8 pb-6 border-b border-gray-50">
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center shadow-sm">
                <i class="fas fa-plus text-xl"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-800">Formulir Kategori</h3>
                <p class="text-xs text-gray-500">Isi data di bawah ini dengan lengkap.</p>
            </div>
        </div>

        <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Input Nama Kategori --}}
            <div>
                <label for="name" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                    Nama Kategori <span class="text-red-500">*</span>
                </label>
                <div class="relative group">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                        <i class="fas fa-heading"></i>
                    </span>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Contoh: Makanan Berat, Minuman Dingin" required
                        class="pl-11 block w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white text-gray-800 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 sm:text-sm py-3 transition-all">
                </div>
                @error('name')
                    <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Input Deskripsi --}}
            <div>
                <label for="description" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                    Deskripsi (Opsional)
                </label>
                <div class="relative group">
                    <span class="absolute top-3 left-0 pl-4 flex items-start pointer-events-none text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                        <i class="fas fa-align-left mt-1"></i>
                    </span>
                    <textarea name="description" id="description" rows="4" placeholder="Jelaskan secara singkat tentang kategori ini..."
                        class="pl-11 block w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white text-gray-800 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 sm:text-sm py-3 transition-all">{{ old('description') }}</textarea>
                </div>
                <p class="text-xs text-gray-400 mt-2 text-right">Maksimal 255 karakter.</p>
                @error('description')
                    <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Action Buttons --}}
            <div class="pt-6 flex items-center justify-end gap-3 border-t border-gray-50">
                <button type="reset" class="px-6 py-2.5 rounded-xl text-gray-500 hover:text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                    Reset
                </button>
                <button type="submit" class="bg-indigo-600 text-white px-8 py-2.5 rounded-xl hover:bg-indigo-700 font-bold shadow-lg shadow-indigo-200 transition-all transform hover:-translate-y-0.5 flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    Simpan Data
                </button>
            </div>
        </form>
    </div>

@endsection
