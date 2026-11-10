<?php

use App\Modules\Storefront\Http\Controllers\StorefrontController;
use Illuminate\Support\Facades\Route;

// Public customer pages (no login). Loaded by L5Modular with the 'web' middleware.
Route::get('/', [StorefrontController::class, 'index'])->name('home');
Route::get('/shops/{shop:slug}', [StorefrontController::class, 'show'])->name('shops.show');
Route::get('/shops/{shop:slug}/products/{product:slug}', [StorefrontController::class, 'product'])
    ->withoutScopedBindings()   // the controller checks for a VISIBLE listing itself
    ->name('shops.products.show');
