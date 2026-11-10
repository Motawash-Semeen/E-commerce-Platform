<?php

namespace App\Modules\Owner\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ShopProduct;
use App\Modules\Owner\Http\Requests\ListingRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Shop owner: the products listed in their shop. A listing only ever changes
 * its own shop_product row, never the catalog product.
 */
class ListingController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->value();

        $listings = $request->user()->shop->listings()
            ->with('product:id,name,slug,image,category_id,subcategory_id', 'product.category:id,name')
            ->when($search, fn ($query) => $query->whereHas('product', fn ($q) => $q->where('name', 'like', "%{$search}%")))
            ->latest()
            ->latest('id')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (ShopProduct $listing) => [
                'id' => $listing->id,
                'name' => $listing->product->name,
                'category' => $listing->product->category->name,
                'image_url' => $listing->display_image_url,
                'price' => $listing->price,
                'old_price' => $listing->old_price,
                'is_active' => $listing->is_active,
            ]);

        return Inertia::render('Owner/Listings/Index', [
            'listings' => $listings,
            'filters' => ['search' => $search],
            'shop' => $request->user()->shop->only(['name', 'slug', 'is_active']),
        ]);
    }

    public function edit(ShopProduct $listing): Response
    {
        // ShopProductPolicy: 403 for another shop's listing.
        Gate::authorize('update', $listing);

        $listing->load('product.category:id,name', 'product.subcategory:id,name');
        $product = $listing->product;

        return Inertia::render('Owner/Listings/Edit', [
            'listing' => [
                ...$listing->only(['id', 'price', 'old_price', 'description', 'is_active']),
                'thumbnail_url' => $listing->thumbnail_url,
            ],
            'product' => [
                ...$product->only(['name', 'description', 'new_price', 'old_price']),
                'image_url' => $product->image_url,
                'category' => $product->category->name,
                'subcategory' => $product->subcategory->name,
            ],
        ]);
    }

    public function update(ListingRequest $request, ShopProduct $listing): RedirectResponse
    {
        // ListingRequest already checked ShopProductPolicy@update.
        $listing->fill($request->safe()->only(['price', 'old_price', 'description']));
        $listing->is_active = $request->boolean('is_active');

        $oldThumbnail = null;
        if ($request->hasFile('thumbnail')) {
            $oldThumbnail = $listing->thumbnail;
            $listing->thumbnail = $request->file('thumbnail')->store('listings', 'public');
        }

        $listing->save();

        if ($oldThumbnail) {
            Storage::disk('public')->delete($oldThumbnail);
        }

        return redirect()->route('owner.listings.index')
            ->with('success', 'Listing updated.');
    }

    public function destroy(ShopProduct $listing): RedirectResponse
    {
        Gate::authorize('delete', $listing);

        $listing->delete();

        if ($listing->thumbnail) {
            Storage::disk('public')->delete($listing->thumbnail);
        }

        return redirect()->route('owner.listings.index')
            ->with('success', 'Removed from your shop. The product stays in the catalog.');
    }
}
