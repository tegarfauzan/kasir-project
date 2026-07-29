@php($money = fn ($amount) => 'Rp'.number_format((int) $amount, 0, ',', '.'))
<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <title>Laporan Penjualan</title>
        <style>
            body { font-family: DejaVu Sans, sans-serif; color: #1f2937; font-size: 12px; }
            h1 { margin: 0; color: #622B14; font-size: 22px; }
            .muted { color: #6b7280; }
            .summary { margin: 18px 0; width: 100%; border-collapse: collapse; }
            .summary td { border: 1px solid #E4D6A9; padding: 10px; }
            table.orders { width: 100%; border-collapse: collapse; }
            .orders th { background: #622B14; color: #E4D6A9; text-align: left; padding: 8px; font-size: 11px; }
            .orders td { border-bottom: 1px solid #E4D6A9; padding: 8px; vertical-align: top; }
            .right { text-align: right; }
        </style>
    </head>
    <body>
        <h1>Laporan Penjualan</h1>
        <div class="muted">{{ $settings->store_name }} | Dicetak {{ now()->format('d M Y H:i') }}</div>
        <div class="muted">
            Periode:
            {{ isset($filters['from']) && $filters['from'] ? \Carbon\Carbon::parse($filters['from'])->format('d M Y') : 'Awal' }}
            -
            {{ isset($filters['to']) && $filters['to'] ? \Carbon\Carbon::parse($filters['to'])->format('d M Y') : 'Akhir' }}
        </div>

        <table class="summary">
            <tr>
                <td><strong>Total omzet</strong><br>{{ $money($totalOmzet) }}</td>
                <td><strong>Total transaksi</strong><br>{{ number_format($totalTransactions) }}</td>
            </tr>
        </table>

        <table class="orders">
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Tanggal</th>
                    <th>Kasir</th>
                    <th>Pembayaran</th>
                    <th class="right">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td>{{ $order->invoice_number }}</td>
                        <td>{{ $order->paid_at?->format('d M Y H:i') }}</td>
                        <td>{{ $order->cashier?->name }}</td>
                        <td>{{ $order->payment_method->label() }}</td>
                        <td class="right">{{ $money($order->total_amount) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Tidak ada transaksi pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </body>
</html>
