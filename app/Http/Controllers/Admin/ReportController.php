<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\OrdersExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Tampilkan detail transaksi (Halaman HTML).
     */
    public function index(Request $request)
    {
        $data = $this->getFilteredData($request);

        $totalRevenue = $data['detailedTransactions']
            ->where('status', 'completed')
            ->sum('total_price');

        return view('admin.reports.index', [
            'startDate' => $data['startDate']->format('Y-m-d'),
            'endDate' => $data['endDate']->format('Y-m-d'),
            'detailedTransactions' => $data['detailedTransactions'],
            'totalRevenue' => $totalRevenue,
        ]);
    }

    /**
     * Export laporan ke PDF menggunakan template khusus.
     */
    public function exportPdf(Request $request)
    {
        // 1. Ambil data yang sama persis
        $data = $this->getFilteredData($request);

        // 2. Hitung Total Pendapatan
        $totalRevenue = $data['detailedTransactions']
            ->where('status', 'completed')
            ->sum('total_price');

        // 3. Siapkan data array untuk view PDF
        $pdfData = [
            'startDate' => $data['startDate'],
            'endDate' => $data['endDate'],
            'detailedTransactions' => $data['detailedTransactions'], // Variabel sesuai dengan template
            'totalRevenue' => $totalRevenue
        ];

        // 4. Load View PDF
        // PERBAIKAN: Mengarah ke 'admin.reports.pdf_template' sesuai permintaan Anda
        $pdf = Pdf::loadView('admin.reports.pdf_template', $pdfData);

        // Opsi tambahan agar layout tabel lebih rapi di PDF (Optional)
        $pdf->setPaper('a4', 'portrait');

        // 5. Generate nama file
        $fileName = 'Laporan_Transaksi_' . $data['startDate']->format('Ymd') . '_to_' . $data['endDate']->format('Ymd') . '.pdf';

        // 6. Download file
        return $pdf->download($fileName);
    }

    public function exportExcel(Request $request)
    {
        // 1. Tentukan Rentang Waktu (Copy logic from history)
        $period = $request->input('period', 'this_month');
        $startDate = Carbon::today();
        $endDate = Carbon::now()->endOfDay();

        switch ($period) {
            case 'today':
                $startDate = Carbon::today()->startOfDay();
                $endDate = Carbon::today()->endOfDay();
                break;
            case 'yesterday':
                $startDate = Carbon::yesterday()->startOfDay();
                $endDate = Carbon::yesterday()->endOfDay();
                break;
            case 'this_week':
                $startDate = Carbon::now()->startOfWeek()->startOfDay();
                $endDate = Carbon::now()->endOfWeek()->endOfDay();
                break;
            case 'this_month':
                $startDate = Carbon::now()->startOfMonth()->startOfDay();
                $endDate = Carbon::now()->endOfMonth()->endOfDay();
                break;
            case 'custom':
                if ($request->filled('start_date') && $request->filled('end_date')) {
                    $startDate = Carbon::parse($request->start_date)->startOfDay();
                    $endDate = Carbon::parse($request->end_date)->endOfDay();
                }
                break;
            default:
                $startDate = Carbon::now()->startOfMonth()->startOfDay();
                $endDate = Carbon::now()->endOfMonth()->endOfDay();
                break;
        }

        $fileName = 'Riwayat_Pesanan_' . $startDate->format('Ymd') . '-' . $endDate->format('Ymd') . '.xlsx';

        return Excel::download(new OrdersExport($startDate, $endDate), $fileName);
    }

    /**
     * Fungsi Helper Private untuk filter & query database.
     */
    private function getFilteredData(Request $request)
    {
        $today = Carbon::today();

        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : $today->copy()->startOfDay();

        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : $today->copy()->endOfDay();

        if ($startDate->toDateString() === $endDate->toDateString()) {
            $startDate->startOfDay();
            $endDate->endOfDay();
        }

        $detailedTransactions = Order::whereBetween('created_at', [$startDate, $endDate])
                            ->with(['table', 'payment'])
                            ->orderBy('created_at', 'asc')
                            ->get();

        return [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'detailedTransactions' => $detailedTransactions
        ];
    }
}
