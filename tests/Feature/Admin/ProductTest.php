<?php

namespace Tests\Feature\Admin;

use App\Models\ShopProduct;
use App\Models\User;
use App\Modules\Product\Models\Product;
use App\Modules\Subcategory\Models\Subcategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Subcategory $subcategory;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->admin = User::factory()->admin()->create();
        $this->actingAs($this->admin);
        $this->subcategory = Subcategory::factory()->create();
    }

    /**
     * A valid form submission; each test overrides only what it checks.
     */
    private function validData(array $overrides = []): array
    {
        return array_merge([
            'category_id' => $this->subcategory->category_id,
            'subcategory_id' => $this->subcategory->id,
            'name' => 'Test Product',
            'description' => 'A product used in tests.',
            'old_price' => '1000',
            'new_price' => '800',
            'image' => UploadedFile::fake()->image('photo.jpg'),
        ], $overrides);
    }

    // Required test 8
    public function test_a_subcategory_from_a_different_category_is_rejected(): void
    {
        $otherSubcategory = Subcategory::factory()->create(); // belongs to a new, different category

        $this->post(route('admin.products.store'), $this->validData([
            'subcategory_id' => $otherSubcategory->id,
        ]))->assertSessionHasErrors('subcategory_id');

        $this->assertDatabaseCount('products', 0);
    }

    public function test_price_greater_than_old_price_is_rejected_but_old_price_is_optional(): void
    {
        $this->post(route('admin.products.store'), $this->validData(['old_price' => '500', 'new_price' => '900']))
            ->assertSessionHasErrors('new_price');

        $this->post(route('admin.products.store'), $this->validData(['old_price' => '', 'new_price' => '900']))
            ->assertSessionHasNoErrors();

        $this->assertNull(Product::sole()->old_price);
    }

    // Required test 13
    public function test_uploading_a_product_stores_the_image_and_records_the_creator(): void
    {
        $owner = User::factory()->create();

        $this->post(route('admin.products.store'), $this->validData(['created_by' => $owner->id]))
            ->assertRedirect(route('admin.products.index'))
            ->assertSessionHasNoErrors();

        $product = Product::sole();

        $this->assertStringStartsWith('products/', $product->image);
        $this->assertStringNotContainsString('photo', $product->image); // random name, not the original
        Storage::disk('public')->assertExists($product->image);
        $this->assertSame($this->admin->id, $product->created_by);       // never taken from the request
    }

    public function test_replacing_the_image_deletes_the_old_file(): void
    {
        $this->post(route('admin.products.store'), $this->validData());
        $product = Product::sole();
        $oldImage = $product->image;

        $this->post(route('admin.products.update', $product), [
            ...$this->validData(['image' => UploadedFile::fake()->image('new.png')]),
            '_method' => 'put',
        ])->assertSessionHasNoErrors();

        Storage::disk('public')->assertMissing($oldImage);
        Storage::disk('public')->assertExists($product->fresh()->image);
    }

    // Required test 12
    public function test_the_admin_cannot_delete_a_product_that_is_listed_in_a_shop(): void
    {
        $listing = ShopProduct::factory()->create();

        $this->from(route('admin.products.index'))
            ->delete(route('admin.products.destroy', $listing->product))
            ->assertRedirect(route('admin.products.index'))
            ->assertSessionHas('error', fn ($message) => str_contains($message, 'Remove it from all shops first.'));

        $this->assertModelExists($listing->product);
    }

    public function test_an_unlisted_product_can_be_deleted_with_its_image(): void
    {
        $this->post(route('admin.products.store'), $this->validData());
        $product = Product::sole();

        $this->delete(route('admin.products.destroy', $product))
            ->assertRedirect(route('admin.products.index'))
            ->assertSessionHas('success');

        $this->assertModelMissing($product);
        Storage::disk('public')->assertMissing($product->image);
    }

    public function test_index_shows_creator_and_listing_count(): void
    {
        $listing = ShopProduct::factory()->create();
        $listing->product->forceFill(['created_by' => $this->admin->id])->save();

        $this->get(route('admin.products.index'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Products/Index')
                ->where('products.data.0.name', $listing->product->name)
                ->where('products.data.0.creator_is_admin', true)
                ->where('products.data.0.listings_count', 1)
                ->missing('products.data.0.description'));   // only the fields the table needs
    }
}
