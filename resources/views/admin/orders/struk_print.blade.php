<!DOCTYPE html>
<html>
<head>
    <title>Struk Pesanan #{{ $order->order_number }}</title>
    <style>
        /* CSS Khusus untuk Struk Thermal (58mm) */
        body {
            width: 58mm;
            margin: 0;
            padding: 0;
            font-family: 'Courier New', Courier, monospace; /* Monospace agar rapi */
            font-size: 9pt;
            color: #000;
        }
        @page {
            size: 58mm auto;
            margin: 0;
        }
        .container {
            padding: 2px 5px;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
        .item-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 3px;
        }
        .item-qty {
            width: 15%;
            font-weight: bold;
        }
        .item-name {
            width: 55%;
            word-wrap: break-word;
        }
        .item-total {
            width: 30%;
            text-align: right;
        }
        .total-ringkasan {
            font-weight: bold;
            font-size: 10pt;
            margin-top: 5px;
        }
        .meta-info {
            font-size: 8pt;
        }

        /* Sembunyikan elemen browser saat print */
        @media print {
            html, body {
                width: 58mm;
                height: auto;
                overflow: hidden;
            }
        }
    </style>
</head>

{{-- LOGIKA PHP UNTUK MENGHITUNG TOTAL SESUAI TAMPILAN --}}
@php
    $user = Auth::user();

    // Default: Ambil total global dari database
    $displayTotal = $order->total_price;

    // Jika Dapur: Hitung ulang total berdasarkan item yang tampil (karena item sudah difilter di Controller)
    if ($user->role === 'dapur') {
        $displayTotal = $order->orderItems->sum(function($item) {
            return $item->price * $item->quantity;
        });
    }
@endphp

<body>
    <div class="container">
        <div class="text-center">
            <strong>CAFEKU</strong><br>
            Jl. Raya Contoh No. 123<br>
            Jakarta Selatan<br>
        </div>

        <div class="divider"></div>

        <div class="meta-info">
            ID: #{{ $order->order_number }}<br>
            Tgl: {{ $order->created_at->format('d/m/y H:i') }}<br>
            Meja: {{ $order->table->name ?? 'Takeaway' }}<br>
            Cust: {{ substr($order->customer_name ?? 'Guest', 0, 15) }}<br>
            Kasir: {{ substr($user->name, 0, 15) }}
        </div>

        <div class="divider"></div>

        {{-- Detail Item --}}
        {{-- Loop ini otomatis menggunakan data yang sudah difilter oleh Controller (MenuController / OrderController) --}}
        @foreach ($order->orderItems as $item)
            <div class="item-row">
                <span class="item-qty">{{ $item->quantity }}x</span>
                <span class="item-name">
                    {{ $item->menu->name ?? 'Item Dihapus' }}
                    @if($item->notes)
                        <br><i style="font-size: 7pt;">({{ $item->notes }})</i>
                    @endif
                </span>
                <span class="item-total">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
            </div>
        @endforeach

        <div class="divider"></div>

        {{-- Ringkasan --}}
        <div class="item-row total-ringkasan">
            <span>TOTAL</span>
            <span class="item-total">Rp{{ number_format($displayTotal, 0, ',', '.') }}</span>
        </div>

        <div class="divider"></div>

        <div class="meta-info text-center">
            Bayar: {{ strtoupper($order->payment->method ?? 'TUNAI') }}<br>
            Status: {{ $order->payment->status == 'paid' ? 'LUNAS' : 'BELUM BAYAR' }}
        </div>

        @if ($order->notes)
            <div class="divider"></div>
            <div class="meta-info">
                <strong>Catatan:</strong><br>
                {{ $order->notes }}
            </div>
        @endif

        <div class="divider"></div>

        <div class="text-center" style="margin-top: 10px; font-size: 8pt;">
            Terima Kasih<br>
            Silakan Datang Kembali
        </div>
        <br>
    </div>

    <script>
        // Otomatis print saat halaman dimuat
        window.onload = function() {
            window.print();
            // Cek jika browser mendukung event afterprint untuk close tab
            window.onafterprint = function() {
                window.close();
            };
        }
    </script>
</body>
</html>
