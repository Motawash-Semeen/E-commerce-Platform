<?php

use App\Modules\Owner\Http\Controllers\CatalogController;
use App\Modules\Owner\Http\Controllers\DashboardController;
use App\Modules\Owner\Http\Controllers\ListingController;
use App\Modules\Owner\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Shop owners only (logged in + role shop_owner). Loaded by L5Modular with the 'web' middleware.
// The owner's shop is ALWAYS auth()->user()->shop: no route takes a shop id.
Route::prefix('owner')->name('owner.')->middleware(['auth', 'role:shop_owner'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    // Catalog products the owner created (works with or without a shop).
    Route::get('my-products', [ProductController::class, 'index'])->name('products.index');
    Route::resource('products', ProductController::class)->only(['create', 'store', 'edit', 'update']);

    // Pages that work on "my shop" show "You don't have a shop yet" without one.
    Route::middleware('has.shop')->group(function () {
        Route::get('catalog', [CatalogController::class, 'index'])->name('catalog.index');
        Route::get('catalog/{product}/add', [CatalogController::class, 'create'])->name('catalog.add');
        Route::post('catalog/{product}', [CatalogController::class, 'store'])->name('catalog.store');

        Route::resource('listings', ListingController::class)->only(['index', 'edit', 'update', 'destroy']);
    });
});
