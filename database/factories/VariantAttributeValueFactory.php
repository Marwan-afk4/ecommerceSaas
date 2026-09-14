<?php

namespace Database\Factories;

use App\Models\Shop;
use App\Models\VariantAttribute;
use App\Models\VariantAttributeValue;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<VariantAttributeValue>
 */
class VariantAttributeValueFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->word();

        return [
            'shop_id' => Shop::factory(),
            'variant_attribute_id' => fn (array $attributes): int => VariantAttribute::factory()->create([
                'shop_id' => $attributes['shop_id'],
            ])->id,
            'name' => $name,
            'slug' => Str::slug($name),
            'hex' => null,
            'sort_order' => 0,
        ];
    }
}
