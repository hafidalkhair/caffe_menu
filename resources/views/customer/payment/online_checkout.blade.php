@extends('layouts.customer')

@section('title', 'Pay Online')

@section('content')
<div class="min-h-[85vh] flex flex-col items-center justify-center px-4 py-10 relative overflow-hidden">

    {{-- Background Glow (Aigle Theme) --}}
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-brand-berry/20 rounded-full blur-[120px] pointer-events-none"></div>

    {{-- MAIN CARD --}}
    <div class="relative w-full max-w-md bg-white border border-brand-taupe/10 rounded-[2.5rem] p-6 md:p-8 shadow-float animate-fade-in-up">

        {{-- Dekorasi Abstrak --}}
        <div class="absolute top-0 right-0 w-32 h-32 bg-brand-surface rounded-bl-full -mr-10 -mt-10 -z-10"></div>

        {{-- Header --}}
        <div class="text-center mb-8 relative z-10">
            <div class="w-16 h-16 mx-auto bg-gradient-primary rounded-2xl flex items-center justify-center shadow-lg shadow-brand-berry/30 mb-5 transform -rotate-3 border-4 border-white">
                <i class="fas fa-wallet text-2xl text-white"></i>
            </div>
            <h1 class="text-2xl font-bold text-brand-dark uppercase tracking-tight leading-none">Confirm & Pay</h1>
            <p class="text-[10px] text-brand-taupe font-bold flex items-center justify-center gap-1.5 mt-2 uppercase tracking-widest">
                <i class="fas fa-lock text-brand-berry"></i> Secure Payment by Midtrans
            </p>
        </div>

        {{-- Order Summary (Receipt Style Aigle) --}}
        <div class="bg-brand-surface rounded-3xl border border-brand-taupe/10 overflow-hidden mb-8 relative">
            {{-- Receipt Header --}}
            <div class="px-5 py-4 bg-white/50 border-b border-brand-taupe/10 flex justify-between items-center backdrop-blur-sm">
                <span class="text-[10px] font-bold text-brand-taupe uppercase tracking-[0.2em]">Order Summary</span>
                <span class="text-[10px] font-mono text-brand-dark font-bold bg-white px-2 py-1 rounded-md border border-brand-taupe/10">#{{ $order->order_number }}</span>
            </div>

            {{-- Scrollable List --}}
            <div class="max-h-48 overflow-y-auto px-5 py-4 space-y-4 custom-scrollbar">
                @php $orderItems = $order->orderItems ?? []; @endphp

                @forelse ($orderItems as $item)
                    <div class="flex justify-between items-start text-xs md:text-sm group">
                        <div class="flex gap-3">
                            <span class="font-bold text-brand-berry min-w-[24px]">{{ $item->quantity }}x</span>
                            <span class="text-brand-dark font-medium line-clamp-2 uppercase tracking-wide group-hover:text-brand-berry transition-colors">{{ $item->menu?->name ?? 'Menu Item' }}</span>
                        </div>
                        <span class="text-brand-dark font-bold whitespace-nowrap">
                            Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                        </span>
                    </div>
                @empty
                    <p class="text-center text-brand-taupe text-xs py-2 italic">No items found.</p>
                @endforelse
            </div>

            {{-- Total Section --}}
            <div class="px-5 py-5 bg-brand-dark text-white border-t border-brand-taupe/10 flex justify-between items-center relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-primary opacity-90"></div>
                <span class="text-xs font-bold text-brand-cream uppercase tracking-widest relative z-10">Total Amount</span>
                <span class="text-xl font-bold text-white relative z-10">
                    Rp{{ number_format($order->total_price, 0, ',', '.') }}
                </span>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="space-y-4 relative z-10">
            {{-- Pay Button --}}
            <button id="pay-button"
                class="w-full relative overflow-hidden group bg-brand-dark hover:bg-brand-berry text-white font-bold py-4 rounded-2xl shadow-lg shadow-brand-dark/20 transition-all duration-300 transform hover:-translate-y-0.5 uppercase tracking-[0.15em] text-xs">
                <span class="relative z-10 flex items-center justify-center gap-2">
                    Pay Now <i class="fas fa-arrow-right"></i>
                </span>
                {{-- Shine Effect --}}
                <div class="absolute top-0 -left-[100%] w-full h-full bg-gradient-to-r from-transparent via-white/10 to-transparent skew-x-12 group-hover:animate-shine"></div>
            </button>

            {{-- Cancel Button --}}
            <a href="{{ route('customer.order.cancel-online', $order) }}"
               class="block w-full text-center py-3 text-[10px] text-brand-taupe hover:text-red-500 transition-colors font-bold uppercase tracking-widest border border-transparent hover:border-red-100 rounded-xl hover:bg-red-50">
                Cancel Order
            </a>
        </div>

    </div>
</div>

<style>
    /* Custom Scrollbar Premium */
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #C7B7A3; border-radius: 4px; } /* brand-taupe */

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
        console.error("Midtrans Error: Snap Token not found.");
        if (payButton) {
            payButton.innerHTML = '<i class="fas fa-exclamation-triangle"></i> System Error';
            payButton.classList.add('bg-gray-400', 'cursor-not-allowed');
            payButton.classList.remove('bg-brand-dark', 'hover:bg-brand-berry');
            payButton.disabled = true;
        }
        return;
    }

    if (payButton) {
        payButton.addEventListener('click', function () {
            // Visual feedback
            const originalText = payButton.innerHTML;
            payButton.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Processing...';
            payButton.disabled = true;

            if (typeof snap === 'undefined') {
                 console.error("Midtrans Snap object missing.");
                 alert("Payment system failed to load. Please refresh.");
                 payButton.innerHTML = originalText;
                 payButton.disabled = false;
                 return;
            }

            snap.pay(snapToken, {
                onSuccess: function (result) {
                    console.log("Payment Success:", result);
                    window.location.href = "{{ route('customer.order.update-success', $order->id) }}";
                },
                onPending: function (result) {
                    console.log("Payment Pending:", result);
                    payButton.innerHTML = originalText;
                    payButton.disabled = false;
                },
                onError: function (result) {
                    console.error("Payment Error:", result);
                    alert('Payment processing failed. Please try again.');
                    payButton.innerHTML = originalText;
                    payButton.disabled = false;
                },
                onClose: function () {
                    payButton.innerHTML = originalText;
                    payButton.disabled = false;
                }
            });
        });
    }
});
</script>
@endpush
