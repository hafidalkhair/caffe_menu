<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') | CafeKU</title>

    {{-- Scripts dan Styles Eksternal --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- AlpineJS --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Font Google: Plus Jakarta Sans (Sangat modern & mudah dibaca) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f3f4f6; /* Gray-100 */
        }

        /* --- SIDEBAR STYLING --- */
        .sidebar {
            background-color: #1e1b4b; /* Indigo-950 (Dark Theme) */
            color: #e0e7ff; /* Indigo-100 */
        }

        .nav-link {
            transition: all 0.2s ease-in-out;
            border-radius: 0.75rem; /* Rounded-xl */
            margin: 0.25rem 1rem;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            transform: translateX(4px);
        }

        .nav-link.active {
            background: linear-gradient(to right, #6366f1, #4f46e5); /* Indigo Gradient */
            color: white;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.3);
        }

        /* --- CONTENT STYLING --- */
        .main-content {
            transition: margin-left 0.3s ease;
        }

        /* --- CARD & GENERAL UI --- */
        .glass-header {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid #e5e7eb;
        }

        /* Scrollbar Halus */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .sidebar::-webkit-scrollbar-thumb { background: #4338ca; }

        /* Animasi Masuk Halaman */
        .fade-in-up { animation: fadeInUp 0.5s ease-out forwards; opacity: 0; transform: translateY(10px); }
        @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }

        /* Notification Badge Pulse */
        @keyframes pulse-red {
            0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
            70% { box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); }
            100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }
        .badge-pulse { animation: pulse-red 2s infinite; }
    </style>
</head>

