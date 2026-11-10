<?php

use App\Modules\Product\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Admin only (logged in + role admin). Loaded by L5Modular with the 'web' middleware.
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('products', ProductController::class)->except('show');
});
