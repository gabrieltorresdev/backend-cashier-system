<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\AccessPermission::factory(2)->create();

        \App\Models\User::factory()->create([
            'name' => 'Admin User',
            'username' => 'admin',
            'activated' => true,
            'email' => 'admin@email.com',
        ]);

        \App\Models\User::factory()->create([
            'name' => 'Normal User',
            'username' => 'user',
            'activated' => true,
            'email' => 'user@email.com',
        ]);

        \App\Models\CashRegister::factory(1)->create();

        \App\Models\StockType::factory(2)->create();

        \App\Models\Product::factory(2)->create();

        // \App\Models\Transaction::factory(2)->create();
    }
}
