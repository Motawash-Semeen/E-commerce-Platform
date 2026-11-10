<?php

namespace Tests\Feature\Admin;

use App\Modules\Category\Models\Category;
use App\Modules\Subcategory\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->admin()->create());
    }

    public function test_creating_a_category_without_a_name_fails_validation(): void
    {
        $this->post(route('admin.categories.store'), ['name' => ''])
            ->assertSessionHasErrors('name');

        $this->assertDatabaseCount('categories', 0);
    }

    public function test_a_category_with_subcategories_cannot_be_deleted(): void
    {
        $category = Category::factory()->create();
        Subcategory::factory()->for($category)->create();

        $this->from(route('admin.categories.index'))
            ->delete(route('admin.categories.destroy', $category))
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('error');

        $this->assertModelExists($category);
    }
}
