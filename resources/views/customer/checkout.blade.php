@extends('layouts.customer')

@section('title', 'Checkout')

@section('content')
<div class="max-w-lg mx-auto pb-24 px-4 pt-6">

    {{-- HEADER (Clean & Minimalist with Aigle Branding) --}}
    <div class="mb-8">
        <a href="{{ route('customer.cart') }}" class="inline-flex items-center gap-2 text-brand-taupe hover:text-brand-berry font-bold text-[10px] uppercase tracking-widest mb-4 transition-colors group">
            <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i> Back to Cart
        </a>
        <h1 class="text-3xl font-bold text-brand-dark tracking-tight uppercase leading-none">Checkout <br> <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-berry to-brand-taupe">Details</span></h1>
        <p class="text-brand-taupe text-xs mt-2 font-medium tracking-wide">Complete your information to proceed.</p>
    </div>

    {{-- ERROR ALERT (Modern Style) --}}
    @if (session('error') || session('table_error'))
        <div class="mb-8 p-5 rounded-[1.5rem] bg-red-50/50 border border-red-100 flex items-start gap-4 animate-fade-in shadow-soft">
            <div class="flex-shrink-0 w-8 h-8 rounded-xl bg-red-100 text-red-500 flex items-center justify-center">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <h4 class="text-red-700 font-bold text-xs uppercase tracking-widest mb-1">Attention</h4>
                <p class="text-red-600/80 text-xs leading-relaxed font-medium">{{ session('error') ?? session('table_error') }}</p>
            </div>
        </div>
    @endif

    <form action="{{ route('customer.order.store') }}" method="POST" class="space-y-8">
        @csrf

        {{-- BAGIAN 1: DATA DIRI --}}
        <div class="bg-white rounded-[2rem] p-6 shadow-soft border border-brand-taupe/10 relative overflow-hidden group">
            {{-- Dekorasi --}}
            <div class="absolute top-0 right-0 w-24 h-24 bg-brand-surface rounded-bl-[2rem] -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>

            <h3 class="text-sm font-bold text-brand-dark mb-6 flex items-center gap-3 relative z-10 uppercase tracking-widest">
                <span class="w-1.5 h-1.5 bg-brand-berry rounded-full"></span>
                Customer Info
            </h3>

            {{-- Input Nama --}}
            <div class="mb-6 relative z-10">
                <label class="block text-[10px] font-bold text-brand-taupe mb-2 uppercase tracking-widest ml-1">Full Name</label>
                <div class="relative group/input">
                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                        <i class="fas fa-user text-brand-taupe/50 group-focus-within/input:text-brand-berry transition-colors"></i>
                    </div>
                    <input type="text" name="customer_name" id="customer_name_input" placeholder="Enter your name"
                        class="w-full bg-brand-surface text-brand-dark border border-transparent rounded-2xl pl-12 pr-4 py-4 focus:ring-0 focus:border-brand-taupe/30 focus:bg-white transition-all placeholder-brand-taupe/40 font-bold tracking-wide"
                        required value="{{ old('customer_name') }}">
                </div>
                @error('customer_name') <p class="text-brand-berry text-[10px] mt-1.5 font-bold ml-1 flex items-center gap-1"><i class="fas fa-info-circle"></i> {{ $message }}</p> @enderror
            </div>

            {{-- Input Telepon --}}
            <div class="relative z-10">
                <label class="block text-[10px] font-bold text-brand-taupe mb-2 uppercase tracking-widest ml-1">WhatsApp Number</label>
                <div class="relative group/input">
                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                        <i class="fab fa-whatsapp text-brand-taupe/50 group-focus-within/input:text-brand-berry transition-colors text-lg"></i>
                    </div>
                    <input type="tel" name="phone_number" id="phone_number_input" placeholder="08xxxxxxxxxx"
                        class="w-full bg-brand-surface text-brand-dark border border-transparent rounded-2xl pl-12 pr-4 py-4 focus:ring-0 focus:border-brand-taupe/30 focus:bg-white transition-all placeholder-brand-taupe/40 font-bold tracking-wide"
                        required value="{{ old('phone_number') }}">
                </div>
                <p class="text-[9px] text-brand-taupe/60 mt-2 font-medium flex items-center gap-1.5 ml-1">
                    <i class="fas fa-shield-alt"></i> Used for order notifications only.
                </p>
                @error('phone_number') <p class="text-brand-berry text-[10px] mt-1.5 font-bold ml-1 flex items-center gap-1"><i class="fas fa-info-circle"></i> {{ $message }}</p> @enderror
            </div>
        </div>

        {{-- BAGIAN 2: LOKASI MEJA --}}
        <div class="bg-white rounded-[2rem] p-6 shadow-soft border border-brand-taupe/10 relative overflow-hidden group">
             {{-- Dekorasi --}}
             <div class="absolute top-0 right-0 w-24 h-24 bg-brand-surface rounded-bl-[2rem] -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>

            <h3 class="text-sm font-bold text-brand-dark mb-6 flex items-center gap-3 relative z-10 uppercase tracking-widest">
                <span class="w-1.5 h-1.5 bg-brand-berry rounded-full"></span>
                Table Number
            </h3>

            <div class="relative group/input relative z-10" id="table-input-container">
                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                    <i class="fas fa-chair text-brand-taupe/50 group-focus-within/input:text-brand-berry transition-colors"></i>
                </div>
                <input type="text" name="table_name" id="table_name_input" placeholder="e.g. M01"
                    class="w-full bg-brand-surface text-brand-dark border border-transparent rounded-2xl pl-12 pr-4 py-4 focus:ring-0 focus:border-brand-taupe/30 focus:bg-white transition-all font-bold tracking-[0.1em] placeholder-brand-taupe/40 uppercase"
                    required value="{{ old('table_name') }}">
            </div>

            <div class="mt-4 flex items-start gap-3 text-[10px] text-brand-taupe bg-brand-surface p-4 rounded-2xl border border-brand-taupe/5 relative z-10">
                <i class="fas fa-info-circle text-brand-berry mt-0.5"></i>
                <span class="leading-relaxed font-medium">Please check the table number sticker on your table.</span>
            </div>
            @error('table_name') <p class="text-brand-berry text-[10px] mt-1.5 font-bold ml-1 flex items-center gap-1"><i class="fas fa-info-circle"></i> {{ $message }}</p> @enderror
        </div>

        {{-- BAGIAN 3: METODE PEMBAYARAN --}}
        <div class="bg-white rounded-[2rem] p-6 shadow-soft border border-brand-taupe/10 relative overflow-hidden group">
             {{-- Dekorasi --}}
             <div class="absolute top-0 right-0 w-24 h-24 bg-brand-surface rounded-bl-[2rem] -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>

            <h3 class="text-sm font-bold text-brand-dark mb-6 flex items-center gap-3 relative z-10 uppercase tracking-widest">
                <span class="w-1.5 h-1.5 bg-brand-berry rounded-full"></span>
                Payment Method
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 relative z-10">
                {{-- Opsi Tunai --}}
                <label class="cursor-pointer relative group/option">
                    <input type="radio" name="payment_method_type" value="cash" class="peer sr-only" {{ old('payment_method_type', 'cash') == 'cash' ? 'checked' : '' }}>

                    <div class="h-full bg-brand-surface border border-transparent rounded-2xl p-4 flex items-center gap-4 transition-all duration-300 peer-checked:border-brand-berry/30 peer-checked:bg-brand-cream/20 peer-checked:shadow-sm hover:bg-white hover:border-brand-taupe/10">
                        <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-brand-taupe shadow-sm peer-checked:text-brand-dark peer-checked:bg-white">
                            <i class="fas fa-money-bill-wave text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <span class="font-bold text-xs text-brand-dark block uppercase tracking-wide">Cash</span>
                            <span class="text-[9px] text-brand-taupe block font-medium mt-0.5">Pay at cashier</span>
                        </div>
                        <div class="w-5 h-5 rounded-full border border-brand-taupe/30 peer-checked:border-brand-berry peer-checked:bg-brand-berry flex items-center justify-center transition-all">
                            <i class="fas fa-check text-white text-[9px] opacity-0 peer-checked:opacity-100"></i>
                        </div>
                    </div>
                </label>

                {{-- Opsi Online --}}
                <label class="cursor-pointer relative group/option">
                    <input type="radio" name="payment_method_type" value="online" class="peer sr-only" {{ old('payment_method_type') == 'online' ? 'checked' : '' }}>

                    <div class="h-full bg-brand-surface border border-transparent rounded-2xl p-4 flex items-center gap-4 transition-all duration-300 peer-checked:border-brand-berry/30 peer-checked:bg-brand-cream/20 peer-checked:shadow-sm hover:bg-white hover:border-brand-taupe/10">
                        <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-brand-taupe shadow-sm peer-checked:text-brand-dark peer-checked:bg-white">
                            <i class="fas fa-qrcode text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <span class="font-bold text-xs text-brand-dark block uppercase tracking-wide">QRIS / E-Wallet</span>
                            <span class="text-[9px] text-brand-taupe block font-medium mt-0.5">Automated & Fast</span>
                        </div>
                        <div class="w-5 h-5 rounded-full border border-brand-taupe/30 peer-checked:border-brand-berry peer-checked:bg-brand-berry flex items-center justify-center transition-all">
                            <i class="fas fa-check text-white text-[9px] opacity-0 peer-checked:opacity-100"></i>
                        </div>
                    </div>
                </label>
            </div>
            @error('payment_method_type') <p class="text-brand-berry text-[10px] mt-2 font-bold ml-1 flex items-center gap-1"><i class="fas fa-info-circle"></i> {{ $message }}</p> @enderror
        </div>

        {{-- BAGIAN 4: CATATAN --}}
        <div class="bg-white rounded-[2rem] p-6 shadow-soft border border-brand-taupe/10 relative overflow-hidden group">
            <h3 class="text-sm font-bold text-brand-dark mb-4 uppercase tracking-widest relative z-10">Additional Notes</h3>
            <textarea name="notes" rows="2" placeholder="Example: Less sugar, extra ice..."
                class="w-full bg-brand-surface text-brand-dark border border-transparent rounded-2xl px-5 py-4 focus:ring-0 focus:border-brand-taupe/30 focus:bg-white transition-all placeholder-brand-taupe/40 resize-none font-bold text-sm relative z-10">{{ old('notes') }}</textarea>
        </div>

        {{-- SUBMIT BUTTON --}}
        <div class="pt-4 pb-12">
            <button type="submit" class="w-full bg-brand-dark text-white font-bold text-sm py-5 rounded-2xl shadow-lg shadow-brand-dark/20 hover:bg-gradient-primary hover:shadow-float hover:-translate-y-1 active:scale-[0.98] transition-all flex items-center justify-center gap-3 uppercase tracking-[0.15em] group">
                Confirm Order <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
            </button>
            <p class="text-center text-[10px] text-brand-taupe mt-4 font-medium tracking-wide uppercase">Please ensure all details are correct.</p>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- FITUR AUTO-FILL JOIN TABLE (Logic Tetap Sama) ---
        const savedSession = localStorage.getItem('cafe_session');

        if (savedSession) {
            const data = JSON.parse(savedSession);
            const nameInput = document.getElementById('customer_name_input');
            const phoneInput = document.getElementById('phone_number_input');
            const tableInput = document.getElementById('table_name_input');
            const tableContainer = document.getElementById('table-input-container');

            if (nameInput && !nameInput.value) nameInput.value = data.name + " (Guest)";
            if (phoneInput && !phoneInput.value) phoneInput.value = data.phone;

            if (tableInput && !tableInput.value) {
                tableInput.value = data.table;
                tableInput.readOnly = true;
                // Style baru untuk readonly (Aigle style)
                tableInput.classList.add('bg-brand-surface', 'cursor-not-allowed', 'text-brand-taupe', 'border-brand-taupe/20');

                const infoDiv = document.createElement('div');
                infoDiv.className = 'flex justify-between items-center mt-3 p-4 bg-brand-cream/20 rounded-xl border border-brand-cream animate-fade-in';
                infoDiv.innerHTML = `
                    <span class="text-brand-dark text-[10px] font-bold uppercase tracking-wide flex items-center gap-2">
                        <i class="fas fa-link text-brand-berry"></i> Joining Table ${data.table}
                    </span>
                    <button type="button" id="reset-session-btn" class="text-[10px] text-brand-berry font-bold hover:underline cursor-pointer uppercase tracking-wider">
                        Cancel
                    </button>
                `;
                tableContainer.parentNode.appendChild(infoDiv);

                document.getElementById('reset-session-btn').addEventListener('click', function() {
                    if(confirm('Cancel joining table?')) {
                        localStorage.removeItem('cafe_session');
                        tableInput.value = '';
                        tableInput.readOnly = false;
                        tableInput.classList.remove('bg-brand-surface', 'cursor-not-allowed', 'text-brand-taupe', 'border-brand-taupe/20');
                        tableInput.focus();
                        infoDiv.remove();
                    }
                });
            }
        }
    });
</script>
@endsection
