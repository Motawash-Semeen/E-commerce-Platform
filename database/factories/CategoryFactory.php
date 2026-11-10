<?php

namespace Database\Factories;

use App\Modules\Category\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    // The model lives in a module, so Laravel can't guess it from the factory name.
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'name' => Str::title(fake()->unique()->words(2, true)),
        ];
    }
}
