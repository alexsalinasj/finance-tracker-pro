<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Database\QueryException;
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
        $validated = $request->validate($this->rules($request, $user->id), $this->messages());

        try {
            $category = $user->categories()->create($validated);
        } catch (QueryException $exception) {
            return $this->duplicateCategoryResponse($exception);
        }

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

        $validated = $request->validate($this->rules($request, $user->id, $category->id), $this->messages());

        try {
            $category->update($validated);
        } catch (QueryException $exception) {
            return $this->duplicateCategoryResponse($exception);
        }

        return response()->json(['data' => $category->refresh()]);
    }

    public function destroy(Request $request, Category $category): JsonResponse
    {
        $category = $request->user()->categories()->whereKey($category->id)->firstOrFail();
        $category->delete();

        return response()->json(status: 204);
    }

    private function rules(Request $request, int $userId, ?int $ignoreId = null): array
    {
        $nameRule = Rule::unique('categories')
            ->where(fn ($query) => $query
                ->where('user_id', $userId)
                ->where('type', $request->input('type')));

        if ($ignoreId) {
            $nameRule->ignore($ignoreId);
        }

        return [
            'name' => ['required', 'string', 'max:100', $nameRule],
            'type' => ['required', Rule::in(['income', 'expense'])],
            'color' => ['required', 'string', 'max:20'],
            'icon' => ['required', 'string', 'max:50'],
        ];
    }

    private function messages(): array
    {
        return [
            'name.unique' => 'Ya existe una categoría con ese nombre para este tipo.',
            'type.in' => 'Selecciona un tipo de categoría válido.',
        ];
    }

    private function duplicateCategoryResponse(QueryException $exception): JsonResponse
    {
        if (($exception->errorInfo[0] ?? null) !== '23000' || (int) ($exception->errorInfo[1] ?? 0) !== 1062) {
            throw $exception;
        }

        return response()->json([
            'message' => 'Ya existe una categoría con ese nombre para este tipo.',
            'errors' => [
                'name' => ['Ya existe una categoría con ese nombre para este tipo.'],
            ],
        ], 422);
    }
}
