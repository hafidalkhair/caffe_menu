<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    // Tampilkan semua gerai
    public function index()
    {
        $stores = Store::latest()->get();
        return view('admin.stores.index', compact('stores'));
    }

    // Form tambah gerai
    public function create()
    {
        return view('admin.stores.create');
    }

    // Simpan gerai baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:stores,name',
        ]);

        Store::create($request->all());

        return redirect()->route('admin.stores.index')
            ->with('success', 'Gerai baru berhasil dibuat!');
    }

    // Form edit gerai
    public function edit(Store $store)
    {
        return view('admin.stores.edit', compact('store'));
    }

    // Update data gerai
    public function update(Request $request, Store $store)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:stores,name,' . $store->id,
        ]);

        $store->update($request->all());

        return redirect()->route('admin.stores.index')
            ->with('success', 'Nama gerai berhasil diperbarui!');
    }

    // Hapus gerai
    public function destroy(Store $store)
    {
        // Opsional: Cek apakah gerai masih punya menu/user sebelum hapus
        // Tapi karena di migration kita set cascade/null, hapus langsung aman.

        $store->delete();

        return redirect()->route('admin.stores.index')
            ->with('success', 'Gerai berhasil dihapus.');
    }
}
