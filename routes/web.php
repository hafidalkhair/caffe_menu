<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\MenuController as AdminMenuController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Models\Order;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;




// Route untuk Daftar Menu (Menggunakan MenuController)
Route::get('/', [MenuController::class, 'index'])->name('customer.menu.index');

// Grouping OrderController routes for customer interactions (Cart, Checkout, Payment)
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
    Route::get('/order/{order}/cancel-online', [App\Http\Controllers\OrderController::class, 'cancelOnlineOrder'])->name('customer.order.cancel-online');
    Route::get('/order/last', [OrderController::class, 'showLastOrder'])->name('customer.order.last');

    // Halaman Payment Confirmation (Sukses)
    Route::get('/order/success/{order}', 'updateStatusSuccess')->name('customer.order.update-success');
    Route::post('/payment/online/{order}', 'onlineCheckout')->name('customer.payment.online');
    Route::get('/order/online-confirmed/{order}', 'onlineConfirmed')->name('customer.order.online-confirmed');

    // Route Notifikasi Midtrans (Harus POST)
    Route::post('/midtrans-notification', [OrderController::class, 'handleMidtransNotification'])->name('midtrans.notification');

    // Tracking & Utilities
    Route::get('/track', 'showTrackForm')->name('customer.track.index');
    Route::post('/track', 'trackOrder')->name('customer.track.order');
    Route::get('/order/clear-session', function () {
        Session::forget('pending_order_id');
        return redirect()->route('customer.menu.index');
    })->name('customer.order.clear-session');


    // Route baru untuk memeriksa status pesanan secara asynchronous (untuk polling)
    Route::get('/order/status/{order}', 'checkOrderStatus')->name('customer.order.status.check');
});


/*
|--------------------------------------------------------------------------
| Authenticated Routes (Admin/Kasir)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
    Route::delete('/profile/photo', [ProfileController::class, 'deletePhoto'])->name('profile.photo.delete');

    Route::get('/dashboard', function () {
        // Redirect dari route dashboard Breeze ke dashboard admin kita
        return redirect()->route('admin.dashboard');
    })->name('dashboard');
    Route::get('admin/orders/check-new', [AdminOrderController::class, 'checkNewOrders'])->name('admin.orders.checkNew');
    Route::prefix('admin')->name('admin.')->group(function () {
        // Dashboard Umum (Statistik Ringkas)
        Route::get('/dashboard', [AdminOrderController::class, 'dashboard'])->name('dashboard');

        // Kelola Pesanan Masuk (Tabel Detail)
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/history', [AdminOrderController::class, 'history'])->name('orders.history'); // BARU
        Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');

        // Route untuk generate dan print struk
        Route::get('/orders/{order}/print', [AdminOrderController::class, 'printStruk'])->name('order.print');

        //Route Untuk Notifikasi pesanan baru masuk//
        Route::get('notifications/details', [AdminOrderController::class, 'getNotificationDetails'])->name('notifications.details');

        // Data Master CRUD
        Route::resource('menus', AdminMenuController::class);
        Route::resource('categories', CategoryController::class);
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
        Route::resource('tables', TableController::class); // <<< Untuk crud meja

        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');

        // Export PDF
        Route::get('reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');

        // Export Excel (SUDAH DIPERBAIKI: Mengarah ke ReportController)
        Route::get('reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');

    });


});

require __DIR__ . '/auth.php';
