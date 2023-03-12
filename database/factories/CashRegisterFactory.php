<?php

namespace Database\Factories;

use App\Models\CashRegister;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cashier>
 */
class CashRegisterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'initial_balance' => 0,
            'current_balance' => 300, // transactions->value sum
            'date_time' => fake()->dateTimeThisMonth(),
            'user_id' => User::inRandomOrder()->pluck('id')->first()
        ];
    }
}
