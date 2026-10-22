<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Support\BlogCache;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Category::withCount('posts')->orderBy('name')->get()->map(fn ($m) => [
                'id' => $m->id,
                'name' => $m->name,
                'posts_count' => $m->posts_count,
            ]),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'min:2', 'max:100', Rule::unique('categories', 'name')]]);
        $model = Category::create(['name' => trim($data['name'])]);
        BlogCache::flush();

        return response()->json(['data' => ['id' => $model->id, 'name' => $model->name, 'posts_count' => 0]], 201);
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'min:2', 'max:100', Rule::unique('categories', 'name')->ignore($category->id)]]);
        $category->update(['name' => trim($data['name'])]);
        BlogCache::flush();

        return response()->json(['data' => ['id' => $category->id, 'name' => $category->name]]);
    }

    public function destroy(Category $category): JsonResponse
    {
        $category->delete();
        BlogCache::flush();

        return response()->json(['message' => 'Catégorie supprimée.']);
    }
}
