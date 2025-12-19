@extends('layouts.customer')

@section('title', 'My Cart')

@section('content')
<div class="max-w-lg mx-auto pb-32 px-4 pt-6">

    {{-- HEADER (Modern Aigle Header) --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-brand-dark tracking-tight uppercase leading-none">Your <br> <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-berry to-brand-taupe">Collection</span></h1>
            <p class="text-brand-taupe text-xs mt-1 font-medium tracking-wide uppercase">
                {{ session('cart') ? count(session('cart')) : 0 }} items ready to order
            </p>
        </div>

        {{-- Tombol Tambah Menu --}}
        <a href="{{ route('customer.menu.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-surface border border-brand-taupe/20 text-brand-dark text-[10px] font-bold uppercase tracking-widest rounded-full hover:bg-brand-dark hover:text-white transition-all group">
            <i class="fas fa-plus group-hover:rotate-90 transition-transform duration-300"></i> Add More
        </a>
    </div>

    @if (session('cart') && count(session('cart')) > 0)

        {{-- LIST ITEM --}}
        <div class="space-y-6">
            @foreach (session('cart') as $id => $details)
                {{-- CART CARD (Modern Horizontal) --}}
                <div class="cart-item group relative bg-white border border-transparent hover:border-brand-taupe/10 rounded-[2rem] p-4 flex gap-5 items-center shadow-soft transition-all hover:shadow-float"
                     data-id="{{ $id }}"
                     data-price="{{ $details['price'] }}">

                    {{-- Image --}}
                    <div class="w-24 h-24 flex-shrink-0 rounded-2xl overflow-hidden bg-brand-surface relative shadow-inner">
                        <img src="{{ strpos($details['image'], 'menu_images/') !== false
                                ? asset('storage/' . $details['image'])
                                : asset($details['image']) }}"
                            alt="{{ $details['name'] }}"
                            class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
                    </div>

                    {{-- Details --}}
                    <div class="flex-1 min-w-0 py-1">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="text-brand-dark font-bold text-sm leading-tight line-clamp-2 pr-2 uppercase tracking-wide">{{ $details['name'] }}</h3>
                                <p class="text-brand-berry font-bold text-xs mt-1 item-price-display">
                                    Rp{{ number_format($details['price'], 0, ',', '.') }}
                                </p>
                            </div>

                            {{-- Remove Button --}}
                            <form action="{{ route('customer.cart.remove') }}" method="POST">
                                @csrf
                                <input type="hidden" name="menu_id" value="{{ $id }}">
                                <button type="submit" class="w-8 h-8 flex items-center justify-center text-brand-taupe/50 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all active:scale-90">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                            </form>
                        </div>

                        {{-- Quantity Controls (Custom Style) --}}
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3 bg-brand-surface rounded-xl p-1 border border-brand-taupe/10">
                                {{-- Minus --}}
                                <button class="minus-btn w-7 h-7 rounded-lg bg-white text-brand-dark shadow-sm hover:bg-brand-dark hover:text-white flex items-center justify-center transition-all active:scale-90 disabled:opacity-50 border border-brand-taupe/10 hover:border-transparent">
                                    <i class="fas fa-minus text-[8px]"></i>
                                </button>

                                {{-- Display --}}
                                <span class="quantity-display text-brand-dark font-bold text-sm min-w-[20px] text-center">{{ $details['quantity'] }}</span>

                                {{-- Plus --}}
                                <button class="plus-btn w-7 h-7 rounded-lg bg-white text-brand-dark shadow-sm hover:bg-brand-dark hover:text-white flex items-center justify-center transition-all active:scale-90 border border-brand-taupe/10 hover:border-transparent">
                                    <i class="fas fa-plus text-[8px]"></i>
                                </button>
                            </div>

                            {{-- Subtotal Kecil --}}
                            <span class="text-[10px] font-bold text-brand-taupe bg-brand-surface px-2.5 py-1 rounded-lg border border-brand-taupe/10 item-subtotal tracking-wide">
                                Rp{{ number_format($details['price'] * $details['quantity'], 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- SUMMARY & CHECKOUT --}}
        <div class="mt-10 bg-white border border-brand-taupe/10 rounded-[2rem] p-8 shadow-float relative overflow-hidden">
            {{-- Dekorasi --}}
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-brand-cream/30 rounded-full blur-2xl"></div>

            <div class="relative z-10 flex justify-between items-end mb-8">
                <div>
                    <p class="text-[10px] font-bold text-brand-taupe uppercase tracking-[0.2em] mb-1">Total Bill</p>
                    <p class="text-[10px] text-brand-taupe/60 italic">Excluding tax (if applicable)</p>
                </div>
                <span id="total-price" class="text-3xl font-bold text-brand-dark tracking-tight">
                    Rp{{ number_format(array_sum(array_column(session('cart'), 'price', 'quantity')), 0, ',', '.') }}
                </span>
            </div>

            <a href="{{ route('customer.checkout') }}"
               class="relative z-10 block w-full text-center bg-brand-dark text-white font-bold text-sm py-5 rounded-2xl shadow-lg shadow-brand-dark/20 hover:bg-gradient-primary hover:shadow-float hover:-translate-y-1 transition-all duration-300 active:scale-[0.98] uppercase tracking-[0.15em] group">
                Proceed to Checkout <i class="fas fa-arrow-right ml-2 text-xs group-hover:translate-x-1 transition-transform"></i>
            </a>

            <p class="text-center text-[9px] text-brand-taupe mt-5 font-medium flex items-center justify-center gap-1.5 uppercase tracking-wider relative z-10">
                <i class="fas fa-shield-alt text-brand-berry"></i> Secure Transaction.
            </p>
        </div>

    @else
        {{-- EMPTY STATE (Clean Aigle Style) --}}
        <div class="flex flex-col items-center justify-center py-32 text-center animate-fade-in">
            <div class="w-24 h-24 bg-brand-surface rounded-full flex items-center justify-center mb-6 shadow-inner">
                <i class="fas fa-shopping-basket text-4xl text-brand-taupe/50"></i>
            </div>
            <h2 class="text-xl font-bold text-brand-dark mb-2 uppercase tracking-wide">Cart is Empty</h2>
            <p class="text-brand-taupe text-xs max-w-xs mx-auto mb-8 leading-relaxed tracking-wide">
                You haven't selected any items yet. Let's find your favorite coffee!
            </p>
            <a href="{{ route('customer.menu.index') }}"
               class="px-8 py-3 bg-brand-dark text-white font-bold text-xs uppercase tracking-[0.15em] rounded-full shadow-lg shadow-brand-dark/20 hover:bg-brand-berry hover:-translate-y-1 transition-all">
                Start Ordering
            </a>
        </div>
    @endif
</div>

{{-- JAVASCRIPT LOGIC (Tetap sama, hanya penyesuaian sedikit jika perlu) --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const plusButtons = document.querySelectorAll('.plus-btn');
        const minusButtons = document.querySelectorAll('.minus-btn');

        // Helper Format Rupiah
        const formatRupiah = (number) => {
            return 'Rp' + number.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        };

        const updateTotalPrice = () => {
            let totalPrice = 0;
            document.querySelectorAll('.cart-item').forEach(item => {
                const quantity = parseInt(item.querySelector('.quantity-display').textContent);
                const price = parseFloat(item.dataset.price);

                // Hitung total keseluruhan
                totalPrice += quantity * price;

                // Update subtotal per item
                const subtotalEl = item.querySelector('.item-subtotal');
                if (subtotalEl) {
                    subtotalEl.textContent = formatRupiah(quantity * price);
                }
            });

            // Update Total Besar
            const totalEl = document.getElementById('total-price');
            if(totalEl) totalEl.textContent = formatRupiah(totalPrice);
        };

        const updateQuantity = async (id, quantity) => {
            try {
                const response = await fetch('{{ route('customer.cart.update') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        menu_id: id,
                        quantity: quantity
                    })
                });

                if (!response.ok) throw new Error('Network response was not ok');

                // Update badge cart di navbar (opsional, jika navbar punya ID khusus)
                const navBadges = document.querySelectorAll('.cart-badge');
                if(navBadges) {
                     // Kita perlu request total qty dari server atau hitung manual di JS
                     // Untuk simplisitas, reload jika ingin akurat, atau biarkan statis sampai refresh
                }

            } catch (error) {
                console.error('Failed to update cart:', error);
            }
        };

        plusButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const item = button.closest('.cart-item');
                const id = item.dataset.id;
                const quantityDisplay = item.querySelector('.quantity-display');

                let quantity = parseInt(quantityDisplay.textContent);
                quantity++;

                quantityDisplay.textContent = quantity;
                updateQuantity(id, quantity);
                updateTotalPrice();
            });
        });

        minusButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const item = button.closest('.cart-item');
                const id = item.dataset.id;
                const quantityDisplay = item.querySelector('.quantity-display');

                let quantity = parseInt(quantityDisplay.textContent);

                if (quantity > 1) {
                    quantity--;
                    quantityDisplay.textContent = quantity;
                    updateQuantity(id, quantity);
                    updateTotalPrice();
                } else {
                    // Konfirmasi hapus
                    if(confirm('Remove this item from your collection?')) {
                         item.querySelector('form button[type="submit"]').click();
                    }
                }
            });
        });

        // Init Hitung Total saat load
        updateTotalPrice();
    });
</script>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
</style>
@endsection
