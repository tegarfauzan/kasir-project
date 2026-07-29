<?php

namespace App\Http\Requests\Pos;

use App\Enums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->hasAnyRole(['admin', 'cashier']);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'items' => ['required', 'string'],
            'discount_amount' => ['nullable', 'integer', 'min:0'],
            'payment_method' => ['required', Rule::in(PaymentMethod::values())],
            'amount_received' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $items = $this->decodedItems();

            if ($items === []) {
                $validator->errors()->add('items', 'Keranjang tidak boleh kosong.');

                return;
            }

            foreach ($items as $item) {
                if (! isset($item['product_id'], $item['quantity']) || (int) $item['quantity'] < 1) {
                    $validator->errors()->add('items', 'Setiap item wajib memiliki produk dan qty minimal 1.');

                    return;
                }
            }
        });
    }

    /**
     * @return array<int, array{product_id:int, quantity:int}>
     */
    public function validatedItems(): array
    {
        return collect($this->decodedItems())
            ->map(fn (array $item) => [
                'product_id' => (int) $item['product_id'],
                'quantity' => (int) $item['quantity'],
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function decodedItems(): array
    {
        $decoded = json_decode((string) $this->input('items'), true);

        return is_array($decoded) ? $decoded : [];
    }
}
