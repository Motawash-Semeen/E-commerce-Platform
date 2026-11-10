<?php

namespace Tests\Feature\Admin;

use App\Models\Shop;
use App\Models\ShopProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ShopTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->actingAs(User::factory()->admin()->create());
    }

    public function test_admin_creates_a_shop_with_a_logo_for_an_owner(): void
    {
        $owner = User::factory()->create();

        $this->post(route('admin.shops.store'), [
            'name' => 'Green Leaf',
            'owner_id' => $owner->id,
            'description' => 'Plants and pots.',
            'logo' => UploadedFile::fake()->image('logo.png'),
            'is_active' => '1',
        ])->assertRedirect(route('admin.shops.index'))->assertSessionHasNoErrors();

        $shop = Shop::sole();

        $this->assertSame($owner->id, $shop->owner_id);
        $this->assertSame('green-leaf', $shop->slug);
        Storage::disk('public')->assertExists($shop->logo);
    }

    public function test_the_owner_must_be_a_shop_owner_without_a_shop(): void
    {
        $admin = User::factory()->admin()->create();
        $taken = Shop::factory()->create()->owner;

        foreach ([$admin->id, $taken->id] as $ownerId) {
            $this->post(route('admin.shops.store'), ['name' => 'X', 'owner_id' => $ownerId, 'is_active' => '1'])
                ->assertSessionHasErrors('owner_id');
        }

        $this->assertDatabaseCount('shops', 1);
    }

    public function test_the_owner_dropdown_lists_only_owners_without_a_shop_plus_the_current_one(): void
    {
        $shop = Shop::factory()->create();
        $free = User::factory()->create();
        Shop::factory()->create();   // another owner who already has a shop

        $this->get(route('admin.shops.edit', $shop))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Shops/Edit')
                ->has('owners', 2)
                ->where('owners', fn ($owners) => collect($owners)->pluck('id')->sort()->values()->all()
                    === collect([$shop->owner_id, $free->id])->sort()->values()->all()));
    }

    public function test_deleting_a_shop_removes_its_listings_and_files(): void
    {
        Storage::disk('public')->put('shops/logo.png', 'x');
        Storage::disk('public')->put('listings/thumb.png', 'x');
        $shop = Shop::factory()->create(['logo' => 'shops/logo.png']);
        $listing = ShopProduct::factory()->for($shop)->create(['thumbnail' => 'listings/thumb.png']);

        $this->delete(route('admin.shops.destroy', $shop))->assertRedirect(route('admin.shops.index'));

        $this->assertModelMissing($shop);
        $this->assertModelMissing($listing);
        $this->assertModelExists($listing->product);   // the catalog product stays
        Storage::disk('public')->assertMissing(['shops/logo.png', 'listings/thumb.png']);
    }
}
