@extends('layouts.customer')

@section('title', 'Lacak Pesanan')

@section('content')
<div class="min-h-[75vh] flex flex-col items-center justify-center relative overflow-hidden pb-20">

    {{-- BACKGROUND DECORATION --}}
    <div class="absolute top-0 right-0 w-80 h-80 bg-brand-cream/20 rounded-full blur-[100px] -z-10 pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-80 h-80 bg-brand-taupe/10 rounded-full blur-[100px] -z-10 pointer-events-none"></div>

    {{-- HEADER SECTION --}}
    <div class="text-center mb-10 relative z-10 animate-fade-in-up">
        <div class="w-20 h-20 mx-auto bg-gradient-to-br from-brand-dark to-brand-berry rounded-[1.5rem] flex items-center justify-center shadow-float mb-6 transform -rotate-3 border-4 border-white">
            <i class="fas fa-search-location text-3xl text-brand-cream"></i>
        </div>

        <h1 class="text-3xl md:text-4xl font-bold text-brand-dark mb-2 tracking-tight uppercase leading-none">
            Cek Status <br> <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-berry to-brand-taupe">Pesanan</span>
        </h1>
        <p class="text-brand-taupe text-[10px] md:text-xs font-bold max-w-xs mx-auto tracking-widest uppercase mt-3">
            Masukkan nomor WhatsApp yang terdaftar.
        </p>
    </div>

    {{-- FORM PENCARIAN --}}
    <div class="w-full max-w-md relative z-10 mb-10 animate-fade-in-up" style="animation-delay: 0.1s;">
        <form action="{{ route('customer.track.order') }}" method="POST" class="relative group">
            @csrf

            <div class="relative shadow-soft rounded-[2rem] bg-white transition-all duration-300 group-focus-within:shadow-float group-focus-within:-translate-y-1 p-2">
                <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                    <i class="fab fa-whatsapp text-brand-taupe text-xl group-focus-within:text-brand-berry transition-colors"></i>
                </div>

                <input type="tel" name="phone_number" placeholder="Contoh: 08123456789" required value="{{ old('phone_number') }}"
                    class="block w-full pl-14 pr-32 py-4 bg-brand-surface border border-transparent rounded-[1.5rem] text-brand-dark placeholder-brand-taupe/40 focus:outline-none focus:bg-white focus:border-brand-taupe/20 transition-all font-bold tracking-widest text-lg">

                <button type="submit" class="absolute right-3 top-3 bottom-3 bg-brand-dark hover:bg-brand-berry text-white font-bold px-6 rounded-2xl transition-all duration-300 flex items-center gap-2 shadow-md group-active:scale-95">
                    <span class="hidden sm:inline text-[10px] uppercase tracking-widest">Cari</span>
                    <i class="fas fa-arrow-right text-xs"></i>
                </button>
            </div>

            @error('phone_number')
                <div class="flex items-center gap-2 mt-3 text-brand-berry text-[10px] font-bold px-4 animate-pulse">
                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                </div>
            @enderror
        </form>
    </div>

    {{-- HASIL PENCARIAN --}}
    @if(isset($order))
        <div class="w-full max-w-md animate-fade-in-up" style="animation-delay: 0.2s;">
            @if($order)
                @php
                    // Konfigurasi Status (Bahasa Indonesia & Warna Aigle)
                    $statusConfig = [
                        'pending'    => ['bg' => 'bg-brand-surface', 'text' => 'text-brand-taupe', 'border' => 'border-brand-taupe/20', 'icon' => 'fa-clock', 'label' => 'Menunggu Pembayaran', 'desc' => 'Mohon segera selesaikan pembayaran di kasir atau via QRIS.'],
                        'processing' => ['bg' => 'bg-brand-surface',   'text' => 'text-brand-berry',   'border' => 'border-brand-berry/20',   'icon' => 'fa-fire-burner', 'label' => 'Sedang Disiapkan', 'desc' => 'Barista kami sedang meracik pesananmu dengan sepenuh hati.'],
                        'delivering' => ['bg' => 'bg-brand-surface', 'text' => 'text-brand-dark', 'border' => 'border-brand-dark/20', 'icon' => 'fa-bell-concierge', 'label' => 'Siap Disajikan', 'desc' => 'Pesananmu sudah siap untuk diantar atau diambil.'],
                        'completed'  => ['bg' => 'bg-brand-surface',  'text' => 'text-green-700',  'border' => 'border-green-200',  'icon' => 'fa-check-circle', 'label' => 'Selesai', 'desc' => 'Terima kasih sudah berkunjung!'],
                        'canceled'   => ['bg' => 'bg-brand-surface',    'text' => 'text-red-700',    'border' => 'border-red-200',    'icon' => 'fa-times-circle', 'label' => 'Dibatalkan', 'desc' => 'Pesanan ini telah dibatalkan.'],
                    ];

                    $status = $statusConfig[$order->status] ?? $statusConfig['pending'];

                    // Override jika status pending tapi sudah lunas (Tunai/Online)
                    if($order->status == 'pending' && optional($order->payment)->status == 'paid') {
                         $status = $statusConfig['processing'];
                    }
                @endphp

                {{-- KARTU STATUS (Tiket Digital) --}}
                <div class="bg-white rounded-[2.5rem] p-8 shadow-float border border-brand-taupe/10 relative overflow-hidden group">

                    {{-- Dekorasi Abstrak --}}
                    <div class="absolute top-0 right-0 w-40 h-40 bg-brand-cream/30 rounded-full blur-3xl -mr-10 -mt-10 pointer-events-none"></div>

                    {{-- Header Info --}}
                    <div class="grid grid-cols-2 gap-4 mb-8 pb-6 border-b border-brand-taupe/10 relative z-10">
                        {{-- Kiri --}}
                        <div>
                            <p class="text-[9px] font-bold text-brand-taupe uppercase tracking-[0.2em] mb-1">Nomor Order</p>
                            <p class="text-2xl md:text-3xl font-bold text-brand-dark tracking-tight">#{{ $order->order_number }}</p>
                        </div>
                        {{-- Kanan --}}
                        <div class="text-right">
                            <p class="text-[9px] font-bold text-brand-taupe uppercase tracking-[0.2em] mb-1">Total</p>
                            <p class="text-lg md:text-xl font-bold text-brand-berry">Rp{{ number_format($order->total_price, 0, ',', '.') }}</p>
                        </div>
                        {{-- Meja (Tambahan Penting) --}}
                         <div class="col-span-2 mt-2">
                            <span class="inline-flex items-center gap-2 px-3 py-1 bg-brand-surface rounded-lg border border-brand-taupe/10">
                                <i class="fas fa-chair text-brand-taupe text-xs"></i>
                                <span class="text-[10px] font-bold text-brand-dark uppercase tracking-wider">Meja {{ $order->table->name ?? '-' }}</span>
                            </span>
                        </div>
                    </div>

                    {{-- Status Body --}}
                    <div class="flex items-start gap-5 mb-8 relative z-10">
                        <div class="w-14 h-14 rounded-2xl {{ $status['bg'] }} border {{ $status['border'] }} {{ $status['text'] }} flex items-center justify-center flex-shrink-0 shadow-sm">
                            <i class="fas {{ $status['icon'] }} text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold {{ $status['text'] }} uppercase tracking-wide leading-none mb-2">{{ $status['label'] }}</h3>
                            <p class="text-xs text-brand-taupe font-medium leading-relaxed">{{ $status['desc'] }}</p>
                            <p class="text-[10px] text-brand-taupe/60 mt-3 flex items-center gap-1.5 uppercase tracking-wider font-bold">
                                <i class="far fa-clock"></i> {{ $order->created_at->format('d M Y, H:i') }}
                            </p>
                        </div>
                    </div>

                    {{-- Tombol Bayar (Jika Pending) --}}
                    @if($order->status == 'pending')
                        <a href="{{ route('customer.payment.cash', ['order' => $order->id]) }}"
                           class="relative z-10 block w-full text-center bg-brand-dark text-white font-bold py-4 rounded-2xl shadow-lg hover:bg-brand-berry hover:-translate-y-1 transition-all duration-300 uppercase tracking-[0.15em] text-xs">
                            Bayar Sekarang
                        </a>
                    @endif
                </div>

                {{-- === PENJELASAN & TOMBOL TAMBAH PESANAN (MODERN CARD) === --}}
                @if($order->status !== 'canceled')
                    <div class="mt-6 bg-brand-surface/50 border border-brand-taupe/10 rounded-[2rem] p-6 text-center animate-fade-in-up" style="animation-delay: 0.3s;">

                        <div class="mb-4">
                            <h3 class="text-sm font-bold text-brand-dark uppercase tracking-widest mb-1">Masih Kurang?</h3>
                            <p class="text-[10px] text-brand-taupe font-medium leading-relaxed max-w-[250px] mx-auto">
                                Kamu bisa menambah menu baru ke <strong class="text-brand-berry">Meja {{ $order->table->name ?? '' }}</strong> tanpa perlu mengisi ulang nama dan nomor HP.
                            </p>
                        </div>

                        <button type="button" id="btn-add-order"
                            class="w-full inline-flex items-center justify-center gap-3 px-6 py-3 bg-white text-brand-dark font-bold text-xs uppercase tracking-widest rounded-2xl border border-brand-taupe/20 shadow-sm hover:border-brand-dark hover:bg-brand-dark hover:text-white transition-all group">
                            <span class="w-6 h-6 rounded-full bg-brand-surface text-brand-dark flex items-center justify-center group-hover:bg-white/20 group-hover:text-white transition-colors">
                                <i class="fas fa-plus text-[10px]"></i>
                            </span>
                            Tambah Pesanan Baru
                        </button>
                    </div>
                @endif

                {{-- JS Session Saver --}}
                <script>
                    document.getElementById('btn-add-order')?.addEventListener('click', function() {
                        // Simpan data user ke LocalStorage agar auto-fill di Checkout
                        const userData = {
                            name: "{{ $order->customer_name }}",
                            phone: "{{ $order->phone_number }}",
                            table: "{{ $order->table->name ?? '' }}"
                        };
                        localStorage.setItem('cafe_session', JSON.stringify(userData));

                        // Redirect ke Menu
                        window.location.href = "{{ route('customer.menu.index') }}";
                    });
                </script>

            @else
                {{-- STATE: TIDAK DITEMUKAN --}}
                <div class="bg-white border border-brand-taupe/10 rounded-[2.5rem] p-10 text-center shadow-soft animate-shake">
                    <div class="w-24 h-24 mx-auto bg-brand-surface rounded-full flex items-center justify-center mb-6 text-brand-taupe/40">
                        <i class="fas fa-search text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-brand-dark mb-2 uppercase tracking-wide">Pesanan Tidak Ditemukan</h3>
                    <p class="text-brand-taupe text-xs font-medium max-w-[220px] mx-auto leading-relaxed">
                        Cek kembali nomor WhatsApp yang kamu masukkan. Pastikan sesuai dengan saat pemesanan.
                    </p>
                </div>
            @endif
        </div>
    @endif
</div>

@push('styles')
<style>
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in-up { animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    @keyframes shake { 0%, 100% { transform: translateX(0); } 10%, 30%, 50%, 70%, 90% { transform: translateX(-4px); } 20%, 40%, 60%, 80% { transform: translateX(4px); } }
    .animate-shake { animation: shake 0.4s ease-in-out; }
</style>
@endpush

@endsection
