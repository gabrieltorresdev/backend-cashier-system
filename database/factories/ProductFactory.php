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
        return [
            'code' => fake()->randomNumber(4),
            'name' => fake()->domainName(),
            'cost' => fake()->randomFloat(2, 10, 100),
            'stock_quantity' => fake()->numberBetween(0, 20),
            'description' => fake()->paragraph(2),
        ];
    }
}
