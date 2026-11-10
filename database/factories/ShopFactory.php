<?php

namespace Database\Factories;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Shop>
 */
class ShopFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),   // a shop owner (the factory default role)
            'name' => fake()->unique()->company(),
            'is_active' => true,
        ];
    }
}
