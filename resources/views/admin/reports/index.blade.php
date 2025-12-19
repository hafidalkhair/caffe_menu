@extends('layouts.admin')

@section('title', 'Laporan Keuangan')

@section('content')

    {{-- HEADER SECTION --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4 fade-in-up">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Laporan Transaksi</h2>
            <p class="text-sm text-gray-500 mt-1">Analisis pendapatan dan riwayat pesanan.</p>
        </div>

        {{-- ACTION BUTTONS --}}
        <div class="flex gap-3">
            {{-- Tombol EXCEL (Pengganti Cetak) --}}
            <button type="button" id="download-excel-btn" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl shadow-sm hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200 transition-colors flex items-center gap-2 text-sm font-medium">
                <i class="fas fa-file-excel text-emerald-600"></i> Export Excel
            </button>

            {{-- Tombol PDF --}}
            <button type="button" id="download-pdf-btn" class="px-4 py-2 bg-indigo-600 text-white rounded-xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition-all flex items-center gap-2 text-sm font-medium transform hover:-translate-y-0.5">
                <i class="fas fa-file-pdf"></i> Unduh PDF
            </button>
        </div>
    </div>

    {{-- FILTER CARD --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8 fade-in-up" style="animation-delay: 0.1s;">
        <div class="flex items-center gap-2 mb-4 text-gray-800 font-bold border-b border-gray-100 pb-3">
            <div class="p-1.5 bg-indigo-50 text-indigo-600 rounded-lg">
                <i class="fas fa-filter text-sm"></i>
            </div>
            <h3>Filter Periode</h3>
        </div>

        <form id="filter-form" action="{{ route('admin.reports.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label for="start_date" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Mulai Tanggal</label>
                <input type="date" name="start_date" id="start_date" value="{{ $startDate ?? '' }}"
                    class="block w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white text-gray-800 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 sm:text-sm py-2.5 transition-all">
            </div>

            <div>
                <label for="end_date" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Sampai Tanggal</label>
                <input type="date" name="end_date" id="end_date" value="{{ $endDate ?? '' }}"
                    class="block w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white text-gray-800 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 sm:text-sm py-2.5 transition-all">
            </div>

            <div class="md:col-span-2 flex justify-start">
                <button type="submit" class="w-full md:w-auto px-6 py-2.5 bg-gray-800 text-white rounded-xl hover:bg-gray-700 font-medium transition-colors shadow-md">
                    Terapkan Filter
                </button>
            </div>
        </form>

        <div class="mt-4 pt-4 border-t border-gray-50 flex items-center gap-2 text-sm text-gray-500">
            <i class="far fa-calendar-check text-indigo-500"></i>
            Menampilkan data dari: <span class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}</span> s/d <span class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</span>
        </div>
    </div>

    {{-- TABEL DATA --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden fade-in-up" style="animation-delay: 0.2s;">
        <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/30">
            <h3 class="text-lg font-bold text-gray-800">Rincian Pesanan</h3>
            <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-lg text-xs font-bold border border-gray-200">
                Total Data: {{ count($detailedTransactions ?? []) }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">No / ID</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Pelanggan</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-wider">Meja</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-wider">Metode</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 bg-white">
                    @forelse($detailedTransactions ?? [] as $index => $order)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            {{-- No / ID --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">#{{ $order->order_number }}</div>
                                <div class="text-[10px] text-gray-400 font-mono">ID: {{ $order->id }}</div>
                            </td>

                            {{-- Waktu --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-600 font-medium">{{ $order->created_at->format('d M Y') }}</div>
                                <div class="text-xs text-gray-400">{{ $order->created_at->format('H:i') }} WIB</div>
                            </td>

                            {{-- Pelanggan --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 text-xs font-bold">
                                        {{ substr($order->customer_name ?? 'A', 0, 1) }}
                                    </div>
                                    <span class="text-sm font-semibold text-gray-700">{{ $order->customer_name ?? 'Guest' }}</span>
                                </div>
                            </td>

                            {{-- Meja --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-2.5 py-1 bg-gray-100 text-gray-600 rounded-md text-xs font-bold border border-gray-200">
                                    {{ $order->table->name ?? '-' }}
                                </span>
                            </td>

                            {{-- Metode --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                {{ ucfirst($order->payment->method ?? '-') }}
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if ($order->status == 'completed')
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-emerald-50 text-emerald-600 border border-emerald-100">
                                        Completed
                                    </span>
                                @elseif($order->status == 'canceled')
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-rose-50 text-rose-600 border border-rose-100">
                                        Canceled
                                    </span>
                                @elseif($order->status == 'processing')
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-blue-50 text-blue-600 border border-blue-100">
                                        Processing
                                    </span>
                                @else
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-amber-50 text-amber-600 border border-amber-100">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                @endif
                            </td>

                            {{-- Total --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="text-sm font-bold {{ $order->status == 'canceled' ? 'text-gray-400 line-through' : 'text-gray-800' }}">
                                    Rp{{ number_format($order->total_price, 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12">
                                <div class="inline-block p-4 rounded-full bg-gray-50 text-gray-300 mb-3">
                                    <i class="fas fa-file-invoice-dollar text-3xl"></i>
                                </div>
                                <h3 class="text-gray-900 font-medium">Tidak ada data transaksi</h3>
                                <p class="text-gray-500 text-sm mt-1">Coba ubah filter periode tanggal di atas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- SCRIPT DOWNLOAD (PDF & EXCEL) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Helper function to build URL with params
            function downloadReport(baseUrl) {
                const startDate = document.getElementById('start_date').value;
                const endDate = document.getElementById('end_date').value;
                let params = [];

                if (startDate) params.push(`start_date=${startDate}`);
                if (endDate) params.push(`end_date=${endDate}`);

                if (params.length > 0) {
                    baseUrl += '?' + params.join('&');
                }
                window.open(baseUrl, '_blank');
            }

            // PDF Button
            const pdfBtn = document.getElementById('download-pdf-btn');
            if (pdfBtn) {
                pdfBtn.addEventListener('click', function() {
                    downloadReport("{{ route('admin.reports.export.pdf') }}");
                });
            }

            // EXCEL Button (NEW)
            const excelBtn = document.getElementById('download-excel-btn');
            if (excelBtn) {
                excelBtn.addEventListener('click', function() {
                    downloadReport("{{ route('admin.reports.export.excel') }}");
                });
            }
        });
    </script>

@endsection
