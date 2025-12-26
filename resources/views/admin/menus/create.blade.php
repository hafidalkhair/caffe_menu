@extends('layouts.admin')

@section('title', 'Tambah Menu')

@section('content')

    {{-- HEADER SECTION --}}
    <div class="max-w-4xl mx-auto mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4 fade-in-up">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Tambah Menu Baru</h2>
            <p class="text-sm text-gray-500 mt-1">Tambahkan sajian lezat baru ke dalam daftar menu cafe.</p>
        </div>

        <a href="{{ route('admin.menus.index') }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50 hover:text-gray-900 font-medium transition-colors shadow-sm flex items-center gap-2">
            <i class="fas fa-arrow-left text-xs"></i> Kembali
        </a>
    </div>

    {{-- FORM CARD --}}
    <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 p-8 fade-in-up" style="animation-delay: 0.1s;">

        <div class="flex items-center gap-4 mb-8 pb-6 border-b border-gray-50">
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center shadow-sm">
                <i class="fas fa-utensils text-xl"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-800">Informasi Produk</h3>
                <p class="text-xs text-gray-500">Lengkapi detail menu, harga, dan gambar.</p>
            </div>
        </div>

        {{-- Pastikan enctype ada untuk upload file --}}
        <form action="{{ route('admin.menus.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- 1. Nama Menu --}}
                <div class="md:col-span-2">
                    <label for="name" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Nama Menu <span class="text-red-500">*</span></label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                            <i class="fas fa-heading"></i>
                        </span>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Contoh: Nasi Goreng Spesial" required
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
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                        <input type="text" name="price" id="price" value="{{ old('price') }}" placeholder="0" required
                            class="pl-11 block w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white text-gray-800 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 sm:text-sm py-3 transition-all"
                            onkeyup="formatRupiah(this)">
                    </div>
                    @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- 4. Pilihan Gerai (KHUSUS ADMIN) --}}
                {{-- Logika: Jika user adalah admin, tampilkan dropdown toko --}}
                @if(auth()->user()->role === 'admin')
                <div class="md:col-span-2 bg-indigo-50 p-4 rounded-xl border border-indigo-100">
                    <label for="store_id" class="block text-xs font-bold text-indigo-800 uppercase tracking-wider mb-2">Pilih Gerai / Tenant <span class="text-red-500">*</span></label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-indigo-400">
                            <i class="fas fa-store"></i>
                        </span>
                        <select name="store_id" id="store_id" required class="pl-11 block w-full rounded-xl border-indigo-200 bg-white focus:bg-white text-gray-800 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 sm:text-sm py-3 transition-all appearance-none cursor-pointer">
                            <option value="">-- Pilih Pemilik Menu --</option>
                            @foreach($stores as $store)
                                <option value="{{ $store->id }}" {{ old('store_id') == $store->id ? 'selected' : '' }}>
                                    {{ $store->name }}
                                </option>
                            @endforeach
                        </select>
                        <span class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-indigo-400">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </span>
                    </div>
                    <p class="text-[10px] text-indigo-600 mt-2">*Menu ini hanya akan muncul untuk akun Dapur di gerai yang dipilih.</p>
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
                            class="pl-11 block w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white text-gray-800 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 sm:text-sm py-3 transition-all">{{ old('description') }}</textarea>
                    </div>
                </div>

                {{-- 6. Upload Gambar dengan PREVIEW --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Foto Menu</label>

                    <div class="flex flex-col md:flex-row gap-6 items-start">
                        {{-- Input Area --}}
                        <div class="flex-1 w-full">
                            <label for="image" class="flex flex-col items-center justify-center w-full h-40 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-all group relative overflow-hidden">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 group-hover:text-indigo-500 mb-3 transition-colors scale-100 group-hover:scale-110 duration-200"></i>
                                    <p class="text-sm text-gray-500"><span class="font-semibold text-indigo-600">Klik upload</span> atau drag & drop</p>
                                    <p class="text-xs text-gray-400 mt-1">PNG, JPG, JPEG (Max. 2MB)</p>
                                </div>
                                <input id="image" name="image" type="file" class="hidden" accept="image/*" onchange="previewImage(event)" />
                            </label>
                        </div>

                        {{-- Preview Area --}}
                        <div class="flex-shrink-0">
                            <div class="w-40 h-40 rounded-xl border border-gray-200 bg-gray-50 flex items-center justify-center overflow-hidden relative shadow-sm">
                                <img id="image-preview" src="#" alt="Preview" class="w-full h-full object-cover hidden">
                                <div id="placeholder-preview" class="text-center p-4">
                                    <i class="fas fa-image text-gray-300 text-4xl mb-2"></i>
                                    <p class="text-[10px] text-gray-400">Preview gambar akan muncul di sini</p>
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
                    Simpan Menu
                </button>
            </div>
        </form>
    </div>

@endsection

@push('scripts')
<script>
    // Format Rupiah (Otomatis Tambah Titik)
    function formatRupiah(input) {
        let value = input.value.replace(/\D/g, '');
        if (value.length > 0) {
            value = new Intl.NumberFormat('id-ID').format(value);
        }
        input.value = value;
    }

    // Preview Gambar
    function previewImage(event) {
        const reader = new FileReader();
        const imageField = document.getElementById("image-preview");
        const placeholder = document.getElementById("placeholder-preview");

        reader.onload = function() {
            if (reader.readyState == 2) {
                imageField.src = reader.result;
                imageField.classList.remove("hidden");
                placeholder.classList.add("hidden");
            }
        }

        if(event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }
</script>
@endpush
