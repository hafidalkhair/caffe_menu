@extends('layouts.admin')

@section('title', 'Pesanan Masuk')

@section('content')

    {{-- HEADER & FILTER SECTION --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-6 gap-4 fade-in-up">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Antrian Pesanan</h2>
            <p class="text-sm text-slate-500 mt-1">
                <span class="flex items-center gap-2">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    Live Monitoring (Auto-Refresh 5d)
                </span>
            </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            {{-- Quick Stats --}}
            <div class="flex gap-2">
                <div class="px-4 py-2 bg-white rounded-xl border border-slate-200 shadow-sm flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></div>
                    <span class="text-xs font-bold text-slate-600">
                        {{ $activeOrders->where('status', 'pending')->count() }} Menunggu
                    </span>
                </div>
                <div class="px-4 py-2 bg-white rounded-xl border border-slate-200 shadow-sm flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-indigo-500"></div>
                    <span class="text-xs font-bold text-slate-600">
                        {{ $activeOrders->where('status', 'processing')->count() }} Proses
                    </span>
                </div>
            </div>

            {{-- FILTER MEJA --}}
            <form action="{{ route('admin.orders.index') }}" method="GET" class="flex-1 sm:flex-none">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-filter text-slate-400 text-xs"></i>
                    </div>
                    <select name="table_id" onchange="this.form.submit()"
                        class="pl-8 pr-8 py-2 w-full sm:w-48 bg-white border border-slate-200 text-slate-700 text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 shadow-sm cursor-pointer font-bold appearance-none outline-none">
                        <option value="">Semua Meja</option>
                        @foreach ($tables as $t)
                            <option value="{{ $t->id }}" {{ request('table_id') == $t->id ? 'selected' : '' }}>
                                {{ $t->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm fade-in-up">
            <i class="fas fa-check-circle"></i>
            <span class="font-medium text-sm">{{ session('success') }}</span>
        </div>
    @endif

    {{-- CONTAINER UTAMA (ID ini penting untuk Script AJAX) --}}
    <div id="order-list-container">
        <div id="update-target">
            @if ($activeOrders->isEmpty())
                <div class="flex flex-col items-center justify-center p-12 text-center bg-white rounded-2xl shadow-sm border border-slate-200 fade-in-up">
                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 text-slate-300">
                        <i class="fas fa-mug-hot text-2xl"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-700">Tidak ada pesanan aktif</h3>
                    <p class="text-xs text-slate-400 mt-1">
                        {{ request('table_id') ? 'Meja ini bersih.' : 'Dapur sedang santai.' }}
                    </p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($activeOrders as $order)
                        @php
                            // Config Warna Status
                            $statusConfig = match ($order->status) {
                                'pending' => ['bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'text' => 'text-amber-700', 'icon' => 'fa-clock'],
                                'processing' => ['bg' => 'bg-white', 'border' => 'border-indigo-100', 'text' => 'text-indigo-700', 'icon' => 'fa-fire-burner'],
                                'delivering' => ['bg' => 'bg-white', 'border' => 'border-blue-100', 'text' => 'text-blue-700', 'icon' => 'fa-bell-concierge'],
                                default => ['bg' => 'bg-slate-50', 'border' => 'border-slate-200', 'text' => 'text-slate-700', 'icon' => 'fa-circle'],
                            };

                            // LOGIKA HITUNG HARGA (PERBAIKAN UTAMA)
                            $displayTotal = $order->total_price;
                            if (auth()->user()->role === 'dapur') {
                                // Hitung manual dari item yang ada di list (karena itemnya sudah difilter controller)
                                $displayTotal = $order->orderItems->sum(function($item) {
                                    return $item->price * $item->quantity;
                                });
                            }
                        @endphp

                        <div class="group relative {{ $statusConfig['bg'] }} border {{ $statusConfig['border'] }} rounded-xl p-4 transition-all hover:shadow-md flex flex-col md:flex-row items-start md:items-center justify-between gap-4 fade-in-up">
                            {{-- Status Bar Indikator --}}
                            <div class="absolute left-0 top-4 bottom-4 w-1 rounded-r-full {{ str_replace('text', 'bg', $statusConfig['text']) }}"></div>

                            {{-- Info Kiri: Status & Identitas --}}
                            <div class="flex items-center gap-4 pl-3">
                                <div class="w-10 h-10 rounded-lg {{ str_replace('text', 'bg', $statusConfig['text']) }} bg-opacity-10 flex items-center justify-center {{ $statusConfig['text'] }}">
                                    <i class="fas {{ $statusConfig['icon'] }}"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-bold text-slate-800 uppercase tracking-tight">#{{ $order->order_number }}</h3>
                                        <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded {{ str_replace('text', 'bg', $statusConfig['text']) }} bg-opacity-10 {{ $statusConfig['text'] }}">
                                            {{ $order->status }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-slate-500 mt-1 flex items-center gap-2">
                                        <span class="font-bold text-slate-700 bg-slate-100 px-1.5 rounded">Meja {{ $order->table->name ?? '-' }}</span>
                                        <span>•</span>
                                        <span class="font-medium">{{ $order->customer_name }}</span>
                                        <span>•</span>
                                        <span class="font-mono text-slate-400">{{ $order->created_at->format('H:i') }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Info Kanan: Harga & Aksi --}}
                            <div class="flex items-center gap-4 w-full md:w-auto justify-between md:justify-end">
                                <div class="text-right">
                                    {{-- TAMPILKAN HARGA YANG SUDAH DIHITUNG DI ATAS --}}
                                    <p class="font-bold text-slate-800 text-lg">Rp{{ number_format($displayTotal, 0, ',', '.') }}</p>
                                    <p class="text-[10px] text-slate-400 font-medium uppercase tracking-wide">
                                        {{ $order->payment->method ?? 'Cash' }} • {{ $order->payment->status ?? 'Unpaid' }}
                                    </p>
                                </div>

                                <div class="flex items-center gap-2">
                                    {{-- Tombol Detail --}}
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="w-9 h-9 flex items-center justify-center bg-white border border-slate-200 rounded-lg text-slate-400 hover:text-indigo-600 hover:border-indigo-200 transition-all shadow-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    {{-- Tombol Aksi Cepat (Quick Action) --}}
                                    @if($order->status == 'pending')
                                        <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="processing">
                                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 transition-colors shadow-md shadow-indigo-200">
                                                Proses
                                            </button>
                                        </form>
                                    @elseif($order->status == 'processing')
                                        <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="delivering">
                                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-xs font-bold rounded-lg hover:bg-blue-700 transition-colors shadow-md shadow-blue-200">
                                                Antar
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fungsi Polling untuk Auto Refresh data tanpa reload halaman
        function fetchOrders() {
            fetch(window.location.href, {
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Refresh failed');
                return response.text();
            })
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.getElementById('update-target');
                const currentContainer = document.getElementById('order-list-container');

                if (newContent && currentContainer) {
                    currentContainer.innerHTML = newContent.outerHTML;
                }
            })
            .catch(error => console.warn('Realtime update paused (network error/tab inactive)'));
        }

        // Jalankan setiap 5 detik
        setInterval(fetchOrders, 5000);
    });
</script>
@endpush