<body class="antialiased text-gray-800">

    <div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

        {{-- MOBILE SIDEBAR OVERLAY --}}
        <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-20 md:hidden glass-panel"></div>

        {{-- SIDEBAR --}}
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="sidebar fixed inset-y-0 left-0 z-30 w-64 overflow-y-auto transition duration-300 transform md:translate-x-0 md:static md:inset-0 shadow-2xl flex flex-col">

            {{-- Logo Area --}}
            <div class="flex items-center justify-center h-20 border-b border-indigo-900/50 bg-indigo-950/50">
                <div class="flex items-center gap-3">
                    <div class="bg-indigo-500 text-white p-2 rounded-lg shadow-lg">
                        <i class="fas fa-mug-hot text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold tracking-tight text-white">CafeKU</h1>
                        <p class="text-[10px] text-indigo-300 uppercase tracking-widest font-semibold">Admin Panel</p>
                    </div>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 px-2 py-6 space-y-1">

                {{-- Dashboard --}}
                <a href="{{ route('admin.dashboard') }}" class="nav-link flex items-center px-4 py-3 {{ request()->routeIs('admin.dashboard') ? 'active' : 'text-indigo-100' }}">
                    <i class="fas fa-home w-6 text-center text-lg"></i>
                    <span class="ml-3 font-medium">Dashboard</span>
                </a>

                <p class="px-6 mt-6 mb-2 text-xs font-bold text-indigo-400 uppercase tracking-wider">Kasir & Pesanan</p>

                {{-- Pesanan Aktif (PENTING) --}}
                <a href="{{ route('admin.orders.index') }}" class="nav-link flex items-center px-4 py-3 {{ request()->routeIs('admin.orders.index') && !request()->routeIs('admin.orders.history') ? 'active' : 'text-indigo-100' }}">
                    <div class="relative">
                        <i class="fas fa-clipboard-list w-6 text-center text-lg"></i>
                        {{-- Dot indikator pesanan baru di sidebar --}}
                        <span id="sidebar-notification-dot" class="hidden absolute -top-1 -right-1 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-[#1e1b4b]"></span>
                    </div>
                    <span class="ml-3 font-medium">Pesanan Masuk</span>
                </a>

                <a href="{{ route('admin.orders.history') }}" class="nav-link flex items-center px-4 py-3 {{ request()->routeIs('admin.orders.history') ? 'active' : 'text-indigo-100' }}">
                    <i class="fas fa-history w-6 text-center text-lg"></i>
                    <span class="ml-3 font-medium">Riwayat Transaksi</span>
                </a>

                <p class="px-6 mt-6 mb-2 text-xs font-bold text-indigo-400 uppercase tracking-wider">Manajemen</p>

                <a href="{{ route('admin.menus.index') }}" class="nav-link flex items-center px-4 py-3 {{ request()->routeIs('admin.menus.*') ? 'active' : 'text-indigo-100' }}">
                    <i class="fas fa-hamburger w-6 text-center text-lg"></i>
                    <span class="ml-3 font-medium">Menu & Produk</span>
                </a>

                <a href="{{ route('admin.categories.index') }}" class="nav-link flex items-center px-4 py-3 {{ request()->routeIs('admin.categories.*') ? 'active' : 'text-indigo-100' }}">
                    <i class="fas fa-tags w-6 text-center text-lg"></i>
                    <span class="ml-3 font-medium">Kategori</span>
                </a>

                <a href="{{ route('admin.tables.index') }}" class="nav-link flex items-center px-4 py-3 {{ request()->routeIs('admin.tables.*') ? 'active' : 'text-indigo-100' }}">
                    <i class="fas fa-chair w-6 text-center text-lg"></i>
                    <span class="ml-3 font-medium">Meja Cafe</span>
                </a>

                <p class="px-6 mt-6 mb-2 text-xs font-bold text-indigo-400 uppercase tracking-wider">Laporan</p>

                <a href="{{ route('admin.reports.index') }}" class="nav-link flex items-center px-4 py-3 {{ request()->routeIs('admin.reports.*') ? 'active' : 'text-indigo-100' }}">
                    <i class="fas fa-chart-line w-6 text-center text-lg"></i>
                    <span class="ml-3 font-medium">Laporan Keuangan</span>
                </a>
            </nav>

            {{-- Sidebar Footer (User Info Simple) --}}
            <div class="p-4 border-t border-indigo-900/50 bg-indigo-950/30">
                <div class="flex items-center gap-3">
                    <img class="h-10 w-10 rounded-full object-cover border-2 border-indigo-500"
                         src="{{ Auth::user()->profile_photo_path ? asset('storage/' . Auth::user()->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=6366F1&color=fff' }}"
                         alt="Admin">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-indigo-400 truncate">Administrator</p>
                    </div>
                    {{-- Logout Button Mini --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-indigo-400 hover:text-red-400 transition-colors" title="Logout">
                            <i class="fas fa-power-off"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- MAIN CONTENT WRAPPER --}}
        <div class="flex-1 flex flex-col overflow-hidden bg-gray-50">

            {{-- HEADER NAVBAR --}}
            <header class="glass-header z-20 sticky top-0 h-16 flex items-center justify-between px-6">

                {{-- Left: Mobile Toggle & Page Title --}}
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="text-gray-500 focus:outline-none md:hidden">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h2 class="text-xl font-bold text-gray-800 tracking-tight hidden md:block">
                        @yield('title', 'Dashboard')
                    </h2>
                </div>

                {{-- Right: Actions --}}
                <div class="flex items-center gap-4">

                    {{-- NOTIFICATION DROPDOWN (AlpineJS) --}}
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open; fetchNotificationDetails();" class="relative p-2 text-gray-400 hover:text-indigo-600 transition-colors rounded-full hover:bg-indigo-50">
                            <i class="fas fa-bell text-xl"></i>

                            {{-- Badge Count --}}
                            <span id="notification-count-badge" class="hidden absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-red-500 rounded-full badge-pulse shadow-sm border border-white">0</span>
                        </button>

                        {{-- Dropdown Panel --}}
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-2"
                             class="absolute right-0 mt-3 w-80 bg-white rounded-xl shadow-2xl border border-gray-100 py-2 z-50 overflow-hidden"
                             style="display: none;">

                            <div class="px-4 py-2 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                                <h3 class="text-sm font-bold text-gray-700">Notifikasi Pesanan</h3>
                                <span class="text-xs text-indigo-500 font-medium cursor-pointer hover:underline">Tandai dibaca</span>
                            </div>

                            <div id="notification-list" class="max-h-64 overflow-y-auto">
                                {{-- JS will populate this --}}
                                <div class="p-6 text-center text-gray-400 text-sm">
                                    <i class="far fa-bell-slash text-2xl mb-2 block"></i>
                                    Tidak ada notifikasi baru
                                </div>
                            </div>

                            <div class="border-t border-gray-100 p-2">
                                <a href="{{ route('admin.orders.index') }}" class="block text-center text-sm font-semibold text-indigo-600 hover:bg-indigo-50 py-2 rounded-lg transition-colors">
                                    Lihat Semua Pesanan
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- User Profile (Simple Link to Profile) --}}
                    <a href="{{ route('profile.edit') }}" class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition-all shadow-sm">
                        <img class="h-6 w-6 rounded-full object-cover" src="{{ Auth::user()->profile_photo_path ? asset('storage/' . Auth::user()->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}" alt="">
                        <span class="text-sm font-medium text-gray-700">Profil</span>
                    </a>
                </div>
            </header>

            {{-- CONTENT SCROLL AREA --}}
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6 fade-in-up">
                @yield('content')
            </main>
        </div>
    </div>

