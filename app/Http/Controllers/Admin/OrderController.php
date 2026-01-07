<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
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
     */
    public function dashboard(Request $request)
    {
        // 1. Setup Filter Waktu
        $timeFilter = $request->input('time_filter', 'today');
        $filteredLabel = 'Hari Ini';
        $startDate = Carbon::today()->startOfDay();
        $endDate = Carbon::today()->endOfDay();
        $customStartDate = $request->input('start_date');
        $customEndDate = $request->input('end_date');

        switch ($timeFilter) {
            case 'today': $startDate = Carbon::today()->startOfDay(); $endDate = Carbon::today()->endOfDay(); $filteredLabel = 'Hari Ini'; break;
            case 'yesterday': $startDate = Carbon::yesterday()->startOfDay(); $endDate = Carbon::yesterday()->endOfDay(); $filteredLabel = 'Kemarin'; break;
            case 'this_week': $startDate = Carbon::now()->startOfWeek()->startOfDay(); $endDate = Carbon::now()->endOfWeek()->endOfDay(); $filteredLabel = 'Minggu Ini'; break;
            case 'this_month': $startDate = Carbon::now()->startOfMonth()->startOfDay(); $endDate = Carbon::now()->endOfMonth()->endOfDay(); $filteredLabel = 'Bulan Ini'; break;
            case 'this_year': $startDate = Carbon::now()->startOfYear()->startOfDay(); $endDate = Carbon::now()->endOfYear()->endOfDay(); $filteredLabel = 'Tahun Ini'; break;
            case 'custom': if ($customStartDate && $customEndDate) { $startDate = Carbon::parse($customStartDate)->startOfDay(); $endDate = Carbon::parse($customEndDate)->endOfDay(); $filteredLabel = 'Rentang Kustom'; } break;
        }

        // --- SETUP MULTI-TENANT ---
        $user = Auth::user();
        $isDapur = $user->role === 'dapur';
        $storeId = $isDapur ? $user->store_id : null;

        // --- 1. Query Dasar Pesanan (Count) ---
        $baseOrderQuery = Order::query();

        if ($isDapur) {
            // Filter: Hanya hitung Order yang MENGANDUNG item dari gerai ini
            $baseOrderQuery->whereHas('orderItems.menu', function($q) use ($storeId) {
                $q->where('store_id', $storeId);
            });
        }

        $pendingCount = (clone $baseOrderQuery)->where('status', 'pending')->count();
        $inProgressCount = (clone $baseOrderQuery)->whereIn('status', ['processing', 'delivering'])->count();
        $completedCount = (clone $baseOrderQuery)->where('status', 'completed')->whereBetween('created_at', [$startDate, $endDate])->count();
        $canceledCount = (clone $baseOrderQuery)->where('status', 'canceled')->whereBetween('created_at', [$startDate, $endDate])->count();

        // --- 2. Query Keuangan (Revenue) ---
        // LOGIKA PENTING: Dapur hanya melihat total harga dari ITEM MEREKA SAJA, bukan total struk.

        $revenueStatuses = ['completed', 'processing', 'delivering'];

        if ($isDapur) {
            $totalRevenue = OrderItem::whereHas('order', function($q) use ($revenueStatuses, $startDate, $endDate) {
                    $q->whereIn('status', $revenueStatuses)->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->whereHas('menu', function($q) use ($storeId) {
                    $q->where('store_id', $storeId);
                })
                ->sum(DB::raw('price * quantity'));

            $filteredLoss = OrderItem::whereHas('order', function($q) use ($startDate, $endDate) {
                    $q->where('status', 'canceled')->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->whereHas('menu', function($q) use ($storeId) {
                    $q->where('store_id', $storeId);
                })
                ->sum(DB::raw('price * quantity'));
        } else {
            // Admin melihat total semua
            $totalRevenue = Order::whereIn('status', $revenueStatuses)->whereBetween('created_at', [$startDate, $endDate])->sum('total_price');
            $filteredLoss = Order::where('status', 'canceled')->whereBetween('created_at', [$startDate, $endDate])->sum('total_price');
        }

        // --- 3. Best Seller & Chart ---
        $chartData = $this->getWeeklyRevenueData($isDapur, $storeId);

        // Query Best Seller (Fix Group By Store ID)
        $bestSellerQuery = \App\Models\Menu::select('menus.id', 'menus.name', 'menus.price', 'menus.image', 'menus.store_id', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->join('order_items', 'menus.id', '=', 'order_items.menu_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->whereBetween('orders.created_at', [$startDate, $endDate]);

        if ($isDapur) {
            $bestSellerQuery->where('menus.store_id', $storeId);
        }

        $bestSellers = $bestSellerQuery->groupBy('menus.id', 'menus.name', 'menus.price', 'menus.image', 'menus.store_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        $customDate = ['start' => $customStartDate, 'end' => $customEndDate];

        return view('admin.dashboard', compact('pendingCount', 'inProgressCount', 'completedCount', 'canceledCount', 'totalRevenue', 'filteredLoss', 'filteredLabel', 'timeFilter', 'chartData', 'customDate', 'bestSellers'));
    }

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
                $revenue = OrderItem::whereHas('order', function($q) use ($revenueStatuses, $date) {
                        $q->whereIn('status', $revenueStatuses)->whereDate('created_at', $date);
                    })
                    ->whereHas('menu', function($q) use ($storeId) {
                        $q->where('store_id', $storeId);
                    })
                    ->sum(DB::raw('price * quantity'));
            } else {
                $revenue = Order::whereDate('created_at', $date)->whereIn('status', $revenueStatuses)->sum('total_price');
            }
            $revenues[] = (float) $revenue;
        }
        return ['labels' => $days, 'data' => $revenues];
    }

    /**
     * Daftar Pesanan Aktif (Index)
     * PERBAIKAN LOGIKA: Filter item agar Dapur hanya melihat produk mereka.
     */
    public function index(Request $request)
    {
        // Auto Cancel Timeout (Sama)
        $expirationTime = Carbon::now()->subSeconds(self::ORDER_TIMEOUT_SECONDS);
        $expiredOrders = Order::where('status', 'pending')->where('created_at', '<', $expirationTime)->get();
        if ($expiredOrders->count() > 0) {
            foreach ($expiredOrders as $order) {
                DB::transaction(function () use ($order) {
                    $order->update(['status' => 'canceled']);
                    $activeCount = Order::where('table_id', $order->table_id)->whereIn('status', ['pending', 'processing', 'delivering'])->where('id', '!=', $order->id)->count();
                    if ($activeCount == 0) {
                        Table::where('id', $order->table_id)->where('status', 'occupied')->update(['status' => 'available']);
                    }
                });
            }
        }

        $tables = Table::orderBy('name', 'asc')->get();

        // --- QUERY ORDER ---
        $query = Order::whereIn('status', ['pending', 'processing', 'delivering']);

        $user = Auth::user();
        $isDapur = $user->role === 'dapur';
        $storeId = $isDapur ? $user->store_id : null;

        if ($isDapur) {
            // 1. Ambil Order yang relevan
            $query->whereHas('orderItems.menu', function($q) use ($storeId) {
                $q->where('store_id', $storeId);
            });

            // 2. FILTER ITEM (Constraining Eager Loads)
            // Ini kuncinya: Saat load 'orderItems', hanya load item milik gerai ini
            $query->with(['table', 'payment', 'orderItems' => function($q) use ($storeId) {
                $q->whereHas('menu', function($subQ) use ($storeId) {
                    $subQ->where('store_id', $storeId);
                })->with('menu');
            }]);
        } else {
            // Admin ambil semua
            $query->with(['table', 'payment', 'orderItems.menu']);
        }

        $activeOrders = $query->orderBy('created_at', 'desc')->get();

        return view('admin.orders.index', compact('activeOrders', 'tables'));
    }

    /**
     * Detail Pesanan
     * PERBAIKAN LOGIKA: Filter item di detail pesanan juga.
     */
    public function show(Order $order)
    {
        $user = Auth::user();

        if ($user->role === 'dapur') {
            // Pastikan Dapur punya akses ke order ini
            $hasAccess = $order->orderItems()->whereHas('menu', function($q) use ($user) {
                $q->where('store_id', $user->store_id);
            })->exists();

            if (!$hasAccess) {
                abort(403, 'Pesanan ini tidak mengandung item dari gerai Anda.');
            }

            // Load items TAPI difilter
            $order->load(['table', 'payment', 'orderItems' => function($q) use ($user) {
                $q->whereHas('menu', function($subQ) use ($user) {
                    $subQ->where('store_id', $user->store_id);
                })->with('menu');
            }]);
        } else {
            // Admin load semua
            $order->load(['orderItems.menu', 'table', 'payment']);
        }

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update Status Pesanan (Logika Tetap)
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|in:pending,processing,delivering,completed,canceled']);
        $newStatus = $request->input('status');
        $oldStatus = $order->status;

        if ($oldStatus === 'canceled' && $newStatus !== 'canceled') {
            return back()->with('error', "Pesanan sudah batal.");
        }

        DB::transaction(function () use ($order, $newStatus, $oldStatus) {
            $order->update(['status' => $newStatus]);

            if ($order->payment) {
                if ($newStatus === 'processing' && $oldStatus === 'pending') $order->payment->update(['status' => 'paid']);
                if ($newStatus === 'completed' && $order->payment->status !== 'paid') $order->payment->update(['status' => 'paid']);
            }

            if ($order->table_id) {
                $table = Table::find($order->table_id);
                if ($table) {
                    if (in_array($newStatus, ['completed', 'canceled'])) {
                        $otherActive = Order::where('table_id', $order->table_id)->whereIn('status', ['pending', 'processing', 'delivering'])->where('id', '!=', $order->id)->exists();
                        if (!$otherActive && $table->status === 'occupied') $table->update(['status' => 'available']);
                    } elseif (in_array($newStatus, ['pending', 'processing', 'delivering'])) {
                        if ($table->status === 'available') $table->update(['status' => 'occupied']);
                    }
                }
            }
        });

        return back()->with('success', "Status Pesanan #{$order->order_number} diperbarui.");
    }

    /**
     * Riwayat Pesanan
     * PERBAIKAN LOGIKA: Filter item di riwayat.
     */
    public function history(Request $request)
    {
        $period = $request->input('period', 'this_month');
        $startDate = Carbon::now()->startOfMonth()->startOfDay();
        $endDate = Carbon::now()->endOfMonth()->endOfDay();

        switch ($period) {
            case 'today': $startDate = Carbon::today()->startOfDay(); $endDate = Carbon::today()->endOfDay(); break;
            case 'yesterday': $startDate = Carbon::yesterday()->startOfDay(); $endDate = Carbon::yesterday()->endOfDay(); break;
            case 'this_week': $startDate = Carbon::now()->startOfWeek()->startOfDay(); $endDate = Carbon::now()->endOfWeek()->endOfDay(); break;
            case 'this_month': $startDate = Carbon::now()->startOfMonth()->startOfDay(); $endDate = Carbon::now()->endOfMonth()->endOfDay(); break;
            case 'custom': if ($request->filled('start_date') && $request->filled('end_date')) { $startDate = Carbon::parse($request->start_date)->startOfDay(); $endDate = Carbon::parse($request->end_date)->endOfDay(); } break;
        }

        $user = Auth::user();
        $isDapur = $user->role === 'dapur';
        $storeId = $isDapur ? $user->store_id : null;

        // Eager Load Default
        $eagerLoads = ['table', 'payment'];

        // Query Dasar
        $queryCanceled = Order::where('status', 'canceled')->whereBetween('created_at', [$startDate, $endDate]);
        $queryCompleted = Order::where('status', 'completed')->whereBetween('created_at', [$startDate, $endDate]);

        if ($isDapur) {
            // Filter Orders
            $filterOrders = function($q) use ($storeId) {
                $q->whereHas('orderItems.menu', function($subQ) use ($storeId) {
                    $subQ->where('store_id', $storeId);
                });
            };
            $queryCanceled->where($filterOrders);
            $queryCompleted->where($filterOrders);

            // Filter Items inside Orders (Constraining Eager Load)
            $itemFilter = function($q) use ($storeId) {
                $q->whereHas('menu', function($subQ) use ($storeId) {
                    $subQ->where('store_id', $storeId);
                })->with('menu');
            };

            // Masukkan filter item ke eager load
            $queryCanceled->with(array_merge($eagerLoads, ['orderItems' => $itemFilter]));
            $queryCompleted->with(array_merge($eagerLoads, ['orderItems' => $itemFilter]));

        } else {
            // Admin Load Semua
            $queryCanceled->with(array_merge($eagerLoads, ['orderItems.menu']));
            $queryCompleted->with(array_merge($eagerLoads, ['orderItems.menu']));
        }

        $canceledOrders = $queryCanceled->orderBy('created_at', 'desc')->get();
        $completedOrders = $queryCompleted->orderBy('updated_at', 'desc')->get();

        return view('admin.orders.history', compact('canceledOrders', 'completedOrders'));
    }

    // --- API NOTIFIKASI (Filter item juga) ---
    public function checkNewOrders() {
        $query = Order::whereIn('status', ['pending', 'processing']);
        if (Auth::user()->role === 'dapur') {
            $query->whereHas('orderItems.menu', function($q) {
                $q->where('store_id', Auth::user()->store_id);
            });
        }
        $c = $query->count();
        return response()->json(['new_count' => $c, 'has_new' => $c > 0]);
    }

    public function getNotificationDetails() {
        $query = Order::whereIn('status', ['pending', 'processing']);
        if (Auth::user()->role === 'dapur') {
            $query->whereHas('orderItems.menu', function($q) {
                $q->where('store_id', Auth::user()->store_id);
            });
        }
        $count = $query->count();
        $orders = $query->with('table')->orderBy('created_at', 'desc')->take(5)->get();
        $notifs = $orders->map(function ($o) {
            return ['id' => $o->id, 'order_number' => $o->order_number, 'table_name' => $o->table->name ?? '-', 'status' => $o->status, 'time_ago' => $o->created_at->diffForHumans(), 'url' => route('admin.orders.index')];
        });
        return response()->json(['notifications' => $notifs, 'count' => $count]);
    }

    public function printStruk(Order $order) {
        $user = Auth::user();
        if ($user->role === 'dapur') {
            $order->load(['table', 'payment', 'orderItems' => function($q) use ($user) {
                $q->whereHas('menu', function($subQ) use ($user) {
                    $subQ->where('store_id', $user->store_id);
                })->with('menu');
            }]);
        } else {
            $order->load(['orderItems.menu', 'payment', 'table']);
        }
        return view('admin.orders.struk_print', compact('order'));
    }
}
