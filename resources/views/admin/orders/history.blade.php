@extends('layouts.admin')

@section('title', 'Riwayat Pesanan')

@section('content')

    {{-- HEADER SECTION --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4 fade-in-up">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Riwayat Transaksi</h2>
            <p class="text-sm text-gray-500 mt-1">Arsip lengkap pesanan yang telah selesai atau dibatalkan.</p>
        </div>
    </div>

    {{-- FILTER CARD --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8 fade-in-up" style="animation-delay: 0.1s;">
        {{-- x-data initializes the 'period' variable. We use the request helper to set the default value. --}}
        <form action="{{ route('admin.orders.history') }}" method="GET"
              x-data="{ period: '{{ request('period', 'this_month') }}' }"
              class="flex flex-col md:flex-row items-end gap-4">

            {{-- Filter Preset --}}
            <div class="w-full md:w-auto">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Periode Cepat</label>
                {{-- x-model binds the select value to the Alpine 'period' variable --}}
                {{-- @change submits the form only if 'custom' is NOT selected --}}
                <select name="period" x-model="period" @change="if(period !== 'custom') $el.closest('form').submit()"
                        class="w-full md:w-48 pl-4 pr-10 py-2.5 rounded-xl border-gray-200 bg-gray-50 text-gray-700 text-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all cursor-pointer">
                    <option value="today">Hari Ini</option>
                    <option value="yesterday">Kemarin</option>
                    <option value="this_week">Minggu Ini</option>
                    <option value="this_month">Bulan Ini</option>
                    <option value="custom">Custom Tanggal</option>
                </select>
            </div>

            {{-- Custom Date Range (Visible only when period is 'custom') --}}
            <div x-show="period === 'custom'" x-transition class="flex flex-col md:flex-row gap-4 w-full md:w-auto">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Dari</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                           class="w-full rounded-xl border-gray-200 bg-gray-50 text-gray-700 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Sampai</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                           class="w-full rounded-xl border-gray-200 bg-gray-50 text-gray-700 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                </div>
                <div>
                    <label class="block text-xs font-bold text-transparent uppercase tracking-wider mb-2">Action</label>
                    <button type="submit" class="w-full md:w-auto px-6 py-2.5 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition-colors shadow-md flex items-center gap-2">
                        <i class="fas fa-filter"></i> Terapkan
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- TABS NAVIGATION --}}
    <div x-data="{ activeTab: 'completed' }" class="fade-in-up" style="animation-delay: 0.2s;">

        {{-- Tabs Header --}}
        <div class="flex space-x-1 rounded-xl bg-gray-200/50 p-1 mb-6 max-w-md">
            <button @click="activeTab = 'completed'"
                :class="activeTab === 'completed' ? 'bg-white text-emerald-700 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                class="w-full rounded-lg py-2.5 text-sm font-bold leading-5 transition-all duration-200">
                <i class="fas fa-check-circle mr-2"></i> Selesai ({{ $completedOrders->count() }})
            </button>
            <button @click="activeTab = 'canceled'"
                :class="activeTab === 'canceled' ? 'bg-white text-rose-700 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                class="w-full rounded-lg py-2.5 text-sm font-bold leading-5 transition-all duration-200">
                <i class="fas fa-times-circle mr-2"></i> Dibatalkan ({{ $canceledOrders->count() }})
            </button>
        </div>

        {{-- TAB CONTENT: COMPLETED --}}
        <div x-show="activeTab === 'completed'"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0">

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                @if ($completedOrders->isEmpty())
                    <div class="p-12 text-center">
                        <div class="inline-block p-4 rounded-full bg-emerald-50 text-emerald-300 mb-4">
                            <i class="fas fa-clipboard-check text-4xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Tidak ada data</h3>
                        <p class="text-gray-500 text-sm mt-1">Tidak ada pesanan selesai pada periode ini.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-emerald-50/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-emerald-800 uppercase tracking-wider">Order ID</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-emerald-800 uppercase tracking-wider">Waktu Selesai</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-emerald-800 uppercase tracking-wider">Pelanggan</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-emerald-800 uppercase tracking-wider">Meja</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-emerald-800 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-emerald-800 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 bg-white">
                                @foreach ($completedOrders as $order)
                                    <tr class="hover:bg-emerald-50/30 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="font-mono text-sm font-bold text-gray-700">#{{ $order->order_number }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $order->updated_at->format('d M Y, H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-gray-800">{{ $order->customer_name }}</div>
                                            <div class="text-xs text-gray-400">{{ ucfirst($order->payment->method ?? '-') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="px-2.5 py-1 rounded-md bg-gray-100 text-gray-600 text-xs font-bold border border-gray-200">
                                                {{ $order->table->name ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <span class="text-sm font-extrabold text-emerald-600">Rp{{ number_format($order->total_price, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <a href="{{ route('admin.orders.show', $order->id) }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-xs border border-indigo-200 hover:bg-indigo-50 px-3 py-1.5 rounded-lg transition-colors">
                                                Detail
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

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                @if ($canceledOrders->isEmpty())
                    <div class="p-12 text-center">
                        <div class="inline-block p-4 rounded-full bg-rose-50 text-rose-300 mb-4">
                            <i class="fas fa-folder-open text-4xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Bersih!</h3>
                        <p class="text-gray-500 text-sm mt-1">Tidak ada riwayat pembatalan pada periode ini.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-rose-50/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-rose-800 uppercase tracking-wider">Order ID</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-rose-800 uppercase tracking-wider">Waktu Batal</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-rose-800 uppercase tracking-wider">Pelanggan</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-rose-800 uppercase tracking-wider">Meja</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-rose-800 uppercase tracking-wider">Nilai Hilang</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-rose-800 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 bg-white">
                                @foreach ($canceledOrders as $order)
                                    <tr class="hover:bg-rose-50/30 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="font-mono text-sm font-bold text-gray-500 line-through">#{{ $order->order_number }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $order->updated_at->format('d M Y, H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-gray-600">{{ $order->customer_name }}</div>
                                            <div class="text-xs text-rose-400">Dibatalkan</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="px-2.5 py-1 rounded-md bg-gray-100 text-gray-400 text-xs font-bold border border-gray-200">
                                                {{ $order->table->name ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <span class="text-sm font-bold text-rose-600">Rp{{ number_format($order->total_price, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <a href="{{ route('admin.orders.show', $order->id) }}" class="text-gray-600 hover:text-gray-800 font-medium text-xs border border-gray-200 hover:bg-gray-50 px-3 py-1.5 rounded-lg transition-colors">
                                                Detail
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
