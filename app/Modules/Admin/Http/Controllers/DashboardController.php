<?php

namespace App\Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\ShopProduct;
use App\Models\User;
use App\Modules\Product\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        // Only numbers go to the page.
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'shops' => Shop::count(),
                'owners' => User::shopOwners()->count(),
                'products' => Product::count(),
                'listings' => ShopProduct::count(),
            ],
        ]);
    }
}
