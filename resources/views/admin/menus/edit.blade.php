@extends('layouts.admin')

@section('title', 'Edit Menu')

@section('content')

    {{-- HEADER SECTION --}}
    <div class="max-w-4xl mx-auto mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4 fade-in-up">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Edit Menu</h2>
            <p class="text-sm text-gray-500 mt-1">Perbarui informasi dan harga menu.</p>
        </div>

        <a href="{{ route('admin.menus.index') }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50 hover:text-gray-900 font-medium transition-colors shadow-sm flex items-center gap-2">
            <i class="fas fa-arrow-left text-xs"></i> Kembali
        </a>
    </div>

    {{-- FORM CARD --}}
    <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 p-8 fade-in-up" style="animation-delay: 0.1s;">

        <div class="flex items-center gap-4 mb-8 pb-6 border-b border-gray-50">
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center shadow-sm">
                <i class="fas fa-edit text-xl"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-800">Edit: {{ $menu->name }}</h3>
                <p class="text-xs text-gray-500">ID Menu: #{{ $menu->id }}</p>
            </div>
        </div>

        <form action="{{ route('admin.menus.update', $menu->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- 1. Nama Menu --}}
                <div class="md:col-span-2">
                    <label for="name" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Nama Menu <span class="text-red-500">*</span></label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                            <i class="fas fa-heading"></i>
                        </span>
                        <input type="text" name="name" id="name" value="{{ old('name', $menu->name) }}" placeholder="Contoh: Nasi Goreng Spesial" required
                            class="pl-11 block w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white text-gray-800 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 sm:text-sm py-3 transition-all">
                    </div>
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- 2. Kategori --}}
                <div>
                    <label for="category_id" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Kategori <span class="text-red-500">*</span></label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                            <i class="fas fa-tags"></i>
                        </span>
                        <select name="category_id" id="category_id" class="pl-11 block w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white text-gray-800 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 sm:text-sm py-3 transition-all appearance-none cursor-pointer">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $menu->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <span class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </span>
                    </div>
                    @error('category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- 3. Harga --}}
                <div>
                    <label for="price" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Harga <span class="text-red-500">*</span></label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500 font-bold group-focus-within:text-indigo-500 transition-colors">
                            Rp
                        </span>
                        {{-- Format harga lama dengan number_format agar muncul titik (misal: 15.000) --}}
                        <input type="text" name="price" id="price"
                            value="{{ old('price', number_format($menu->price, 0, ',', '.')) }}"
                            placeholder="0" required
                            class="pl-11 block w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white text-gray-800 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 sm:text-sm py-3 transition-all"
                            onkeyup="formatRupiah(this)">
                    </div>
                    @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- 4. Pilihan Gerai (KHUSUS ADMIN) - PENTING UNTUK MULTI TENANT --}}
                @if(auth()->user()->role === 'admin')
                <div class="md:col-span-2 bg-indigo-50 p-4 rounded-xl border border-indigo-100">
                    <label for="store_id" class="block text-xs font-bold text-indigo-800 uppercase tracking-wider mb-2">Gerai Pemilik Menu <span class="text-red-500">*</span></label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-indigo-400">
                            <i class="fas fa-store"></i>
                        </span>
                        <select name="store_id" id="store_id" required class="pl-11 block w-full rounded-xl border-indigo-200 bg-white focus:bg-white text-gray-800 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 sm:text-sm py-3 transition-all appearance-none cursor-pointer">
                            <option value="">-- Pilih Gerai --</option>
                            {{-- Kita asumsikan $stores dikirim dari controller (MenuController@edit) --}}
                            @if(isset($stores))
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}" {{ old('store_id', $menu->store_id) == $store->id ? 'selected' : '' }}>
                                        {{ $store->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <span class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-indigo-400">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </span>
                    </div>
                    @error('store_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                @endif

                {{-- 5. Deskripsi --}}
                <div class="md:col-span-2">
                    <label for="description" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Deskripsi</label>
                    <div class="relative group">
                        <span class="absolute top-3 left-0 pl-4 flex items-start pointer-events-none text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                            <i class="fas fa-align-left mt-1"></i>
                        </span>
                        <textarea name="description" id="description" rows="3" placeholder="Jelaskan komposisi atau rasa menu ini..."
                            class="pl-11 block w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white text-gray-800 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 sm:text-sm py-3 transition-all">{{ old('description', $menu->description) }}</textarea>
                    </div>
                </div>

                {{-- 6. Upload Gambar (DENGAN PREVIEW LAMA & BARU) --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Foto Menu</label>

                    <div class="flex flex-col sm:flex-row gap-6">

                        {{-- Foto Saat Ini --}}
                        @if($menu->image)
                        <div class="flex-shrink-0">
                            <p class="text-[10px] text-gray-400 uppercase font-bold mb-1">Foto Saat Ini</p>
                            <div class="w-32 h-32 rounded-xl overflow-hidden border border-gray-200 shadow-sm relative">
                                <img src="{{ str_contains($menu->image, 'images/menus/') ? asset($menu->image) : asset('storage/' . $menu->image) }}"
                                     alt="Current" class="w-full h-full object-cover">
                            </div>
                        </div>
                        @endif

                        {{-- Area Upload & Preview Baru --}}
                        <div class="flex-grow">
                             <p class="text-[10px] text-gray-400 uppercase font-bold mb-1">Ganti Foto Baru</p>

                             <div class="flex flex-col md:flex-row gap-4">
                                <label for="image" class="flex flex-col items-center justify-center flex-1 h-32 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-all group">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 group-hover:text-indigo-500 mb-2 transition-colors"></i>
                                        <p class="text-xs text-gray-500 text-center"><span class="font-semibold text-indigo-600">Klik upload</span><br>atau drag & drop</p>
                                    </div>
                                    <input id="image" name="image" type="file" class="hidden" accept="image/*" onchange="previewImage(event)" />
                                </label>

                                {{-- Preview Gambar Baru --}}
                                <div class="w-32 h-32 rounded-xl border border-gray-200 bg-gray-50 flex items-center justify-center overflow-hidden relative shadow-sm" id="preview-container" style="display: none;">
                                    <img id="image-preview" src="#" alt="Preview" class="w-full h-full object-cover">
                                </div>
                             </div>
                        </div>

                    </div>
                    @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

            </div>

            {{-- Action Buttons --}}
            <div class="pt-6 flex items-center justify-end gap-3 border-t border-gray-50">
                <a href="{{ route('admin.menus.index') }}" class="px-6 py-2.5 rounded-xl text-gray-500 hover:text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                    Batal
                </a>
                <button type="submit" class="bg-indigo-600 text-white px-8 py-2.5 rounded-xl hover:bg-indigo-700 font-bold shadow-lg shadow-indigo-200 transition-all transform hover:-translate-y-0.5 flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

@endsection

@push('scripts')
<script>
    // Format Rupiah
    function formatRupiah(input) {
        let value = input.value.replace(/\D/g, '');
        if (value.length > 0) {
            value = new Intl.NumberFormat('id-ID').format(value);
        }
        input.value = value;
    }

    // Preview Image Baru
    function previewImage(event) {
        const reader = new FileReader();
        const imageField = document.getElementById("image-preview");
        const container = document.getElementById("preview-container");

        reader.onload = function() {
            if (reader.readyState == 2) {
                imageField.src = reader.result;
                container.style.display = 'block'; // Tampilkan container preview
            }
        }

        if(event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }
</script>
@endpush
