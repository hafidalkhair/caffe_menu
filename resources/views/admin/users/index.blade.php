@extends('layouts.admin')

@section('title', 'Kelola Pegawai')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header & Tombol Tambah --}}
    <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Daftar Pegawai</h1>
            <p class="mt-2 text-sm text-gray-700">Kelola akun untuk Kasir (Admin) dan Koki per Gerai.</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:-translate-y-0.5">
                <i class="fas fa-user-plus mr-2"></i> Tambah Pegawai
            </a>
        </div>
    </div>

    {{-- Alert Success/Error (Dengan AlpineJS untuk Close) --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm flex justify-between items-center">
            <div class="flex">
                <div class="flex-shrink-0"><i class="fas fa-check-circle text-green-400"></i></div>
                <div class="ml-3"><p class="text-sm text-green-700 font-medium">{{ session('success') }}</p></div>
            </div>
            <button @click="show = false" class="text-green-400 hover:text-green-600"><i class="fas fa-times"></i></button>
        </div>
    @endif

    {{-- Tabel User --}}
    <div class="bg-white shadow-lg shadow-indigo-100/50 rounded-2xl overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama & Email</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Role (Posisi)</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Gerai / Tenant</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Bergabung</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse ($users as $user)
                    <tr class="hover:bg-indigo-50/30 transition-colors duration-200">
                        {{-- Kolom Nama --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-full object-cover border-2 border-indigo-100 shadow-sm"
                                         src="{{ $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random&color=fff' }}"
                                         alt="">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-bold text-gray-900">{{ $user->name }}</div>
                                    <div class="text-xs text-gray-500 font-medium">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>

                        {{-- Kolom Role --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->role === 'admin')
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-indigo-100 text-indigo-700 border border-indigo-200 shadow-sm">
                                    <i class="fas fa-user-shield mr-1.5 mt-0.5"></i> Admin
                                </span>
                            @elseif($user->role === 'dapur')
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-orange-100 text-orange-700 border border-orange-200 shadow-sm">
                                    <i class="fas fa-utensils mr-1.5 mt-0.5"></i> Dapur
                                </span>
                            @endif
                        </td>

                        {{-- Kolom Gerai (BARU) --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->role === 'dapur')
                                @if($user->store)
                                    <span class="text-sm font-bold text-gray-700 bg-gray-100 px-3 py-1 rounded-lg border border-gray-200">
                                        {{ $user->store->name }}
                                    </span>
                                @else
                                    <span class="text-xs text-red-500 italic">Belum pilih gerai</span>
                                @endif
                            @else
                                <span class="text-xs text-gray-400">Semua Akses (Pusat)</span>
                            @endif
                        </td>

                        {{-- Kolom Tanggal --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-medium">
                            {{ $user->created_at->format('d M Y') }}
                        </td>

                        {{-- Kolom Aksi --}}
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline-block"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $user->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors p-2 rounded-lg hover:bg-red-50" title="Hapus Akun">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-indigo-300 italic font-medium">Akun Anda</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                            <i class="fas fa-users text-4xl mb-3 text-indigo-200"></i>
                            <p>Belum ada pegawai lain yang terdaftar.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
