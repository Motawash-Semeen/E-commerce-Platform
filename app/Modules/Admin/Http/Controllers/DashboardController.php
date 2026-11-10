<?php

namespace App\Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Category\Models\Category;
use App\Modules\Product\Models\Product;
use App\Modules\Subcategory\Models\Subcategory;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('Admin::dashboard', [
            'stats' => [
                'categories' => Category::count(),
                'subcategories' => Subcategory::count(),
                'products' => Product::count(),
            ],
        ]);
    }
}
