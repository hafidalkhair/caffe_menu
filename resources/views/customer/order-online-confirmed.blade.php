@extends('layouts.customer')

@section('title', 'Pembayaran Berhasil')

@section('content')
    <div class="min-h-[80vh] flex flex-col items-center justify-center px-4 pb-20 relative">

        {{-- SUCCESS CARD --}}
        <div class="w-full max-w-sm bg-white rounded-[2.5rem] p-8 shadow-float border border-brand-taupe/10 relative overflow-hidden text-center animate-fade-in-up">

            {{-- Dekorasi Latar --}}
            <div class="absolute top-0 right-0 w-32 h-32 bg-brand-surface rounded-bl-full -mr-10 -mt-10 z-0"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-brand-cream/20 rounded-tr-full -ml-8 -mb-8 z-0"></div>

            {{-- Content Wrapper --}}
            <div class="relative z-10">

                {{-- Icon Animasi (Checkmark) --}}
                <div class="mb-8 relative inline-block">
                    <div class="w-20 h-20 bg-gradient-primary rounded-2xl rotate-3 flex items-center justify-center mx-auto shadow-lg shadow-brand-berry/30 animate-bounce-slow">
                        <div class="-rotate-3">
                            <i class="fas fa-check text-3xl text-white"></i>
                        </div>
                    </div>
                    {{-- Dekorasi Confetti (Brand Colors) --}}
                    <div class="absolute -top-2 -right-2 w-3 h-3 bg-brand-cream rounded-full animate-ping opacity-75"></div>
                    <div class="absolute bottom-0 -left-2 w-2 h-2 bg-brand-taupe rounded-full animate-ping opacity-75 delay-100"></div>
                </div>

                {{-- Judul & Pesan --}}
                <h1 class="text-2xl font-bold text-brand-dark mb-3 tracking-tight uppercase leading-none">
                    Payment <br> <span class="text-brand-berry">Successful</span>
                </h1>
                <p class="text-brand-taupe text-xs font-medium mb-8 leading-relaxed max-w-[260px] mx-auto">
                    Terima kasih! Pesanan kamu sudah terkonfirmasi dan sedang disiapkan oleh barista kami.
                </p>

                {{-- TIKET NOMOR PESANAN (FIX SIZE) --}}
                <div class="bg-brand-surface rounded-2xl p-6 mb-8 border border-brand-taupe/20 border-dashed relative group cursor-default">
                    {{-- Cutout effect kiri kanan --}}
                    <div class="absolute -left-2 top-1/2 -mt-1.5 w-3 h-3 bg-white rounded-full border-r border-brand-taupe/20"></div>
                    <div class="absolute -right-2 top-1/2 -mt-1.5 w-3 h-3 bg-white rounded-full border-l border-brand-taupe/20"></div>

                    <p class="text-[10px] font-bold text-brand-taupe uppercase tracking-[0.2em] mb-2">Order Number</p>

                    {{-- PERBAIKAN UKURAN FONT DISINI --}}
                    {{-- Sebelumnya: text-2xl md:text-5xl (Terlalu Besar) --}}
                    {{-- Sekarang: text-2xl md:text-3xl (Pas & Elegan) --}}
                    <h2 class="text-2xl md:text-3xl font-bold text-brand-dark tracking-widest">
                        #{{ $order->order_number }}
                    </h2>
                </div>

                {{-- Tombol Aksi --}}
                <div class="space-y-4">
                    {{-- Primary: Lacak Pesanan --}}
                    <a href="{{ route('customer.track.index') }}"
                        class="block w-full py-4 bg-brand-dark text-white text-xs font-bold rounded-2xl shadow-lg shadow-brand-dark/20 hover:bg-gradient-primary hover:shadow-float hover:-translate-y-1 transition-all duration-300 uppercase tracking-[0.15em] group">
                        Track Order Status <i class="fas fa-arrow-right ml-2 text-[10px] group-hover:translate-x-1 transition-transform"></i>
                    </a>

                    {{-- Secondary: Kembali ke Menu --}}
                    <a href="{{ route('customer.menu.index') }}"
                        class="block w-full py-4 bg-transparent text-brand-taupe text-xs font-bold rounded-2xl border border-brand-taupe/20 hover:bg-brand-surface hover:text-brand-dark hover:border-brand-dark transition-all uppercase tracking-[0.15em]">
                        Order Again
                    </a>
                </div>
            </div>

        </div>

        {{-- Footer Kecil --}}
        <p class="mt-8 text-[10px] text-brand-taupe/60 font-bold uppercase tracking-widest flex items-center gap-2">
            <i class="fas fa-mug-hot text-brand-berry animate-pulse"></i>
            Aigle Solitaire Experience
        </p>
    </div>

    {{-- Style Tambahan untuk Animasi --}}
    @push('scripts')
        <style>
            @keyframes bounce-slow {
                0%, 100% { transform: translateY(-3%) rotate(3deg); }
                50% { transform: translateY(3%) rotate(3deg); }
            }
            .animate-bounce-slow {
                animation: bounce-slow 3s infinite ease-in-out;
            }
            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .animate-fade-in-up {
                animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }
        </style>
    @endpush
@endsection
