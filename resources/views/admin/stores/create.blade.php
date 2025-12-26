@extends('layouts.admin')

@section('title', 'Tambah Gerai')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-900">Tambah Gerai Baru</h2>
        </div>
        <form action="{{ route('admin.stores.store') }}" method="POST" class="p-6">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Gerai</label>
                <input type="text" name="name" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Contoh: Gerai Somay" required>
            </div>
            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.stores.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700">Batal</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
