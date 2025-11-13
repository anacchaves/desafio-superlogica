<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $stock = fake()->numberBetween(0, 100);

        return [
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 10, 1000),
            'stock' => $stock,
            'is_active' => $stock > 0,
        ];
    }

    /**
     * Indicate that the product is inactive (zero stock).
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 0,
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the product is active (positive stock).
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => fake()->numberBetween(1, 100),
            'is_active' => true,
        ]);
    }
}
