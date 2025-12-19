@extends('layouts.customer')

@section('title', 'Bayar Online')

@section('content')
<div class="min-h-[80vh] flex flex-col items-center justify-center px-4 py-10 relative overflow-hidden">

    {{-- Background Glow (Indigo for Tech/Payment Feel) --}}
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-indigo-600/20 rounded-full blur-[120px] pointer-events-none"></div>

    {{-- MAIN CARD --}}
    <div class="relative w-full max-w-md bg-[#121212] border border-indigo-500/30 rounded-3xl p-6 md:p-8 shadow-2xl backdrop-blur-xl animate-fade-in-up">

        {{-- Header --}}
        <div class="text-center mb-6">
            <div class="w-16 h-16 mx-auto bg-gradient-to-br from-indigo-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/30 mb-4 transform rotate-3">
                <i class="fas fa-wallet text-2xl text-white"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-white">Konfirmasi & Bayar</h1>
            <p class="text-xs text-indigo-300 flex items-center justify-center gap-1 mt-1">
                <i class="fas fa-lock text-[10px]"></i> Transaksi Aman via Midtrans
            </p>
        </div>

        {{-- Order Summary (Receipt Style) --}}
        <div class="bg-zinc-900/60 rounded-xl border border-zinc-800 overflow-hidden mb-6">
            <div class="px-4 py-3 bg-zinc-800/80 border-b border-zinc-700 flex justify-between items-center">
                <span class="text-xs font-bold text-zinc-400 uppercase tracking-wider">Rincian Pesanan</span>
                <span class="text-xs font-mono text-zinc-500">#{{ $order->order_number }}</span>
            </div>

            {{-- Scrollable List --}}
            <div class="max-h-48 overflow-y-auto px-4 py-3 space-y-3 custom-scrollbar">
                @php $orderItems = $order->orderItems ?? []; @endphp

                @forelse ($orderItems as $item)
                    <div class="flex justify-between items-start text-sm">
                        <div class="flex gap-3">
                            <span class="font-bold text-indigo-400 min-w-[20px]">{{ $item->quantity }}x</span>
                            <span class="text-zinc-300 line-clamp-2">{{ $item->menu?->name ?? 'Item Menu' }}</span>
                        </div>
                        <span class="text-white font-medium whitespace-nowrap">
                            Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                        </span>
                    </div>
                @empty
                    <p class="text-center text-zinc-500 text-sm py-2">Item tidak ditemukan.</p>
                @endforelse
            </div>

            {{-- Total Section --}}
            <div class="px-4 py-4 bg-indigo-900/20 border-t border-indigo-500/20 flex justify-between items-center">
                <span class="text-sm font-medium text-indigo-200">Total Bayar</span>
                <span class="text-2xl font-extrabold text-white">
                    Rp{{ number_format($order->total_price, 0, ',', '.') }}
                </span>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="space-y-3">
            {{-- Pay Button --}}
            <button id="pay-button"
                class="w-full relative overflow-hidden group bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-500 hover:to-blue-500 text-white font-bold py-4 rounded-xl shadow-lg shadow-indigo-600/30 transition-all duration-300 transform hover:-translate-y-0.5">
                <span class="relative z-10 flex items-center justify-center gap-2">
                    <i class="fas fa-credit-card"></i> Bayar Sekarang
                </span>
                {{-- Shine Effect --}}
                <div class="absolute top-0 -left-[100%] w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent skew-x-12 group-hover:animate-shine"></div>
            </button>

            {{-- Cancel Button --}}
            <a href="{{ route('customer.order.cancel-online', $order) }}"
               class="block w-full text-center py-3 text-sm text-zinc-500 hover:text-red-400 transition-colors font-medium border border-transparent hover:border-red-500/20 rounded-xl hover:bg-red-500/10">
                <i class="fas fa-times mr-1"></i> Batalkan Pesanan
            </a>
        </div>

    </div>
</div>

<style>
    /* Custom Scrollbar untuk area rincian */
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 4px; }

    @keyframes shine {
        100% { left: 200%; }
    }
    .group-hover\:animate-shine:hover {
        animation: shine 1s;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.6s ease-out forwards;
    }
</style>
@endsection

@push('scripts')
{{-- (Scripts Midtrans Snap) --}}
<script type="text/javascript"
    src="https://app.sandbox.midtrans.com/snap/snap.js"
    data-client-key="{{ config('midtrans.client_key') }}"></script>

<script>
window.addEventListener('load', function() {
    const snapToken = "{{ $snapToken }}";
    const payButton = document.getElementById('pay-button');

    if (!snapToken) {
        console.error("Midtrans Error: Snap Token tidak ditemukan.");
        if (payButton) {
            payButton.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Sistem Error';
            payButton.classList.add('bg-zinc-700', 'cursor-not-allowed', 'opacity-50');
            payButton.classList.remove('bg-gradient-to-r');
            payButton.disabled = true;
        }
        return;
    }

    if (payButton) {
        payButton.addEventListener('click', function () {
            // Visual feedback saat diklik
            const originalText = payButton.innerHTML;
            payButton.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Memproses...';
            payButton.disabled = true;

            if (typeof snap === 'undefined') {
                 console.error("Midtrans Snap object is not defined yet.");
                 alert("Gagal memuat sistem pembayaran. Coba refresh halaman.");
                 payButton.innerHTML = originalText;
                 payButton.disabled = false;
                 return;
            }

            snap.pay(snapToken, {
                onSuccess: function (result) {
                    console.log("Pembayaran sukses:", result);
                    window.location.href = "{{ route('customer.order.update-success', $order->id) }}";
                },
                onPending: function (result) {
                    console.log("Menunggu pembayaran:", result);
                    // Tidak reload, user mungkin mau ganti metode di popup
                    payButton.innerHTML = originalText;
                    payButton.disabled = false;
                },
                onError: function (result) {
                    console.error("Terjadi kesalahan:", result);
                    alert('Terjadi kesalahan saat memproses pembayaran.');
                    payButton.innerHTML = originalText;
                    payButton.disabled = false;
                },
                onClose: function () {
                    // Reset tombol jika popup ditutup
                    payButton.innerHTML = originalText;
                    payButton.disabled = false;
                }
            });
        });
    }
});
</script>
@endpush
