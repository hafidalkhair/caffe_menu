<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    /**
     * Tampilkan daftar menu dengan Filter dan Pagination.
     */
    public function index(Request $request)
    {
        // 1. Ambil semua kategori untuk isi Dropdown Filter di View
        $categories = Category::all();

        // 2. Mulai Query Menu (Eager Load kategori biar ringan)
        $query = Menu::with('category');

        // 3. Logika Filter: Jika ada category_id di URL, filter query-nya
        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        // 4. Ambil data dengan Pagination (10 per halaman) & urutkan terbaru
        $menus = $query->latest()->paginate(10);

        // Penting: Append query string agar saat pindah halaman, filter tidak hilang
        $menus->appends($request->all());

        return view('admin.menus.index', compact('menus', 'categories'));
    }

    /**
     * Tampilkan form untuk membuat menu baru.
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.menus.create', compact('categories'));
    }

    /**
     * Simpan menu baru ke database.
     */
    public function store(Request $request)
    {
        // --- PERBAIKAN PENTING DI SINI ---
        // Hapus tanda titik (.) dari input harga agar bisa disimpan sebagai angka
        if ($request->has('price')) {
            $cleanPrice = str_replace('.', '', $request->price);
            $request->merge(['price' => $cleanPrice]);
        }
        // ---------------------------------

        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0', // Validasi aman karena titik sudah dihapus
            'category_id' => 'nullable|exists:categories,id',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('menu_images', 'public');
        }

        Menu::create([
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price, // Menggunakan harga yang sudah bersih
            'category_id' => $request->category_id,
            'image'       => $imagePath,
        ]);

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu berhasil ditambahkan!');
    }

    /**
     * Tampilkan form untuk mengedit menu.
     */
    public function edit(Menu $menu)
    {
        $categories = Category::all();
        return view('admin.menus.edit', compact('menu', 'categories'));
    }

    /**
     * Perbarui menu di database.
     */
    public function update(Request $request, Menu $menu)
    {
        // --- PERBAIKAN PENTING DI SINI JUGA ---
        // Hapus tanda titik (.) sebelum validasi update
        if ($request->has('price')) {
            $cleanPrice = str_replace('.', '', $request->price);
            $request->merge(['price' => $cleanPrice]);
        }
        // --------------------------------------

        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = $menu->image; // Default pakai gambar lama

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }
            // Upload gambar baru
            $imagePath = $request->file('image')->store('menu_images', 'public');
        }

        $menu->update([
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'category_id' => $request->category_id,
            'image'       => $imagePath,
        ]);

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu berhasil diperbarui!');
    }

    /**
     * Hapus menu dari database.
     */
    public function destroy(Menu $menu)
    {
        // Hapus gambar dari storage jika ada
        if ($menu->image) {
            Storage::disk('public')->delete($menu->image);
        }

        $menu->delete();

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu berhasil dihapus!');
    }
}
