<!DOCTYPE html>
<html>

<head>
    <title>Struk Pesanan #{{ $order->id }}</title>
    <style>
        /* CSS Khusus untuk Struk Thermal */
        body {
            width: 58mm;
            /* Lebar kertas termal umum */
            margin: 0;
            padding: 0;
            font-family: monospace;
            font-size: 9pt;
            color: #000;
        }

        @page {
            size: auto;
            margin: 0;
        }

        .container {
            padding: 5px;
        }

        .text-center {
            text-align: center;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
        }

        .item-qty {
            width: 10%;
        }

        .item-name {
            width: 60%;
        }

        .item-total {
            width: 30%;
            text-align: right;
        }

        .total-ringkasan {
            font-weight: bold;
        }

        /* Media Print untuk mencegah elemen navbar/footer muncul */
        @media print {

            html,
            body {
                width: 58mm;
                overflow: hidden;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="container">
        <div class="text-center">
            <strong>CAFEKU</strong><br>
            Jl. Contoh No. 123, Kota Anda<br>
            (021) 12345678<br>
        </div>

        <div class="divider"></div>

        <div>
            ID Pesanan: {{ $order->order_number }}<br>
            Nama Pelanggan: {{ $order->customer_name ?? 'Anonim' }}<br>
            Meja: {{ $order->table->name ?? 'Dine In' }}<br>
            Waktu: {{ \Carbon\Carbon::parse($order->created_at)->format('d M y, H:i') }}<br>
            Kasir: Admin/Pelayan (Nama Admin Login Saat Ini)<br>
        </div>

        <div class="divider"></div>

        {{-- Detail Item --}}
        @foreach ($order->orderItems as $item)
            <div class="item-row">
                <span class="item-qty">{{ $item->quantity }}x</span>
                <span class="item-name">{{ $item->menu->name ?? 'Item Dihapus' }}</span>
                <span class="item-total">Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
            </div>
        @endforeach

        <div class="divider"></div>

        {{-- Ringkasan --}}
        <div class="item-row">
            <span>Subtotal</span>
            <span class="item-total">Rp{{ number_format($order->total_price, 0, ',', '.') }}</span>
        </div>

        {{-- Tambahkan baris Diskon/Pajak jika ada --}}

        <div class="divider"></div>

        <div class="item-row total-ringkasan">
            <span>TOTAL BAYAR</span>
            <span class="item-total">Rp{{ number_format($order->total_price, 0, ',', '.') }}</span>
        </div>

        <div class="divider"></div>

        <div>
            Metode Bayar: {{ strtoupper($order->payment_method) }}
            ({{ $order->status === 'completed' || $order->status === 'processing' || $order->status === 'delivering' ? 'LUNAS' : 'BELUM LUNAS' }})
        </div>

        @if ($order->note)
            <div style="margin-top: 5px;">
                Catatan: {{ $order->note }}
            </div>
        @endif

        <div class="divider"></div>

        <div class="text-center">
            TERIMA KASIH ATAS KUNJUNGAN ANDA
        </div>

    </div>

    <script>
        // Panggil window.print() saat body dimuat
        window.onload = function() {
            window.print();
            // Opsional: Kembali ke halaman sebelumnya setelah mencetak
            window.onafterprint = function() {
                window.history.back();
            };
        }
    </script>
</body>

</html>
