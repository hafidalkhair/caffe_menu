<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Category;
use App\Models\Store; // PENTING: Import Model Store
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller
{
    /**
     * Tampilkan daftar menu dengan Filter dan Pagination.
     */
    public function index(Request $request)
    {
        // 1. Ambil semua kategori untuk isi Dropdown Filter di View
        $categories = Category::all();

        // 2. Mulai Query Menu
        // Load category dan store (agar kita bisa tampilkan nama gerai di tabel jika perlu)
        $query = Menu::with(['category', 'store']);

        // --- LOGIKA MULTI-TENANT ---
        // Jika user adalah Dapur, paksa filter hanya menu milik gerai dia
        if (Auth::user()->role === 'dapur') {
            $query->where('store_id', Auth::user()->store_id);
        }
        // ---------------------------

        // 3. Logika Filter Kategori
        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        // 4. Ambil data dengan Pagination & urutkan terbaru
        $menus = $query->latest()->paginate(10);

        $menus->appends($request->all());

        return view('admin.menus.index', compact('menus', 'categories'));
    }

    /**
     * Tampilkan form untuk membuat menu baru.
     */
    public function create()
    {
        $categories = Category::all();

        // --- LOGIKA MULTI-TENANT ---
        // Admin butuh data semua toko untuk dropdown pilihan gerai
        $stores = [];
        if (Auth::user()->role === 'admin') {
            $stores = Store::all();
        }
        // ---------------------------

        return view('admin.menus.create', compact('categories', 'stores'));
    }

    /**
     * Simpan menu baru ke database.
     */
    public function store(Request $request)
    {
        // 1. Bersihkan Harga (Hapus titik format rupiah)
        if ($request->has('price')) {
            $cleanPrice = str_replace('.', '', $request->price);
            $request->merge(['price' => $cleanPrice]);
        }

        // 2. Validasi
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // Jika Admin, store_id wajib dipilih.
            'store_id'    => Auth::user()->role === 'admin' ? 'required|exists:stores,id' : 'nullable',
        ]);

        // 3. Upload Gambar
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('menu_images', 'public');
        }

        // 4. Tentukan Store ID
        $storeId = null;
        if (Auth::user()->role === 'dapur') {
            // Jika Dapur, otomatis pakai ID gerai akun tersebut
            $storeId = Auth::user()->store_id;
        } else {
            // Jika Admin, pakai inputan dari form
            $storeId = $request->store_id;
        }

        // 5. Simpan Data
        Menu::create([
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'category_id' => $request->category_id,
            'image'       => $imagePath,
            'store_id'    => $storeId,
        ]);

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu berhasil ditambahkan!');
    }

    /**
     * Tampilkan form untuk mengedit menu.
     */
    public function edit(Menu $menu)
    {
        // Keamanan: Cegah Dapur mengedit menu gerai lain (via URL langsung)
        if (Auth::user()->role === 'dapur' && $menu->store_id !== Auth::user()->store_id) {
            abort(403, 'Anda tidak memiliki akses ke menu ini.');
        }

        $categories = Category::all();

        // --- PERBAIKAN: Kirim data Stores jika Admin ---
        $stores = [];
        if (Auth::user()->role === 'admin') {
            $stores = Store::all();
        }

        return view('admin.menus.edit', compact('menu', 'categories', 'stores'));
    }

    /**
     * Perbarui menu di database.
     */
    public function update(Request $request, Menu $menu)
    {
        // Keamanan: Cegah Dapur update menu gerai lain
        if (Auth::user()->role === 'dapur' && $menu->store_id !== Auth::user()->store_id) {
            abort(403);
        }

        // 1. Bersihkan Harga
        if ($request->has('price')) {
            $cleanPrice = str_replace('.', '', $request->price);
            $request->merge(['price' => $cleanPrice]);
        }

        // 2. Validasi
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // Validasi store_id hanya untuk Admin
            'store_id'    => Auth::user()->role === 'admin' ? 'required|exists:stores,id' : 'nullable',
        ]);

        $imagePath = $menu->image;

        if ($request->hasFile('image')) {
            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }
            $imagePath = $request->file('image')->store('menu_images', 'public');
        }

        // 3. Siapkan Data Update
        $dataToUpdate = [
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'category_id' => $request->category_id,
            'image'       => $imagePath,
        ];

        // --- PERBAIKAN: Update Store ID jika Admin ---
        if (Auth::user()->role === 'admin') {
            $dataToUpdate['store_id'] = $request->store_id;
        }
        // ---------------------------------------------

        $menu->update($dataToUpdate);

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu berhasil diperbarui!');
    }

    /**
     * Hapus menu dari database.
     */
    public function destroy(Menu $menu)
    {
        // Keamanan: Cegah Dapur hapus menu gerai lain
        if (Auth::user()->role === 'dapur' && $menu->store_id !== Auth::user()->store_id) {
            abort(403);
        }

        if ($menu->image) {
            Storage::disk('public')->delete($menu->image);
        }

        $menu->delete();

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu berhasil dihapus!');
    }
}
