<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'store_name',
    'address',
    'phone',
    'receipt_footer',
    'logo_path',
    'bank_name',
    'bank_account_name',
    'bank_account_number',
])]
class StoreSetting extends Model
{
    public static function current(): self
    {
        return self::query()->firstOrCreate([], [
            'store_name' => 'Warung Kopi Senja',
            'address' => 'Jl. Kopi No. 13',
            'phone' => '0812-0000-0000',
            'receipt_footer' => 'Terima kasih sudah berkunjung',
            'bank_name' => 'BRI',
            'bank_account_name' => 'Warung Kopi Senja',
            'bank_account_number' => '1234567890',
        ]);
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? Storage::url($this->logo_path) : null;
    }
}
