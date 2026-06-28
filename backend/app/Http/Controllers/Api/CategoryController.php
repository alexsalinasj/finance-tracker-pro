<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['nullable', Rule::in(['income', 'expense'])],
        ]);

        $categories = $request->user()
            ->categories()
            ->when($validated['type'] ?? null, fn ($query, $type) => $query->where('type', $type))
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return response()->json(['data' => $categories]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categories')->where(fn ($query) => $query
                    ->where('user_id', $user->id)
                    ->where('type', $request->input('type'))),
            ],
            'type' => ['required', Rule::in(['income', 'expense'])],
            'color' => ['required', 'string', 'max:20'],
            'icon' => ['required', 'string', 'max:50'],
        ]);

        $category = $user->categories()->create($validated);

        return response()->json(['data' => $category], 201);
    }

    public function show(Request $request, Category $category): JsonResponse
    {
        $category = $request->user()->categories()->whereKey($category->id)->firstOrFail();

        return response()->json(['data' => $category]);
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $category = $request->user()->categories()->whereKey($category->id)->firstOrFail();
        $user = $request->user();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categories')
                    ->ignore($category->id)
                    ->where(fn ($query) => $query
                        ->where('user_id', $user->id)
                        ->where('type', $request->input('type'))),
            ],
            'type' => ['required', Rule::in(['income', 'expense'])],
            'color' => ['required', 'string', 'max:20'],
            'icon' => ['required', 'string', 'max:50'],
        ]);

        $category->update($validated);

        return response()->json(['data' => $category->refresh()]);
    }

    public function destroy(Request $request, Category $category): JsonResponse
    {
        $category = $request->user()->categories()->whereKey($category->id)->firstOrFail();
        $category->delete();

        return response()->json(status: 204);
    }
}

