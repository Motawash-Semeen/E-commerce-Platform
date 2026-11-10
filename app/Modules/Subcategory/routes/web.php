<?php

use App\Modules\Subcategory\Http\Controllers\SubcategoryController;
use Illuminate\Support\Facades\Route;

// Admin only (logged in + role admin). Loaded by L5Modular with the 'web' middleware.
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('subcategories', SubcategoryController::class)->except('show');
});
