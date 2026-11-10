<?php

use App\Modules\Admin\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Admin only (logged in + role admin). Loaded by L5Modular with the 'web' middleware.
// The category, subcategory and product modules use this same group in their own route files.
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
});
