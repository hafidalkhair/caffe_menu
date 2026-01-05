<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem; // PENTING: Tambahkan ini untuk hitung revenue per item
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    const ORDER_TIMEOUT_SECONDS = 30;

    /**
     * Dashboard Statistik & Grafik
     * (Logika Dashboard SUDAH DIMODIFIKASI untuk Multi-Tenant)
     */
    public function dashboard(Request $request)
    {
        // 1. Tentukan Default Filter Waktu
        $timeFilter = $request->input('time_filter', 'today');
        $filteredLabel = 'Hari Ini';

        $startDate = Carbon::today()->startOfDay();
        $endDate = Carbon::today()->endOfDay();

        $customStartDate = $request->input('start_date');
        $customEndDate = $request->input('end_date');

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
        // LOGIKA MULTI-TENANT (MODIFIKASI TERBARU)
        // =============================================================
        $user = Auth::user();
        $isDapur = $user->role === 'dapur';
        $storeId = $isDapur ? $user->store_id : null;

        // --- 1. Query Dasar Pesanan (Untuk Count) ---
        $baseOrderQuery = Order::query();

        // Jika Dapur: Filter pesanan yang punya item dari gerai ini
        if ($isDapur) {
            $baseOrderQuery->whereHas('orderItems.menu', function($q) use ($storeId) {
                $q->where('store_id', $storeId);
            });
        }

        // Hitung Jumlah Pesanan (Clone query agar tidak tabrakan)
        $pendingCount = (clone $baseOrderQuery)->where('status', 'pending')->count();
        $inProgressCount = (clone $baseOrderQuery)->whereIn('status', ['processing', 'delivering'])->count();

        $completedCount = (clone $baseOrderQuery)->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $canceledCount = (clone $baseOrderQuery)->where('status', 'canceled')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // --- 2. Query Keuangan (Untuk Revenue) ---
        $revenueStatuses = ['completed', 'processing', 'delivering'];

        if ($isDapur) {
            // QUERY KHUSUS DAPUR:
            // Hanya jumlahkan (harga * qty) dari tabel OrderItems milik gerai tersebut
            // Bukan total bill pesanan (karena bill mengandung item gerai lain)

            $totalRevenue = OrderItem::whereHas('order', function($q) use ($revenueStatuses, $startDate, $endDate) {
                    $q->whereIn('status', $revenueStatuses)
                      ->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->whereHas('menu', function($q) use ($storeId) {
                    $q->where('store_id', $storeId);
                })
                ->sum(DB::raw('price * quantity'));

            $filteredLoss = OrderItem::whereHas('order', function($q) use ($startDate, $endDate) {
                    $q->where('status', 'canceled')
                      ->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->whereHas('menu', function($q) use ($storeId) {
                    $q->where('store_id', $storeId);
                })
                ->sum(DB::raw('price * quantity'));

        } else {
            // QUERY ADMIN: Jumlahkan total_price dari tabel Orders (Global)
            $totalRevenue = Order::whereIn('status', $revenueStatuses)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('total_price');

            $filteredLoss = Order::where('status', 'canceled')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('total_price');
        }

        // Ambil Data Grafik (Kirim parameter role & storeId)
        $chartData = $this->getWeeklyRevenueData($isDapur, $storeId);

        $customDate = ['start' => $customStartDate, 'end' => $customEndDate];

        return view('admin.dashboard', compact('pendingCount', 'inProgressCount', 'completedCount', 'canceledCount', 'totalRevenue', 'filteredLoss', 'filteredLabel', 'timeFilter', 'chartData', 'customDate'));
    }

    /**
     * Helper Grafik Revenue (Dimodifikasi untuk support parameter)
     */
    protected function getWeeklyRevenueData($isDapur = false, $storeId = null)
    {
        $days = [];
        $revenues = [];
        $today = Carbon::today();
        $revenueStatuses = ['completed', 'processing', 'delivering'];

        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $days[] = $date->isoFormat('dd');

            if ($isDapur) {
                // Hitung Revenue Harian per Gerai (OrderItems)
                $revenue = OrderItem::whereHas('order', function($q) use ($revenueStatuses, $date) {
                        $q->whereIn('status', $revenueStatuses)
                          ->whereDate('created_at', $date);
                    })
                    ->whereHas('menu', function($q) use ($storeId) {
                        $q->where('store_id', $storeId);
                    })
                    ->sum(DB::raw('price * quantity'));
            } else {
                // Hitung Revenue Harian Global (Orders)
                $revenue = Order::whereDate('created_at', $date)
                    ->whereIn('status', $revenueStatuses)
                    ->sum('total_price');
            }

            $revenues[] = (float) $revenue;
        }

        return ['labels' => $days, 'data' => $revenues];
    }

    /**
     * Daftar Pesanan Aktif (Index)
     */
    public function index(Request $request)
    {
        // 1. Cek Timeout Pesanan Pending (Auto Cancel)
        $timeoutSeconds = self::ORDER_TIMEOUT_SECONDS;
        $expirationTime = Carbon::now()->subSeconds($timeoutSeconds);

        $expiredOrders = Order::where('status', 'pending')->where('created_at', '<', $expirationTime)->get();

        if ($expiredOrders->count() > 0) {
            foreach ($expiredOrders as $order) {
                DB::transaction(function () use ($order) {
                    $order->update(['status' => 'canceled']);

                    // Cek meja available
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

        // 2. PERSIAPAN DATA VIEW (DENGAN FILTER MULTI-TENANT)
        $tables = Table::orderBy('name', 'asc')->get();

        // Mulai Query
        $query = Order::whereIn('status', ['pending', 'processing', 'delivering'])
            ->with(['table', 'payment', 'orderItems.menu']);

        // --- LOGIKA MULTI-TENANT ---
        if (Auth::user()->role === 'dapur') {
            $storeId = Auth::user()->store_id;

            // Filter: Hanya ambil order yang memiliki ITEM MENU dari gerai si user
            $query->whereHas('orderItems.menu', function($q) use ($storeId) {
                $q->where('store_id', $storeId);
            });
        }
        // ---------------------------

        $activeOrders = $query->orderBy('created_at', 'desc')->get();

        if ($request->ajax()) {
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
     * Update Status Pesanan
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string|in:pending,processing,delivering,completed,canceled',
        ]);

        $newStatus = $request->input('status');
        $oldStatus = $order->status;

        if ($oldStatus === 'canceled' && $newStatus !== 'canceled') {
            return redirect()->back()->with('error', "Pesanan #{$order->order_number} sudah batal.");
        }

        DB::transaction(function () use ($order, $newStatus, $oldStatus) {
            $order->update(['status' => $newStatus]);

            // Logika Pembayaran
            if ($order->payment) {
                if ($newStatus === 'processing' && $oldStatus === 'pending') {
                    $order->payment->update(['status' => 'paid']);
                }
                if ($newStatus === 'completed' && $order->payment->status !== 'paid') {
                    $order->payment->update(['status' => 'paid']);
                }
            }

            // Logika Meja
            if ($order->table_id) {
                $table = Table::find($order->table_id);
                if ($table) {
                    if (in_array($newStatus, ['completed', 'canceled'])) {
                        $otherActive = Order::where('table_id', $order->table_id)
                            ->whereIn('status', ['pending', 'processing', 'delivering'])
                            ->where('id', '!=', $order->id)
                            ->exists();

                        if (!$otherActive && $table->status === 'occupied') {
                            $table->update(['status' => 'available']);
                        }
                    }
                    elseif (in_array($newStatus, ['pending', 'processing', 'delivering'])) {
                        if ($table->status === 'available') {
                            $table->update(['status' => 'occupied']);
                        }
                    }
                }
            }
        });

        return redirect()->back()->with('success', "Status Pesanan #{$order->order_number} diperbarui.");
    }

    /**
     * Riwayat Pesanan
     * (Logika SUDAH DIMODIFIKASI untuk filter History per Gerai)
     */
    public function history(Request $request)
    {
        $eagerLoads = ['table', 'payment', 'orderItems.menu'];
        $period = $request->input('period', 'this_month');
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
                if ($request->filled('start_date') && $request->filled('end_date')) {
                    $startDate = Carbon::parse($request->start_date)->startOfDay();
                    $endDate = Carbon::parse($request->end_date)->endOfDay();
                } else {
                    $startDate = Carbon::now()->startOfMonth()->startOfDay();
                    $endDate = Carbon::now()->endOfMonth()->endOfDay();
                }
                break;
            default:
                $startDate = Carbon::now()->startOfMonth()->startOfDay();
                $endDate = Carbon::now()->endOfMonth()->endOfDay();
                break;
        }

        // Query Dasar
        $queryCanceled = Order::where('status', 'canceled')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with($eagerLoads);

        $queryCompleted = Order::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with($eagerLoads);

        // --- FILTER HISTORY MULTI-TENANT ---
        // Jika Dapur, filter query agar hanya menampilkan order yang punya item milik gerainya
        if (Auth::user()->role === 'dapur') {
            $storeId = Auth::user()->store_id;

            $filterByStore = function($q) use ($storeId) {
                $q->whereHas('orderItems.menu', function($subQ) use ($storeId) {
                    $subQ->where('store_id', $storeId);
                });
            };

            $queryCanceled->where($filterByStore);
            $queryCompleted->where($filterByStore);
        }
        // -----------------------------------

        $canceledOrders = $queryCanceled->orderBy('created_at', 'desc')->get();
        $completedOrders = $queryCompleted->orderBy('updated_at', 'desc')->get();

        return view('admin.orders.history', compact('canceledOrders', 'completedOrders'));
    }

    // --- API UNTUK NOTIFIKASI NAVBAR ---

    public function checkNewOrders()
    {
        $query = Order::whereIn('status', ['pending', 'processing']);

        // --- FILTER NOTIFIKASI (DAPUR) ---
        if (Auth::user()->role === 'dapur') {
            $storeId = Auth::user()->store_id;
            $query->whereHas('orderItems.menu', function($q) use ($storeId) {
                $q->where('store_id', $storeId);
            });
        }
        // ---------------------------------

        $newOrderCount = $query->count();
        return response()->json(['new_count' => $newOrderCount, 'has_new' => $newOrderCount > 0]);
    }

    public function getNotificationDetails()
    {
        $query = Order::whereIn('status', ['pending', 'processing']);

        // --- FILTER DETAIL NOTIFIKASI (DAPUR) ---
        if (Auth::user()->role === 'dapur') {
            $storeId = Auth::user()->store_id;
            $query->whereHas('orderItems.menu', function($q) use ($storeId) {
                $q->where('store_id', $storeId);
            });
        }
        // ----------------------------------------

        // Clone query untuk count agar tidak bentrok dengan take(5)
        $countQuery = clone $query;
        $totalCount = $countQuery->count();

        $newOrders = $query->with('table')
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
                'url' => route('admin.orders.index'),
            ];
        });

        return response()->json([
            'notifications' => $notifications,
            'count' => $totalCount,
        ]);
    }

    public function printStruk(Order $order)
    {
        $order->load(['orderItems.menu', 'payment', 'table']);
        return view('admin.orders.struk_print', compact('order'));
    }
}
