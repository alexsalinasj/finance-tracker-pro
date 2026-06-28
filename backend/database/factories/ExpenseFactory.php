<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category_id' => fn (array $attributes) => Category::factory()->create([
                'user_id' => $attributes['user_id'],
                'type' => 'expense',
            ])->id,
            'name' => fake()->randomElement(['Groceries', 'Rent', 'Internet', 'Fuel', 'Dinner']),
            'amount' => fake()->randomFloat(2, 10, 1500),
            'date' => fake()->dateTimeBetween('-6 months')->format('Y-m-d'),
            'payment_method' => fake()->randomElement(['Cash', 'Debit card', 'Credit card', 'Bank transfer']),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
