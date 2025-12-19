@extends('layouts.admin')

@section('title', 'Overview')

@section('content')

    {{-- HEADER SECTION --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4 fade-in-up">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Ringkasan Operasional</h2>
            <p class="text-sm text-gray-500 mt-1">Pantau performa resto Anda hari ini.</p>
        </div>

        {{-- FILTER WAKTU (Integrated UI) --}}
        <div class="bg-white p-1.5 rounded-xl border border-gray-200 shadow-sm flex items-center gap-2">
            <form id="time-filter-form" action="{{ route('admin.dashboard') }}" method="GET" class="flex items-center gap-2">
                <div class="relative">
                    <i class="fas fa-calendar-alt absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <select name="time_filter" id="time-filter" onchange="toggleCustomDates();"
                        class="pl-8 pr-8 py-2 bg-gray-50 hover:bg-gray-100 border-none rounded-lg text-sm font-semibold text-gray-700 focus:ring-2 focus:ring-indigo-500 transition-colors cursor-pointer appearance-none">
                        <option value="today" {{ $timeFilter == 'today' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="yesterday" {{ $timeFilter == 'yesterday' ? 'selected' : '' }}>Kemarin</option>
                        <option value="this_week" {{ $timeFilter == 'this_week' ? 'selected' : '' }}>Minggu Ini</option>
                        <option value="this_month" {{ $timeFilter == 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
                        <option value="this_year" {{ $timeFilter == 'this_year' ? 'selected' : '' }}>Tahun Ini</option>
                        <option value="custom" {{ $timeFilter == 'custom' ? 'selected' : '' }}>Custom</option>
                    </select>
                </div>

                {{-- Custom Date Inputs (Hidden by default) --}}
                <div id="custom-dates" class="hidden flex items-center gap-2 border-l border-gray-200 pl-2">
                    <input type="date" name="start_date" value="{{ $customDate['start'] ?? '' }}" class="py-1.5 px-2 bg-gray-50 border border-gray-200 rounded-md text-xs">
                    <span class="text-gray-400">-</span>
                    <input type="date" name="end_date" value="{{ $customDate['end'] ?? '' }}" class="py-1.5 px-2 bg-gray-50 border border-gray-200 rounded-md text-xs">
                    <button type="submit" class="p-1.5 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 shadow-sm">
                        <i class="fas fa-check text-xs"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- STATS CARDS GRID --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10 fade-in-up" style="animation-delay: 0.1s;">

        {{-- Card 1: Menunggu --}}
        <a href="{{ route('admin.orders.index') }}" class="group bg-white p-5 rounded-2xl border border-gray-100 hover:border-amber-300 shadow-sm hover:shadow-md transition-all duration-300 relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <i class="fas fa-clock text-6xl text-amber-500"></i>
            </div>
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-amber-50 rounded-lg text-amber-600">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Menunggu</span>
            </div>
            <h3 class="text-3xl font-extrabold text-gray-800 mb-1 group-hover:text-amber-600 transition-colors">
                {{ $pendingCount ?? 0 }}
            </h3>
            <p class="text-xs text-gray-400">Belum dibayar/dikonfirmasi</p>
        </a>

        {{-- Card 2: Diproses --}}
        <a href="{{ route('admin.orders.index') }}" class="group bg-white p-5 rounded-2xl border border-gray-100 hover:border-indigo-300 shadow-sm hover:shadow-md transition-all duration-300 relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <i class="fas fa-fire-burner text-6xl text-indigo-500"></i>
            </div>
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                    <i class="fas fa-concierge-bell"></i>
                </div>
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Diproses</span>
            </div>
            <h3 class="text-3xl font-extrabold text-gray-800 mb-1 group-hover:text-indigo-600 transition-colors">
                {{ $inProgressCount ?? 0 }}
            </h3>
            <p class="text-xs text-gray-400">Sedang dimasak/diantar</p>
        </a>

        {{-- Card 3: Selesai --}}
        <a href="{{ route('admin.orders.history') }}" class="group bg-white p-5 rounded-2xl border border-gray-100 hover:border-green-300 shadow-sm hover:shadow-md transition-all duration-300 relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <i class="fas fa-check-circle text-6xl text-green-500"></i>
            </div>
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-green-50 rounded-lg text-green-600">
                    <i class="fas fa-check"></i>
                </div>
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Selesai</span>
            </div>
            <h3 class="text-3xl font-extrabold text-gray-800 mb-1 group-hover:text-green-600 transition-colors">
                {{ $completedCount ?? 0 }}
            </h3>
            <p class="text-xs text-gray-400">Transaksi sukses</p>
        </a>

        {{-- Card 4: Dibatalkan --}}
        <a href="{{ route('admin.orders.history') }}" class="group bg-white p-5 rounded-2xl border border-gray-100 hover:border-red-300 shadow-sm hover:shadow-md transition-all duration-300 relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <i class="fas fa-ban text-6xl text-red-500"></i>
            </div>
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-red-50 rounded-lg text-red-600">
                    <i class="fas fa-times"></i>
                </div>
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Batal</span>
            </div>
            <h3 class="text-3xl font-extrabold text-gray-800 mb-1 group-hover:text-red-600 transition-colors">
                {{ $canceledCount ?? 0 }}
            </h3>
            <p class="text-xs text-gray-400">Expired / Ditolak</p>
        </a>
    </div>

    {{-- FINANCIAL SECTION --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8 fade-in-up" style="animation-delay: 0.2s;">

        {{-- Revenue Big Card --}}
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Analitik Pendapatan</h3>
                    <p class="text-xs text-gray-400">Tren pemasukan 7 hari terakhir.</p>
                </div>
                {{-- Legend Custom --}}
                <div class="flex items-center gap-2 text-xs">
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                    <span class="text-gray-500">Revenue</span>
                </div>
            </div>
            <div class="h-64 w-full">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        {{-- Financial Summary Small Cards --}}
        <div class="flex flex-col gap-6">

            {{-- Total Revenue --}}
            <div class="bg-gradient-to-br from-indigo-600 to-indigo-800 rounded-3xl p-6 text-white shadow-lg shadow-indigo-200 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10">
                    <i class="fas fa-wallet text-8xl"></i>
                </div>
                <p class="text-indigo-200 text-xs font-bold uppercase tracking-wider mb-1">Total Pendapatan</p>
                <h3 class="text-3xl font-extrabold mb-1 tracking-tight">
                    Rp{{ number_format($totalRevenue ?? 0, 0, ',', '.') }}
                </h3>
                <p class="text-xs text-indigo-300 mt-2 flex items-center gap-1">
                    <i class="fas fa-calendar-day"></i> {{ $filteredLabel }}
                </p>
            </div>

            {{-- Lost Revenue --}}
            <div class="bg-white rounded-3xl p-6 border border-red-100 shadow-sm relative overflow-hidden">
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-red-50 rounded-full z-0"></div>
                <div class="relative z-10">
                    <p class="text-red-500 text-xs font-bold uppercase tracking-wider mb-1">Potensi Hilang (Batal)</p>
                    <h3 class="text-2xl font-bold text-gray-800 mb-1">
                        Rp{{ number_format($filteredLoss ?? 0, 0, ',', '.') }}
                    </h3>
                    <p class="text-xs text-gray-400 mt-1">
                        Dari {{ $canceledCount ?? 0 }} pesanan dibatalkan.
                    </p>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let isInitializing = true;

    function toggleCustomDates() {
        const filter = document.getElementById('time-filter').value;
        const customDatesDiv = document.getElementById('custom-dates');

        if (filter === 'custom') {
            customDatesDiv.classList.remove('hidden');
            customDatesDiv.classList.add('flex');
        } else {
            customDatesDiv.classList.add('hidden');
            customDatesDiv.classList.remove('flex');

            if (!isInitializing) {
                 document.getElementById('time-filter-form').submit();
            }
        }
    }

    function initializeRevenueChart() {
        const canvasElement = document.getElementById('revenueChart');
        if (!canvasElement) return;

        const ctx = canvasElement.getContext('2d');

        if (Chart.getChart(canvasElement)) {
            Chart.getChart(canvasElement).destroy();
        }

        const defaultChartData = { labels: [], data: [] };
        // Parse data safely
        let chartDataRaw = '{!! json_encode($chartData ?? $defaultChartData) !!}';
        let chartData = JSON.parse(chartDataRaw);

        // Modern Gradient
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(99, 102, 241, 0.5)'); // Indigo-500
        gradient.addColorStop(1, 'rgba(99, 102, 241, 0.0)'); // Transparent

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Pendapatan',
                    data: chartData.data,
                    backgroundColor: gradient,
                    borderColor: '#4f46e5', // Indigo-600
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#4f46e5',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4 // Smooth curve
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e1b4b',
                        padding: 12,
                        titleFont: { size: 13, family: "'Plus Jakarta Sans', sans-serif" },
                        bodyFont: { size: 13, family: "'Plus Jakarta Sans', sans-serif" },
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                let value = context.parsed.y;
                                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(value);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9', borderDash: [4, 4] },
                        ticks: {
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 10 },
                            color: '#94a3b8',
                            callback: function(value) {
                                if (value >= 1000000) return (value/1000000) + 'jt';
                                if (value >= 1000) return (value/1000) + 'k';
                                return value;
                            }
                        },
                        border: { display: false }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 10 },
                            color: '#64748b'
                        },
                        border: { display: false }
                    }
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        toggleCustomDates();
        setTimeout(() => { isInitializing = false; }, 200);
        setTimeout(initializeRevenueChart, 100);
    });
</script>
@endpush
