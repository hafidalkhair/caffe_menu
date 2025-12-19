@extends('layouts.admin')

@section('title', 'Kelola Profil')

@section('content')
    <div class="space-y-6">
        <header>
            <h2 class="text-2xl font-semibold text-gray-800">
                <i class="fas fa-user-cog mr-2 text-purple-600"></i> Pengaturan Profil
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                Perbarui informasi akun, foto profil, dan alamat email Anda.
            </p>
        </header>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- 1. FORM UPDATE PROFILE INFORMATION (Nama & Email) --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg card-hover">
                <section>
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">
                            Informasi Profil
                        </h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Perbarui informasi akun dan alamat email Anda.
                        </p>
                    </header>
                
                    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                        @csrf
                    </form>
                
                    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
                        @csrf
                        @method('patch')
                
                        <div>
                            {{-- Input Nama --}}
                            <label for="name" class="block font-medium text-sm text-gray-700">Nama</label>
                            {{-- Menggunakan kelas fokus ungu yang seragam --}}
                            <input id="name" name="name" type="text" class="mt-1 block w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm" 
                                    value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
                            @error('name')
                                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                
                        <div>
                            {{-- Input Email --}}
                            <label for="email" class="block font-medium text-sm text-gray-700">Email</label>
                            <input id="email" name="email" type="email" class="mt-1 block w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm" 
                                    value="{{ old('email', $user->email) }}" required autocomplete="username" />
                            @error('email')
                                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                            @enderror
                
                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                <div class="mt-2">
                                    <p class="text-sm text-gray-800">
                                        Alamat email Anda belum terverifikasi.
                                        <button form="send-verification" class="text-sm text-purple-600 hover:text-purple-900 underline">
                                            Klik di sini untuk mengirim ulang email verifikasi.
                                        </button>
                                    </p>
                
                                    @if (session('status') === 'verification-link-sent')
                                        <p class="mt-2 font-medium text-sm text-green-600">
                                            Tautan verifikasi baru telah dikirim ke alamat email Anda.
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </div>
                
                        <div class="flex items-center gap-4">
                            {{-- Tombol Save --}}
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fas fa-save mr-2"></i> Simpan
                            </button>
                
                            @if (session('status') === 'profile-updated')
                                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-sm text-gray-600">
                                    Berhasil disimpan.
                                </p>
                            @endif
                        </div>
                    </form>
                </section>
            </div>

            {{-- 2. FORM UPDATE PROFILE PHOTO (Integrasi Langsung) --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg card-hover">
                <section>
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">
                            🖼️ Foto Profil
                        </h2>

                        <p class="mt-1 text-sm text-gray-600">
                            Perbarui atau hapus foto profil Anda.
                        </p>
                    </header>
                
                    <div class="mt-6 flex flex-col sm:flex-row items-start sm:items-center space-y-4 sm:space-y-0 sm:space-x-4">
                        
                        {{-- Kontainer Foto Profil Saat Ini / Pratinjau Baru --}}
                        <div class="shrink-0">
                            {{-- ID untuk Pratinjau JavaScript --}}
                            <img id="profile-photo-preview" class="h-20 w-20 object-cover rounded-full shadow-lg border-2 border-purple-300"
                                src="{{ Auth::user()->profile_photo_path 
                                    ? asset('storage/' . Auth::user()->profile_photo_path) 
                                    : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&color=7F9CF5&background=EBF4FF' }}"
                                alt="{{ Auth::user()->name ?? 'Admin' }} Profile Photo">
                        </div>
                
                        <div class="flex flex-col space-y-3">
                            {{-- PASTIKAN METHOD HANYA POST --}}
                            <form method="POST" action="{{ route('profile.photo.update') }}" enctype="multipart/form-data" class="flex items-center space-x-3">
                                @csrf
                                
                                {{-- Input File --}}
                                <input type="file" name="photo" id="photo-upload" class="hidden" 
                                    {{-- Panggil fungsi pratinjau yang ada di layout admin --}}
                                    onchange="previewProfilePhoto(event); document.getElementById('upload-submit-btn').classList.remove('hidden')">
                    
                                {{-- Tombol Custom untuk Memilih File --}}
                                <label for="photo-upload" class="cursor-pointer inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition duration-150">
                                    <i class="fas fa-upload mr-2"></i> Pilih Foto Baru
                                </label>
                    
                                {{-- Tombol Submit --}}
                                <button type="submit" id="upload-submit-btn" class="hidden inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <i class="fas fa-save mr-2"></i> Simpan
                                </button>
                            </form>
                            
                            {{-- Nama File yang Dipilih --}}
                            <span id="file-name-display-2" class="text-xs text-gray-500 truncate max-w-[150px] sm:ml-4">
                                Belum ada file dipilih
                            </span>
                        </div>

                        {{-- Form Hapus Foto (Menggunakan DELETE, sudah benar) --}}
                        @if (Auth::user()->profile_photo_path)
                            <form method="POST" action="{{ route('profile.photo.delete') }}" class="sm:ml-auto">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-150"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus foto profil?');">
                                    <i class="fas fa-trash-alt mr-2"></i> Hapus
                                </button>
                            </form>
                        @endif
                
                    </div>
                    
                    {{-- Notifikasi Error/Sukses (untuk Foto Profil) --}}
                    @if (session('status') === 'profile-photo-updated')
                        <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-sm text-green-600 mt-2">
                            Foto profil berhasil diperbarui.
                        </p>
                    @endif
                    @if ($errors->has('photo'))
                        <p class="text-sm text-red-600 mt-2">{{ $errors->first('photo') }}</p>
                    @endif
                </section>
            </div>
        </div>

        ---
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- 3. FORM UPDATE PASSWORD --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg card-hover">
                <section>
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">
                            Perbarui Kata Sandi
                        </h2>
                
                        <p class="mt-1 text-sm text-gray-600">
                            Pastikan akun Anda menggunakan kata sandi yang panjang dan acak agar tetap aman.
                        </p>
                    </header>
                
                    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
                        @csrf
                        @method('put')
                
                        <div>
                            {{-- Input Kata Sandi Saat Ini --}}
                            <label for="current_password" class="block font-medium text-sm text-gray-700">Kata Sandi Saat Ini</label>
                            <input id="current_password" name="current_password" type="password" class="mt-1 block w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm" autocomplete="current-password" />
                            @error('current_password')
                                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                
                        <div>
                            {{-- Input Kata Sandi Baru --}}
                            <label for="password" class="block font-medium text-sm text-gray-700">Kata Sandi Baru</label>
                            <input id="password" name="password" type="password" class="mt-1 block w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm" autocomplete="new-password" />
                            @error('password')
                                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                
                        <div>
                            {{-- Input Konfirmasi Kata Sandi --}}
                            <label for="password_confirmation" class="block font-medium text-sm text-gray-700">Konfirmasi Kata Sandi</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm" autocomplete="new-password" />
                            @error('password_confirmation')
                                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                
                        <div class="flex items-center gap-4">
                            {{-- Tombol Save --}}
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fas fa-save mr-2"></i> Simpan
                            </button>
                
                            @if (session('status') === 'password-updated')
                                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-sm text-gray-600">
                                    Berhasil disimpan.
                                </p>
                            @endif
                        </div>
                    </form>
                </section>
            </div>

            {{-- 4. FORM DELETE USER --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg card-hover">
                <section class="space-y-6">
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">
                            Hapus Akun
                        </h2>
                
                        <p class="mt-1 text-sm text-gray-600">
                            Setelah akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara permanen. Sebelum menghapus akun Anda, harap unduh data atau informasi apa pun yang ingin Anda simpan.
                        </p>
                    </header>
                
                    {{-- Tombol untuk Membuka Modal Hapus Akun --}}
                    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Hapus Akun
                    </button>
                
                    {{-- Modal Konfirmasi Hapus Akun (Membutuhkan Alpine.js) --}}
                    <div x-data="{ open: false }" x-init="
                        $watch('open', value => {
                            if (value) {
                                document.body.classList.add('overflow-hidden');
                            } else {
                                document.body.classList.remove('overflow-hidden');
                            }
                        });"
                        @open-modal.window="open = $event.detail === 'confirm-user-deletion'" 
                        x-show="open" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <div class="fixed inset-0 transition-opacity" aria-hidden="true" x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                            </div>
                
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                            <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                                @click.away="open = false">
                                <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
                                    @csrf
                                    @method('delete')
                
                                    <h2 class="text-lg font-medium text-gray-900">
                                        Apakah Anda yakin ingin menghapus akun Anda?
                                    </h2>
                
                                    <p class="mt-1 text-sm text-gray-600">
                                        Masukkan kata sandi Anda untuk mengonfirmasi bahwa Anda ingin menghapus akun Anda secara permanen.
                                    </p>
                
                                    <div class="mt-6">
                                        <label for="password_delete" class="block font-medium text-sm text-gray-700">Kata Sandi</label>
                                        <input id="password_delete" name="password" type="password" class="mt-1 block w-full border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-md shadow-sm" placeholder="Kata Sandi Anda" />
                                        @error('password', 'userDeletion')
                                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>
                
                                    <div class="mt-6 flex justify-end">
                                        <button type="button" @click="open = false" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                            Batal
                                        </button>
                
                                        <button type="submit" class="ml-3 inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            Hapus Akun
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            
        </div>
        
    </div>
@endsection