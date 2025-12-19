<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;
use Illuminate\Support\Str;
use Carbon\Carbon;

class OrderController extends Controller
{
    const ORDER_TIMEOUT_SECONDS = 30;

    // --- ADD TO CART (AJAX Friendly) ---
    public function addToCart(Request $request)
    {
        $menuId = $request->input('menu_id');
        $menu = Menu::find($menuId);

        if (!$menu) {
            return response()->json(['status' => 'error', 'message' => 'Menu tidak ditemukan.'], 404);
        }

        $cart = Session::get('cart', []);

        if (isset($cart[$menuId])) {
            $cart[$menuId]['quantity']++;
        } else {
            $cart[$menuId] = [
                "id" => $menu->id,
                "name" => $menu->name,
                "quantity" => 1,
                "price" => $menu->price,
                "image" => $menu->image,
            ];
        }

        Session::put('cart', $cart);

        $totalQty = 0;
        foreach ($cart as $item) {
            $totalQty += $item['quantity'];
        }

        return response()->json([
            'status' => 'success',
            'success' => true,
            'message' => 'Berhasil masuk keranjang!',
            'total_qty' => $totalQty,
            'cart_count' => $totalQty
        ]);
    }

    public function cart()
    {
        $cart = Session::get('cart', []);
        return view('customer.cart', compact('cart'));
    }

