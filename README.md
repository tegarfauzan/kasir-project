# Kasir Kopi

Aplikasi kasir warung kopi berbasis Laravel 13, Blade, Breeze Auth, Spatie Permission, Tailwind CSS, MySQL, dan DomPDF.

## Akun Demo

- Admin: `admin@example.com` / `password`
- Cashier: `cashier@example.com` / `password`

1. Jalankan instalasi.

```bash
composer install
npm install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm run build
php artisan serve
```

2. Buka `http://127.0.0.1:8000/login`.

## Mind Map Arsitektur

```text
Kasir Kopi
|
|-- Auth & Security
|   |-- Breeze login
|   |-- Public register disabled
|   |-- User is_active checked on login
|   |-- Spatie roles: admin, cashier
|   |-- FormRequest validation
|
|-- POS Checkout
|   |-- Browser sends product_id + quantity only
|   |-- CheckoutService reloads product price from database
|   |-- DB transaction stores orders + order_items
|   |-- Snapshot: product_name, unit_price, quantity, subtotal
|   |-- Validates active product/category, cash received, discount
|
|-- Master Data
|   |-- Categories
|   |-- Products
|   |-- Users
|   |-- Store settings
|
|-- Transactions
|   |-- History filters
|   |-- Detail page
|   |-- Admin cancellation
|   |-- Receipt reprint
|
|-- Reporting
|   |-- Dashboard period filters
|   |-- Simple CSS bar chart
|   |-- Sales PDF export via DomPDF
|
|-- Printing
    |-- 58mm browser receipt page
    |-- Plain text thermal route
    |-- Future Bluetooth/ESC-POS isolated in ReceiptService
```

## Alur Checkout

1. Kasir memilih produk di POS.
2. Alpine menyimpan cart lokal untuk UX cepat.
3. Form checkout hanya mengirim JSON `items` berisi `product_id` dan `quantity`.
4. `CheckoutRequest` memvalidasi struktur cart, metode pembayaran, diskon, dan uang diterima.
5. `CheckoutService` membuka database transaction.
6. Produk diambil ulang dari database dengan `lockForUpdate()`.
7. Harga final dihitung dari `products.price`, bukan dari browser.
8. Order dibuat dengan invoice harian.
9. Order item dibuat dengan snapshot harga dan nama produk.
10. User diarahkan ke halaman struk.

## Catatan Cetak Bluetooth / Thermal

Browser tidak bisa langsung mengirim ESC/POS ke semua printer Bluetooth karena pembatasan keamanan browser. Implementasi MVP yang stabil:

- `GET /orders/{order}/receipt` untuk print dialog ukuran 58mm.
- `GET /orders/{order}/thermal` untuk teks thermal/plain text.
- Logic format struk ada di `app/Services/ReceiptService.php`, jadi nanti mudah diganti ke RawBT, Web Bluetooth, atau library ESC/POS ketika mesin printer sudah dipilih.

## Verifikasi

```bash
php artisan test
npm run build
php artisan route:list --except-vendor
```

## Keputusan Penting

- Public registration dimatikan; user dibuat oleh admin.
- Produk yang pernah masuk transaksi tidak dihapus permanen, tetapi dinonaktifkan.
- Kategori tidak bisa dihapus jika masih punya produk.
- Cashier bisa membuat transaksi dan melihat transaksi sendiri atau transaksi hari ini.
- Admin bisa melihat dashboard, mengelola master data, melihat semua transaksi, dan membatalkan transaksi.
