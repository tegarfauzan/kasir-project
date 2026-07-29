<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Repositories\OrderRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    public function __construct(private readonly OrderRepository $orders)
    {
    }

    /**
     * @param  array<int, array{product_id:int, quantity:int}>  $items
     */
    public function checkout(User $cashier, array $items, PaymentMethod $paymentMethod, int $discountAmount = 0, ?int $amountReceived = null): Order
    {
        if ($items === []) {
            throw ValidationException::withMessages(['items' => 'Keranjang tidak boleh kosong.']);
        }

        return DB::transaction(function () use ($cashier, $items, $paymentMethod, $discountAmount, $amountReceived): Order {
            $cart = collect($items)
                ->groupBy('product_id')
                ->map(fn (Collection $group) => [
                    'product_id' => (int) $group->first()['product_id'],
                    'quantity' => (int) $group->sum('quantity'),
                ])
                ->values();

            $productIds = $cart->pluck('product_id')->all();
            $products = Product::query()
                ->with('category')
                ->whereKey($productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $orderItems = [];
            $subtotalAmount = 0;

            foreach ($cart as $line) {
                $quantity = (int) $line['quantity'];
                $product = $products->get($line['product_id']);

                if ($quantity < 1) {
                    throw ValidationException::withMessages(['items' => 'Qty setiap item minimal 1.']);
                }

                if (! $product) {
                    throw ValidationException::withMessages(['items' => 'Produk tidak ditemukan.']);
                }

                if (! $product->is_active || ! $product->category?->is_active) {
                    throw ValidationException::withMessages(['items' => "Produk {$product->name} sedang tidak aktif."]);
                }

                $unitPrice = (int) $product->price;
                $lineSubtotal = $unitPrice * $quantity;
                $subtotalAmount += $lineSubtotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $unitPrice,
                    'quantity' => $quantity,
                    'subtotal_amount' => $lineSubtotal,
                ];
            }

            if ($discountAmount < 0) {
                throw ValidationException::withMessages(['discount_amount' => 'Diskon tidak boleh negatif.']);
            }

            if ($discountAmount > $subtotalAmount) {
                throw ValidationException::withMessages(['discount_amount' => 'Total tidak boleh negatif setelah diskon.']);
            }

            $totalAmount = $subtotalAmount - $discountAmount;

            if ($paymentMethod === PaymentMethod::Cash && ($amountReceived === null || $amountReceived < $totalAmount)) {
                throw ValidationException::withMessages(['amount_received' => 'Uang diterima tidak boleh kurang dari total pembayaran.']);
            }

            $receivedAmount = $paymentMethod === PaymentMethod::Cash ? $amountReceived : null;
            $changeAmount = $paymentMethod === PaymentMethod::Cash ? max(0, ($receivedAmount ?? 0) - $totalAmount) : 0;

            $order = Order::query()->create([
                'invoice_number' => $this->orders->nextInvoiceNumber(),
                'user_id' => $cashier->id,
                'status' => OrderStatus::Paid,
                'payment_method' => $paymentMethod,
                'subtotal_amount' => $subtotalAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'amount_received' => $receivedAmount,
                'change_amount' => $changeAmount,
                'paid_at' => now(),
            ]);

            $order->items()->createMany($orderItems);

            return $order->load(['cashier', 'items.product']);
        });
    }
}
