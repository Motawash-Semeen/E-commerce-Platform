<?php

namespace Database\Factories;

use App\Models\User;
use App\Modules\Product\Models\Product;
use App\Modules\Subcategory\Models\Subcategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    // The model lives in a module, so Laravel can't guess it from the factory name.
    protected $model = Product::class;

    public function definition(): array
    {
        $oldPrice = fake()->randomFloat(2, 500, 10000);

        return [
            'subcategory_id' => Subcategory::factory(),
            // Always match the subcategory's own category.
            'category_id' => fn (array $attributes) => Subcategory::find($attributes['subcategory_id'])->category_id,
            'created_by' => null,
            'name' => Str::title(fake()->words(3, true)),
            'description' => fake()->paragraph(),
            'image' => 'products/placeholder.jpg',
            'old_price' => $oldPrice,
            'new_price' => round($oldPrice * fake()->randomFloat(2, 0.6, 1), 2),
        ];
    }

    /**
     * Added to the catalog by this user.
     */
    public function createdBy(User $user): static
    {
        return $this->state(fn (array $attributes) => ['created_by' => $user->id]);
    }
}
