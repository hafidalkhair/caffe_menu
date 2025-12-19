<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi - CafeKU</title>
    <style>
        /* Base Styling */
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        /* Header Layout (Table based for PDF compatibility) */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #4f46e5; /* Indigo */
        }
        .report-title {
            font-size: 14px;
            font-weight: bold;
            color: #111;
            margin-top: 5px;
        }
        .meta-info {
            text-align: right;
            font-size: 10px;
            color: #555;
        }

        /* Summary Section */
        .summary-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: separate;
            border-spacing: 10px 0;
        }
        .summary-box {
            background-color: #f3f4f6;
            border: 1px solid #e5e7eb;
            padding: 10px;
            border-radius: 5px;
            width: 48%; /* Adjust for 2 columns */
        }
        .summary-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 5px;
        }
        .summary-value {
            font-size: 16px;
            font-weight: bold;
            color: #059669;
        }

        /* Main Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .data-table th {
            background-color: #4f46e5;
            color: #ffffff;
            padding: 8px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
        }
        .data-table td {
            border-bottom: 1px solid #e5e7eb;
            padding: 8px;
            vertical-align: middle;
        }
        /* Zebra Striping */
        .data-table tr:nth-child(even) {
            background-color: #f9fafb;
        }

        /* Helpers */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }

        /* Status Badges */
        .badge {
            padding: 3px 6px;
            border-radius: 4px;
            color: white;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
        }
        .status-completed { background-color: #10b981; } /* Green */
        .status-canceled { background-color: #ef4444; }  /* Red */
        .status-pending { background-color: #f59e0b; }   /* Orange */
        .status-processing { background-color: #3b82f6; } /* Blue */

        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    {{-- 1. HEADER --}}
    <table class="header-table">
        <tr>
            <td width="60%">
                <div class="company-name">CafeKU</div>
                <div class="report-title">Laporan Detail Transaksi</div>
            </td>
            <td width="40%" class="text-right">
                <div class="meta-info">
                    <strong>Periode:</strong> {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}<br>
                    <strong>Dicetak:</strong> {{ now()->format('d F Y, H:i') }}<br>
                    <strong>Oleh:</strong> {{ auth()->user()->name ?? 'Admin' }}
                </div>
            </td>
        </tr>
    </table>

    {{-- 2. RINGKASAN --}}
    <table class="summary-table">
        <tr>
            <td class="summary-box">
                <div class="summary-label">Total Pendapatan Bersih</div>
                <div class="summary-value">Rp{{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</div>
            </td>
            <td class="summary-box">
                <div class="summary-label">Total Pesanan</div>
                <div class="summary-value" style="color: #374151;">
                    {{ count($detailedTransactions ?? []) }} Transaksi
                </div>
            </td>
        </tr>
    </table>

    {{-- 3. TABEL DATA --}}
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">ID Order</th>
                <th width="15%">Waktu</th>
                <th width="10%" class="text-center">Status</th>
                <th width="10%">Metode</th>
                <th width="15%">Pelanggan</th>
                <th width="10%">Meja</th>
                <th width="20%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            {{-- PERBAIKAN: Menggunakan variable $detailedTransactions --}}
            @forelse($detailedTransactions ?? [] as $index => $order)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="font-bold">#{{ $order->order_number }}</td>
                    <td>{{ $order->created_at->format('d/m/y H:i') }}</td>
                    <td class="text-center">
                        @php
                            $statusClass = match($order->status) {
                                'completed' => 'status-completed',
                                'canceled' => 'status-canceled',
                                'processing' => 'status-processing',
                                default => 'status-pending'
                            };
                        @endphp
                        <span class="badge {{ $statusClass }}">
                            {{ strtoupper($order->status) }}
                        </span>
                    </td>
                    <td>{{ ucfirst($order->payment->method ?? '-') }}</td>
                    <td>{{ $order->customer_name ?? 'Guest' }}</td>
                    <td>{{ $order->table->name ?? '-' }}</td>
                    <td class="text-right font-bold">
                        Rp{{ number_format($order->total_price, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px; color: #999;">
                        Tidak ada data transaksi pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- 4. FOOTER --}}
    <div class="footer">
        Dicetak otomatis oleh Sistem CafeKU | Halaman <script type="text/php">if (isset($pdf)) { echo $pdf->get_page_number(); }</script>
    </div>

</body>
</html>
