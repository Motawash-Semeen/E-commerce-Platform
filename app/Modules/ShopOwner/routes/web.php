<?php

use App\Modules\ShopOwner\Http\Controllers\ShopOwnerController;
use Illuminate\Support\Facades\Route;

// Admin only (logged in + role admin). Loaded by L5Modular with the 'web' middleware.
// {shop_owner} resolves only to users with role shop_owner (binding in AppServiceProvider).
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('shop-owners', ShopOwnerController::class)->except('show');
});
