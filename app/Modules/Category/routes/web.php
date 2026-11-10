<?php

use App\Modules\Category\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

// Admin only (logged in + role admin). Loaded by L5Modular with the 'web' middleware.
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('categories', CategoryController::class)->except('show');
});
