<?php

namespace Tests\Feature;

use App\Models\ShopProduct;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * Every page loads with the demo data. Lazy loading is disabled outside production
 * (AppServiceProvider), so any N+1 query would throw and turn the response into a 500.
 */
class ListPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->seed([AdminUserSeeder::class, DemoDataSeeder::class]);
    }

    public function test_the_seeders_create_the_demo_data(): void
    {
        $this->assertSame(2, User::shopOwners()->count());
        $this->assertSame(10, ShopProduct::visible()->count());   // one demo listing is hidden
    }

    public function test_admin_pages_load(): void
    {
        $this->actingAs(User::where('email', config('admin.email'))->sole());

        $pages = [
            'admin.dashboard' => 'Admin/Dashboard',
            'admin.shops.index' => 'Admin/Shops/Index',
            'admin.shops.create' => 'Admin/Shops/Create',
            'admin.products.index' => 'Admin/Products/Index',
            'admin.products.create' => 'Admin/Products/Create',
            'admin.shop-owners.index' => 'Admin/ShopOwners/Index',
            'admin.categories.index' => 'Admin/Categories/Index',
            'admin.subcategories.index' => 'Admin/Subcategories/Index',
        ];

        foreach ($pages as $name => $component) {
            $this->get(route($name))->assertOk()->assertInertia(fn (Assert $page) => $page->component($component));
        }
    }

    public function test_owner_pages_load(): void
    {
        $owner = User::where('email', 'owner1@example.com')->sole();
        $this->actingAs($owner);

        $pages = [
            'owner.dashboard' => 'Owner/Dashboard',
            'owner.catalog.index' => 'Owner/Catalog/Index',
            'owner.listings.index' => 'Owner/Listings/Index',
            'owner.products.index' => 'Owner/Products/Index',
            'owner.products.create' => 'Owner/Products/Create',
        ];

        foreach ($pages as $name => $component) {
            $this->get(route($name))->assertOk()->assertInertia(fn (Assert $page) => $page->component($component));
        }

        $listing = $owner->shop->listings()->first();
        $this->get(route('owner.listings.edit', $listing))->assertOk();
    }

    public function test_public_pages_load(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Public/Home')->has('shops', 2)->has('latest', 8));

        $this->get(route('shops.show', 'urban-bazaar'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Public/Shop')->where('productsCount', 5));

        $this->get(route('shops.products.show', ['urban-bazaar', 'classic-cotton-crew-tee']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('product.price', '899.00')
                ->where('elsewhere.0.shop.slug', 'corner-store'));

        // In no shop at all, so never visible.
        $this->get(route('shops.products.show', ['urban-bazaar', 'rattan-pendant-light']))->assertNotFound();
    }
}
