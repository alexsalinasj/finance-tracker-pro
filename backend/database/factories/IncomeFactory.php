<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class IncomeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category_id' => fn (array $attributes) => Category::factory()->create([
                'user_id' => $attributes['user_id'],
                'type' => 'income',
            ])->id,
            'name' => fake()->randomElement(['Salary', 'Freelance project', 'Bonus', 'Refund']),
            'amount' => fake()->randomFloat(2, 100, 5000),
            'date' => fake()->dateTimeBetween('-6 months')->format('Y-m-d'),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
