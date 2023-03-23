<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Stock;
use App\Models\StockType;
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
            'name' => fake()->userName(),
            'cost' => fake()->randomFloat(2, 10, 100),
            'description' => fake()->paragraph(2),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Product $product) {
            $stockType = StockType::inRandomOrder()->first();

            Stock::create([
                'quantity' => fake()->randomFloat($stockType->decimals, 0, 100),
                'product_id' => $product->id,
                'stock_type_id' => $stockType->id
            ]);
        });
    }
}
