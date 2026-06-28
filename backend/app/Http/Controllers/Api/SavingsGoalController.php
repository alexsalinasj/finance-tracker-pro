<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SavingsGoal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SavingsGoalController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $goals = $request->user()
            ->savingsGoals()
            ->orderByRaw("FIELD(status, 'active', 'paused', 'completed')")
            ->orderBy('deadline')
            ->get();

        return response()->json(['data' => $goals]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'target_amount' => ['required', 'numeric', 'min:0.01', 'max:999999999.99'],
            'current_amount' => ['required', 'numeric', 'min:0', 'max:999999999.99'],
            'deadline' => ['required', 'date'],
            'status' => ['required', Rule::in(['active', 'paused', 'completed'])],
        ]);

        $goal = $request->user()->savingsGoals()->create($validated);

        return response()->json(['data' => $goal], 201);
    }

    public function show(Request $request, SavingsGoal $savingsGoal): JsonResponse
    {
        $goal = $request->user()->savingsGoals()->whereKey($savingsGoal->id)->firstOrFail();

        return response()->json(['data' => $goal]);
    }

    public function update(Request $request, SavingsGoal $savingsGoal): JsonResponse
    {
        $goal = $request->user()->savingsGoals()->whereKey($savingsGoal->id)->firstOrFail();
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'target_amount' => ['required', 'numeric', 'min:0.01', 'max:999999999.99'],
            'current_amount' => ['required', 'numeric', 'min:0', 'max:999999999.99'],
            'deadline' => ['required', 'date'],
            'status' => ['required', Rule::in(['active', 'paused', 'completed'])],
        ]);

        $goal->update($validated);

        return response()->json(['data' => $goal->refresh()]);
    }

    public function destroy(Request $request, SavingsGoal $savingsGoal): JsonResponse
    {
        $goal = $request->user()->savingsGoals()->whereKey($savingsGoal->id)->firstOrFail();
        $goal->delete();

        return response()->json(status: 204);
    }
}

