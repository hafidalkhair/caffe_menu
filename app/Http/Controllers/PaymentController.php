<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Tampilkan halaman pembayaran dan proses pembayaran.
     */
    public function processPayment(Request $request, Order $order)
    {
        // Logika dasar untuk pembayaran, bisa dikembangkan untuk integrasi payment gateway
        // Untuk saat ini, kita anggap pembayaran langsung berhasil
        
        $payment = Payment::create([
            'order_id' => $order->id,
            'amount' => $order->total_price,
            'method' => 'cash', // Atau 'online'
            'status' => 'paid'
        ]);
        
        // Update status pesanan menjadi 'completed'
        $order->update(['status' => 'completed']);

        // Redirect ke halaman sukses atau dashboard
        return redirect()->route('customer.menu.index')->with('success', 'Pembayaran berhasil dan pesanan Anda sedang diproses!');
    }
}