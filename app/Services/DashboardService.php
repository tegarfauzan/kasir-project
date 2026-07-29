<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * @return array<string, mixed>
     */
    public function summary(string $period): array
    {
        [$from, $to] = $this->range($period);

        $orders = Order::query()
            ->paid()
            ->whereBetween('paid_at', [$from->startOfDay(), $to->endOfDay()]);

        $chart = (clone $orders)
            ->selectRaw('DATE(paid_at) as sale_date, SUM(total_amount) as total')
            ->groupBy(DB::raw('DATE(paid_at)'))
            ->orderBy('sale_date')
            ->get()
            ->map(fn (Order $order) => [
                'label' => CarbonImmutable::parse($order->sale_date)->format('d M'),
                'total' => (int) $order->total,
            ]);

        return [
            'period' => $period,
            'from' => $from,
            'to' => $to,
            'total_omzet' => (int) (clone $orders)->sum('total_amount'),
            'total_transactions' => (clone $orders)->count(),
            'active_products' => Product::query()->active()->count(),
            'top_products' => $this->topProducts($from, $to),
            'latest_orders' => Order::query()
                ->with('cashier')
                ->latest('paid_at')
                ->limit(8)
                ->get(),
            'chart' => $chart,
            'chart_max' => max(1, (int) $chart->max('total')),
        ];
    }

    /**
     * @return array{0: CarbonImmutable, 1: CarbonImmutable}
     */
    private function range(string $period): array
    {
        $today = CarbonImmutable::now();

        return match ($period) {
            '7days' => [$today->subDays(6), $today],
            'month' => [$today->startOfMonth(), $today],
            default => [$today, $today],
        };
    }

    private function topProducts(CarbonImmutable $from, CarbonImmutable $to): Collection
    {
        return OrderItem::query()
            ->select('product_name')
            ->selectRaw('SUM(quantity) as total_quantity')
            ->selectRaw('SUM(subtotal_amount) as total_amount')
            ->whereHas('order', fn ($query) => $query->paid()->whereBetween('paid_at', [$from->startOfDay(), $to->endOfDay()]))
            ->groupBy('product_name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();
    }
}