    public function updateCart(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'quantity' => 'required|integer|min:0'
        ]);

        $cart = Session::get('cart', []);
        $menuId = $request->input('menu_id');
        $quantity = $request->input('quantity');

        if ($quantity <= 0) {
            unset($cart[$menuId]);
        } else {
            if (isset($cart[$menuId])) {
                $cart[$menuId]['quantity'] = $quantity;
            }
        }
        Session::put('cart', $cart);

        $total_price = 0;
        foreach ($cart as $item) {
            $total_price += $item['price'] * $item['quantity'];
        }

        return response()->json([
            'success' => true,
            'cart' => $cart,
            'total_price' => $total_price,
        ]);
    }

    public function removeFromCart(Request $request)
    {
        $menu_id = $request->input('menu_id');
        $cart = Session::get('cart', []);

        if (isset($cart[$menu_id])) {
            unset($cart[$menu_id]);
            Session::put('cart', $cart);
            return redirect()->back()->with('success', 'Menu berhasil dihapus.');
        }
        return redirect()->back()->with('error', 'Menu tidak ditemukan.');
    }

    public function checkout()
    {
        $cart = Session::get('cart');

        if (empty($cart) && Session::has('online_pending_order_id')) {
            $tables = Table::orderBy('name')->get();
            return view('customer.checkout', compact('tables'));
        }
        if (empty($cart)) {
            return redirect()->route('customer.menu.index')->with('error', 'Keranjang Anda kosong.');
        }

        $tables = Table::orderBy('name')->get();
        return view('customer.checkout', compact('tables'));
    }

    // --- STORE ORDER (LOGIKA BARU: JOIN TABLE) ---
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:15',
            'table_name' => 'required|string|exists:tables,name',
            'notes' => 'nullable|string',
            'payment_method_type' => 'required|string|in:cash,online',
        ], [
            'table_name.exists' => 'Kode meja tidak valid.'
        ]);

        $cart = Session::get('cart', []);
        if (empty($cart)) {
            return redirect()->route('customer.menu.index')->with('error', 'Keranjang Anda kosong.');
        }

        $table = Table::where('name', $request->input('table_name'))->first();

        // =========================================================
        // LOGIKA PENGECEKAN MEJA (DIPERKETAT)
        // =========================================================
        if ($table->status === 'occupied') {

            // 1. Cari siapa yang SEDANG AKTIF duduk di meja ini.
            // Kita cari pesanan yang statusnya masih 'pending', 'processing', atau 'delivering'.
            $activeOrder = Order::where('table_id', $table->id)
                ->whereIn('status', ['pending', 'processing', 'delivering'])
                ->latest() // Ambil yang paling baru masuk
                ->first();

            // 2. Jika ditemukan ada pesanan aktif (artinya meja benar-benar ada orangnya)
            if ($activeOrder) {

                // 3. Bandingkan Nomor HP Inputan dengan Nomor HP Pemilik Meja
                if ($request->input('phone_number') !== $activeOrder->phone_number) {

                    // JIKA BEDA: Berarti ini orang asing yang salah input meja / mau menyerobot.
                    return redirect()->back()
                        ->withInput()
                        ->with('table_error', "Meja {$table->name} sedang digunakan oleh pelanggan lain. Jika Anda teman/rombongan, mohon gunakan Nomor HP yang SAMA dengan pemesan awal.");
                }

                // JIKA SAMA: Berarti ini teman/rombongan (Join Table) -> IZINKAN LEWAT.
            } else {
                // Edge Case: Status meja 'occupied' di database, tapi TIDAK ADA pesanan aktif.
                // Ini bisa terjadi jika sistem error sebelumnya atau data tidak sinkron.
                // Solusi: Auto-fix status meja jadi available agar bisa dipesan.
                $table->update(['status' => 'available']);
            }
        }
        // =========================================================

        $total_price = 0;
        foreach ($cart as $item) {
            $total_price += $item['price'] * $item['quantity'];
        }

        do {
            $uniqueOrderNumber = 'CAFE' . Str::upper(Str::random(6));
            $exists = Order::where('order_number', $uniqueOrderNumber)->exists();
        } while ($exists);

        $isCash = $request->input('payment_method_type') === 'cash';
        $orderStatus = $isCash ? 'pending' : 'initial';
        $paymentMethod = $isCash ? 'cash' : 'online';
        $paymentStatus = 'unpaid';

        $order = Order::create([
            'order_number' => $uniqueOrderNumber,
            'customer_name' => $request->input('customer_name'),
            'phone_number' => $request->input('phone_number'),
            'table_id' => $table->id,
            'order_type' => 'dine_in',
            'total_price' => $total_price,
            'status' => $orderStatus,
            'notes' => $request->input('notes'),
        ]);

        foreach ($cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        Payment::create([
            'order_id' => $order->id,
            'amount' => $order->total_price,
            'method' => $paymentMethod,
            'status' => $paymentStatus
        ]);

        // Pastikan meja di-set occupied
        $table->update(['status' => 'occupied']);

        Session::forget('cart');

        if ($isCash) {
            Session::put('pending_order_id', $order->id);
            return redirect()->route('customer.payment.cash', ['order' => $order->id]);
        } else {
            Session::forget('pending_order_id');
            return $this->onlineCheckout($request, $order);
        }
    }

    public function updateStatusSuccess(Order $order)
    {
        if ($order->status === 'initial' || $order->status === 'pending' || $order->payment->status === 'unpaid') {
            $order->status = 'processing';
            $order->payment->status = 'paid';
            $order->payment->save();
            $order->save();
        }
        return redirect()->route('customer.order.online-confirmed', $order);
    }

    public function cashInstructions(Order $order)
    {
        if ($order->payment && $order->payment->method !== 'cash') {
            return redirect()->route('customer.order.online-confirmed', $order);
        }

        $timeoutSeconds = self::ORDER_TIMEOUT_SECONDS;
        $expirationTime = $order->created_at->copy()->addSeconds($timeoutSeconds);
        $isTimedOut = $order->status === 'pending' && $expirationTime->isPast();

        if ($isTimedOut) {
            DB::transaction(function () use ($order) {
                $order->status = 'canceled';
                $order->save();

                // Cek apakah masih ada pesanan LAIN yang aktif di meja ini?
                $activeOrdersCount = Order::where('table_id', $order->table_id)
                    ->whereIn('status', ['pending', 'processing', 'delivering'])
                    ->where('id', '!=', $order->id)
                    ->count();

                // Hanya bebaskan meja jika TIDAK ADA pesanan lain
                if ($activeOrdersCount == 0) {
                    $table = Table::find($order->table_id);
                    if ($table && $table->status === 'occupied') {
                        $table->update(['status' => 'available']);
                    }
                }

                Session::forget('pending_order_id');
            });
            $order->refresh();
        }

        if (in_array($order->status, ['processing', 'delivering', 'completed'])) {
            Session::forget('pending_order_id');
            return redirect()->route('customer.order.online-confirmed', $order);
        }

        if ($order->status === 'canceled') {
            Session::forget('pending_order_id');
            return view('customer.payment-cash-instructions', compact('order'));
        }

        if ($order->status === 'pending') {
            $expirationTimestamp = $expirationTime->timestamp * 1000;
            return view('customer.payment-cash-instructions', compact('order', 'expirationTimestamp'));
        }

        return redirect()->route('customer.menu.index')->with('error', 'Pesanan tidak ditemukan.');
    }

    public function showTrackForm()
    {
        return view('customer.track');
    }

    public function trackOrder(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string|max:15',
        ]);

        $order = Order::where('phone_number', $request->phone_number)
            ->orderBy('created_at', 'desc')
            ->first();

        return view('customer.track', compact('order'));
    }

    public function checkOrderStatus(Order $order)
    {
        return response()->json([
            'order_status' => $order->status,
            'payment_status' => $order->payment?->status ?? 'unpaid'
        ]);
    }

    public function onlineConfirmed(Order $order)
    {
        Session::forget('pending_order_id');
        return view('customer.order-online-confirmed', compact('order'));
    }

    public function onlineCheckout(Request $request, Order $order)
    {
        $isPaid = $order->payment && $order->payment->status === 'paid';
        $isActiveOrCompleted = in_array($order->status, ['processing', 'delivering', 'completed']);

        if ($isPaid && $isActiveOrCompleted) {
            return redirect()->route('customer.order.online-confirmed', $order);
        }

        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');

        $transaction_details = [
            'order_id' => $order->id . '-' . time(),
            'gross_amount' => $order->total_price,
        ];

        $customer_details = [
            'first_name' => $order->customer_name ?? 'Pelanggan',
            'email' => 'customer-' . $order->id . '@example.com',
            'phone' => $order->phone_number ?? '08123456789',
        ];

        $snap_params = [
            'transaction_details' => $transaction_details,
            'customer_details' => $customer_details,
        ];

        try {
            $snapToken = Snap::getSnapToken($snap_params);
            return view('customer.payment.online_checkout', compact('snapToken', 'order'));
        } catch (\Exception $e) {
            return redirect()->route('customer.menu.index')->with('error', 'Gagal membuat token pembayaran: ' . $e->getMessage());
        }
    }

    public function handleMidtransNotification(Request $request)
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');

        try {
            $notification = new Notification();
            $transaction = $notification->transaction_status;
            $orderIdMidtrans = $notification->order_id;

            $orderId = explode('-', $orderIdMidtrans)[0];
            $order = Order::with('payment')->find($orderId);

            if (!$order) {
                return response('Order not found', 404);
            }

            if ($transaction == 'capture' || $transaction == 'settlement') {
                if ($order->status !== 'completed' && $order->status !== 'processing') {
                    $order->status = 'processing';
                    $order->save();

                    if ($order->payment) {
                        $order->payment->status = 'paid';
                        $order->payment->save();
                    }
                }
            } elseif ($transaction == 'cancel' || $transaction == 'expire' || $transaction == 'deny') {
                if ($order->status !== 'canceled') {
                    $order->status = 'canceled';
                    $order->save();

                    // Cek order aktif lain sebelum membebaskan meja
                    $activeOrdersCount = Order::where('table_id', $order->table_id)
                        ->whereIn('status', ['pending', 'processing', 'delivering'])
                        ->where('id', '!=', $order->id)
                        ->count();

                    if ($activeOrdersCount == 0) {
                        $table = Table::find($order->table_id);
                        if ($table && $table->status === 'occupied') {
                            $table->update(['status' => 'available']);
                        }
                    }
                }
            }

            return response('Notification handled successfully', 200);
        } catch (\Exception $e) {
            return response('Error handling notification', 500);
        }
    }

    public function cancelOnlineOrder(Request $request, Order $order)
    {
        if ($order->status === 'pending' || $order->status === 'initial') {
            DB::transaction(function () use ($order, $request) {
                $order->status = 'canceled';
                $order->save();

                // Cek order aktif lain sebelum membebaskan meja
                $activeOrdersCount = Order::where('table_id', $order->table_id)
                        ->whereIn('status', ['pending', 'processing', 'delivering'])
                        ->where('id', '!=', $order->id)
                        ->count();

                if ($activeOrdersCount == 0) {
                    $table = Table::find($order->table_id);
                    if ($table && $table->status === 'occupied') {
                        $table->update(['status' => 'available']);
                    }
                }

                $request->session()->forget('online_pending_order_id');
            });
            return redirect()->route('customer.menu.index')->with('sweet_success', 'Pesanan Anda berhasil dibatalkan');
        } else {
            return redirect()->route('customer.menu.index')->with('error', 'Pesanan tidak dapat dibatalkan karena statusnya sudah ' . strtoupper($order->status));
        }
    }
}