{{-- ... (kode HTML layout sebelumnya tetap sama) ... --}}

    @stack('scripts')

    {{-- AUDIO PRELOAD --}}
    <audio id="notif-sound" src="{{ asset('audio/order_alert.mp3') }}" preload="auto"></audio>

    {{-- JAVASCRIPT LOGIC (POLLING & NOTIFIKASI) --}}
    <script>
        // --- 1. SETUP AUDIO ---
        function playNotificationSound() {
            const audio = document.getElementById('notif-sound');
            if (audio) {
                audio.play().catch(e => console.log("Audio play blocked:", e));
            }
        }

        // --- 2. LOGIKA POLLING DATA ---
        const detailUrl = "{{ route('admin.notifications.details') }}";
        const orderIndexUrl = "{{ route('admin.orders.index') }}"; // URL Halaman Pesanan Masuk

        const badgeElement = document.getElementById('notification-count-badge');
        const sidebarDot = document.getElementById('sidebar-notification-dot');
        const notificationList = document.getElementById('notification-list');

        let lastCount = 0;

        // Fetch Data dari Server
        async function fetchNotificationDetails() {
            try {
                const response = await fetch(detailUrl);
                if (!response.ok) throw new Error('Network response was not ok');
                const data = await response.json();

                updateUI(data.notifications, data.count);
                return data.count;
            } catch (error) {
                console.error("Polling error:", error);
                return 0;
            }
        }

        // Update Tampilan UI (Badge & Dropdown)
        function updateUI(notifications, count) {
            // Update Badges
            if (count > 0) {
                if(badgeElement) {
                    badgeElement.innerText = count;
                    badgeElement.classList.remove('hidden');
                }
                if(sidebarDot) sidebarDot.classList.remove('hidden');
            } else {
                if(badgeElement) badgeElement.classList.add('hidden');
                if(sidebarDot) sidebarDot.classList.add('hidden');
            }

            // Update Dropdown Content
            if (notificationList) {
                if (notifications.length === 0) {
                    notificationList.innerHTML = `
                        <div class="p-6 text-center text-gray-400 text-sm">
                            <i class="far fa-bell-slash text-2xl mb-2 block"></i>
                            Tidak ada notifikasi baru
                        </div>`;
                } else {
                    let html = '';
                    notifications.forEach(n => {
                        // Pastikan n.url ada (dari controller)
                        html += `
                        <a href="${n.url}" class="block px-4 py-3 hover:bg-indigo-50 border-b border-gray-100 last:border-0 transition-colors group">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-bold text-gray-800 group-hover:text-indigo-600 transition-colors">
                                        #${n.order_number}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-0.5">Meja: <span class="font-medium text-gray-700">${n.table_name}</span></p>
                                </div>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 border border-amber-200">
                                    BARU
                                </span>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-2 flex items-center gap-1">
                                <i class="far fa-clock"></i> ${n.time_ago}
                            </p>
                        </a>`;
                    });
                    notificationList.innerHTML = html;
                }
            }
        }

        // Polling Interval
        function startPolling() {
            // Cek pertama kali
            fetchNotificationDetails().then(count => { lastCount = count; });

            // Loop Cek setiap 5 detik
            setInterval(async () => {
                const currentCount = await fetchNotificationDetails();

                // JIKA ADA PESANAN BARU MASUK
                if (currentCount > lastCount) {
                    playNotificationSound();

                    const newOrders = currentCount - lastCount;

                    // TAMPILKAN TOAST YANG BISA DIKLIK
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true,
                        background: '#1e1b4b', // Warna Indigo Gelap
                        color: '#ffffff',
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)

                            // --- FITUR KLIK NOTIFIKASI ---
                            toast.addEventListener('click', () => {
                                window.location.href = orderIndexUrl; // Redirect ke halaman pesanan
                            });
                            toast.style.cursor = 'pointer'; // Ubah kursor jadi tangan
                        }
                    });

                    Toast.fire({
                        icon: 'warning',
                        iconColor: '#fbbf24', // Amber
                        title: 'Pesanan Baru Masuk!',
                        text: `${newOrders} pesanan menunggu konfirmasi. Klik untuk lihat.`
                    });
                }
                lastCount = currentCount;

            }, 5000); // Cek setiap 5 detik
        }

        document.addEventListener('DOMContentLoaded', startPolling);
    </script>
</body>
</html>
