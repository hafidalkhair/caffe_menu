<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Menu') - Aigle Solitaire</title>

    {{-- 1. FONT: CLASH DISPLAY (Full Implementation) --}}
    <link href="https://api.fontshare.com/v2/css?f[]=clash-display@200,300,400,500,600,700&display=swap" rel="stylesheet">

    {{-- 2. ICONS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- 3. TAILWIND CONFIGURATION --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Clash Display"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            dark: '#561C24',    // Maroon
                            berry: '#6D2932',   // Berry
                            taupe: '#C7B7A3',   // Taupe
                            cream: '#E8D8C4',   // Cream
                            surface: '#FAF9F6', // Off-White
                        }
                    },
                    backgroundImage: {
                        // GRADASI MODERN
                        'gradient-primary': 'linear-gradient(135deg, #561C24 0%, #6D2932 100%)', // Dark Luxury
                        'gradient-gold': 'linear-gradient(135deg, #E8D8C4 0%, #C7B7A3 100%)',   // Soft Gold
                        'gradient-surface': 'linear-gradient(180deg, #FAF9F6 0%, #F5F0EB 100%)', // Clean Background
                    },
                    boxShadow: {
                        'glow': '0 0 20px rgba(109, 41, 50, 0.15)',
                        'float': '0 10px 30px -5px rgba(86, 28, 36, 0.3)',
                        'soft': '0 10px 40px -10px rgba(86, 28, 36, 0.08)',
                    }
                }
            }
        }
    </script>

    <style>
        body {
            /* Background dengan gradasi halus agar tidak flat */
            background: linear-gradient(180deg, #FAF9F6 0%, #FDFBF9 100%);
            color: #561C24;
            font-family: 'Clash Display', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        /* Navigasi Kaca dengan sentuhan Cream */
        .glass-nav {
            background: rgba(255, 255, 255, 0.85); /* Sedikit lebih opaque */
            backdrop-filter: blur(20px); /* Blur lebih kuat */
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(232, 216, 196, 0.5); /* Border sedikit lebih terlihat */
        }

        /* Hide Scrollbar */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Animation untuk Text Gradient */
        .text-gradient-anim {
            background-size: 200% auto;
            animation: shine 4s linear infinite;
        }
        @keyframes shine {
            to { background-position: 200% center; }
        }

        /* Smooth Scroll */
        html { scroll-behavior: smooth; }
    </style>
</head>
<body class="antialiased font-sans selection:bg-brand-berry selection:text-white">

    {{-- ========================================== --}}
    {{-- A. DESKTOP NAVBAR (Dengan Gradasi Mewah) --}}
    {{-- ========================================== --}}
    <nav class="hidden md:block fixed top-0 w-full z-50 transition-all duration-300 glass-nav shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20"> {{-- Tinggi dikurangi dari 24 ke 20 --}}

                {{-- Logo Area: Menggunakan Text Gradient --}}
                <a href="{{ route('customer.menu.index') }}" class="flex items-center gap-3 group"> {{-- Gap dikurangi --}}
                    {{-- Icon Box dengan Gradient Background --}}
                    <div class="w-10 h-10 rounded-xl bg-gradient-primary text-white flex items-center justify-center text-lg shadow-lg transform group-hover:rotate-12 transition-transform duration-500"> {{-- Ukuran dikurangi --}}
                        <i class="fas fa-mug-hot"></i>
                    </div>
                    <div class="flex flex-col">
                        {{-- Text Gradient untuk Nama Brand --}}
                        <span class="text-2xl font-bold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-brand-dark via-brand-berry to-brand-dark bg-[length:200%_auto] animate-[shine_5s_linear_infinite] uppercase leading-none"> {{-- Ukuran font dikurangi --}}
                            AIGLE
                        </span>
                        <span class="text-[9px] font-semibold tracking-[0.4em] text-brand-taupe uppercase mt-0.5 pl-0.5">Solitaire</span> {{-- Ukuran font dikurangi --}}
                    </div>
                </a>

                {{-- Links --}}
                <div class="hidden md:flex space-x-10 items-center"> {{-- Spasi antar link dikurangi --}}
                    <a href="{{ route('customer.menu.index') }}" class="text-sm font-medium uppercase tracking-widest transition-all hover:text-brand-berry hover:font-bold relative group {{ request()->routeIs('customer.menu.*') ? 'text-brand-dark font-bold' : 'text-brand-taupe' }}">
                        Menu
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-brand-berry transition-all group-hover:w-full {{ request()->routeIs('customer.menu.*') ? 'w-full' : '' }}"></span>
                    </a>
                    <a href="{{ route('customer.track.index') }}" class="text-sm font-medium uppercase tracking-widest transition-all hover:text-brand-berry hover:font-bold relative group {{ request()->routeIs('customer.track.*') ? 'text-brand-dark font-bold' : 'text-brand-taupe' }}">
                        Orders
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-brand-berry transition-all group-hover:w-full {{ request()->routeIs('customer.track.*') ? 'w-full' : '' }}"></span>
                    </a>
                </div>

                {{-- Cart Button --}}
                <div class="flex items-center gap-4">
                    <a href="{{ route('customer.cart') }}" class="relative group">
                        <div class="w-10 h-10 rounded-full bg-white border border-brand-cream flex items-center justify-center text-brand-dark group-hover:bg-gradient-primary group-hover:text-white group-hover:border-transparent transition-all duration-300 shadow-sm"> {{-- Ukuran dikurangi --}}
                            <i class="fas fa-shopping-bag text-base"></i>
                        </div>
                        {{-- Badge dengan Gradient --}}
                        <span class="cart-badge absolute -top-1 -right-1 h-4 w-4 bg-gradient-to-r from-brand-berry to-brand-dark text-white text-[9px] font-semibold flex items-center justify-center rounded-full border border-white {{ session('cart') && count(session('cart')) > 0 ? '' : 'hidden' }}"> {{-- Ukuran badge dikurangi --}}
                            {{ session('cart') ? count(session('cart')) : 0 }}
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    {{-- ========================================== --}}
    {{-- B. MOBILE HEADER (Clean & Gradient) --}}
    {{-- ========================================== --}}
    <div class="md:hidden fixed top-0 w-full z-40 glass-nav px-5 py-3 flex justify-between items-center shadow-sm"> {{-- Padding dikurangi --}}
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-gradient-primary text-white flex items-center justify-center text-xs shadow-md"> {{-- Ukuran dikurangi --}}
                <i class="fas fa-mug-hot"></i>
            </div>
            <div>
                <h1 class="text-lg font-bold tracking-tight text-brand-dark leading-none uppercase">Aigle.</h1> {{-- Ukuran font dikurangi --}}
            </div>
        </div>

        @if (Session::has('pending_order_id'))
            {{-- Tombol Bayar Mobile dengan Gradient --}}
            <a href="{{ route('customer.payment.cash', ['order' => Session::get('pending_order_id')]) }}" class="flex items-center gap-1.5 px-4 py-2 bg-gradient-to-r from-brand-dark to-brand-berry text-white rounded-full text-[9px] font-semibold uppercase tracking-widest shadow-lg shadow-brand-berry/30 animate-pulse">
               <i class="fas fa-receipt"></i> Pay
            </a>
        @endif
    </div>

    {{-- ========================================== --}}
    {{-- C. MAIN CONTENT AREA --}}
    {{-- ========================================== --}}
    <main class="pt-20 pb-28 md:pt-28 md:pb-12 min-h-screen "> {{-- Padding top disesuaikan --}}

        {{-- AMBIENT GRADIENT BACKGROUND (Membuat kesan "Mahal" & Berdimensi) --}}
        {{-- Blob 1: Cream/Gold di Kanan Atas --}}
        <div class="fixed top-[-10%] right-[-10%] w-[500px] h-[500px] bg-gradient-to-br from-brand-cream to-transparent opacity-30 rounded-full blur-[80px] -z-10 pointer-events-none"></div>

        {{-- Blob 2: Taupe di Kiri Bawah --}}
        <div class="fixed bottom-[-10%] left-[-10%] w-[400px] h-[400px] bg-gradient-to-tr from-brand-taupe/20 to-transparent opacity-40 rounded-full blur-[100px] -z-10 pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"> {{-- Padding horizontal disesuaikan --}}
            @yield('content')
        </div>
    </main>

    {{-- ========================================== --}}
    {{-- D. MOBILE BOTTOM NAV (Floating Gradient Diamond) --}}
    {{-- ========================================== --}}
    <nav class="md:hidden fixed bottom-0 w-full z-50 bg-white/95 backdrop-blur-md border-t border-brand-cream/50 pb-safe shadow-[0_-10px_40px_rgba(86,28,36,0.05)]">
        <div class="grid grid-cols-3 h-[70px] items-center px-4"> {{-- Tinggi dikurangi dari 80px ke 70px --}}

            {{-- 1. MENU --}}
            <a href="{{ route('customer.menu.index') }}" class="group flex flex-col items-center justify-center gap-1 h-full w-full">
                <div class="text-lg transition-all duration-300 group-active:scale-90 relative {{ request()->routeIs('customer.menu.*') ? 'text-brand-dark -translate-y-0.5' : 'text-brand-taupe' }}"> {{-- Ukuran icon dikurangi --}}
                    <i class="fas fa-utensils"></i>
                    @if(request()->routeIs('customer.menu.*'))
                        <span class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-1 h-1 bg-brand-berry rounded-full shadow-lg"></span> {{-- Ukuran dot dikurangi --}}
                    @endif
                </div>
                <span class="text-[8px] font-semibold tracking-[0.2em] uppercase {{ request()->routeIs('customer.menu.*') ? 'text-brand-dark' : 'text-brand-taupe' }}">Menu</span> {{-- Ukuran font dikurangi --}}
            </a>

            {{-- 2. CENTER CART (DIAMOND GRADIENT) --}}
            <div class="relative -top-6 flex justify-center w-full pointer-events-none"> {{-- Top position disesuaikan --}}
                {{-- Border luar putih agar terpisah dari nav --}}
                <div class="pointer-events-auto p-1 rounded-xl bg-white rotate-45 shadow-float -mt-1">
                    {{-- Tombol Utama dengan Gradient --}}
                    <a href="{{ route('customer.cart') }}" class="w-12 h-12 rounded-lg bg-gradient-primary flex items-center justify-center text-white transform transition-all active:scale-95 group relative overflow-hidden"> {{-- Ukuran dikurangi --}}

                        {{-- Shine Effect --}}
                        <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>

                        <div class="-rotate-45 relative">
                            <i class="fas fa-shopping-basket text-lg"></i> {{-- Ukuran icon dikurangi --}}
                            <span class="cart-badge absolute -top-2 -right-2 h-4 w-4 bg-brand-cream text-brand-dark rounded-full flex items-center justify-center text-[8px] font-bold border border-white {{ session('cart') && count(session('cart')) > 0 ? '' : 'hidden' }}"> {{-- Ukuran badge dikurangi --}}
                                {{ session('cart') ? count(session('cart')) : 0 }}
                            </span>
                        </div>
                    </a>
                </div>
            </div>

            {{-- 3. ORDER --}}
            <a href="{{ route('customer.track.index') }}" class="group flex flex-col items-center justify-center gap-1 h-full w-full">
                <div class="text-lg transition-all duration-300 group-active:scale-90 relative {{ request()->routeIs('customer.track.*') ? 'text-brand-dark -translate-y-0.5' : 'text-brand-taupe' }}"> {{-- Ukuran icon dikurangi --}}
                    <i class="fas fa-clipboard-list"></i>
                    @if(request()->routeIs('customer.track.*'))
                        <span class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-1 h-1 bg-brand-berry rounded-full shadow-lg"></span> {{-- Ukuran dot dikurangi --}}
                    @endif
                </div>
                <span class="text-[8px] font-semibold tracking-[0.2em] uppercase {{ request()->routeIs('customer.track.*') ? 'text-brand-dark' : 'text-brand-taupe' }}">Order</span> {{-- Ukuran font dikurangi --}}
            </a>

        </div>
    </nav>

    {{-- SCRIPTS --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const CoffeeToast = Swal.mixin({
            toast: true,
            position: 'top',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            background: '#ffffff',
            color: '#561C24',
            iconColor: '#6D2932',
            customClass: {
                popup: 'rounded-2xl shadow-soft border border-brand-cream/50 mt-4 font-sans'
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
