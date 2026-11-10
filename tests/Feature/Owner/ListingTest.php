<?php

namespace Tests\Feature\Owner;

use App\Models\Shop;
use App\Models\ShopProduct;
use App\Models\User;
use App\Modules\Product\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ListingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    public function test_owner_adds_a_catalog_product_with_their_own_price_and_thumbnail(): void
    {
        $shop = Shop::factory()->create();
        $product = Product::factory()->create(['new_price' => 1000]);

        $this->actingAs($shop->owner)
            ->post(route('owner.catalog.store', $product), [
                'price' => '850',
                'old_price' => '1000',
                'description' => 'Shop note',
                'thumbnail' => UploadedFile::fake()->image('mine.jpg'),
                'is_active' => '1',
            ])
            ->assertRedirect(route('owner.listings.index'))
            ->assertSessionHasNoErrors();

        $listing = $shop->listings()->sole();
        $this->assertSame('850.00', $listing->price);
        Storage::disk('public')->assertExists($listing->thumbnail);
        $this->assertSame('1000.00', $product->fresh()->new_price);   // the catalog is untouched
    }

    public function test_the_shop_comes_from_the_logged_in_owner_not_the_request(): void
    {
        $myShop = Shop::factory()->create();
        $otherShop = Shop::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($myShop->owner)
            ->post(route('owner.catalog.store', $product), ['price' => '10', 'is_active' => '1', 'shop_id' => $otherShop->id]);

        $this->assertSame(1, $myShop->listings()->count());
        $this->assertSame(0, $otherShop->listings()->count());
    }

    // Required test 7
    public function test_adding_the_same_product_twice_to_one_shop_fails_validation(): void
    {
        $listing = ShopProduct::factory()->create();

        $this->actingAs($listing->shop->owner)
            ->post(route('owner.catalog.store', $listing->product), ['price' => '99', 'is_active' => '1'])
            ->assertSessionHasErrors('product_id');

        $this->assertDatabaseCount('shop_product', 1);
    }

    public function test_old_price_must_not_be_below_the_price(): void
    {
        $shop = Shop::factory()->create();

        $this->actingAs($shop->owner)
            ->post(route('owner.catalog.store', Product::factory()->create()), ['price' => '500', 'old_price' => '400', 'is_active' => '1'])
            ->assertSessionHasErrors('old_price');
    }

    // Required test 5
    public function test_a_shop_owner_cannot_edit_or_delete_another_shops_listing(): void
    {
        $intruder = Shop::factory()->create()->owner;
        $listing = ShopProduct::factory()->create(['price' => 100]);

        $this->actingAs($intruder)->get(route('owner.listings.edit', $listing))->assertForbidden();
        $this->actingAs($intruder)
            ->put(route('owner.listings.update', $listing), ['price' => '1', 'is_active' => '1'])
            ->assertForbidden();
        $this->actingAs($intruder)->delete(route('owner.listings.destroy', $listing))->assertForbidden();

        $this->assertSame('100.00', $listing->fresh()->price);
    }

    public function test_updating_a_listing_changes_only_the_listing(): void
    {
        $listing = ShopProduct::factory()->create(['price' => 100]);
        $catalogPrice = $listing->product->new_price;

        $this->actingAs($listing->shop->owner)
            ->put(route('owner.listings.update', $listing), ['price' => '120', 'description' => 'Ours', 'is_active' => '0'])
            ->assertRedirect(route('owner.listings.index'))
            ->assertSessionHasNoErrors();

        $listing->refresh();
        $this->assertSame('120.00', $listing->price);
        $this->assertFalse($listing->is_active);
        $this->assertSame($catalogPrice, $listing->product->fresh()->new_price);
    }

    public function test_removing_a_listing_deletes_its_thumbnail_but_keeps_the_product(): void
    {
        Storage::disk('public')->put('listings/t.jpg', 'x');
        $listing = ShopProduct::factory()->create(['thumbnail' => 'listings/t.jpg']);

        $this->actingAs($listing->shop->owner)
            ->delete(route('owner.listings.destroy', $listing))
            ->assertRedirect(route('owner.listings.index'));

        $this->assertModelMissing($listing);
        $this->assertModelExists($listing->product);
        Storage::disk('public')->assertMissing('listings/t.jpg');
    }

    public function test_an_owner_without_a_shop_sees_the_no_shop_page(): void
    {
        $this->actingAs(User::factory()->create());

        foreach (['owner.catalog.index', 'owner.listings.index'] as $name) {
            $this->get(route($name))
                ->assertForbidden()
                ->assertInertia(fn (Assert $page) => $page->component('Owner/NoShop'));
        }

        $this->get(route('owner.dashboard'))->assertOk();
    }
}
