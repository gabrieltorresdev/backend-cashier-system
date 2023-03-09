<?php

namespace Database\Factories;

use App\Models\Cashier;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    private $products;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cashier_id' => Cashier::inRandomOrder()->pluck('id')->first(),
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function (Transaction $transaction) {
            $products = Product::inRandomOrder()->take(rand(2, 5))->get();
            
            $transaction->total_amount = $products->sum('cost');
            $transaction->save();
            $transaction->products()->sync($products->pluck('id'));
            $transaction->save();
        });
    }
}
