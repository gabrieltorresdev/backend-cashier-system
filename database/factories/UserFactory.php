<?php

namespace Database\Factories;

use App\Models\AccessPermission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->name();

        return [
            'name' => $name,
            'email' => fake()->safeEmail(),
            'username' => str($name)->slug('.'),
            'password' => bcrypt('password'),
            'activated' => false,
            'access_permission_id' => AccessPermission::all()->random(),
        ];
    }

    /**
     * Indicate that the model should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'activated' => false,
        ]);
    }
}
