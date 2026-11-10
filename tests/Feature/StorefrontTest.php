<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\ShopProduct;
use App\Modules\Product\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    // Required test 9
    public function test_an_unassigned_product_is_not_visible_to_customers(): void
    {
        $shop = Shop::factory()->create();
        $listed = ShopProduct::factory()->for($shop)->create();
        $unassigned = Product::factory()->create(['name' => 'Secret Product']);

        $this->get(route('shops.products.show', [$shop, $unassigned]))->assertNotFound();

        $this->get(route('shops.show', $shop))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Public/Shop')
                ->where('productsCount', 1)
                ->where('sections.0.items.0.slug', $listed->product->slug)
                ->where('sections', fn ($sections) => ! str_contains(json_encode($sections), 'Secret Product')));

        $this->get(route('home'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('latest', fn ($latest) => ! str_contains(json_encode($latest), 'Secret Product')));
    }

    // Required test 10
    public function test_products_of_an_inactive_shop_or_inactive_listing_are_not_visible(): void
    {
        $inactiveShop = Shop::factory()->inactive()->create();
        $hiddenInClosedShop = ShopProduct::factory()->for($inactiveShop)->create();

        $openShop = Shop::factory()->create();
        $hiddenListing = ShopProduct::factory()->for($openShop)->inactive()->create();

        $this->get(route('shops.show', $inactiveShop))->assertNotFound();
        $this->get(route('shops.products.show', [$inactiveShop, $hiddenInClosedShop->product]))->assertNotFound();
        $this->get(route('shops.products.show', [$openShop, $hiddenListing->product]))->assertNotFound();

        $this->get(route('shops.show', $openShop))
            ->assertInertia(fn (Assert $page) => $page->where('productsCount', 0)->where('sections', []));

        $this->get(route('home'))
            ->assertInertia(fn (Assert $page) => $page
                ->has('shops', 1)                       // the inactive shop isn't listed
                ->where('shops.0.slug', $openShop->slug)
                ->where('shops.0.products_count', 0)
                ->where('latest', []));
    }

    // Required test 11
    public function test_the_same_product_shows_a_different_price_in_each_shop(): void
    {
        $product = Product::factory()->create(['new_price' => 500]);
        $shopA = Shop::factory()->create();
        $shopB = Shop::factory()->create();
        ShopProduct::factory()->for($shopA)->for($product)->create(['price' => 450]);
        ShopProduct::factory()->for($shopB)->for($product)->create(['price' => 520]);

        $this->get(route('shops.products.show', [$shopA, $product]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Public/Product')
                ->where('product.price', '450.00')
                ->where('elsewhere.0.price', '520.00')
                ->missing('product.new_price'));      // catalog prices are never sent

        $this->get(route('shops.products.show', [$shopB, $product]))
            ->assertInertia(fn (Assert $page) => $page->where('product.price', '520.00'));
    }

    public function test_the_listing_overrides_the_catalog_description_and_image(): void
    {
        $listing = ShopProduct::factory()->create([
            'description' => "Shop's own words",
            'thumbnail' => 'listings/own.jpg',
        ]);

        $this->get(route('shops.products.show', [$listing->shop, $listing->product]))
            ->assertInertia(fn (Assert $page) => $page
                ->where('product.description', "Shop's own words")
                ->where('product.image_url', fn ($url) => str_ends_with($url, '/storage/listings/own.jpg')));
    }

    public function test_shop_page_groups_products_by_category_and_subcategory(): void
    {
        $listing = ShopProduct::factory()->create();
        $product = $listing->product->load('category', 'subcategory');

        $this->get(route('shops.show', $listing->shop))
            ->assertInertia(fn (Assert $page) => $page
                ->where('sections.0.category', $product->category->name)
                ->where('sections.0.subcategory', $product->subcategory->name)
                ->where('sections.0.items.0.price', $listing->price));
    }
}
