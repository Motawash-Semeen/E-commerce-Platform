<?php

namespace App\Modules\Owner\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Category\Models\Category;
use App\Modules\Product\Http\Requests\ProductRequest;
use App\Modules\Product\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function store(ProductRequest $request): RedirectResponse
    {
        $product = new Product($request->safe()->except(['image', 'add_to_shop']));
        $product->image = $request->file('image')->store('products', 'public');
        $product->created_by = $request->user()->id;   // never taken from the request
        $product->save();

        // "Also add to my shop": list it at the catalog price. With no own thumbnail,
        // the listing shows the catalog image.
        $shop = $request->user()->shop;
        if ($shop && $request->boolean('add_to_shop')) {
            $shop->listings()->create([
                'product_id' => $product->id,
                'price' => $product->new_price,
                'old_price' => $product->old_price,
            ]);
        }

        return redirect()->route('owner.products.index')
            ->with('success', "Product \"{$product->name}\" added to the catalog.");
    }

    public function edit(Request $request, Product $product): Response
    {
        $this->onlyCreator($request, $product);

        return Inertia::render('Owner/Products/Edit', [
            'product' => [
                ...$product->only(['id', 'name', 'slug', 'category_id', 'subcategory_id', 'description', 'old_price', 'new_price']),
                'image_url' => $product->image_url,
            ],
            'categories' => Category::withSubcategoryOptions(),
        ]);
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $this->onlyCreator($request, $product);

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

    /**
     * Owners may only edit catalog products they created (403 otherwise).
     */
    private function onlyCreator(Request $request, Product $product): void
    {
        abort_if($product->created_by != $request->user()->id, 403);
    }
}
