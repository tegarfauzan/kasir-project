<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PosCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_can_checkout_with_server_side_price_snapshot(): void
    {
        Role::create(['name' => 'cashier']);

        $cashier = User::factory()->create();
        $cashier->assignRole('cashier');

        $category = Category::create([
            'name' => 'Kopi',
            'slug' => 'kopi',
            'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Es Kopi Susu',
            'slug' => 'es-kopi-susu',
            'price' => 18000,
            'is_active' => true,
        ]);

        $response = $this->actingAs($cashier)->post(route('pos.checkout'), [
            'items' => json_encode([
                ['product_id' => $product->id, 'quantity' => 2],
            ]),
            'discount_amount' => 1000,
            'payment_method' => 'cash',
            'amount_received' => 50000,
        ]);

        $order = Order::with('items')->first();

        $response->assertRedirect(route('orders.receipt', $order, absolute: false));
        $this->assertSame(35000, (int) $order->total_amount);
        $this->assertSame(15000, (int) $order->change_amount);
        $this->assertSame('Es Kopi Susu', $order->items->first()->product_name);
        $this->assertSame(18000, (int) $order->items->first()->unit_price);
    }

    public function test_cash_checkout_requires_enough_received_amount(): void
    {
        Role::create(['name' => 'cashier']);

        $cashier = User::factory()->create();
        $cashier->assignRole('cashier');

        $category = Category::create([
            'name' => 'Kopi',
            'slug' => 'kopi',
            'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Americano',
            'slug' => 'americano',
            'price' => 15000,
            'is_active' => true,
        ]);

        $response = $this->actingAs($cashier)->from(route('pos.index'))->post(route('pos.checkout'), [
            'items' => json_encode([
                ['product_id' => $product->id, 'quantity' => 1],
            ]),
            'discount_amount' => 0,
            'payment_method' => 'cash',
            'amount_received' => 10000,
        ]);

        $response->assertRedirect(route('pos.index', absolute: false));
        $response->assertSessionHasErrors('amount_received');
        $this->assertDatabaseCount('orders', 0);
    }
}
