<x-app-layout>
    <x-slot name="headerTitle">Dashboard</x-slot>
    <x-slot name="headerSubtitle">Ringkasan performa penjualan yang ringan dibaca.</x-slot>

    @php
        $money = fn ($amount) => 'Rp'.number_format((int) $amount, 0, ',', '.');
        $periods = [
            'today' => 'Hari ini',
            '7days' => '7 hari terakhir',
            'month' => 'Bulan ini',
        ];
    @endphp

    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap gap-2">
                @foreach ($periods as $key => $label)
                    <a href="{{ route('dashboard', ['period' => $key]) }}" class="{{ $summary['period'] === $key ? 'btn-primary' : 'btn-secondary' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <a href="{{ route('reports.sales.pdf', ['from' => $summary['from']->toDateString(), 'to' => $summary['to']->toDateString()]) }}" class="btn-secondary">
                Export PDF
            </a>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="app-card p-5">
                <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">Total omzet</p>
                <p class="mt-3 text-3xl font-extrabold text-coffee-950 dark:text-coffee-200">{{ $money($summary['total_omzet']) }}</p>
            </div>
            <div class="app-card p-5">
                <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">Total transaksi</p>
                <p class="mt-3 text-3xl font-extrabold text-coffee-950 dark:text-coffee-200">{{ number_format($summary['total_transactions']) }}</p>
            </div>
            <div class="app-card p-5">
                <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">Produk aktif</p>
                <p class="mt-3 text-3xl font-extrabold text-coffee-950 dark:text-coffee-200">{{ number_format($summary['active_products']) }}</p>
            </div>
            <div class="app-card p-5">
                <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">Menu paling laku</p>
                <p class="mt-3 text-2xl font-extrabold text-coffee-950 dark:text-coffee-200">
                    {{ $summary['top_products']->first()->product_name ?? '-' }}
                </p>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.5fr_1fr]">
            <section class="app-card p-5">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-extrabold text-coffee-950 dark:text-coffee-200">Grafik penjualan</h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $summary['from']->format('d M Y') }} - {{ $summary['to']->format('d M Y') }}</p>
                    </div>
                </div>

                <div class="mt-6 flex h-72 items-end gap-3 overflow-x-auto rounded-2xl border border-coffee-100 bg-cream/60 p-4 dark:border-slate-800 dark:bg-slate-950">
                    @forelse ($summary['chart'] as $point)
                        <div class="flex min-w-16 flex-1 flex-col items-center gap-2">
                            <div class="text-xs font-bold text-slate-500 dark:text-slate-400">{{ $money($point['total']) }}</div>
                            <div class="w-full rounded-t-2xl bg-coffee-700 transition duration-300 hover:bg-coffee-950 dark:bg-coffee-300" style="height: {{ max(8, ((int) $point['total'] / $summary['chart_max']) * 190) }}px"></div>
                            <div class="text-xs font-semibold text-slate-600 dark:text-slate-300">{{ $point['label'] }}</div>
                        </div>
                    @empty
                        <div class="m-auto text-center text-sm font-semibold text-slate-500 dark:text-slate-400">Belum ada penjualan pada periode ini.</div>
                    @endforelse
                </div>
            </section>

            <section class="app-card p-5">
                <h2 class="text-lg font-extrabold text-coffee-950 dark:text-coffee-200">Produk/menu paling laku</h2>
                <div class="mt-4 space-y-3">
                    @forelse ($summary['top_products'] as $product)
                        <div class="rounded-2xl border border-coffee-100 p-4 dark:border-slate-800">
                            <div class="flex items-center justify-between gap-3">
                                <div class="font-bold text-slate-900 dark:text-slate-100">{{ $product->product_name }}</div>
                                <span class="badge bg-coffee-200 text-coffee-950 dark:bg-coffee-300 dark:text-slate-950">{{ $product->total_quantity }}x</span>
                            </div>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $money($product->total_amount) }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 dark:text-slate-400">Belum ada produk terjual.</p>
                    @endforelse
                </div>
            </section>
        </div>

        <section class="app-card overflow-hidden">
            <div class="flex items-center justify-between gap-3 p-5">
                <h2 class="text-lg font-extrabold text-coffee-950 dark:text-coffee-200">Transaksi terbaru</h2>
                <a href="{{ route('orders.index') }}" class="btn-secondary px-3 py-2">Lihat semua</a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="table-head">
                        <tr>
                            <th class="px-4 py-3">Invoice</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Kasir</th>
                            <th class="px-4 py-3">Total</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($summary['latest_orders'] as $order)
                            <tr class="hover:bg-coffee-200/20 dark:hover:bg-slate-800">
                                <td class="table-cell font-bold">{{ $order->invoice_number }}</td>
                                <td class="table-cell">{{ $order->paid_at?->format('d M Y H:i') }}</td>
                                <td class="table-cell">{{ $order->cashier?->name }}</td>
                                <td class="table-cell">{{ $money($order->total_amount) }}</td>
                                <td class="table-cell">
                                    <span class="badge {{ $order->status->value === 'paid' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-200' : 'bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-200' }}">
                                        {{ $order->status->label() }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="table-cell text-center">Belum ada transaksi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-app-layout>
