<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') | CafeKU</title>

    {{-- Fonts & Icons --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- Tailwind & Plugins --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        [x-cloak] { display: none !important; }

        /* Smooth Transition */
        .transition-width { transition-property: width, transform; transition-duration: 300ms; transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1); }

        /* Custom Scrollbar */
        .sidebar-scroll::-webkit-scrollbar { width: 5px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #334155; border-radius: 20px; }

        /* Active Link Style */
        .nav-active {
            background-color: #4f46e5; /* Indigo-600 */
            color: white !important;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
        }
        .nav-active i { color: white !important; }
    </style>
</head>

<body class="text-slate-800 antialiased bg-slate-50">

    {{--
        STATE MANAGEMENT (AlpineJS)
        isSidebarOpen: Untuk Mobile (Slide in/out)
        isSidebarCollapsed: Untuk Desktop (Lebar/Kecil)
    --}}
    <div x-data="{
            isSidebarOpen: false,
            isSidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
            toggleSidebar() {
                if (window.innerWidth >= 1024) {
                    this.isSidebarCollapsed = !this.isSidebarCollapsed;
                    localStorage.setItem('sidebarCollapsed', this.isSidebarCollapsed);
                } else {
                    this.isSidebarOpen = !this.isSidebarOpen;
                }
            }
         }"
         class="flex h-screen overflow-hidden">

        {{-- MOBILE BACKDROP --}}
        <div x-show="isSidebarOpen" @click="isSidebarOpen = false" x-transition.opacity
             class="fixed inset-0 z-20 bg-slate-900/60 backdrop-blur-sm lg:hidden"></div>

        {{-- SIDEBAR --}}
        <aside :class="[
                    isSidebarCollapsed ? 'lg:w-20' : 'lg:w-72',
                    isSidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
               ]"
               class="fixed inset-y-0 left-0 z-30 flex flex-col bg-slate-900 text-white transition-width duration-300 shadow-2xl lg:static">

            {{-- HEADER SIDEBAR (LOGO) --}}
            <div class="flex items-center h-20 px-6 border-b border-slate-800 bg-slate-950/30"
                 :class="isSidebarCollapsed ? 'justify-center px-0' : 'justify-between'">

                <div class="flex items-center gap-3 overflow-hidden whitespace-nowrap">
                    <div class="flex-shrink-0 bg-indigo-600 p-2 rounded-lg">
                        <i class="fas fa-mug-hot text-xl"></i>
                    </div>
                    {{-- Teks Logo (Hilang saat collapsed) --}}
                    <div class="transition-opacity duration-200"
                         :class="isSidebarCollapsed ? 'hidden opacity-0' : 'block opacity-100'">
                        <h1 class="text-xl font-bold tracking-tight">CafeKU</h1>
                    </div>
                </div>

                {{-- Close Button (Mobile Only) --}}
                <button @click="isSidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- NAVIGATION LINKS --}}
            <nav class="flex-1 overflow-y-auto py-6 px-3 space-y-1 sidebar-scroll">

                {{-- Helper: Fungsi Render Menu Item --}}
                @php
                    function renderMenu($route, $icon, $label, $collapsedRef, $activePattern = null) {
                        $isActive = request()->routeIs($route) || ($activePattern && request()->routeIs($activePattern));
                        $activeClass = $isActive ? 'nav-active' : 'text-slate-400 hover:bg-slate-800 hover:text-white';

                        echo '
                        <a href="'.route($route).'"
                           class="group relative flex items-center px-3 py-3 rounded-xl transition-all duration-200 '.$activeClass.'"
                           title="'.$label.'">

                            <div class="flex-shrink-0 w-6 text-center">
                                <i class="'.$icon.' text-lg transition-transform group-hover:scale-110"></i>
                            </div>

                            <span class="ml-3 font-medium whitespace-nowrap transition-all duration-300 origin-left"
                                  :class="'.$collapsedRef.' ? \'hidden opacity-0 w-0\' : \'block opacity-100 w-auto\'">
                                '.$label.'
                            </span>

                            <div x-show="'.$collapsedRef.'" style="display: none"
                                 class="absolute left-16 z-50 px-3 py-2 text-xs font-bold text-white bg-slate-900 rounded shadow-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity border border-slate-700 whitespace-nowrap">
                                '.$label.'
                            </div>
                        </a>
                        ';
                    }

                    function renderLabel($text, $collapsedRef) {
                        echo '
                        <div class="px-3 mt-6 mb-2 text-xs font-bold text-slate-500 uppercase tracking-wider transition-opacity duration-300"
                             :class="'.$collapsedRef.' ? \'hidden\' : \'block\'">
                            '.$text.'
                        </div>
                        <div class="my-4 border-t border-slate-800" :class="'.$collapsedRef.' ? \'block\' : \'hidden\'"></div>
                        ';
                    }
                @endphp

                {{-- Dashboard --}}
                {{ renderMenu('admin.dashboard', 'fas fa-th-large', 'Dashboard', 'isSidebarCollapsed') }}

                {{ renderLabel('Aktivitas', 'isSidebarCollapsed') }}

                {{-- Pesanan Masuk (Custom Badge) --}}
                <a href="{{ route('admin.orders.index') }}"
                   class="group relative flex items-center px-3 py-3 rounded-xl transition-all duration-200 {{ (request()->routeIs('admin.orders.index') && !request()->routeIs('admin.orders.history')) ? 'nav-active' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex-shrink-0 w-6 text-center relative">
                        <i class="fas fa-clipboard-list text-lg"></i>
                        {{-- Dot Merah Notifikasi --}}
                        <span id="sidebar-notification-dot" class="hidden absolute -top-1.5 -right-1.5 w-3 h-3 bg-red-500 border-2 border-slate-900 rounded-full"></span>
                    </div>
                    <span class="ml-3 font-medium whitespace-nowrap" :class="isSidebarCollapsed ? 'hidden' : 'block'">
                        Pesanan Masuk
                    </span>

                    {{-- Tooltip --}}
                    <div x-show="isSidebarCollapsed" style="display: none" class="absolute left-16 z-50 px-3 py-2 text-xs font-bold text-white bg-slate-900 rounded shadow-lg opacity-0 group-hover:opacity-100 pointer-events-none border border-slate-700">Pesanan</div>
                </a>

                {{-- History --}}
                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'dapur')
                    {{ renderMenu('admin.orders.history', 'fas fa-history', 'Riwayat Transaksi', 'isSidebarCollapsed') }}
                @endif

                {{ renderLabel('Produk', 'isSidebarCollapsed') }}
                {{ renderMenu('admin.menus.index', 'fas fa-hamburger', 'Menu & Produk', 'isSidebarCollapsed', 'admin.menus.*') }}

                {{-- KHUSUS ADMIN --}}
                @if(Auth::user()->role === 'admin')
                    {{ renderMenu('admin.categories.index', 'fas fa-tags', 'Kategori', 'isSidebarCollapsed', 'admin.categories.*') }}
                    {{ renderMenu('admin.tables.index', 'fas fa-chair', 'Meja Cafe', 'isSidebarCollapsed', 'admin.tables.*') }}

                    {{ renderLabel('Admin', 'isSidebarCollapsed') }}
                    {{ renderMenu('admin.users.index', 'fas fa-users-cog', 'Kelola Pegawai', 'isSidebarCollapsed', 'admin.users.*') }}
                    {{ renderMenu('admin.stores.index', 'fas fa-store', 'Manajemen Gerai', 'isSidebarCollapsed', 'admin.stores.*') }}
                    {{ renderMenu('admin.reports.index', 'fas fa-chart-line', 'Laporan Keuangan', 'isSidebarCollapsed', 'admin.reports.*') }}
                @endif

            </nav>

            {{-- SIDEBAR FOOTER (Profile Mini) --}}
            <div class="p-4 border-t border-slate-800 bg-slate-950/30">
                <div class="flex items-center gap-3" :class="isSidebarCollapsed ? 'justify-center' : ''">
                    <img class="h-9 w-9 rounded-full object-cover border-2 border-indigo-500"
                         src="{{ Auth::user()->profile_photo_path ? asset('storage/' . Auth::user()->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=6366F1&color=fff' }}">

                    <div class="overflow-hidden" :class="isSidebarCollapsed ? 'hidden' : 'block'">
                        <p class="text-sm font-semibold text-white truncate w-32">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ ucfirst(Auth::user()->role) }}</p>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" :class="isSidebarCollapsed ? 'hidden' : 'block'">
                        @csrf
                        <button type="submit" class="text-slate-500 hover:text-red-400 ml-2" title="Logout">
                            <i class="fas fa-power-off"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- MAIN CONTENT AREA --}}
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50 transition-all duration-300">

            {{-- TOPBAR / HEADER --}}
            <header class="sticky top-0 z-20 h-16 flex items-center justify-between px-4 sm:px-6 bg-white/80 backdrop-blur-md border-b border-slate-200 shadow-sm">

                {{-- Left: Toggle Sidebar --}}
                <div class="flex items-center gap-4">
                    <button @click="toggleSidebar()" class="p-2 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                        <i class="fas fa-bars text-xl" x-show="!isSidebarCollapsed || window.innerWidth < 1024"></i>
                        <i class="fas fa-bars-staggered text-xl" x-show="isSidebarCollapsed && window.innerWidth >= 1024" style="display: none;"></i>
                    </button>
                    <h2 class="text-lg font-bold text-slate-800 tracking-tight">@yield('title', 'Dashboard')</h2>
                </div>

                {{-- Right: Actions --}}
                <div class="flex items-center gap-3 sm:gap-4">

                    {{-- Role Badge --}}
                    <span class="hidden md:inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                        {{ Auth::user()->role === 'admin' ? 'Super Admin' : 'Kitchen Staff' }}
                        @if(Auth::user()->role === 'dapur' && Auth::user()->store)
                             | {{ Auth::user()->store->name }}
                        @endif
                    </span>

                    {{-- Notifications --}}
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open; fetchNotificationDetails();"
                                class="relative p-2 text-slate-400 hover:text-indigo-600 transition-colors">
                            <i class="far fa-bell text-xl"></i>
                            <span id="notification-count-badge" class="hidden absolute top-1 right-1 flex h-2.5 w-2.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"></span>
                            </span>
                        </button>

                        {{-- Dropdown --}}
                        <div x-show="open" style="display: none;"
                             x-transition.origin.top.right
                             class="absolute right-0 mt-3 w-80 bg-white rounded-xl shadow-2xl border border-slate-100 py-0 z-50 overflow-hidden">
                            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50">
                                <h3 class="text-sm font-bold text-slate-800">Notifikasi Pesanan</h3>
                            </div>
                            <div id="notification-list" class="max-h-64 overflow-y-auto bg-white">
                                <div class="p-6 text-center text-slate-400 text-sm">Belum ada notifikasi</div>
                            </div>
                            <div class="border-t border-slate-100 p-2 bg-slate-50">
                                <a href="{{ route('admin.orders.index') }}" class="block text-center text-xs font-bold text-indigo-600 hover:underline">LIHAT SEMUA</a>
                            </div>
                        </div>
                    </div>

                    {{-- Profile --}}
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2">
                        <img class="h-9 w-9 rounded-full object-cover border border-slate-200"
                             src="{{ Auth::user()->profile_photo_path ? asset('storage/' . Auth::user()->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}">
                    </a>
                </div>
            </header>

            {{-- MAIN CONTENT --}}
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50 p-4 sm:p-6 lg:p-8">
                <div class="transition-opacity duration-500 ease-in-out opacity-100">
                    @yield('content')
                </div>
            </main>

        </div>
    </div>

    @stack('scripts')

    {{-- Audio & Scripts Notifikasi (Sama Seperti Sebelumnya) --}}
    <audio id="notif-sound" src="{{ asset('audio/order_alert.mp3') }}" preload="auto"></audio>
    <script>
        function playNotificationSound() { const audio = document.getElementById('notif-sound'); if (audio) audio.play().catch(e => console.log("Audio blocked:", e)); }
        const detailUrl = "{{ route('admin.notifications.details') }}";
        const orderIndexUrl = "{{ route('admin.orders.index') }}";
        const badgeElement = document.getElementById('notification-count-badge');
        const sidebarDot = document.getElementById('sidebar-notification-dot');
        const notificationList = document.getElementById('notification-list');
        let lastCount = 0;

        async function fetchNotificationDetails() {
            try {
                const response = await fetch(detailUrl);
                if (!response.ok) throw new Error('Network err');
                const data = await response.json();
                updateUI(data.notifications, data.count);
                return data.count;
            } catch (error) { console.error("Polling error:", error); return 0; }
        }

        function updateUI(notifications, count) {
            if (count > 0) {
                if(badgeElement) badgeElement.classList.remove('hidden');
                if(sidebarDot) sidebarDot.classList.remove('hidden');
            } else {
                if(badgeElement) badgeElement.classList.add('hidden');
                if(sidebarDot) sidebarDot.classList.add('hidden');
            }
            if (notificationList) {
                if (notifications.length === 0) {
                    notificationList.innerHTML = `<div class="p-6 text-center text-slate-400 text-sm"><i class="far fa-bell-slash text-2xl mb-2 block opacity-50"></i>Tidak ada notifikasi baru</div>`;
                } else {
                    let html = '';
                    notifications.forEach(n => {
                        html += `
                        <a href="${n.url}" class="block px-4 py-3 hover:bg-indigo-50 border-b border-slate-50 transition-colors">
                            <div class="flex justify-between">
                                <p class="text-sm font-bold text-slate-800">#${n.order_number}</p>
                                <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">Meja: ${n.table_name} <span class="mx-1">•</span> ${n.time_ago}</p>
                        </a>`;
                    });
                    notificationList.innerHTML = html;
                }
            }
        }

        function startPolling() {
            fetchNotificationDetails().then(count => { lastCount = count; });
            setInterval(async () => {
                const currentCount = await fetchNotificationDetails();
                if (currentCount > lastCount) {
                    playNotificationSound();
                    const newOrders = currentCount - lastCount;
                    const Toast = Swal.mixin({
                        toast: true, position: 'top-end', showConfirmButton: false, timer: 5000, timerProgressBar: true,
                        background: '#1e293b', color: '#fff',
                        didOpen: (toast) => {
                            toast.addEventListener('click', () => { window.location.href = orderIndexUrl; });
                            toast.style.cursor = 'pointer';
                        }
                    });
                    Toast.fire({ icon: 'info', title: 'Pesanan Baru!', text: `${newOrders} pesanan menunggu.` });
                }
                lastCount = currentCount;
            }, 5000);
        }
        document.addEventListener('DOMContentLoaded', startPolling);
    </script>
</body>
</html>
