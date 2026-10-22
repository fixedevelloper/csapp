<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Support\BlogCache;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TagController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Tag::withCount('posts')->orderBy('name')->get()->map(fn ($m) => [
                'id' => $m->id,
                'name' => $m->name,
                'posts_count' => $m->posts_count,
            ]),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'min:2', 'max:100', Rule::unique('tags', 'name')]]);
        $model = Tag::create(['name' => trim($data['name'])]);
        BlogCache::flush();

        return response()->json(['data' => ['id' => $model->id, 'name' => $model->name, 'posts_count' => 0]], 201);
    }

    public function update(Request $request, Tag $tag): JsonResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'min:2', 'max:100', Rule::unique('tags', 'name')->ignore($tag->id)]]);
        $tag->update(['name' => trim($data['name'])]);
        BlogCache::flush();

        return response()->json(['data' => ['id' => $tag->id, 'name' => $tag->name]]);
    }

    public function destroy(Tag $tag): JsonResponse
    {
        $tag->delete();
        BlogCache::flush();

        return response()->json(['message' => 'Tag supprimé.']);
    }
}
