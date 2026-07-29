@php($money = fn ($amount) => 'Rp'.number_format((int) $amount, 0, ',', '.'))
<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex,nofollow">
        <title>Struk {{ $order->invoice_number }}</title>
        <style>
            body { margin: 0; background: #f7f0da; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; color: #111827; }
            .toolbar { display: flex; gap: 8px; justify-content: center; padding: 16px; font-family: sans-serif; }
            .toolbar a, .toolbar button { border: 1px solid #622B14; border-radius: 12px; background: #622B14; color: #fff; padding: 10px 14px; font-weight: 700; cursor: pointer; text-decoration: none; }
            .receipt { width: 58mm; margin: 0 auto 24px; background: #fff; padding: 12px; box-shadow: 0 20px 45px -30px rgba(0,0,0,.7); }
            .center { text-align: center; }
            .line { border-top: 1px dashed #111827; margin: 8px 0; }
            .row { display: flex; justify-content: space-between; gap: 8px; }
            .small { font-size: 11px; }
            .bold { font-weight: 800; }
            @media print {
                @page { size: 58mm auto; margin: 0; }
                body { background: #fff; }
                .toolbar { display: none; }
                .receipt { box-shadow: none; margin: 0; width: auto; }
            }
        </style>
    </head>
    <body>
        <div class="toolbar">
            <button onclick="window.print()">Cetak</button>
            <a href="{{ route('orders.show', $order) }}">Detail</a>
            <a href="{{ route('orders.thermal', $order) }}" target="_blank">Teks thermal</a>
        </div>

        <main class="receipt small">
            <div class="center">
                @if ($settings->logo_url)
                    <img src="{{ $settings->logo_url }}" alt="{{ $settings->store_name }}" style="max-width: 42px; max-height: 42px;">
                @endif
                <div class="bold">{{ $settings->store_name }}</div>
                <div>{{ $settings->address }}</div>
                <div>{{ $settings->phone }}</div>
            </div>
            <div class="line"></div>
            <div>Invoice: {{ $order->invoice_number }}</div>
            <div>Tanggal: {{ $order->paid_at?->format('d/m/Y H:i') }}</div>
            <div>Kasir: {{ $order->cashier?->name }}</div>
            <div class="line"></div>

            @foreach ($order->items as $item)
                <div class="bold">{{ $item->product_name }}</div>
                <div class="row">
                    <span>{{ $item->quantity }} x {{ $money($item->unit_price) }}</span>
                    <span>{{ $money($item->subtotal_amount) }}</span>
                </div>
            @endforeach

            <div class="line"></div>
            <div class="row"><span>Subtotal</span><span>{{ $money($order->subtotal_amount) }}</span></div>
            <div class="row"><span>Diskon</span><span>{{ $money($order->discount_amount) }}</span></div>
            <div class="row bold"><span>Total</span><span>{{ $money($order->total_amount) }}</span></div>
            <div class="row"><span>Bayar</span><span>{{ $order->amount_received === null ? '-' : $money($order->amount_received) }}</span></div>
            <div class="row"><span>Kembali</span><span>{{ $money($order->change_amount) }}</span></div>
            <div class="line"></div>
            <div class="center">{{ $settings->receipt_footer }}</div>
        </main>

        @if (session('success'))
            <script>
                setTimeout(() => window.print(), 500);
            </script>
        @endif
    </body>
</html>
