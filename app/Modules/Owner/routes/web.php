<?php

use App\Modules\Owner\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Shop owners only (logged in + role shop_owner). Loaded by L5Modular with the 'web' middleware.
Route::prefix('owner')->name('owner.')->middleware(['auth', 'role:shop_owner'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
});
