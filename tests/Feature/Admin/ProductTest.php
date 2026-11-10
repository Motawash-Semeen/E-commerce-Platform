<?php

namespace Tests\Feature\Admin;

use App\Modules\Product\Models\Product;
use App\Modules\Subcategory\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private Subcategory $subcategory;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->actingAs(User::factory()->admin()->create());
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

    public function test_a_subcategory_from_a_different_category_is_rejected(): void
    {
        $otherSubcategory = Subcategory::factory()->create(); // belongs to a new, different category

        $this->post(route('admin.products.store'), $this->validData([
            'subcategory_id' => $otherSubcategory->id,
        ]))->assertSessionHasErrors('subcategory_id');

        $this->assertDatabaseCount('products', 0);
    }

    public function test_new_price_greater_than_old_price_is_rejected(): void
    {
        $this->post(route('admin.products.store'), $this->validData([
            'old_price' => '500',
            'new_price' => '900',
        ]))->assertSessionHasErrors('new_price');

        $this->assertDatabaseCount('products', 0);
    }

    public function test_uploading_a_product_stores_the_image(): void
    {
        $this->post(route('admin.products.store'), $this->validData())
            ->assertRedirect(route('admin.products.index'))
            ->assertSessionHasNoErrors();

        $product = Product::sole();

        $this->assertStringStartsWith('products/', $product->image);
        $this->assertStringNotContainsString('photo', $product->image); // random name, not the original
        Storage::disk('public')->assertExists($product->image);
    }
}
