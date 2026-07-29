<?php

namespace App\Repositories;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class OrderRepository
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginateForUser(User $user, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->visibleTo($user)
            ->with(['cashier', 'items'])
            ->when($filters['search'] ?? null, fn (Builder $query, string $search) => $query->where('invoice_number', 'like', "%{$search}%"))
            ->when($filters['from'] ?? null, fn (Builder $query, string $from) => $query->whereDate('paid_at', '>=', $from))
            ->when($filters['to'] ?? null, fn (Builder $query, string $to) => $query->whereDate('paid_at', '<=', $to))
            ->when($filters['payment_method'] ?? null, fn (Builder $query, string $method) => $query->where('payment_method', $method))
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->latest('paid_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function visibleTo(User $user): Builder
    {
        $query = Order::query();

        if ($user->isAdmin()) {
            return $query;
        }

        return $query->where(function (Builder $query) use ($user): void {
            $query->where('user_id', $user->id)
                ->orWhereDate('paid_at', now()->toDateString());
        });
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function paidReport(array $filters = []): Collection
    {
        return Order::query()
            ->with(['cashier', 'items'])
            ->paid()
            ->betweenDates($filters['from'] ?? null, $filters['to'] ?? null)
            ->latest('paid_at')
            ->get();
    }

    public function nextInvoiceNumber(): string
    {
        $prefix = 'INV-'.now()->format('Ymd');
        $sequence = Order::query()
            ->where('invoice_number', 'like', "{$prefix}-%")
            ->lockForUpdate()
            ->count() + 1;

        do {
            $invoice = $prefix.'-'.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
            $sequence++;
        } while (Order::query()->where('invoice_number', $invoice)->exists());

        return $invoice;
    }

    public function findVisibleOrFail(User $user, Order $order): Order
    {
        return $this->visibleTo($user)
            ->with(['cashier', 'items.product', 'cancelledBy'])
            ->whereKey($order->id)
            ->firstOrFail();
    }

    public function hasPaidTransactions(): bool
    {
        return Order::query()->where('status', OrderStatus::Paid->value)->exists();
    }
}
