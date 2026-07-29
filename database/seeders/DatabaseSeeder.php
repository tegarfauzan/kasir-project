<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\StoreSetting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $cashierRole = Role::firstOrCreate(['name' => 'cashier']);

        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin Kasir',
                'password' => Hash::make('password'),
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );
        $admin->assignRole($adminRole);

        $cashier = User::firstOrCreate(
            ['email' => 'cashier@example.com'],
            [
                'name' => 'Cashier Demo',
                'password' => Hash::make('password'),
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );
        $cashier->assignRole($cashierRole);

        StoreSetting::query()->firstOrCreate([], [
            'store_name' => 'Warung Kopi Senja',
            'address' => 'Jl. Kopi No. 13, Jakarta',
            'phone' => '0812-0000-0000',
            'receipt_footer' => 'Terima kasih sudah berkunjung',
            'bank_name' => 'BRI',
            'bank_account_name' => 'Warung Kopi Senja',
            'bank_account_number' => '1234567890',
        ]);

        $categories = collect([
            ['name' => 'Kopi', 'description' => 'Menu kopi hangat dan dingin'],
            ['name' => 'Non Kopi', 'description' => 'Minuman segar tanpa kopi'],
            ['name' => 'Makanan', 'description' => 'Makanan pendamping'],
            ['name' => 'Snack', 'description' => 'Camilan ringan'],
        ])->mapWithKeys(function (array $category) {
            $model = Category::firstOrCreate(
                ['slug' => Str::slug($category['name'])],
                ['name' => $category['name'], 'description' => $category['description'], 'is_active' => true],
            );

            return [$category['name'] => $model];
        });

        collect([
            ['Kopi', 'Kopi Tubruk', 10000],
            ['Kopi', 'Es Kopi Susu', 18000],
            ['Kopi', 'Americano', 15000],
            ['Non Kopi', 'Teh Tarik', 12000],
            ['Non Kopi', 'Cokelat Dingin', 16000],
            ['Makanan', 'Nasi Goreng Kampung', 24000],
            ['Makanan', 'Mie Goreng', 20000],
            ['Snack', 'Pisang Goreng', 14000],
            ['Snack', 'Roti Bakar Cokelat', 16000],
        ])->each(function (array $product) use ($categories): void {
            [$categoryName, $name, $price] = $product;

            Product::firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'category_id' => $categories[$categoryName]->id,
                    'name' => $name,
                    'description' => 'Menu favorit pelanggan warung kopi.',
                    'price' => $price,
                    'is_active' => true,
                ],
            );
        });
    }
}
