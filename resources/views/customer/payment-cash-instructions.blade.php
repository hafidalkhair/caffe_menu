@extends('layouts.customer')

@section('title', 'Menunggu Pembayaran')

@section('content')
<div class="min-h-[80vh] flex flex-col items-center justify-center px-4 py-8 relative overflow-hidden">

    {{-- DEKORASI BACKGROUND --}}
    <div class="absolute top-0 right-0 w-64 h-64 bg-brand-cream/20 rounded-full blur-[80px] -z-10 pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-64 h-64 bg-brand-taupe/10 rounded-full blur-[80px] -z-10 pointer-events-none"></div>

    {{-- CONTAINER UTAMA --}}
    <div class="relative w-full max-w-sm bg-white border border-brand-taupe/10 rounded-[2.5rem] p-8 shadow-float animate-fade-in-up overflow-hidden">

        {{-- Dekorasi Lubang Tiket (Visual Illusion) --}}
        <div class="absolute -left-3 top-1/2 -mt-4 w-6 h-6 bg-[#FAF9F6] rounded-full z-20"></div>
        <div class="absolute -right-3 top-1/2 -mt-4 w-6 h-6 bg-[#FAF9F6] rounded-full z-20"></div>

        {{-- === KONDISI 1: PENDING (Tunai) === --}}
        @if ($order->status === 'pending')

            <div id="pending-message-section" class="text-center relative z-10">

                {{-- Animated Icon (Cash Register) --}}
                <div class="relative mb-6 mx-auto w-20 h-20">
                    <div class="absolute inset-0 bg-brand-berry/20 rounded-full animate-ping opacity-75"></div>
                    <div class="relative w-full h-full bg-gradient-to-br from-brand-dark to-brand-berry rounded-full flex items-center justify-center shadow-lg shadow-brand-dark/30">
                        <i class="fas fa-cash-register text-3xl text-white"></i>
                    </div>
                </div>

                <h1 class="text-xl font-bold text-brand-dark mb-2 tracking-tight uppercase leading-none">
                    Pay at <br> <span class="text-brand-berry">Cashier</span>
                </h1>
                <p class="text-brand-taupe text-[10px] mb-8 leading-relaxed font-bold tracking-wide max-w-[250px] mx-auto uppercase">
                    Show this to the cashier.
                </p>

                {{-- TIKET NOMOR PESANAN (UKURAN DIPERBAIKI) --}}
                <div class="bg-brand-surface border-2 border-dashed border-brand-taupe/20 rounded-2xl p-6 mb-8 relative group hover:border-brand-berry/30 transition-colors cursor-default">

                    <p class="text-[9px] font-bold text-brand-taupe uppercase tracking-[0.25em] mb-2 group-hover:text-brand-berry transition-colors">Order Number</p>

                    {{--
                        PERBAIKAN UTAMA:
                        text-2xl (24px) untuk HP -> Pas, tidak meledak.
                        md:text-3xl (30px) untuk Laptop -> Jelas dan elegan.
                        tracking-widest -> Memberi jarak antar huruf agar mewah.
                    --}}
                    <h2 class="text-2xl md:text-3xl font-black text-brand-dark tracking-widest leading-none group-hover:scale-105 transition-transform duration-300">
                        #{{ $order->order_number }}
                    </h2>

                    <div class="mt-4 pt-4 border-t border-brand-taupe/10 flex justify-between items-center">
                        <span class="text-[9px] font-bold text-brand-taupe uppercase tracking-widest">Total Bill</span>
                        <span class="text-base font-bold text-brand-berry">Rp{{ number_format($order->total_price, 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- COUNTDOWN TIMER --}}
                <div class="mb-8 flex flex-col items-center">
                    <p class="text-[9px] font-bold text-red-500 uppercase tracking-widest mb-2 flex items-center gap-1 animate-pulse">
                        <i class="far fa-clock"></i> Expires In
                    </p>
                    <div class="px-5 py-1.5 bg-red-50 text-red-600 font-mono text-lg font-bold rounded-xl border border-red-100 shadow-sm">
                        <span id="countdown">Loading...</span>
                    </div>
                </div>

                {{-- Tombol Kembali --}}
                <a href="{{ route('customer.menu.index') }}"
                    class="block w-full py-3.5 rounded-xl bg-transparent text-brand-taupe font-bold border border-brand-taupe/20 hover:bg-brand-surface hover:text-brand-dark hover:border-brand-dark transition-all text-[10px] uppercase tracking-[0.15em]">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Menu
                </a>
            </div>

        {{-- === KONDISI 2: SUKSES/DIPROSES === --}}
        @elseif ($order->status === 'processing' || $order->status === 'delivering' || $order->status === 'completed')

            <div class="text-center py-12">
                <div class="w-20 h-20 mx-auto bg-brand-surface rounded-full flex items-center justify-center mb-6 shadow-inner">
                    <i class="fas fa-circle-notch fa-spin text-3xl text-brand-berry"></i>
                </div>
                <h1 class="text-xl font-bold text-brand-dark mb-2 uppercase tracking-wide">Payment Received!</h1>
                <p class="text-brand-taupe text-xs font-medium tracking-wide">Please wait a moment...</p>
            </div>

            <script>
                setTimeout(() => {
                    window.location.href = "{{ route('customer.order.online-confirmed', $order) }}";
                }, 1500);
            </script>

        {{-- === KONDISI 3: DIBATALKAN === --}}
        @elseif ($order->status === 'canceled')

            <div id="canceled-message-section" class="text-center py-6">
                <div class="w-24 h-24 mx-auto bg-red-50 rounded-full flex items-center justify-center mb-6 shadow-inner">
                    <i class="fas fa-times text-4xl text-red-500"></i>
                </div>

                <h1 class="text-xl font-bold text-brand-dark mb-2 uppercase tracking-wide">Order Canceled</h1>
                <p class="text-brand-taupe text-xs mb-8 leading-relaxed font-medium">
                    Payment time expired for order <br> <span class="font-bold text-brand-dark">#{{ $order->order_number }}</span>.
                </p>

                <a href="{{ route('customer.menu.index') }}"
                    class="block w-full bg-brand-dark text-white font-bold py-4 rounded-2xl shadow-lg shadow-brand-dark/20 hover:bg-brand-berry hover:-translate-y-1 transition-all uppercase tracking-[0.15em] text-xs">
                    Order Again
                </a>
            </div>

        @endif

    </div>
</div>

{{-- === STYLE ANIMASI MASUK === --}}
@push('styles')
<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
</style>
@endpush

{{-- === SCRIPT LOGIKA (TIDAK BERUBAH) === --}}
@if ($order->status === 'pending')
    <script>
        const EXPIRATION_TIME = {{ $expirationTimestamp ?? 0 }};
        const countdownDisplay = document.getElementById('countdown');
        const checkUrl = "{{ route('customer.order.status.check', $order) }}";
        const TARGET_STATUS = 'processing';

        let pollingInterval;

        const countdownInterval = setInterval(() => {
            const now = new Date().getTime();
            const distance = EXPIRATION_TIME - now;

            if (distance <= 0) {
                clearInterval(countdownInterval);
                clearInterval(pollingInterval);
                window.location.reload();
            } else {
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                if(countdownDisplay) {
                    countdownDisplay.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                }
            }
        }, 1000);

        pollingInterval = setInterval(async () => {
            try {
                const response = await fetch(checkUrl);
                const data = await response.json();
                if (data.order_status === TARGET_STATUS || data.payment_status === 'paid' || data.order_status === 'canceled') {
                    clearInterval(countdownInterval);
                    clearInterval(pollingInterval);
                    window.location.reload();
                }
            } catch (error) { console.error("Check failed"); }
        }, 3000);
    </script>
@endif

@endsection
