<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExpenseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['nullable', 'integer', 'between:2000,2100'],
        ]);

        $expenses = $request->user()
            ->expenses()
            ->with('category')
            ->when($validated['month'] ?? null, fn ($query, $month) => $query->whereMonth('date', $month))
            ->when($validated['year'] ?? null, fn ($query, $year) => $query->whereYear('date', $year))
            ->latest('date')
            ->paginate(15);

        return response()->json($expenses);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999999.99'],
            'date' => ['required', 'date'],
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(fn ($query) => $query
                    ->where('user_id', $user->id)
                    ->where('type', 'expense')),
            ],
            'payment_method' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $expense = $user->expenses()->create($validated)->load('category');

        return response()->json(['data' => $expense], 201);
    }

    public function show(Request $request, Expense $expense): JsonResponse
    {
        $expense = $request->user()->expenses()->with('category')->whereKey($expense->id)->firstOrFail();

        return response()->json(['data' => $expense]);
    }

    public function update(Request $request, Expense $expense): JsonResponse
    {
        $expense = $request->user()->expenses()->whereKey($expense->id)->firstOrFail();
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999999.99'],
            'date' => ['required', 'date'],
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(fn ($query) => $query
                    ->where('user_id', $user->id)
                    ->where('type', 'expense')),
            ],
            'payment_method' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $expense->update($validated);

        return response()->json(['data' => $expense->refresh()->load('category')]);
    }

    public function destroy(Request $request, Expense $expense): JsonResponse
    {
        $expense = $request->user()->expenses()->whereKey($expense->id)->firstOrFail();
        $expense->delete();

        return response()->json(status: 204);
    }
}

