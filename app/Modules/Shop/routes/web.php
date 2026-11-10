<?php

use App\Modules\Shop\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

// Public shop (no login). Loaded by L5Modular with the 'web' middleware.
Route::get('/', [ShopController::class, 'index'])->name('home');
Route::get('/products/{product:slug}', [ShopController::class, 'show'])->name('products.show');
