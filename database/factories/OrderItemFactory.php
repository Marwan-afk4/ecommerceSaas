<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'shop_id' => Shop::factory(),
            'order_id' => fn (array $attributes): int => Order::factory()->create([
                'shop_id' => $attributes['shop_id'],
            ])->id,
            'product_id' => fn (array $attributes): int => Product::factory()->create([
                'shop_id' => $attributes['shop_id'],
            ])->id,
            'product_variant_id' => null,
            'name' => fake()->words(3, true),
            'quantity' => 1,
            'unit_price' => '19.99',
        ];
    }
}
