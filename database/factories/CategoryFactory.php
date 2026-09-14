<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'shop_id' => Shop::factory(),
            'parent_id' => null,
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => null,
            'is_active' => true,
        ];
    }
}
