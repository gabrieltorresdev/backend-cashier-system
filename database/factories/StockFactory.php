<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\StockType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stock>
 */
class StockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'quantity' => fake()->randomFloat(0, 10, 100),
            'stock_type_id' => StockType::inRandomOrder()->pluck('id')->first(),
            'product_id' => Product::inRandomOrder()->pluck('id')->first(),
        ];
    }
}
