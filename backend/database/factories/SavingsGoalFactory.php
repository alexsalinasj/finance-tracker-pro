<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SavingsGoalFactory extends Factory
{
    public function definition(): array
    {
        $target = fake()->randomFloat(2, 500, 10000);

        return [
            'user_id' => User::factory(),
            'name' => fake()->randomElement(['Emergency fund', 'Vacation', 'New laptop', 'Home deposit']),
            'target_amount' => $target,
            'current_amount' => fake()->randomFloat(2, 0, $target),
            'deadline' => fake()->dateTimeBetween('+1 month', '+18 months')->format('Y-m-d'),
            'status' => 'active',
        ];
    }
}

