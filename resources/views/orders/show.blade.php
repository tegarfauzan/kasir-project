<x-app-layout>
    <x-slot name="headerTitle">Detail Transaksi</x-slot>
    <x-slot name="headerSubtitle">{{ $order->invoice_number }}</x-slot>

    @php($money = fn ($amount) => 'Rp'.number_format((int) $amount, 0, ',', '.'))

    <div class="grid gap-6 xl:grid-cols-[1fr_360px]">
        <section class="app-card overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-3 p-5">
                <div>
                    <h2 class="text-xl font-extrabold text-coffee-950 dark:text-coffee-200">{{ $order->invoice_number }}</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ $order->paid_at?->format('d M Y H:i') }} oleh {{ $order->cashier?->name }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('orders.receipt', $order) }}" class="btn-primary">Cetak struk</a>
                    <a href="{{ route('orders.thermal', $order) }}" target="_blank" class="btn-secondary">Teks thermal</a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="table-head">
                        <tr>
                            <th class="px-4 py-3">Item</th>
                            <th class="px-4 py-3">Harga</th>
                            <th class="px-4 py-3">Qty</th>
                            <th class="px-4 py-3">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td class="table-cell font-bold">{{ $item->product_name }}</td>
                                <td class="table-cell">{{ $money($item->unit_price) }}</td>
                                <td class="table-cell">{{ $item->quantity }}</td>
                                <td class="table-cell">{{ $money($item->subtotal_amount) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <aside class="space-y-5">
            <div class="app-card p-5">
                <h2 class="text-lg font-extrabold text-coffee-950 dark:text-coffee-200">Pembayaran</h2>
                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex justify-between gap-3"><span class="text-slate-500 dark:text-slate-400">Status</span><span class="font-bold">{{ $order->status->label() }}</span></div>
                    <div class="flex justify-between gap-3"><span class="text-slate-500 dark:text-slate-400">Metode</span><span class="font-bold">{{ $order->payment_method->label() }}</span></div>
                    <div class="flex justify-between gap-3"><span class="text-slate-500 dark:text-slate-400">Subtotal</span><span class="font-bold">{{ $money($order->subtotal_amount) }}</span></div>
                    <div class="flex justify-between gap-3"><span class="text-slate-500 dark:text-slate-400">Diskon</span><span class="font-bold">{{ $money($order->discount_amount) }}</span></div>
                    <div class="flex justify-between gap-3 text-lg"><span class="font-extrabold">Total</span><span class="font-extrabold text-coffee-950 dark:text-coffee-200">{{ $money($order->total_amount) }}</span></div>
                    <div class="flex justify-between gap-3"><span class="text-slate-500 dark:text-slate-400">Uang diterima</span><span class="font-bold">{{ $order->amount_received === null ? '-' : $money($order->amount_received) }}</span></div>
                    <div class="flex justify-between gap-3"><span class="text-slate-500 dark:text-slate-400">Kembalian</span><span class="font-bold">{{ $money($order->change_amount) }}</span></div>
                </div>
            </div>

            @role('admin')
                @if ($order->status->value === 'paid')
                    <form method="POST" action="{{ route('orders.cancel', $order) }}" class="app-card p-5" onsubmit="return confirm('Batalkan transaksi ini?')">
                        @csrf
                        <h2 class="text-lg font-extrabold text-red-700 dark:text-red-300">Batalkan transaksi</h2>
                        <label class="app-label mt-4 block" for="cancel_reason">Alasan</label>
                        <input id="cancel_reason" name="cancel_reason" class="app-input mt-1" placeholder="Opsional">
                        <button class="btn-danger mt-4 w-full" type="submit">Batalkan transaksi</button>
                    </form>
                @else
                    <div class="app-card p-5 text-sm text-slate-600 dark:text-slate-300">
                        Dibatalkan oleh {{ $order->cancelledBy?->name ?? '-' }} pada {{ $order->cancelled_at?->format('d M Y H:i') }}.
                    </div>
                @endif
            @endrole
        </aside>
    </div>
</x-app-layout>
