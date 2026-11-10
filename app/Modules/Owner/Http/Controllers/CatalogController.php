<?php

namespace App\Modules\Owner\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Category\Models\Category;
use App\Modules\Owner\Http\Requests\ListingRequest;
use App\Modules\Product\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Shop owner: browse the shared catalog and add products to their own shop.
 */
class CatalogController extends Controller
{
    public function index(Request $request): Response
    {
        $shop = $request->user()->shop;   // guaranteed by the 'has.shop' middleware
        $search = $request->string('search')->trim()->value();
        $categoryId = $request->integer('category') ?: null;

        $products = Product::with(['category:id,name', 'subcategory:id,name'])
            ->withExists(['listings as in_my_shop' => fn ($query) => $query->where('shop_id', $shop->id)])
            ->when($search, fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->when($categoryId, fn ($query) => $query->where('category_id', $categoryId))
            ->orderBy('name')
            ->orderBy('id')
            ->paginate(12)
            ->withQueryString()
            ->through(fn (Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'image_url' => $product->image_url,
                'category' => $product->category->name,
                'subcategory' => $product->subcategory->name,
                'new_price' => $product->new_price,
                'old_price' => $product->old_price,
                'in_my_shop' => (bool) $product->in_my_shop,
            ]);

        return Inertia::render('Owner/Catalog/Index', [
            'products' => $products,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'filters' => ['search' => $search, 'category' => $categoryId],
        ]);
    }

    /**
     * "Add to my shop" form, prefilled with the catalog values.
     */
    public function create(Request $request, Product $product): Response|RedirectResponse
    {
        if ($request->user()->shop->listings()->where('product_id', $product->id)->exists()) {
            return redirect()->route('owner.listings.index')
                ->with('error', "\"{$product->name}\" is already in your shop.");
        }

        $product->load(['category:id,name', 'subcategory:id,name']);

        return Inertia::render('Owner/Catalog/Add', [
            'product' => [
                ...$product->only(['id', 'name', 'slug', 'description', 'new_price', 'old_price']),
                'image_url' => $product->image_url,
                'category' => $product->category->name,
                'subcategory' => $product->subcategory->name,
            ],
        ]);
    }

    public function store(ListingRequest $request, Product $product): RedirectResponse
    {
        $shop = $request->user()->shop;

        // A product is listed at most once per shop.
        if ($shop->listings()->where('product_id', $product->id)->exists()) {
            return back()->withErrors(['product_id' => 'This product is already in your shop.']);
        }

        $listing = $shop->listings()->make($request->safe()->only(['price', 'old_price', 'description']));
        $listing->product_id = $product->id;   // from the URL
        $listing->is_active = $request->boolean('is_active');

        if ($request->hasFile('thumbnail')) {
            $listing->thumbnail = $request->file('thumbnail')->store('listings', 'public');
        }

        $listing->save();

        return redirect()->route('owner.listings.index')
            ->with('success', "\"{$product->name}\" added to your shop.");
    }
}
