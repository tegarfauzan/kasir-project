<?php

namespace App\Http\Controllers;

use App\Enums\PaymentMethod;
use App\Http\Requests\Pos\CheckoutRequest;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
use App\Repositories\StoreSettingRepository;
use App\Services\CheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PosController extends Controller
{
    public function __construct(
        private readonly ProductRepository $products,
        private readonly CategoryRepository $categories,
        private readonly StoreSettingRepository $settings,
        private readonly CheckoutService $checkout,
    ) {
    }

    public function index(Request $request): View
    {
        return view('pos.index', [
            'products' => $this->products->activeForPos(
                $request->integer('category_id') ?: null,
                $request->string('search')->toString() ?: null,
            ),
            'categories' => $this->categories->activeOptions(),
            'selectedCategory' => $request->integer('category_id'),
            'search' => $request->string('search')->toString(),
            'settings' => $this->settings->current(),
        ]);
    }

    public function checkout(CheckoutRequest $request): RedirectResponse
    {
        $order = $this->checkout->checkout(
            $request->user(),
            $request->validatedItems(),
            PaymentMethod::from($request->string('payment_method')->toString()),
            (int) $request->input('discount_amount', 0),
            $request->filled('amount_received') ? (int) $request->input('amount_received') : null,
        );

        return redirect()
            ->route('orders.receipt', $order)
            ->with('success', 'Transaksi berhasil disimpan.');
    }
}
