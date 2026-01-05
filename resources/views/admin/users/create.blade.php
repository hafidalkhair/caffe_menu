@extends('layouts.admin')

@section('title', 'Tambah Pegawai')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="max-w-2xl mx-auto">
        {{-- Tombol Kembali --}}
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-indigo-600 mb-6 transition-colors group">
            <i class="fas fa-arrow-left mr-2 transform group-hover:-translate-x-1 transition-transform"></i> Kembali ke Daftar Pegawai
        </a>

        <div class="bg-white shadow-xl shadow-indigo-100/50 rounded-2xl overflow-hidden border border-gray-100">
            {{-- Header Card --}}
            <div class="px-8 py-6 bg-gradient-to-r from-indigo-50 to-white border-b border-indigo-100">
                <h2 class="text-xl font-bold text-indigo-900">Registrasi Pegawai Baru</h2>
                <p class="text-sm text-indigo-600/80 mt-1">Buat akun untuk staf agar bisa mengakses sistem.</p>
            </div>

            <form action="{{ route('admin.users.store') }}" method="POST" class="p-8 space-y-6">
                @csrf

                {{-- Nama Lengkap --}}
                <div class="space-y-1">
                    <label for="name" class="block text-sm font-bold text-gray-700">Nama Lengkap</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400 text-sm"></i>
                        </div>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-xl py-3"
                               placeholder="Contoh: Chef Juna">
                    </div>
                    @error('name') <p class="text-red-500 text-xs mt-1 font-medium"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p> @enderror
                </div>

                {{-- Email --}}
                <div class="space-y-1">
                    <label for="email" class="block text-sm font-bold text-gray-700">Alamat Email</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400 text-sm"></i>
                        </div>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                               class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-xl py-3"
                               placeholder="nama@cafe.com">
                    </div>
                    @error('email') <p class="text-red-500 text-xs mt-1 font-medium"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p> @enderror
                </div>

                {{-- AREA PILIHAN ROLE & GERAI (ALPINE JS) --}}
                {{-- Kita set default 'role' ke 'admin' atau sesuai old input jika validasi gagal --}}
                <div x-data="{ role: '{{ old('role', 'admin') }}' }" class="space-y-4 bg-gray-50 p-5 rounded-2xl border border-gray-200">

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-3">Pilih Posisi / Jabatan</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Pilihan Admin --}}
                            <label class="cursor-pointer">
                                <input type="radio" name="role" value="admin" x-model="role" class="peer sr-only">
                                <div class="relative flex items-start p-4 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 peer-checked:border-indigo-500 peer-checked:ring-1 peer-checked:ring-indigo-500 peer-checked:bg-indigo-50 transition-all">
                                    <div class="flex-shrink-0 mt-0.5">
                                        <div class="h-10 w-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600">
                                            <i class="fas fa-user-shield text-lg"></i>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <span class="block text-sm font-bold text-gray-900">Admin / Kasir</span>
                                        <span class="block text-xs text-gray-500 mt-1">Akses penuh ke semua gerai & laporan.</span>
                                    </div>
                                    <div class="absolute top-4 right-4 text-indigo-600 opacity-0 peer-checked:opacity-100">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                            </label>

                            {{-- Pilihan Dapur --}}
                            <label class="cursor-pointer">
                                <input type="radio" name="role" value="dapur" x-model="role" class="peer sr-only">
                                <div class="relative flex items-start p-4 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 peer-checked:border-orange-500 peer-checked:ring-1 peer-checked:ring-orange-500 peer-checked:bg-orange-50 transition-all">
                                    <div class="flex-shrink-0 mt-0.5">
                                        <div class="h-10 w-10 bg-orange-100 rounded-full flex items-center justify-center text-orange-600">
                                            <i class="fas fa-utensils text-lg"></i>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <span class="block text-sm font-bold text-gray-900">Kitchen / Dapur</span>
                                        <span class="block text-xs text-gray-500 mt-1">Hanya akses pesanan & menu gerai.</span>
                                    </div>
                                    <div class="absolute top-4 right-4 text-orange-600 opacity-0 peer-checked:opacity-100">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- DROPDOWN GERAI (Hanya muncul jika Role = Dapur) --}}
                    <div x-show="role === 'dapur'"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         class="pt-2 border-t border-gray-200">

                        <label for="store_id" class="block text-sm font-bold text-gray-700 mb-2">Pilih Gerai / Tenant <span class="text-red-500">*</span></label>

                        <div class="relative">
                            <select name="store_id" id="store_id"
                                    class="block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm rounded-xl">
                                <option value="">-- Silakan Pilih Gerai --</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}" {{ old('store_id') == $store->id ? 'selected' : '' }}>
                                        {{ $store->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-info-circle text-orange-500 mr-1"></i>
                            Pegawai ini hanya akan melihat pesanan yang masuk ke gerai yang dipilih.
                        </p>
                        @error('store_id') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror

                        @if($stores->isEmpty())
                             <div class="mt-2 p-3 bg-red-50 text-red-700 text-sm rounded-lg border border-red-200">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Belum ada Gerai yang dibuat. Silakan buat Gerai terlebih dahulu di database (Tabel Stores).
                             </div>
                        @endif
                    </div>
                </div>

                {{-- Password Section --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                    <div class="space-y-1">
                        <label for="password" class="block text-sm font-bold text-gray-700">Password</label>
                        <div class="relative rounded-md shadow-sm">
                             <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400 text-sm"></i>
                            </div>
                            <input type="password" name="password" id="password" required
                                   class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-xl py-3"
                                   placeholder="Minimal 8 karakter">
                        </div>
                        @error('password') <p class="text-red-500 text-xs mt-1 font-medium"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="password_confirmation" class="block text-sm font-bold text-gray-700">Konfirmasi Password</label>
                        <div class="relative rounded-md shadow-sm">
                             <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400 text-sm"></i>
                            </div>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                   class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-xl py-3"
                                   placeholder="Ulangi password">
                        </div>
                    </div>
                </div>

                {{-- Tombol Submit --}}
                <div class="pt-6 flex items-center justify-end border-t border-gray-100">
                    <button type="submit" class="w-full md:w-auto px-8 py-3 bg-indigo-600 text-white font-bold rounded-xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:shadow-indigo-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:-translate-y-0.5">
                        <i class="fas fa-save mr-2"></i> Simpan Pegawai
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
