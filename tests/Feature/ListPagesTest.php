<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Lazy loading is disabled outside production (AppServiceProvider), so any
 * N+1 query on these pages would throw and turn the response into a 500.
 */
class ListPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->seed(DemoDataSeeder::class);
    }

    public function test_admin_list_pages_load_without_lazy_loading(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $this->get(route('admin.categories.index'))->assertOk()->assertSee('Electronics');
        $this->get(route('admin.subcategories.index'))->assertOk()->assertSee('Headphones');
        $this->get(route('admin.products.index'))->assertOk()->assertSee('LED Desk Lamp');
    }

    public function test_public_home_page_loads_without_lazy_loading(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Electronics › Headphones')
            ->assertSee('True Wireless Earbuds');
    }
}
