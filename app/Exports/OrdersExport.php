<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrdersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        // Query yang sama dengan controller
        return Order::whereBetween('created_at', [$this->startDate, $this->endDate])
            ->with(['table', 'payment'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID Order',
            'No. Pesanan',
            'Waktu Pesanan',
            'Nama Pelanggan',
            'Meja',
            'Status Order',
            'Status Bayar',
            'Metode Bayar',
            'Total Harga (Rp)',
        ];
    }

    public function map($order): array
    {
        return [
            $order->id,
            $order->order_number,
            $order->created_at->format('d/m/Y H:i'),
            $order->customer_name,
            $order->table->name ?? '-',
            strtoupper($order->status),
            strtoupper($order->payment->status ?? 'UNPAID'),
            ucfirst($order->payment->method ?? '-'),
            $order->total_price,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style Baris 1 (Header) menjadi Bold
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
