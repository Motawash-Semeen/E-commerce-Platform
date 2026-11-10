<?php

namespace App\Modules\Shop\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Category\Models\Category;
use App\Modules\Product\Models\Product;
use App\Modules\Subcategory\Models\Subcategory;
use Illuminate\View\View;

class ShopController extends Controller
{
    /**
     * Home page: every subcategory that has products, grouped under
     * "Category › Subcategory" headings.
     */
    public function index(): View
    {
        $subcategories = Subcategory::whereHas('products')
            ->with([
                'category',
                'products' => fn ($query) => $query->orderBy('name'),
            ])
            // Sort by category name, then subcategory name, so sections of one category stay together.
            ->orderBy(
                Category::select('name')->whereColumn('categories.id', 'subcategories.category_id')
            )
            ->orderBy('name')
            ->get();

        // Hero numbers, counted from what is already loaded (no extra queries).
        $stats = [
            'products' => $subcategories->sum(fn ($subcategory) => $subcategory->products->count()),
            'categories' => $subcategories->pluck('category_id')->unique()->count(),
            'collections' => $subcategories->count(),
        ];

        return view('Shop::index', compact('subcategories', 'stats'));
    }

    /**
     * Product details. Unknown slugs return 404 through route model binding.
     */
    public function show(Product $product): View
    {
        $product->load(['category', 'subcategory']);

        return view('Shop::show', compact('product'));
    }
}
