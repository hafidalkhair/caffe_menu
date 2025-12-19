<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Tampilkan daftar kategori dan form input.
     */
     public function index()
    {
        $categories = Category::withCount('menus')->get();
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Tampilkan form untuk membuat kategori baru (Create View).
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Simpan kategori baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        Category::create($request->only(['name', 'description']));

        return redirect()->route('admin.categories.index')->with('success', 'Kategori baru berhasil ditambahkan!');
    }

    /**
     * Tampilkan form untuk mengedit kategori (Edit View).
     */
    public function edit(Category $category)
    {
        // Mengirim data kategori yang akan diedit ke view
        return view('admin.categories.edit', compact('category')); // Menggunakan view edit.blade.php
    }

    /**
     * Perbarui kategori yang sudah ada.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);

        $category->update($request->only(['name', 'description']));

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diperbarui!');
    }
    /**
     * Hapus kategori.
     */
    public function destroy(Category $category)
    {
        // Catatan: Relasi onDelete('set null') di migration menus akan mengatur category_id yang terkait menjadi NULL.
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dihapus!');
    }
}