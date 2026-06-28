<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BudgetFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category_id' => fn (array $attributes) => Category::factory()->create([
                'user_id' => $attributes['user_id'],
                'type' => 'expense',
            ])->id,
            'monthly_limit' => fake()->randomFloat(2, 200, 2500),
            'month' => now()->month,
            'year' => now()->year,
        ];
    }
}
