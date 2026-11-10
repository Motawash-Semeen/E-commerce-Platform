<?php

namespace Database\Factories;

use App\Modules\Category\Models\Category;
use App\Modules\Subcategory\Models\Subcategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Subcategory>
 */
class SubcategoryFactory extends Factory
{
    // The model lives in a module, so Laravel can't guess it from the factory name.
    protected $model = Subcategory::class;

    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => Str::title(fake()->unique()->words(2, true)),
        ];
    }
}
