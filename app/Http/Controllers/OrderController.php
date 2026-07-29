<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Http\Requests\Order\CancelOrderRequest;
use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Repositories\StoreSettingRepository;
use App\Services\ReceiptService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderRepository $orders,
        private readonly StoreSettingRepository $settings,
        private readonly ReceiptService $receipts,
    ) {
    }

    public function index(Request $request): View
    {
        return view('orders.index', [
            'orders' => $this->orders->paginateForUser($request->user(), $request->only(['search', 'from', 'to', 'payment_method', 'status'])),
            'paymentMethods' => PaymentMethod::cases(),
            'statuses' => OrderStatus::cases(),
        ]);
    }

    public function show(Request $request, Order $order): View
    {
        return view('orders.show', [
            'order' => $this->orders->findVisibleOrFail($request->user(), $order),
            'settings' => $this->settings->current(),
        ]);
    }

    public function receipt(Request $request, Order $order): View
    {
        return view('orders.receipt', [
            'order' => $this->orders->findVisibleOrFail($request->user(), $order),
            'settings' => $this->settings->current(),
        ]);
    }

    public function thermal(Request $request, Order $order): Response
    {
        $order = $this->orders->findVisibleOrFail($request->user(), $order);

        return response($this->receipts->thermalText($order, $this->settings->current()), 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
        ]);
    }

    public function cancel(CancelOrderRequest $request, Order $order): RedirectResponse
    {
        if ($order->status === OrderStatus::Cancelled) {
            return back()->with('error', 'Transaksi sudah dibatalkan.');
        }

        $order->update([
            'status' => OrderStatus::Cancelled,
            'cancelled_at' => now(),
            'cancelled_by' => $request->user()->id,
            'cancel_reason' => $request->string('cancel_reason')->toString() ?: 'Dibatalkan admin',
        ]);

        return redirect()->route('orders.show', $order)->with('success', 'Transaksi berhasil dibatalkan.');
    }
}
