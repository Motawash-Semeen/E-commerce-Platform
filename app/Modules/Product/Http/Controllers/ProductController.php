<?php

namespace App\Modules\Product\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Category\Models\Category;
use App\Modules\Product\Http\Requests\ProductRequest;
use App\Modules\Product\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Admin: the shared catalog. Products added here (or by shop owners)
 * are visible to every shop owner, who can list them in their shop.
 */
class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->value();

        $products = Product::with(['category:id,name', 'subcategory:id,name', 'creator:id,name,role'])
            ->withCount('listings')
            ->when($search, fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->latest()
            ->latest('id')  // tie-breaker: rows saved in the same second keep a stable order
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'image_url' => $product->image_url,
                'category' => $product->category->name,
                'subcategory' => $product->subcategory->name,
                'creator' => $product->creator?->name,
                'creator_is_admin' => (bool) $product->creator?->isAdmin(),
                'listings_count' => $product->listings_count,
                'old_price' => $product->old_price,
                'new_price' => $product->new_price,
            ]);

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
            'filters' => ['search' => $search],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Products/Create', [
            'categories' => Category::withSubcategoryOptions(),
        ]);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $product = new Product($request->safe()->except(['image', 'add_to_shop']));
        // store() gives the file a random name; the client's file name is never used.
        $product->image = $request->file('image')->store('products', 'public');
        $product->created_by = $request->user()->id;   // never taken from the request
        $product->save();

        return redirect()->route('admin.products.index')
            ->with('success', "Product \"{$product->name}\" added to the catalog.");
    }

    public function edit(Product $product): Response
    {
        return Inertia::render('Admin/Products/Edit', [
            'product' => [
                ...$product->only(['id', 'name', 'slug', 'category_id', 'subcategory_id', 'description', 'old_price', 'new_price']),
                'image_url' => $product->image_url,
            ],
            'categories' => Category::withSubcategoryOptions(),
        ]);
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        // ProductRequest already checked ProductPolicy@update.
        $product->fill($request->safe()->except(['image', 'add_to_shop']));

        $oldImage = null;
        if ($request->hasFile('image')) {
            $oldImage = $product->image;
            $product->image = $request->file('image')->store('products', 'public');
        }

        $product->save();

        // Remove the old file only after the row points to the new one.
        if ($oldImage) {
            Storage::disk('public')->delete($oldImage);
        }

        return redirect()->route('admin.products.index')
            ->with('success', "Product \"{$product->name}\" updated.");
    }

    public function destroy(Product $product): RedirectResponse
    {
        // Friendly message first; the policy (and the restrictOnDelete key) back it up.
        if ($product->listings()->exists()) {
            return back()->with('error', "Cannot delete \"{$product->name}\": it's listed in a shop. Remove it from all shops first.");
        }

        Gate::authorize('delete', $product);

        $product->delete();
        Storage::disk('public')->delete($product->image);

        return redirect()->route('admin.products.index')
            ->with('success', "Product \"{$product->name}\" deleted.");
    }
}
