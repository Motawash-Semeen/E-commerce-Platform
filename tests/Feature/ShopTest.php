<?php

namespace Tests\Feature;

use App\Modules\Product\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_product_page_loads_by_slug(): void
    {
        $product = Product::factory()->create(['name' => 'Blue Running Shoe']);

        $this->assertSame('blue-running-shoe', $product->slug);

        $this->get('/products/blue-running-shoe')
            ->assertOk()
            ->assertSee('Blue Running Shoe')
            ->assertSee($product->category->name)
            ->assertSee($product->subcategory->name);
    }

    public function test_public_product_page_returns_404_for_a_bad_slug(): void
    {
        $this->get('/products/this-product-does-not-exist')
            ->assertNotFound();
    }
}
