<?php

namespace Tests\Feature\Owner;

use App\Models\Shop;
use App\Models\User;
use App\Modules\Product\Models\Product;
use App\Modules\Subcategory\Models\Subcategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class OwnerProductTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    private function productData(array $overrides = []): array
    {
        $subcategory = Subcategory::factory()->create();

        return array_merge([
            'category_id' => $subcategory->category_id,
            'subcategory_id' => $subcategory->id,
            'name' => 'Handmade Mug',
            'description' => 'Stoneware, 350 ml.',
            'old_price' => '',
            'new_price' => '650',
            'image' => UploadedFile::fake()->image('mug.jpg'),
        ], $overrides);
    }

    public function test_owner_creates_a_catalog_product_and_can_add_it_to_their_shop(): void
    {
        $shop = Shop::factory()->create();

        $this->actingAs($shop->owner)
            ->post(route('owner.products.store'), $this->productData(['add_to_shop' => '1']))
            ->assertRedirect(route('owner.products.index'))
            ->assertSessionHasNoErrors();

        $product = Product::sole();
        $this->assertSame($shop->owner_id, $product->created_by);

        // Listed at the catalog price, with no own thumbnail (falls back to the catalog image).
        $listing = $shop->listings()->sole();
        $this->assertSame($product->id, $listing->product_id);
        $this->assertSame('650.00', $listing->price);
        $this->assertNull($listing->thumbnail);
    }

    // Required test 6
    public function test_a_product_created_by_owner_a_shows_in_owner_b_catalog(): void
    {
        $shopA = Shop::factory()->create();
        $shopB = Shop::factory()->create();

        $this->actingAs($shopA->owner)->post(route('owner.products.store'), $this->productData());

        $this->actingAs($shopB->owner)
            ->get(route('owner.catalog.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Owner/Catalog/Index')
                ->has('products.data', 1)
                ->where('products.data.0.name', 'Handmade Mug')
                ->where('products.data.0.in_my_shop', false));
    }

    // Required test 4
    public function test_a_shop_owner_cannot_edit_a_catalog_product_created_by_someone_else(): void
    {
        $owner = Shop::factory()->create()->owner;
        $othersProduct = Product::factory()->createdBy(User::factory()->create())->create(['name' => 'Not Mine']);
        $adminProduct = Product::factory()->createdBy(User::factory()->admin()->create())->create();

        foreach ([$othersProduct, $adminProduct] as $product) {
            $this->actingAs($owner)->get(route('owner.products.edit', $product))->assertForbidden();
            $this->actingAs($owner)
                ->put(route('owner.products.update', $product), $this->productData(['name' => 'Hacked', 'image' => null]))
                ->assertForbidden();
        }

        $this->assertSame('Not Mine', $othersProduct->fresh()->name);
    }

    public function test_the_creator_can_edit_their_own_product(): void
    {
        $owner = User::factory()->create();
        $product = Product::factory()->createdBy($owner)->create();

        $this->actingAs($owner)
            ->put(route('owner.products.update', $product), [
                ...$this->productData(['name' => 'Renamed', 'image' => null]),
            ])
            ->assertRedirect(route('owner.products.index'))
            ->assertSessionHasNoErrors();

        $this->assertSame('Renamed', $product->fresh()->name);
    }

    public function test_owners_cannot_delete_catalog_products(): void
    {
        $owner = User::factory()->create();
        $product = Product::factory()->createdBy($owner)->create();

        // There is no owner delete route, and the admin route is role-protected.
        $this->actingAs($owner)->delete(route('admin.products.destroy', $product))->assertForbidden();
        $this->assertModelExists($product);
    }
}
