@extends('layouts.admin')

@section('title', 'Detail Pesanan #' . $order->order_number)

@section('content')

    {{-- HEADER WITH ACTIONS --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4 fade-in-up">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.orders.index') }}" class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:text-indigo-600 hover:border-indigo-200 transition-colors shadow-sm">
                    <i class="fas fa-arrow-left text-sm"></i>
                </a>
                <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Detail Pesanan</h2>
            </div>
            <p class="text-sm text-gray-500 mt-1 ml-11">Audit lengkap dan rincian transaksi #{{ $order->order_number }}.</p>
        </div>

        @if ($order->status !== 'canceled' && $order->status !== 'pending')
            <a href="{{ route('admin.order.print', $order->id) }}" target="_blank" onclick="window.open(this.href, 'Struk', 'width=350,height=600'); return false;"
               class="px-4 py-2 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200 flex items-center gap-2 transform hover:-translate-y-0.5">
                <i class="fas fa-print"></i> Cetak Struk
            </a>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 fade-in-up" style="animation-delay: 0.1s;">

        {{-- KOLOM KIRI: STATUS & ITEM (2/3 width) --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- 1. STATUS CARD --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative overflow-hidden">
                @php
                    $statusColor = match ($order->status) {
                        'pending' => 'amber',
                        'processing' => 'indigo',
                        'delivering' => 'blue',
                        'completed' => 'emerald',
                        'canceled' => 'rose',
                        default => 'gray',
                    };
                @endphp

                {{-- Status Bar Top --}}
                <div class="absolute top-0 left-0 w-full h-1.5 bg-{{ $statusColor }}-500"></div>

                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-2xl bg-{{ $statusColor }}-50 flex items-center justify-center text-{{ $statusColor }}-600">
                            <i class="fas {{ $order->status == 'completed' ? 'fa-check-circle' : ($order->status == 'canceled' ? 'fa-times-circle' : 'fa-clock') }} text-3xl"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Status Saat Ini</p>
                            <h3 class="text-2xl font-bold text-gray-800 capitalize">{{ $order->status }}</h3>
                            <p class="text-xs text-gray-500 mt-1">{{ $order->created_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    {{-- Action Form --}}
                    @if (!in_array($order->status, ['completed', 'canceled']))
                        <div class="w-full md:w-auto">
                            <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Update Status</label>
                                <div class="flex gap-2">
                                    <div class="relative">
                                        <select name="status" class="appearance-none bg-gray-50 border border-gray-200 text-gray-700 py-2.5 pl-4 pr-10 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent font-medium cursor-pointer hover:bg-gray-100 transition-colors">
                                            <option value="" disabled selected>Pilih Aksi...</option>
                                            @if ($order->status === 'pending')
                                                <option value="processing">⚙️ Proses (Bayar)</option>
                                                <option value="canceled">❌ Batalkan</option>
                                            @elseif ($order->status === 'processing')
                                                <option value="delivering">🍽️ Siap Diantar</option>
                                                <option value="completed">✅ Selesai</option>
                                                <option value="canceled">❌ Batalkan</option>
                                            @elseif ($order->status === 'delivering')
                                                <option value="completed">✅ Selesai</option>
                                                <option value="processing">⚠️ Kembali ke Dapur</option>
                                            @endif
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-gray-500">
                                            <i class="fas fa-chevron-down text-xs"></i>
                                        </div>
                                    </div>
                                    <button type="submit" class="bg-gray-800 text-white px-4 rounded-xl hover:bg-gray-900 transition-colors shadow-md">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 2. ORDER ITEMS --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/30">
                    <h3 class="text-lg font-bold text-gray-800">Rincian Menu</h3>
                    <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-lg text-xs font-bold border border-gray-200">
                        {{ $order->orderItems->count() }} Item
                    </span>
                </div>

                <div class="p-6">
                    <ul class="space-y-6">
                        @foreach ($order->orderItems as $item)
                            <li class="flex justify-between items-start group">
                                <div class="flex items-start gap-4">
                                    <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 font-bold text-sm border border-indigo-100 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                        {{ $item->quantity }}x
                                    </span>
                                    <div>
                                        <p class="font-bold text-gray-800 text-base">{{ $item->menu?->name ?? 'Menu Dihapus' }}</p>
                                        @if ($item->notes)
                                            <div class="flex items-center gap-1 mt-1 text-xs text-amber-600 bg-amber-50 px-2 py-1 rounded w-fit border border-amber-100">
                                                <i class="fas fa-pen"></i> {{ $item->notes }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-gray-800">Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</p>
                                    <p class="text-xs text-gray-400">@ Rp{{ number_format($item->price, 0, ',', '.') }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    {{-- Divider Total --}}
                    <div class="mt-8 pt-6 border-t border-dashed border-gray-200">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 font-medium">Total Pembayaran</span>
                            <span class="text-2xl font-extrabold text-indigo-600">Rp{{ number_format($order->total_price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. CATATAN ORDER --}}
            @if ($order->notes)
                <div class="bg-amber-50 rounded-2xl border border-amber-100 p-5 flex gap-4">
                    <i class="fas fa-sticky-note text-amber-400 text-xl mt-1"></i>
                    <div>
                        <h4 class="text-sm font-bold text-amber-800 uppercase tracking-wide mb-1">Catatan Pesanan</h4>
                        <p class="text-amber-900 text-sm leading-relaxed">"{{ $order->notes }}"</p>
                    </div>
                </div>
            @endif

        </div>

        {{-- KOLOM KANAN: INFO SIDEBAR (1/3 width) --}}
        <div class="lg:col-span-1 space-y-6">

            {{-- 1. INFO CUSTOMER --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-6">Informasi Pelanggan</h4>

                <div class="space-y-5">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Nama</p>
                            <p class="text-sm font-bold text-gray-800">{{ $order->customer_name ?? 'Anonim' }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500">
                            <i class="fas fa-chair"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Meja</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-bold bg-gray-800 text-white">
                                {{ $order->table->name ?? '-' }}
                            </span>
                        </div>
                    </div>

                    @if ($order->phone_number)
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Kontak</p>
                            <p class="text-sm font-bold text-gray-800">{{ $order->phone_number }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- 2. INFO PEMBAYARAN --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-6">Detail Pembayaran</h4>

                <div class="space-y-5">
                    <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                        <span class="text-sm text-gray-500">Metode</span>
                        <span class="text-sm font-bold text-gray-800">{{ ucfirst($order->payment->method ?? 'Tunai') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Status Bayar</span>
                        @php
                            $paymentStatus = $order->payment->status ?? 'unpaid';
                            $payColor = $paymentStatus == 'paid' ? 'emerald' : 'rose';
                        @endphp
                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-{{ $payColor }}-50 text-{{ $payColor }}-600 border border-{{ $payColor }}-100 uppercase">
                            {{ $paymentStatus }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- 3. AUDIT LOG --}}
            <div class="bg-gray-50 rounded-2xl border border-gray-200 p-6">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Jejak Waktu</h4>
                <div class="space-y-3">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Dibuat</span>
                        <span class="font-mono text-gray-700">{{ $order->created_at->format('d/m H:i') }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Update Terakhir</span>
                        <span class="font-mono text-gray-700">{{ $order->updated_at->format('d/m H:i') }}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection
