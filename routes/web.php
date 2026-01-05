<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\MenuController as AdminMenuController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\StoreController; // Pastikan import ini ada
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Admin\UserController; // Pastikan import ini ada
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Models\Order;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| Web Routes (Customer / Public)
|--------------------------------------------------------------------------
*/

// Route untuk Daftar Menu (Halaman Depan)
Route::get('/', [MenuController::class, 'index'])->name('customer.menu.index');

// Grouping OrderController routes for customer interactions
Route::controller(OrderController::class)->group(function () {
    // Cart & Order Management
    Route::get('/cart', 'cart')->name('customer.cart');
    Route::post('/cart/add', 'addToCart')->name('customer.cart.add');
    Route::post('/cart/update', 'updateCart')->name('customer.cart.update');
    Route::post('/cart/remove', 'removeFromCart')->name('customer.cart.remove');

    // Checkout & Store
    Route::get('/checkout', 'checkout')->name('customer.checkout');
    Route::post('/order', 'store')->name('customer.order.store');

    // Payment & Confirmation Pages
    Route::get('/payment/cash/{order}', 'cashInstructions')->name('customer.payment.cash');
    Route::get('/order/confirmed/{order}', function (Order $order) {
        return view('customer.order-confirmed', compact('order'));
    })->name('customer.order.confirmed');
    Route::get('/order/{order}/cancel-online', 'cancelOnlineOrder')->name('customer.order.cancel-online');
    Route::get('/order/last', 'showLastOrder')->name('customer.order.last');

    // Halaman Payment Confirmation (Sukses)
    Route::get('/order/success/{order}', 'updateStatusSuccess')->name('customer.order.update-success');
    Route::post('/payment/online/{order}', 'onlineCheckout')->name('customer.payment.online');
    Route::get('/order/online-confirmed/{order}', 'onlineConfirmed')->name('customer.order.online-confirmed');

    // Route Notifikasi Midtrans
    Route::post('/midtrans-notification', 'handleMidtransNotification')->name('midtrans.notification');

    // Tracking & Utilities
    Route::get('/track', 'showTrackForm')->name('customer.track.index');
    Route::post('/track', 'trackOrder')->name('customer.track.order');
    Route::get('/order/clear-session', function () {
        Session::forget('pending_order_id');
        return redirect()->route('customer.menu.index');
    })->name('customer.order.clear-session');

    // Polling Status Pesanan
    Route::get('/order/status/{order}', 'checkOrderStatus')->name('customer.order.status.check');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Admin Panel)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // 1. Profile (Semua user boleh edit profil sendiri)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
    Route::delete('/profile/photo', [ProfileController::class, 'deletePhoto'])->name('profile.photo.delete');

    // 2. Redirect Dashboard (Helper)
    Route::get('/dashboard', function () {
        return redirect()->route('admin.dashboard');
    })->name('dashboard');

    // --- MULAI GRUP ADMIN ---
    Route::prefix('admin')
        ->name('admin.')
        ->group(function () {

            // =========================================================
            // GRUP 1: KHUSUS SUPER ADMIN
            // (Manajemen User, Gerai, Laporan Global, Meja)
            // =========================================================
            Route::middleware(['role:admin'])->group(function () {
                // Manajemen Master Data
                Route::resource('users', UserController::class);
                Route::resource('stores', StoreController::class);
                Route::resource('tables', TableController::class);

                // Laporan Keuangan
                Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
                Route::get('reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
                Route::get('reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');
            });

            // =========================================================
            // GRUP 2: SHARED AKSES (ADMIN & DAPUR)
            // (Dashboard, Pesanan, History, Menu)
            // =========================================================
            Route::middleware(['role:admin,dapur'])->group(function () {
                // Dashboard
                Route::get('/dashboard', [AdminOrderController::class, 'dashboard'])->name('dashboard');

                // --- ROUTE PESANAN (URUTAN SANGAT PENTING!) ---

                // 1. Route Spesifik (Harus di ATAS wildcard)
                // History dipindah ke sini agar Dapur bisa lihat riwayat gerai mereka
                Route::get('orders/history', [AdminOrderController::class, 'history'])->name('orders.history');

                Route::get('orders/check-new', [AdminOrderController::class, 'checkNewOrders'])->name('orders.checkNew');
                Route::get('notifications/details', [AdminOrderController::class, 'getNotificationDetails'])->name('notifications.details');

                // Print Struk (Kasir & Dapur bisa print)
                Route::get('/orders/{order}/print', [AdminOrderController::class, 'printStruk'])->name('order.print');

                // 2. Route Resource/Index
                Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');

                // 3. Route Wildcard (Harus paling BAWAH agar 'history' tidak dianggap ID)
                Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
                Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');

                // Manajemen Menu & Kategori
                Route::resource('menus', AdminMenuController::class);
                Route::resource('categories', CategoryController::class);
            });
        });
});

require __DIR__ . '/auth.php';
