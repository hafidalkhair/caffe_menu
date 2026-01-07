@extends('layouts.admin')

@section('title', 'Detail Pesanan #' . $order->order_number)

@section('content')

    {{-- HEADER WITH ACTIONS --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4 fade-in-up">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.orders.index') }}" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-indigo-600 hover:border-indigo-200 transition-colors shadow-sm">
                    <i class="fas fa-arrow-left text-sm"></i>
                </a>
                <div>
                    <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Detail Pesanan</h2>
                    <p class="text-xs text-slate-500 font-medium">ID Transaksi: #{{ $order->order_number }}</p>
                </div>
            </div>
        </div>

        @if ($order->status !== 'canceled' && $order->status !== 'pending')
            {{-- PERBAIKAN: HANYA ADMIN YANG BISA LIHAT TOMBOL CETAK --}}
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.order.print', $order->id) }}" target="_blank" onclick="window.open(this.href, 'Struk', 'width=350,height=600'); return false;"
                   class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200 flex items-center gap-2 transform hover:-translate-y-0.5">
                    <i class="fas fa-print"></i> Cetak Struk
                </a>
            @endif
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 fade-in-up" style="animation-delay: 0.1s;">

        {{-- KOLOM KIRI: STATUS & ITEM (2/3 width) --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- 1. STATUS CARD --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 relative overflow-hidden">
                @php
                    $statusConfig = match ($order->status) {
                        'pending' => ['color' => 'amber', 'icon' => 'fa-clock', 'label' => 'Menunggu Konfirmasi'],
                        'processing' => ['color' => 'indigo', 'icon' => 'fa-fire-burner', 'label' => 'Sedang Diproses'],
                        'delivering' => ['color' => 'blue', 'icon' => 'fa-bell-concierge', 'label' => 'Siap Diantar'],
                        'completed' => ['color' => 'emerald', 'icon' => 'fa-check-circle', 'label' => 'Selesai'],
                        'canceled' => ['color' => 'rose', 'icon' => 'fa-times-circle', 'label' => 'Dibatalkan'],
                        default => ['color' => 'slate', 'icon' => 'fa-question', 'label' => 'Tidak Diketahui'],
                    };
                    $color = $statusConfig['color'];
                @endphp

                {{-- Status Bar Top --}}
                <div class="absolute top-0 left-0 w-full h-1 bg-{{ $color }}-500"></div>

                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-{{ $color }}-50 flex items-center justify-center text-{{ $color }}-600 shadow-sm border border-{{ $color }}-100">
                            <i class="fas {{ $statusConfig['icon'] }} text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Status Saat Ini</p>
                            <h3 class="text-xl font-bold text-slate-800">{{ $statusConfig['label'] }}</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Diupdate {{ $order->updated_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    {{-- Action Form (Dropdown Status) --}}
                    @if (!in_array($order->status, ['completed', 'canceled']))
                        <div class="w-full md:w-auto">
                            <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Update Status Pesanan</label>
                                <div class="flex gap-2">
                                    <div class="relative">
                                        <select name="status" class="appearance-none bg-slate-50 border border-slate-200 text-slate-700 py-2.5 pl-4 pr-10 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent font-bold text-sm cursor-pointer hover:bg-slate-100 transition-colors w-full md:w-48">
                                            <option value="" disabled selected>Pilih Aksi...</option>
                                            @if ($order->status === 'pending')
                                                <option value="processing">⚙️ Proses Pesanan</option>
                                                <option value="canceled">❌ Tolak / Batalkan</option>
                                            @elseif ($order->status === 'processing')
                                                <option value="delivering">🍽️ Pesanan Siap</option>
                                                <option value="completed">✅ Selesaikan</option>
                                                <option value="canceled">❌ Batalkan</option>
                                            @elseif ($order->status === 'delivering')
                                                <option value="completed">✅ Selesai (Bayar)</option>
                                                <option value="processing">⚠️ Kembali ke Dapur</option>
                                            @endif
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-slate-400">
                                            <i class="fas fa-chevron-down text-xs"></i>
                                        </div>
                                    </div>
                                    <button type="submit" class="bg-slate-800 text-white w-10 rounded-xl hover:bg-slate-900 transition-colors shadow-md flex items-center justify-center">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 2. ORDER ITEMS --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="text-base font-bold text-slate-800">Rincian Menu</h3>
                    <span class="px-3 py-1 bg-white text-slate-600 rounded-lg text-xs font-bold border border-slate-200 shadow-sm">
                        {{ $order->orderItems->count() }} Item
                    </span>
                </div>

                <div class="p-6">
                    <ul class="space-y-6">
                        @foreach ($order->orderItems as $item)
                            <li class="flex justify-between items-start group">
                                <div class="flex items-start gap-4">
                                    {{-- Qty Badge --}}
                                    <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 font-bold text-sm border border-indigo-100 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                        {{ $item->quantity }}x
                                    </span>

                                    {{-- Detail Item --}}
                                    <div>
                                        <p class="font-bold text-slate-800 text-base">{{ $item->menu?->name ?? 'Menu Terhapus' }}</p>

                                        {{-- Catatan Item --}}
                                        @if ($item->notes)
                                            <div class="flex items-center gap-1 mt-1 text-xs text-amber-600 bg-amber-50 px-2 py-1 rounded w-fit border border-amber-100 font-medium">
                                                <i class="fas fa-pen text-[10px]"></i> {{ $item->notes }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Harga Per Item --}}
                                <div class="text-right">
                                    <p class="font-bold text-slate-800">Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</p>
                                    <p class="text-[10px] text-slate-400">@ Rp{{ number_format($item->price, 0, ',', '.') }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    {{-- PERHITUNGAN TOTAL (LOGIKA MULTI-TENANT) --}}
                    @php
                        $displayTotal = $order->total_price;
                        $labelTotal = 'Total Global';

                        // Jika user adalah Dapur, hitung ulang total berdasarkan item yang ada di list saja
                        if (auth()->user()->role === 'dapur') {
                            $displayTotal = $order->orderItems->sum(function($item) {
                                return $item->price * $item->quantity;
                            });
                            $labelTotal = 'Total (Gerai Ini)';
                        }
                    @endphp

                    <div class="mt-8 pt-6 border-t border-dashed border-slate-200">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 font-medium text-sm">{{ $labelTotal }}</span>
                            <span class="text-2xl font-extrabold text-indigo-600">Rp{{ number_format($displayTotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. CATATAN ORDER --}}
            @if ($order->notes)
                <div class="bg-amber-50 rounded-2xl border border-amber-100 p-5 flex gap-4">
                    <i class="fas fa-sticky-note text-amber-400 text-xl mt-0.5"></i>
                    <div>
                        <h4 class="text-xs font-bold text-amber-800 uppercase tracking-wide mb-1">Catatan Tambahan</h4>
                        <p class="text-amber-900 text-sm leading-relaxed font-medium">"{{ $order->notes }}"</p>
                    </div>
                </div>
            @endif

        </div>

        {{-- KOLOM KANAN: INFO SIDEBAR (1/3 width) --}}
        <div class="lg:col-span-1 space-y-6">

            {{-- 1. INFO CUSTOMER --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-6">Informasi Pelanggan</h4>

                <div class="space-y-5">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 border border-slate-100">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 uppercase font-bold">Nama</p>
                            <p class="text-sm font-bold text-slate-700">{{ $order->customer_name ?? 'Guest' }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 border border-slate-100">
                            <i class="fas fa-chair"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 uppercase font-bold">Lokasi</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-slate-800 text-white">
                                Meja {{ $order->table->name ?? '-' }}
                            </span>
                        </div>
                    </div>

                    @if ($order->phone_number)
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 border border-slate-100">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 uppercase font-bold">Kontak</p>
                            <p class="text-sm font-bold text-slate-700 font-mono">{{ $order->phone_number }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- 2. INFO PEMBAYARAN --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-6">Detail Pembayaran</h4>

                <div class="space-y-4">
                    <div class="flex justify-between items-center border-b border-slate-50 pb-3">
                        <span class="text-sm text-slate-500 font-medium">Metode</span>
                        <span class="text-sm font-bold text-slate-800 flex items-center gap-2">
                            <i class="fas {{ $order->payment->method == 'cash' ? 'fa-money-bill-wave' : 'fa-qrcode' }} text-slate-400"></i>
                            {{ ucfirst($order->payment->method ?? 'Tunai') }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-500 font-medium">Status</span>
                        @php
                            $paymentStatus = $order->payment->status ?? 'unpaid';
                            $payConfig = match($paymentStatus) {
                                'paid' => ['color' => 'emerald', 'label' => 'LUNAS'],
                                'unpaid' => ['color' => 'rose', 'label' => 'BELUM BAYAR'],
                                default => ['color' => 'slate', 'label' => $paymentStatus],
                            };
                        @endphp
                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-{{ $payConfig['color'] }}-50 text-{{ $payConfig['color'] }}-600 border border-{{ $payConfig['color'] }}-100 uppercase tracking-wide">
                            {{ $payConfig['label'] }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- 3. AUDIT LOG --}}
            <div class="bg-slate-50 rounded-2xl border border-slate-200 p-6">
                <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-4">Jejak Waktu</h4>
                <div class="space-y-3">
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500">Dibuat</span>
                        <span class="font-mono text-slate-700 font-medium">{{ $order->created_at->format('d/m/y H:i') }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500">Update Terakhir</span>
                        <span class="font-mono text-slate-700 font-medium">{{ $order->updated_at->format('d/m/y H:i') }}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection
