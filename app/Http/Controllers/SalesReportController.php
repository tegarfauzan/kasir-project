<?php

namespace App\Http\Controllers;

use App\Http\Requests\Report\ExportSalesReportRequest;
use App\Repositories\OrderRepository;
use App\Repositories\StoreSettingRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class SalesReportController extends Controller
{
    public function __construct(
        private readonly OrderRepository $orders,
        private readonly StoreSettingRepository $settings,
    ) {
    }

    public function exportPdf(ExportSalesReportRequest $request): Response
    {
        $filters = $request->validated();
        $orders = $this->orders->paidReport($filters);

        $pdf = Pdf::loadView('reports.sales-pdf', [
            'orders' => $orders,
            'settings' => $this->settings->current(),
            'filters' => $filters,
            'totalOmzet' => (int) $orders->sum('total_amount'),
            'totalTransactions' => $orders->count(),
        ])->setPaper('a4');

        return $pdf->download('laporan-penjualan-'.now()->format('Ymd-His').'.pdf');
    }
}
