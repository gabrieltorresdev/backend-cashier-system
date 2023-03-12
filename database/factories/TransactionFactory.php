<?php

namespace Database\Factories;

use App\Models\CashRegister;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cash_register_id' => CashRegister::inRandomOrder()->pluck('id')->first(),
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function (Transaction $transaction) {
            $products = Product::inRandomOrder()->take(rand(2, 5))->get();
            
            $transaction->type = 'sale';
            $transaction->value = $products->sum('cost');
            $transaction->note = fake()->paragraph();
            $transaction->save();
            $transaction->products()->sync($products->pluck('id'));
            $transaction->save();
        });
    }
}
