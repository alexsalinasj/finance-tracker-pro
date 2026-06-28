<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Income;
use App\Models\SavingsGoal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->updateOrCreate(
            ['email' => 'demo@financepro.test'],
            [
                'name' => 'Demo User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $incomeCategories = collect([
            ['name' => 'Salary', 'color' => '#198754', 'icon' => 'briefcase'],
            ['name' => 'Freelance', 'color' => '#0d6efd', 'icon' => 'laptop'],
            ['name' => 'Investments', 'color' => '#20c997', 'icon' => 'trending-up'],
        ])->map(fn (array $category) => Category::query()->updateOrCreate(
            ['user_id' => $user->id, 'type' => 'income', 'name' => $category['name']],
            [...$category, 'user_id' => $user->id, 'type' => 'income']
        ));

        $expenseCategories = collect([
            ['name' => 'Housing', 'color' => '#6f42c1', 'icon' => 'home'],
            ['name' => 'Food', 'color' => '#fd7e14', 'icon' => 'cart'],
            ['name' => 'Transport', 'color' => '#0dcaf0', 'icon' => 'car'],
            ['name' => 'Health', 'color' => '#dc3545', 'icon' => 'heart'],
            ['name' => 'Entertainment', 'color' => '#d63384', 'icon' => 'music'],
        ])->map(fn (array $category) => Category::query()->updateOrCreate(
            ['user_id' => $user->id, 'type' => 'expense', 'name' => $category['name']],
            [...$category, 'user_id' => $user->id, 'type' => 'expense']
        ));

        Income::query()->where('user_id', $user->id)->delete();
        Expense::query()->where('user_id', $user->id)->delete();
        Budget::query()->where('user_id', $user->id)->delete();
        SavingsGoal::query()->where('user_id', $user->id)->delete();

        foreach (range(0, 5) as $offset) {
            $month = Carbon::now()->subMonths($offset);

            Income::query()->create([
                'user_id' => $user->id,
                'category_id' => $incomeCategories->firstWhere('name', 'Salary')->id,
                'name' => 'Monthly salary',
                'amount' => 3600,
                'date' => $month->copy()->day(1)->format('Y-m-d'),
                'description' => 'Seeded monthly payroll income',
            ]);

            Income::query()->create([
                'user_id' => $user->id,
                'category_id' => $incomeCategories->random()->id,
                'name' => 'Side income',
                'amount' => random_int(250, 900),
                'date' => $month->copy()->day(random_int(4, 15))->format('Y-m-d'),
                'description' => null,
            ]);

            foreach ($expenseCategories as $category) {
                Expense::query()->create([
                    'user_id' => $user->id,
                    'category_id' => $category->id,
                    'name' => $category->name.' expense',
                    'amount' => random_int(80, 900),
                    'date' => $month->copy()->day(random_int(2, 25))->format('Y-m-d'),
                    'payment_method' => collect(['Cash', 'Debit card', 'Credit card', 'Bank transfer'])->random(),
                    'description' => 'Seeded transaction',
                ]);
            }
        }

        foreach ($expenseCategories as $index => $category) {
            Budget::query()->create([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'monthly_limit' => [1300, 650, 420, 350, 300][$index],
                'month' => now()->month,
                'year' => now()->year,
            ]);
        }

        SavingsGoal::query()->create([
            'user_id' => $user->id,
            'name' => 'Emergency fund',
            'target_amount' => 6000,
            'current_amount' => 2700,
            'deadline' => now()->addMonths(10)->format('Y-m-d'),
            'status' => 'active',
        ]);

        SavingsGoal::query()->create([
            'user_id' => $user->id,
            'name' => 'Vacation reserve',
            'target_amount' => 2200,
            'current_amount' => 1450,
            'deadline' => now()->addMonths(5)->format('Y-m-d'),
            'status' => 'active',
        ]);
    }
}

