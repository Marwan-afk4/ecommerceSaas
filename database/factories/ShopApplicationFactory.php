<?php

namespace Database\Factories;

use App\Enums\ShopApplicationStatus;
use App\Models\ShopApplication;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ShopApplication>
 */
class ShopApplicationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
            'tagline' => fake()->sentence(3),
            'description' => fake()->sentence(),
            'public_owner_name' => fake()->name(),
            'public_email' => fake()->unique()->safeEmail(),
            'public_phone' => fake()->numerify('555-####'),
            'logo_path' => 'shops/logos/example.png',
            'admin_name' => fake()->name(),
            'admin_email' => fake()->unique()->safeEmail(),
            'admin_password' => 'password',
            'status' => ShopApplicationStatus::Pending,
            'shop_id' => null,
            'reviewed_at' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ShopApplicationStatus::Pending,
            'shop_id' => null,
            'reviewed_at' => null,
        ]);
    }

    public function accepted(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ShopApplicationStatus::Accepted,
            'admin_password' => null,
            'reviewed_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ShopApplicationStatus::Rejected,
            'admin_password' => null,
            'reviewed_at' => now(),
        ]);
    }
}
