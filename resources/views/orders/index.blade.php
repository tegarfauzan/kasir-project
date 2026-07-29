<x-app-layout>
    <x-slot name="headerTitle">Riwayat Transaksi</x-slot>
    <x-slot name="headerSubtitle">Cari invoice, filter tanggal, metode bayar, dan cetak ulang struk.</x-slot>

    @php($money = fn ($amount) => 'Rp'.number_format((int) $amount, 0, ',', '.'))

    <div class="space-y-5">
        <form method="GET" action="{{ route('orders.index') }}" class="app-card p-4">
            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-[1fr_170px_170px_180px_160px_auto]">
                <input name="search" value="{{ request('search') }}" class="app-input" placeholder="Search invoice...">
                <input name="from" type="date" value="{{ request('from') }}" class="app-input">
                <input name="to" type="date" value="{{ request('to') }}" class="app-input">
                <select name="payment_method" class="app-input">
                    <option value="">Semua pembayaran</option>
                    @foreach ($paymentMethods as $method)
                        <option value="{{ $method->value }}" @selected(request('payment_method') === $method->value)>{{ $method->label() }}</option>
                    @endforeach
                </select>
                <select name="status" class="app-input">
                    <option value="">Semua status</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>
                <button class="btn-primary" type="submit">Filter</button>
            </div>

            @role('admin')
                <div class="mt-3 flex justify-end">
                    <a href="{{ route('reports.sales.pdf', request()->only(['from', 'to'])) }}" class="btn-secondary">Export laporan PDF</a>
                </div>
            @endrole
        </form>

        <section class="app-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="table-head">
                        <tr>
                            <th class="px-4 py-3">Invoice</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Kasir</th>
                            <th class="px-4 py-3">Total</th>
                            <th class="px-4 py-3">Pembayaran</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr class="hover:bg-coffee-200/20 dark:hover:bg-slate-800">
                                <td class="table-cell font-bold">{{ $order->invoice_number }}</td>
                                <td class="table-cell">{{ $order->paid_at?->format('d M Y H:i') }}</td>
                                <td class="table-cell">{{ $order->cashier?->name }}</td>
                                <td class="table-cell">{{ $money($order->total_amount) }}</td>
                                <td class="table-cell">{{ $order->payment_method->label() }}</td>
                                <td class="table-cell">
                                    <span class="badge {{ $order->status->value === 'paid' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-200' : 'bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-200' }}">
                                        {{ $order->status->label() }}
                                    </span>
                                </td>
                                <td class="table-cell">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('orders.show', $order) }}" class="btn-secondary px-3 py-2">Detail</a>
                                        <a href="{{ route('orders.receipt', $order) }}" class="btn-secondary px-3 py-2">Struk</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="table-cell text-center">Belum ada transaksi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{ $orders->links() }}
    </div>
</x-app-layout>
