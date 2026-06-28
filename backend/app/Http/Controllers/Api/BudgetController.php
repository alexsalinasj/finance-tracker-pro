<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BudgetController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['nullable', 'integer', 'between:2000,2100'],
        ]);

        $month = (int) ($validated['month'] ?? now()->month);
        $year = (int) ($validated['year'] ?? now()->year);

        $budgets = $request->user()
            ->budgets()
            ->with('category')
            ->where('month', $month)
            ->where('year', $year)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->map(fn (Budget $budget) => $this->withUsage($budget));

        return response()->json(['data' => $budgets]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(fn ($query) => $query
                    ->where('user_id', $user->id)
                    ->where('type', 'expense')),
                Rule::unique('budgets')->where(fn ($query) => $query
                    ->where('user_id', $user->id)
                    ->where('month', $request->integer('month'))
                    ->where('year', $request->integer('year'))),
            ],
            'monthly_limit' => ['required', 'numeric', 'min:0.01', 'max:999999999.99'],
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'between:2000,2100'],
        ]);

        $budget = $user->budgets()->create($validated)->load('category');

        return response()->json(['data' => $this->withUsage($budget)], 201);
    }

    public function show(Request $request, Budget $budget): JsonResponse
    {
        $budget = $request->user()->budgets()->with('category')->whereKey($budget->id)->firstOrFail();

        return response()->json(['data' => $this->withUsage($budget)]);
    }

    public function update(Request $request, Budget $budget): JsonResponse
    {
        $budget = $request->user()->budgets()->whereKey($budget->id)->firstOrFail();
        $user = $request->user();

        $validated = $request->validate([
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(fn ($query) => $query
                    ->where('user_id', $user->id)
                    ->where('type', 'expense')),
                Rule::unique('budgets')
                    ->ignore($budget->id)
                    ->where(fn ($query) => $query
                        ->where('user_id', $user->id)
                        ->where('month', $request->integer('month'))
                        ->where('year', $request->integer('year'))),
            ],
            'monthly_limit' => ['required', 'numeric', 'min:0.01', 'max:999999999.99'],
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'between:2000,2100'],
        ]);

        $budget->update($validated);

        return response()->json(['data' => $this->withUsage($budget->refresh()->load('category'))]);
    }

    public function destroy(Request $request, Budget $budget): JsonResponse
    {
        $budget = $request->user()->budgets()->whereKey($budget->id)->firstOrFail();
        $budget->delete();

        return response()->json(status: 204);
    }

    private function withUsage(Budget $budget): array
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
            ...$budget->toArray(),
            'spent' => round($spent, 2),
            'usage_percentage' => $usage,
            'alert_level' => $usage >= 100 ? 'danger' : ($usage >= 80 ? 'warning' : 'ok'),
        ];
    }
}

