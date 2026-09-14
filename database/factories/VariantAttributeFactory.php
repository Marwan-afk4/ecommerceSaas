<?php

namespace Database\Factories;

use App\Models\Shop;
use App\Models\VariantAttribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<VariantAttribute>
 */
class VariantAttributeFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->randomElement(['Color', 'Size', 'Storage', 'RAM', 'Material']).' '.fake()->unique()->numerify('##');

        return [
            'shop_id' => Shop::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'sort_order' => 0,
        ];
    }
}
