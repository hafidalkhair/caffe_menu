@extends('layouts.admin')

@section('title', 'Riwayat Pesanan')

@section('content')

    {{-- HEADER SECTION --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4 fade-in-up">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Riwayat Transaksi</h2>
            <p class="text-sm text-slate-500 mt-1">Arsip lengkap pesanan yang telah selesai atau dibatalkan.</p>
        </div>
    </div>

    {{-- FILTER CARD --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 mb-8 fade-in-up" style="animation-delay: 0.1s;">
        <form action="{{ route('admin.orders.history') }}" method="GET"
              x-data="{ period: '{{ request('period', 'this_month') }}' }"
              class="flex flex-col md:flex-row items-end gap-4">

            {{-- Filter Preset --}}
            <div class="w-full md:w-auto relative">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Periode Cepat</label>
                <div class="relative">
                    <select name="period" x-model="period" @change="if(period !== 'custom') $el.closest('form').submit()"
                            class="w-full md:w-56 pl-4 pr-10 py-2.5 rounded-xl border-slate-200 bg-slate-50 text-slate-700 text-sm font-bold focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all cursor-pointer appearance-none outline-none">
                        <option value="today">Hari Ini</option>
                        <option value="yesterday">Kemarin</option>
                        <option value="this_week">Minggu Ini</option>
                        <option value="this_month">Bulan Ini</option>
                        <option value="custom">Custom Tanggal</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-slate-400">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </div>
                </div>
            </div>

            {{-- Custom Date Range --}}
            <div x-show="period === 'custom'" x-transition class="flex flex-col md:flex-row gap-4 w-full md:w-auto items-end">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Dari</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                           class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-700 text-sm font-medium focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 py-2.5 px-3 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Sampai</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                           class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-700 text-sm font-medium focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 py-2.5 px-3 outline-none">
                </div>
                <button type="submit" class="w-full md:w-auto px-6 py-2.5 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition-all shadow-md shadow-indigo-200 flex items-center justify-center gap-2">
                    <i class="fas fa-filter text-xs"></i> Terapkan
                </button>
            </div>
        </form>
    </div>

    {{-- TABS NAVIGATION --}}
    <div x-data="{ activeTab: 'completed' }" class="fade-in-up" style="animation-delay: 0.2s;">

        {{-- Tabs Header --}}
        <div class="flex space-x-1 rounded-xl bg-slate-100 p-1 mb-6 max-w-md border border-slate-200">
            <button @click="activeTab = 'completed'"
                :class="activeTab === 'completed' ? 'bg-white text-emerald-700 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50'"
                class="w-full rounded-lg py-2.5 text-sm font-bold leading-5 transition-all duration-200 flex items-center justify-center gap-2">
                <i class="fas fa-check-circle"></i> Selesai ({{ $completedOrders->count() }})
            </button>
            <button @click="activeTab = 'canceled'"
                :class="activeTab === 'canceled' ? 'bg-white text-rose-700 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50'"
                class="w-full rounded-lg py-2.5 text-sm font-bold leading-5 transition-all duration-200 flex items-center justify-center gap-2">
                <i class="fas fa-times-circle"></i> Dibatalkan ({{ $canceledOrders->count() }})
            </button>
        </div>

        {{-- TAB CONTENT: COMPLETED --}}
        <div x-show="activeTab === 'completed'"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0">

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                @if ($completedOrders->isEmpty())
                    <div class="p-16 text-center">
                        <div class="inline-block p-4 rounded-full bg-slate-50 text-slate-300 mb-4 border border-slate-100">
                            <i class="fas fa-clipboard-check text-4xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800">Tidak ada data</h3>
                        <p class="text-slate-500 text-sm mt-1">Belum ada pesanan selesai pada periode ini.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-emerald-50/30">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-emerald-800 uppercase tracking-wider w-32">Order ID</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-emerald-800 uppercase tracking-wider">Waktu Selesai</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-emerald-800 uppercase tracking-wider">Pelanggan</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-emerald-800 uppercase tracking-wider">Meja</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-emerald-800 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-emerald-800 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 bg-white">
                                @foreach ($completedOrders as $order)
                                    @php
                                        // LOGIKA TOTAL HARGA (MULTI-TENANT)
                                        $displayTotal = $order->total_price;
                                        if (auth()->user()->role === 'dapur') {
                                            $displayTotal = $order->orderItems->sum(function($item) {
                                                return $item->price * $item->quantity;
                                            });
                                        }
                                    @endphp
                                    <tr class="hover:bg-emerald-50/20 transition-colors group">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="font-mono text-sm font-bold text-slate-700 bg-slate-100 px-2 py-1 rounded">#{{ $order->order_number }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                            {{ $order->updated_at->format('d M Y, H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-slate-800">{{ $order->customer_name }}</div>
                                            <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wide">{{ ucfirst($order->payment->method ?? 'Cash') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="px-2.5 py-1 rounded-md bg-slate-100 text-slate-600 text-xs font-bold border border-slate-200">
                                                {{ $order->table->name ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <span class="text-sm font-extrabold text-emerald-600">Rp{{ number_format($displayTotal, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <a href="{{ route('admin.orders.show', $order->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 border border-transparent hover:border-indigo-100 transition-all">
                                                <i class="fas fa-eye text-sm"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- TAB CONTENT: CANCELED --}}
        <div x-show="activeTab === 'canceled'"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             style="display: none;">

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                @if ($canceledOrders->isEmpty())
                    <div class="p-16 text-center">
                        <div class="inline-block p-4 rounded-full bg-slate-50 text-slate-300 mb-4 border border-slate-100">
                            <i class="fas fa-folder-open text-4xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800">Bersih!</h3>
                        <p class="text-slate-500 text-sm mt-1">Tidak ada riwayat pembatalan pada periode ini.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-rose-50/30">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-rose-800 uppercase tracking-wider w-32">Order ID</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-rose-800 uppercase tracking-wider">Waktu Batal</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-rose-800 uppercase tracking-wider">Pelanggan</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-rose-800 uppercase tracking-wider">Meja</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-rose-800 uppercase tracking-wider">Nilai Hilang</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-rose-800 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 bg-white">
                                @foreach ($canceledOrders as $order)
                                    @php
                                        // LOGIKA TOTAL HARGA (MULTI-TENANT)
                                        $displayTotal = $order->total_price;
                                        if (auth()->user()->role === 'dapur') {
                                            $displayTotal = $order->orderItems->sum(function($item) {
                                                return $item->price * $item->quantity;
                                            });
                                        }
                                    @endphp
                                    <tr class="hover:bg-rose-50/20 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="font-mono text-sm font-bold text-slate-400 line-through bg-slate-50 px-2 py-1 rounded">#{{ $order->order_number }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                            {{ $order->updated_at->format('d M Y, H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-slate-700">{{ $order->customer_name }}</div>
                                            <div class="text-[10px] text-rose-500 font-bold uppercase tracking-wide">Dibatalkan</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="px-2.5 py-1 rounded-md bg-slate-100 text-slate-400 text-xs font-bold border border-slate-200">
                                                {{ $order->table->name ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <span class="text-sm font-bold text-rose-500">Rp{{ number_format($displayTotal, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <a href="{{ route('admin.orders.show', $order->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-slate-800 hover:bg-slate-100 border border-transparent hover:border-slate-200 transition-all">
                                                <i class="fas fa-eye text-sm"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

    </div>

@endsection
