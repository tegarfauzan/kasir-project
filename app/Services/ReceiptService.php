<?php

namespace App\Services;

use App\Models\Order;
use App\Models\StoreSetting;

class ReceiptService
{
    public function thermalText(Order $order, StoreSetting $settings): string
    {
        $order->loadMissing(['cashier', 'items']);

        $lines = [
            $this->center($settings->store_name),
            $this->center((string) $settings->address),
            $this->center((string) $settings->phone),
            str_repeat('-', 32),
            'Invoice: '.$order->invoice_number,
            'Tanggal: '.$order->paid_at?->format('d/m/Y H:i'),
            'Kasir  : '.$order->cashier?->name,
            str_repeat('-', 32),
        ];

        foreach ($order->items as $item) {
            $lines[] = $item->product_name;
            $lines[] = $this->columns($item->quantity.' x '.$this->money((int) $item->unit_price), $this->money((int) $item->subtotal_amount));
        }

        $lines = array_merge($lines, [
            str_repeat('-', 32),
            $this->columns('Subtotal', $this->money((int) $order->subtotal_amount)),
            $this->columns('Diskon', $this->money((int) $order->discount_amount)),
            $this->columns('Total', $this->money((int) $order->total_amount)),
            $this->columns('Bayar', $order->amount_received === null ? '-' : $this->money((int) $order->amount_received)),
            $this->columns('Kembali', $this->money((int) $order->change_amount)),
            str_repeat('-', 32),
            $this->center((string) $settings->receipt_footer),
            '',
        ]);

        return implode(PHP_EOL, $lines);
    }

    private function center(string $text): string
    {
        $text = trim($text);

        if ($text === '') {
            return '';
        }

        return str_pad(substr($text, 0, 32), 32, ' ', STR_PAD_BOTH);
    }

    private function columns(string $left, string $right): string
    {
        $left = substr($left, 0, 20);
        $right = substr($right, 0, 12);

        return str_pad($left, 20).str_pad($right, 12, ' ', STR_PAD_LEFT);
    }

    private function money(int $amount): string
    {
        return 'Rp'.number_format($amount, 0, ',', '.');
    }
}
