<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\StoreSettingController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => auth()->check()
    ? redirect()->route(auth()->user()->hasRole('admin') ? 'dashboard' : 'pos.index')
    : redirect()->route('login'));

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)
        ->middleware('role:admin')
        ->name('dashboard');

    Route::middleware('role:admin|cashier')->group(function (): void {
        Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
        Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');

        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('/orders/{order}/receipt', [OrderController::class, 'receipt'])->name('orders.receipt');
        Route::get('/orders/{order}/thermal', [OrderController::class, 'thermal'])->name('orders.thermal');
    });

    Route::middleware('role:admin')->group(function (): void {
        Route::resource('products', ProductController::class)->except(['create', 'show', 'edit']);
        Route::patch('/products/{product}/toggle', [ProductController::class, 'toggle'])->name('products.toggle');

        Route::resource('categories', CategoryController::class)->except(['create', 'show', 'edit']);
        Route::patch('/categories/{category}/toggle', [CategoryController::class, 'toggle'])->name('categories.toggle');

        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::patch('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
        Route::patch('/users/{user}/toggle', [UserManagementController::class, 'toggle'])->name('users.toggle');
        Route::patch('/users/{user}/password', [UserManagementController::class, 'resetPassword'])->name('users.password');

        Route::get('/store-settings', [StoreSettingController::class, 'edit'])->name('store-settings.edit');
        Route::patch('/store-settings', [StoreSettingController::class, 'update'])->name('store-settings.update');

        Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
        Route::get('/reports/sales.pdf', [SalesReportController::class, 'exportPdf'])->name('reports.sales.pdf');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
