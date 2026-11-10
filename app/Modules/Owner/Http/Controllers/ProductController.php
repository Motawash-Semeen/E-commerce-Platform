<?php

namespace App\Modules\Owner\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Category\Models\Category;
use App\Modules\Product\Http\Requests\StoreProductRequest;
use App\Modules\Product\Http\Requests\UpdateProductRequest;
use App\Modules\Product\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Shop owner: catalog products they created. New products join the shared
 * catalog right away. Owners can't delete catalog products (other shops may sell them).
 */
class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $shopId = $user->shop?->id;
        $search = $request->string('search')->trim()->value();

        $products = Product::where('created_by', $user->id)
            ->with(['category:id,name', 'subcategory:id,name'])
            ->withCount('listings')
            ->withExists(['listings as in_my_shop' => fn ($query) => $query->where('shop_id', $shopId)])
            ->when($search, fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->latest()
            ->latest('id')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'image_url' => $product->image_url,
                'category' => $product->category->name,
                'subcategory' => $product->subcategory->name,
                'new_price' => $product->new_price,
                'listings_count' => $product->listings_count,
                'in_my_shop' => (bool) $product->in_my_shop,
            ]);

        return Inertia::render('Owner/Products/Index', [
            'products' => $products,
            'filters' => ['search' => $search],
            'hasShop' => $shopId !== null,
        ]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('Owner/Products/Create', [
            'categories' => Category::withSubcategoryOptions(),
            'hasShop' => $request->user()->shop !== null,
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $user = $request->user();
        $shop = $user->shop;

        $product = DB::transaction(function () use ($request, $user, $shop) {
            $product = new Product($request->safe()->except(['image', 'add_to_shop']));
            $product->image = $request->file('image')->store('products', 'public');
            $product->created_by = $user->id;   // never taken from the request
            $product->save();

            // "Also add to my shop": list it at the catalog price, with the catalog image
            // (no own thumbnail, so the listing falls back to it).
            if ($shop && $request->boolean('add_to_shop')) {
                $listing = $shop->listings()->make([
                    'price' => $product->new_price,
                    'old_price' => $product->old_price,
                ]);
                $listing->product_id = $product->id;
                $listing->is_active = true;
                $listing->save();
            }

            return $product;
        });

        return redirect()->route('owner.products.index')
            ->with('success', "Product \"{$product->name}\" added to the catalog.");
    }

    public function edit(Product $product): Response
    {
        // Only the owner who created it (ProductPolicy@update); 403 otherwise.
        Gate::authorize('update', $product);

        return Inertia::render('Owner/Products/Edit', [
            'product' => [
                ...$product->only(['id', 'name', 'slug', 'category_id', 'subcategory_id', 'description', 'old_price', 'new_price']),
                'image_url' => $product->image_url,
            ],
            'categories' => Category::withSubcategoryOptions(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        // UpdateProductRequest already checked ProductPolicy@update.
        $product->fill($request->safe()->except(['image', 'add_to_shop']));

        $oldImage = null;
        if ($request->hasFile('image')) {
            $oldImage = $product->image;
            $product->image = $request->file('image')->store('products', 'public');
        }

        $product->save();

        if ($oldImage) {
            Storage::disk('public')->delete($oldImage);
        }

        return redirect()->route('owner.products.index')
            ->with('success', "Product \"{$product->name}\" updated.");
    }
}
