<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    const ORDER_TIMEOUT_SECONDS = 30;

    /**
     * Dashboard Statistik & Grafik
     */
    public function dashboard(Request $request)
    {
        // 1. Tentukan Default Filter
        // Default: Hari Ini (Agar dashboard langsung relevan saat dibuka)
        $timeFilter = $request->input('time_filter', 'today');
        $filteredLabel = 'Hari Ini';

        // Inisialisasi Tanggal (Default Today)
        $startDate = Carbon::today()->startOfDay();
        $endDate = Carbon::today()->endOfDay();

        $customStartDate = $request->input('start_date');
        $customEndDate = $request->input('end_date');

        // 2. Logika Switch Case Tanggal (Strict Mode)
        switch ($timeFilter) {
            case 'today':
                $startDate = Carbon::today()->startOfDay();
                $endDate = Carbon::today()->endOfDay();
                $filteredLabel = 'Hari Ini';
                break;
            case 'yesterday':
                $startDate = Carbon::yesterday()->startOfDay();
                $endDate = Carbon::yesterday()->endOfDay();
                $filteredLabel = 'Kemarin';
                break;
            case 'this_week':
                $startDate = Carbon::now()->startOfWeek()->startOfDay();
                $endDate = Carbon::now()->endOfWeek()->endOfDay();
                $filteredLabel = 'Minggu Ini';
                break;
            case 'this_month':
                $startDate = Carbon::now()->startOfMonth()->startOfDay();
                $endDate = Carbon::now()->endOfMonth()->endOfDay();
                $filteredLabel = 'Bulan Ini';
                break;
            case 'this_year':
                $startDate = Carbon::now()->startOfYear()->startOfDay();
                $endDate = Carbon::now()->endOfYear()->endOfDay();
                $filteredLabel = 'Tahun Ini';
                break;
            case 'custom':
                if ($customStartDate && $customEndDate) {
                    $startDate = Carbon::parse($customStartDate)->startOfDay();
                    $endDate = Carbon::parse($customEndDate)->endOfDay();
                    $filteredLabel = 'Rentang Kustom';
                }
                break;
        }

        // =============================================================
        // 3. QUERY DATA (DIPISAH ANTARA REALTIME & HISTORICAL)
        // =============================================================

        // A. METRIK OPERASIONAL (REALTIME - JANGAN DI-FILTER TANGGAL)
        // Alasannya: Pesanan "Pending" dari kemarin tetap harus muncul hari ini agar diproses.
        $pendingCount = Order::where('status', 'pending')->count();
        $inProgressCount = Order::whereIn('status', ['processing', 'delivering'])->count();

        // B. METRIK HISTORIS (FILTERED - SESUAI TANGGAL DIPILIH)
        // Alasannya: Kita ingin tahu "Berapa yang selesai HARI INI/BULAN INI?"
        $completedCount = Order::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $canceledCount = Order::where('status', 'canceled')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // C. METRIK KEUANGAN (FILTERED)
        $revenueStatuses = ['completed', 'processing', 'delivering'];

        $totalRevenue = Order::whereIn('status', $revenueStatuses)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_price');

        $filteredLoss = Order::where('status', 'canceled')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_price');

        // 4. Chart Data (Tetap 7 Hari Terakhir Statis untuk Tren)
        $chartData = $this->getWeeklyRevenueData();

        // Data untuk view
        $customDate = ['start' => $customStartDate, 'end' => $customEndDate];

        return view('admin.dashboard', compact('pendingCount', 'inProgressCount', 'completedCount', 'canceledCount', 'totalRevenue', 'filteredLoss', 'filteredLabel', 'timeFilter', 'chartData', 'customDate'));
    }

    protected function getWeeklyRevenueData()
    {
        $days = [];
        $revenues = [];
        $today = Carbon::today();
        $revenueStatuses = ['completed', 'processing', 'delivering'];

        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $days[] = $date->isoFormat('dd');
            $revenue = Order::whereDate('created_at', $date)->whereIn('status', $revenueStatuses)->sum('total_price');
            $revenues[] = (float) $revenue;
        }

        return ['labels' => $days, 'data' => $revenues];
    }

    /**
     * Daftar Pesanan Aktif (Index)
     * Menangani View, Filter, Sorting, dan Auto-Cancel Timeout.
     */
    public function index(Request $request)
    {
        // 1. Cek Timeout Pesanan Pending (Auto Cancel jika lebih 30 detik tak dibayar)
        $timeoutSeconds = self::ORDER_TIMEOUT_SECONDS;
        $expirationTime = Carbon::now()->subSeconds($timeoutSeconds);

        $expiredOrders = Order::where('status', 'pending')->where('created_at', '<', $expirationTime)->get();

        if ($expiredOrders->count() > 0) {
            foreach ($expiredOrders as $order) {
                DB::transaction(function () use ($order) {
                    $order->update(['status' => 'canceled']);

                    // Cek apakah meja bisa dibebaskan (Logic Join Table)
                    // Jika tidak ada order aktif lain di meja itu, baru set available
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
                });
            }
        }

        // 2. PERSIAPAN DATA VIEW

        // A. Ambil Data Meja (PENTING: Untuk mengisi dropdown filter)
        $tables = Table::orderBy('name', 'asc')->get();
        $activeOrders = Order::whereIn('status', ['pending', 'processing', 'delivering'])
            ->with(['table', 'payment', 'orderItems.menu'])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($request->ajax()) {
            // Kita gunakan renderSection untuk mengambil hanya bagian content
            // Atau cara termudah: kirim data dan biarkan view yang memfilter
            return view('admin.orders.index', compact('activeOrders', 'tables'));
        }

        return view('admin.orders.index', compact('activeOrders', 'tables'));
    }

    /**
     * Detail Pesanan
     */
    public function show(Order $order)
    {
        $order->load(['orderItems.menu', 'table', 'payment']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update Status Pesanan (Aksi Utama Kasir)
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string|in:pending,processing,delivering,completed,canceled',
        ]);

        $newStatus = $request->input('status');
        $oldStatus = $order->status;

        if ($oldStatus === 'canceled' && $newStatus !== 'canceled') {
            return redirect()
                ->back()
                ->with('error', "Pesanan #{$order->order_number} sudah batal, tidak bisa diproses.");
        }

        DB::transaction(function () use ($order, $newStatus, $oldStatus) {
            // Update Status Order
            $order->update(['status' => $newStatus]);

            // Logika Pembayaran (Otomatis Lunas jika diproses/selesai)
            if ($order->payment) {
                if ($newStatus === 'processing' && $oldStatus === 'pending') {
                    $order->payment->update(['status' => 'paid']); // Terima Tunai
                }
                if ($newStatus === 'completed' && $order->payment->status !== 'paid') {
                    $order->payment->update(['status' => 'paid']); // Pastikan lunas saat selesai
                }
            }

            // Logika Meja (Bebaskan meja jika selesai/batal & tidak ada order lain)
            if ($order->table_id) {
                $table = Table::find($order->table_id);
                if ($table) {
                    if (in_array($newStatus, ['completed', 'canceled'])) {
                        // Cek Join Table: Apakah masih ada pesanan lain di meja ini?
                        $otherActive = Order::where('table_id', $order->table_id)
                            ->whereIn('status', ['pending', 'processing', 'delivering'])
                            ->where('id', '!=', $order->id)
                            ->exists();

                        if (!$otherActive && $table->status === 'occupied') {
                            $table->update(['status' => 'available']);
                        }
                    }
                    // Jika status balik ke aktif (misal salah pencet), kunci meja lagi
                    elseif (in_array($newStatus, ['pending', 'processing', 'delivering'])) {
                        if ($table->status === 'available') {
                            $table->update(['status' => 'occupied']);
                        }
                    }
                }
            }
        });

        return redirect()
            ->back()
            ->with('success', "Status Pesanan #{$order->order_number} diperbarui.");
    }

    /**
     * Riwayat Pesanan (Selesai & Batal)
     */
    public function history(Request $request)
    {
        $eagerLoads = ['table', 'payment', 'orderItems.menu'];

        // 1. Tentukan Rentang Waktu
        $period = $request->input('period', 'this_month');

        // Default variabel
        $startDate = null;
        $endDate = null;

        switch ($period) {
            case 'today':
                $startDate = Carbon::today()->startOfDay();
                $endDate = Carbon::today()->endOfDay();
                break;
            case 'yesterday':
                $startDate = Carbon::yesterday()->startOfDay();
                $endDate = Carbon::yesterday()->endOfDay();
                break;
            case 'this_week':
                $startDate = Carbon::now()->startOfWeek()->startOfDay();
                $endDate = Carbon::now()->endOfWeek()->endOfDay();
                break;
            case 'this_month':
                $startDate = Carbon::now()->startOfMonth()->startOfDay();
                $endDate = Carbon::now()->endOfMonth()->endOfDay();
                break;
            case 'custom':
                // Validasi input custom
                if ($request->filled('start_date') && $request->filled('end_date')) {
                    $startDate = Carbon::parse($request->start_date)->startOfDay();
                    $endDate = Carbon::parse($request->end_date)->endOfDay();
                } else {
                    // Fallback jika custom dipilih tapi tanggal kosong -> Tampilkan semua / bulan ini
                    $startDate = Carbon::now()->startOfMonth()->startOfDay();
                    $endDate = Carbon::now()->endOfMonth()->endOfDay();
                }
                break;
            default:
                // Default fallback
                $startDate = Carbon::now()->startOfMonth()->startOfDay();
                $endDate = Carbon::now()->endOfMonth()->endOfDay();
                break;
        }

        // 2. Query dengan Filter yang Lebih Ketat
        // Menggunakan created_at untuk konsistensi pencatatan order

        $canceledOrders = Order::where('status', 'canceled')->where('created_at', '>=', $startDate)->where('created_at', '<=', $endDate)->with($eagerLoads)->orderBy('created_at', 'desc')->get();

        $completedOrders = Order::where('status', 'completed')->where('created_at', '>=', $startDate)->where('created_at', '<=', $endDate)->with($eagerLoads)->orderBy('updated_at', 'desc')->get();

        return view('admin.orders.history', compact('canceledOrders', 'completedOrders'));
    }

    // --- API UNTUK NOTIFIKASI NAVBAR ---
    public function checkNewOrders()
    {
        // Cek pesanan pending/processing
        $newOrderCount = Order::whereIn('status', ['pending', 'processing'])->count();
        return response()->json(['new_count' => $newOrderCount, 'has_new' => $newOrderCount > 0]);
    }

    public function getNotificationDetails()
    {
        $newOrders = Order::whereIn('status', ['pending', 'processing'])
            ->with('table')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $notifications = $newOrders->map(function ($order) {
            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $order->customer_name,
                'table_name' => $order->table->name ?? 'Ambil',
                'status' => $order->status,
                'time_ago' => $order->created_at->diffForHumans(),
                'url' => route('admin.orders.show', $order->id),
            ];
        });

        return response()->json([
            'notifications' => $notifications,
            'count' => Order::whereIn('status', ['pending', 'processing'])->count(),
        ]);
    }

    /**
     * Cetak Struk
     */

    public function printStruk(Order $order)
    {
        $order->load(['orderItems.menu', 'payment', 'table']);
        return view('admin.orders.struk_print', compact('order'));
    }
}
