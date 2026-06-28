<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Income;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IncomeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['nullable', 'integer', 'between:2000,2100'],
        ]);

        $incomes = $request->user()
            ->incomes()
            ->with('category')
            ->when($validated['month'] ?? null, fn ($query, $month) => $query->whereMonth('date', $month))
            ->when($validated['year'] ?? null, fn ($query, $year) => $query->whereYear('date', $year))
            ->latest('date')
            ->paginate(15);

        return response()->json($incomes);
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
                    ->where('type', 'income')),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $income = $user->incomes()->create($validated)->load('category');

        return response()->json(['data' => $income], 201);
    }

    public function show(Request $request, Income $income): JsonResponse
    {
        $income = $request->user()->incomes()->with('category')->whereKey($income->id)->firstOrFail();

        return response()->json(['data' => $income]);
    }

    public function update(Request $request, Income $income): JsonResponse
    {
        $income = $request->user()->incomes()->whereKey($income->id)->firstOrFail();
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999999.99'],
            'date' => ['required', 'date'],
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(fn ($query) => $query
                    ->where('user_id', $user->id)
                    ->where('type', 'income')),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $income->update($validated);

        return response()->json(['data' => $income->refresh()->load('category')]);
    }

    public function destroy(Request $request, Income $income): JsonResponse
    {
        $income = $request->user()->incomes()->whereKey($income->id)->firstOrFail();
        $income->delete();

        return response()->json(status: 204);
    }
}

