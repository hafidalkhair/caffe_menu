@extends('layouts.customer')

@section('title', 'Pesanan Saya: #' . $order->order_number)

@section('content')

    <div class="container mx-auto px-4 py-8">

        <div class="flex items-center justify-between mb-8">
            <h1 class="text-4xl font-extrabold text-amber-400">
                <i class="fas fa-receipt mr-2"></i> Status Pesanan Anda
            </h1>

            {{-- Tombol untuk Pesan Lagi (Hanya jika pesanan sudah selesai atau dibatalkan) --}}
            @if ($order->status === 'completed' || $order->status === 'canceled')
                <form action="{{ route('customer.order.reorder', $order) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-amber-400 text-zinc-900 px-6 py-2 rounded-full font-bold hover:bg-amber-300 transition-transform transform hover:scale-105 shadow-lg">
                        <i class="fas fa-redo-alt mr-1"></i> Pesan Lagi Item Ini
                    </button>
                </form>
            @endif
        </div>

        {{-- STATUS HEADER --}}
        @php
            $status = $order->status;
            $isPaid = $order->payment?->status === 'paid';
            $statusClass = match ($status) {
                'completed' => 'bg-green-600',
                'processing', 'delivering' => 'bg-indigo-600',
                'pending', 'initial' => 'bg-amber-600',
                'canceled' => 'bg-red-600',
                default => 'bg-gray-600',
            };
        @endphp

        <div class="bg-zinc-800 rounded-xl shadow-2xl p-6 mb-8 border border-amber-400/20">
            <div class="grid grid-cols-3 gap-6">

                {{-- KODE PESANAN --}}
                <div>
                    <p class="text-sm font-semibold text-zinc-400">Kode Pesanan</p>
                    <h2 class="text-3xl font-extrabold text-white mt-1">#{{ $order->order_number }}</h2>
                </div>

                {{-- STATUS SAAT INI --}}
                <div>
                    <p class="text-sm font-semibold text-zinc-400">Status</p>
                    <span class="inline-block px-4 py-1 rounded-full text-white font-bold mt-1 {{ $statusClass }}">
                        {{ strtoupper($status) }}
                    </span>
                </div>

                {{-- WAKTU PESANAN --}}
                <div>
                    <p class="text-sm font-semibold text-zinc-400">Waktu Masuk</p>
                    <p class="text-white font-bold mt-1">{{ $order->created_at->format('d M, H:i') }}</p>
                    <p class="text-xs text-zinc-500">{{ $order->created_at->diffForHumans() }}</p>
                </div>
            </div>
        </div>

        {{-- DETAIL UTAMA: Rincian Item dan Total --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Kolom Kiri (2/3): Detail Item --}}
            <div class="lg:col-span-2 bg-zinc-800 p-6 rounded-lg shadow-xl border border-amber-400/20">
                <h3 class="text-xl font-bold text-indigo-400 mb-4 border-b border-zinc-700 pb-2">
                    Rincian Item Pesanan
                </h3>

                <ul class="space-y-4">
                    @forelse ($order->orderItems as $item)
                        <li class="flex justify-between items-center border-b border-zinc-700/50 pb-3">
                            <div class="flex items-start">
                                <span class="inline-block w-6 h-6 rounded-full bg-amber-400 text-zinc-900 text-xs font-bold mr-3 flex items-center justify-center flex-shrink-0">
                                    {{ $item->quantity }}
                                </span>
                                <div>
                                    <p class="font-medium text-white text-base">{{ $item->menu?->name ?? 'Menu Hilang' }}</p>
                                    @if ($item->notes)
                                        <p class="text-xs text-zinc-500 italic mt-0.5">Catatan: {{ $item->notes }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-green-400">Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</p>
                                <p class="text-xs text-zinc-500">@ Rp{{ number_format($item->price, 0, ',', '.') }}</p>
                            </div>
                        </li>
                    @empty
                        <li class="text-red-400 text-center py-4">Tidak ada item ditemukan dalam pesanan ini.</li>
                    @endforelse
                </ul>

                <div class="mt-6 pt-4 border-t border-zinc-700">
                    <div class="flex justify-between items-center text-xl font-bold">
                        <span class="text-white">TOTAL AKHIR</span>
                        <span class="text-yellow-400">Rp{{ number_format($order->total_price, 0, ',', '.') }}</span>
                    </div>
                </div>

                @if ($order->notes)
                    <div class="mt-4 p-3 bg-zinc-900 rounded-lg border border-zinc-700">
                        <p class="font-bold text-zinc-400">Catatan Khusus:</p>
                        <p class="text-sm text-amber-400">{{ $order->notes }}</p>
                    </div>
                @endif
            </div>

            {{-- Kolom Kanan (1/3): Info Tambahan --}}
            <div class="lg:col-span-1 space-y-6">

                {{-- Info Pelanggan & Meja --}}
                <div class="bg-zinc-800 p-6 rounded-lg shadow-xl border border-amber-400/20">
                    <h3 class="text-xl font-bold text-indigo-400 mb-4 border-b border-zinc-700 pb-2">Detail Pengguna</h3>
                    <div class="space-y-3 text-zinc-300">
                        <p><strong>Nama:</strong> {{ $order->customer_name ?? 'Anonim' }}</p>
                        <p><strong>Meja:</strong> <span class="text-amber-400 font-bold">{{ $order->table->name ?? '-' }}</span></p>
                        @if ($order->phone_number)
                            <p><strong>Telepon:</strong> <span class="text-green-400">{{ $order->phone_number }}</span></p>
                        @endif
                    </div>
                </div>

                {{-- Detail Pembayaran --}}
                <div class="bg-zinc-800 p-6 rounded-lg shadow-xl border border-amber-400/20">
                    <h3 class="text-xl font-bold text-indigo-400 mb-4 border-b border-zinc-700 pb-2">Pembayaran</h3>
                    <div class="space-y-3 text-zinc-300">
                        <p><strong>Metode:</strong> <span class="font-bold">{{ ucfirst($order->payment->method ?? 'Tunai') }}</span></p>
                        <p><strong>Status Bayar:</strong>
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold
                                {{ $isPaid ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                                {{ strtoupper($order->payment?->status ?? 'UNPAID') }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
