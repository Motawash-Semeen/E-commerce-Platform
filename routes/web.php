<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
| Feature routes live inside each module (L5Modular loads them with the
| 'web' middleware):
|   app/Modules/Shop/routes/web.php         public shop
|   app/Modules/Admin/routes/web.php        /admin/dashboard           (role:admin)
|   app/Modules/Category/routes/web.php     /admin/categories          (role:admin)
|   app/Modules/Subcategory/routes/web.php  /admin/subcategories + JSON (role:admin)
|   app/Modules/Product/routes/web.php      /admin/products            (role:admin)
|   app/Modules/Owner/routes/web.php        /owner/dashboard           (role:shop_owner)
| This file keeps only the account routes every logged-in user shares.
*/

// Laravel's 'guest' middleware sends logged-in users to "dashboard":
// forward them to the dashboard for their role.
Route::get('/dashboard', function (Request $request) {
    return redirect($request->user()->homeRoute());
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // No DELETE /profile: users can't delete their own accounts.
});

require __DIR__.'/auth.php';
