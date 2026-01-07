<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Category;
use App\Models\OrderItem; // BARU: Diperlukan untuk menghitung Best Seller
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon; // BARU: Diperlukan untuk filter tanggal

class MenuController extends Controller
{
    /**
     * Tampilkan daftar menu untuk pelanggan, dikelompokkan berdasarkan kategori,
     * dan menyertakan daftar Best Seller.
     */
    public function index()
    {
        // --- 1. Logika Best Seller (30 Hari Terakhir) ---
        $startDate = Carbon::now()->subDays(30);

        $bestSellers = Menu::select('menus.*')
            // Join dengan tabel order_items
            ->join('order_items', 'menus.id', '=', 'order_items.menu_id')
            // Join dengan tabel orders untuk filter status 'completed' dan tanggal
            ->join('orders', 'order_items.order_id', '=', 'orders.id')

            ->where('orders.status', 'completed') // HANYA hitung pesanan yang berhasil
            ->where('orders.created_at', '>=', $startDate)

            // Menjumlahkan kuantitas dari order_items dan memilih kolom menu
            ->selectRaw('SUM(order_items.quantity) as total_sold')

            // Grouping: Harus menyertakan semua kolom non-aggregate
            ->groupBy('menus.id', 'menus.name', 'menus.description', 'menus.price', 'menus.image', 'menus.category_id', 'menus.store_id','menus.created_at', 'menus.updated_at')

            ->orderByDesc('total_sold')
            ->limit(10) // Ambil 10 menu teratas
            ->with('category')
            ->get();


        // --- 2. Logika Grouped Menus (Tampilan Kategori) ---
        // PENTING: Eager loading 'with(category)' sudah benar.
        $menus = Menu::with('category')->get();

        $groupedMenus = $menus->groupBy(function($menu) {
            return $menu->category->name ?? 'Lain-lain';
        });

        // 3. Kirim kedua variabel ke view
        return view('customer.menu', compact('groupedMenus', 'bestSellers'));
    }
}
