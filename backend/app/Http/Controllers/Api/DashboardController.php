<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['nullable', 'integer', 'between:2000,2100'],
        ]);

        $month = (int) ($validated['month'] ?? now()->month);
        $year = (int) ($validated['year'] ?? now()->year);
        $user = $request->user();

        $monthlyIncome = (float) $user->incomes()->whereMonth('date', $month)->whereYear('date', $year)->sum('amount');
        $monthlyExpense = (float) $user->expenses()->whereMonth('date', $month)->whereYear('date', $year)->sum('amount');
        $currentBalance = (float) $user->incomes()->sum('amount') - (float) $user->expenses()->sum('amount');

        $goalTarget = (float) $user->savingsGoals()->sum('target_amount');
        $goalCurrent = (float) $user->savingsGoals()->sum('current_amount');
        $goalCompletion = $goalTarget > 0 ? round(min(($goalCurrent / $goalTarget) * 100, 100), 2) : 0;

        $monthlyTrend = collect(range(1, 12))->map(function (int $monthNumber) use ($user, $year): array {
            $date = Carbon::create($year, $monthNumber, 1);

            return [
                'label' => $date->format('M'),
                'income' => round((float) $user->incomes()->whereMonth('date', $monthNumber)->whereYear('date', $year)->sum('amount'), 2),
                'expense' => round((float) $user->expenses()->whereMonth('date', $monthNumber)->whereYear('date', $year)->sum('amount'), 2),
            ];
        });

        $expensesByCategory = $user->expenses()
            ->select('categories.name', 'categories.color', DB::raw('SUM(expenses.amount) as total'))
            ->join('categories', 'categories.id', '=', 'expenses.category_id')
            ->whereMonth('expenses.date', $month)
            ->whereYear('expenses.date', $year)
            ->groupBy('categories.id', 'categories.name', 'categories.color')
            ->orderByDesc('total')
            ->get();

        $budgetAlerts = $user->budgets()
            ->with('category')
            ->where('month', $month)
            ->where('year', $year)
            ->get()
            ->map(fn (Budget $budget) => $this->budgetAlert($budget))
            ->filter(fn (array $budget) => $budget['alert_level'] !== 'ok')
            ->values();

        $recentTransactions = collect()
            ->merge($user->incomes()->with('category')->latest('date')->limit(5)->get()->map(fn ($income) => [
                'id' => $income->id,
                'type' => 'income',
                'name' => $income->name,
                'amount' => (float) $income->amount,
                'date' => $income->date->format('Y-m-d'),
                'category' => $income->category?->name,
            ]))
            ->merge($user->expenses()->with('category')->latest('date')->limit(5)->get()->map(fn ($expense) => [
                'id' => $expense->id,
                'type' => 'expense',
                'name' => $expense->name,
                'amount' => (float) $expense->amount,
                'date' => $expense->date->format('Y-m-d'),
                'category' => $expense->category?->name,
            ]))
            ->sortByDesc('date')
            ->take(8)
            ->values();

        return response()->json([
            'data' => [
                'summary' => [
                    'current_balance' => round($currentBalance, 2),
                    'monthly_income' => round($monthlyIncome, 2),
                    'monthly_expense' => round($monthlyExpense, 2),
                    'available_savings' => round($monthlyIncome - $monthlyExpense, 2),
                    'goals_completion_percentage' => $goalCompletion,
                ],
                'income_vs_expense' => $monthlyTrend,
                'expenses_by_category' => $expensesByCategory,
                'budget_alerts' => $budgetAlerts,
                'recent_transactions' => $recentTransactions,
            ],
        ]);
    }

    private function budgetAlert(Budget $budget): array
    {
        $spent = (float) $budget->user
            ->expenses()
            ->where('category_id', $budget->category_id)
            ->whereMonth('date', $budget->month)
            ->whereYear('date', $budget->year)
            ->sum('amount');

        $limit = (float) $budget->monthly_limit;
        $usage = $limit > 0 ? round(($spent / $limit) * 100, 2) : 0;

        return [
            'id' => $budget->id,
            'category' => $budget->category?->name,
            'monthly_limit' => $limit,
            'spent' => round($spent, 2),
            'usage_percentage' => $usage,
            'alert_level' => $usage >= 100 ? 'danger' : ($usage >= 80 ? 'warning' : 'ok'),
        ];
    }
}

