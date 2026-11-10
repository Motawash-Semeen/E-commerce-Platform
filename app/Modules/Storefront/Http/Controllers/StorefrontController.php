<?php

namespace App\Modules\Storefront\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\ShopProduct;
use App\Modules\Product\Models\Product;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Customer pages. Products are ONLY reached through ShopProduct::visible()
 * (active listing in an active shop), never straight from the products table,
 * and only the shop's own display values are sent to Vue.
 */
class StorefrontController extends Controller
{
    /**
     * Relations needed to show a listing card.
     */
    private const CARD_RELATIONS = [
        'product:id,name,slug,image,description,category_id,subcategory_id',
        'product.category:id,name',
        'product.subcategory:id,name',
    ];

    public function index(): Response
    {
        $shops = Shop::where('is_active', true)
            ->withCount(['listings as products_count' => fn ($query) => $query->visible()])
            ->orderBy('name')
            ->get()
            ->map(fn (Shop $shop) => [
                'name' => $shop->name,
                'slug' => $shop->slug,
                'description' => Str::limit((string) $shop->description, 140),
                'logo_url' => $shop->logo_url,
                'products_count' => $shop->products_count,
            ]);

        $latest = ShopProduct::visible()
            ->with([...self::CARD_RELATIONS, 'shop:id,name,slug'])
            ->latest()
            ->latest('id')
            ->take(8)
            ->get()
            ->map(fn (ShopProduct $listing) => [
                ...$this->card($listing),
                'shop' => $listing->shop->only(['name', 'slug']),
            ]);

        return Inertia::render('Public/Home', [
            'shops' => $shops,
            'latest' => $latest,
        ]);
    }

    public function show(Shop $shop): Response
    {
        abort_unless($shop->is_active, 404);

        $listings = $shop->listings()->visible()->with(self::CARD_RELATIONS)->get();

        // "Category › Subcategory" sections, sorted by category then subcategory name.
        $sections = $listings
            ->sortBy([
                fn ($a, $b) => strcmp($a->product->category->name, $b->product->category->name),
                fn ($a, $b) => strcmp($a->product->subcategory->name, $b->product->subcategory->name),
                fn ($a, $b) => strcmp($a->product->name, $b->product->name),
            ])
            ->groupBy('product.subcategory_id')
            ->map(fn ($group) => [
                'category' => $group->first()->product->category->name,
                'subcategory' => $group->first()->product->subcategory->name,
                'items' => $group->map(fn (ShopProduct $listing) => $this->card($listing))->values(),
            ])
            ->values();

        return Inertia::render('Public/Shop', [
            'shop' => [
                ...$shop->only(['name', 'slug', 'description']),
                'logo_url' => $shop->logo_url,
            ],
            'sections' => $sections,
            'productsCount' => $listings->count(),
        ]);
    }

    public function product(Shop $shop, Product $product): Response
    {
        abort_unless($shop->is_active, 404);

        // 404 unless THIS shop has a visible listing of the product.
        $listing = $shop->listings()->visible()
            ->where('product_id', $product->id)
            ->with(self::CARD_RELATIONS)
            ->firstOrFail();

        // The same product in other shops, each at that shop's own price.
        $elsewhere = ShopProduct::visible()
            ->where('product_id', $product->id)
            ->where('shop_id', '!=', $shop->id)
            ->with('shop:id,name,slug')
            ->orderBy('price')
            ->get()
            ->map(fn (ShopProduct $other) => [
                'shop' => $other->shop->only(['name', 'slug']),
                'price' => $other->price,
            ]);

        return Inertia::render('Public/Product', [
            'shop' => [
                ...$shop->only(['name', 'slug']),
                'logo_url' => $shop->logo_url,
            ],
            'product' => [
                ...$this->card($listing),
                'description' => $listing->display_description,
            ],
            'elsewhere' => $elsewhere,
        ]);
    }

    /**
     * Shop-specific display data for one listing (no catalog prices or internal fields).
     */
    private function card(ShopProduct $listing): array
    {
        return [
            'name' => $listing->product->name,
            'slug' => $listing->product->slug,
            'price' => $listing->price,
            'old_price' => $listing->old_price,
            'image_url' => $listing->display_image_url,
            'category' => $listing->product->category->name,
            'subcategory' => $listing->product->subcategory->name,
        ];
    }
}
