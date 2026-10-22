<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminPostResource;
use App\Models\Post;
use App\Support\BlogCache;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    private function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:300'],
            'content' => ['required', 'string', 'min:10'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:255'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['integer', 'exists:categories,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function index(Request $request)
    {
        $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'integer'],
            'per_page' => ['nullable', 'integer'],
        ]);

        $perPage = min(max((int) $request->get('per_page', 15), 1), 100);

        $posts = Post::with(['categories', 'tags', 'user', 'media'])->withCount('comments')
            ->when($request->q, function ($q, $term) {
                $term = '%'.str_replace(['%', '_'], ['\%', '\_'], strip_tags($term)).'%';
                $q->where(fn ($i) => $i->where('title', 'like', $term)->orWhere('excerpt', 'like', $term));
            })
            ->when($request->category, fn ($q, $id) => $q->whereHas('categories', fn ($c) => $c->where('categories.id', $id)))
            ->latest()
            ->paginate($perPage);

        return AdminPostResource::collection($posts)->additional(['counts' => ['all' => Post::count()]]);
    }

    public function show(Post $post): AdminPostResource
    {
        return new AdminPostResource($post->load(['categories', 'tags', 'user', 'media']));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->rules());

        $post = Post::create([
            'user_id' => $request->user()->id,
            'title' => $data['title'],
            'slug' => Post::uniqueSlug($data['title']),
            'content' => $data['content'],
            'excerpt' => $data['excerpt'] ?? null ?: Str::limit(trim(strip_tags($data['content'])), 160),
            'meta_title' => $data['meta_title'] ?? null ?: $data['title'],
            'meta_keywords' => $data['meta_keywords'] ?? '',
            'meta_description' => $data['meta_description'] ?? null ?: Str::limit(trim(strip_tags($data['content'])), 160),
        ]);

        $this->syncRelations($post, $request, $data);
        BlogCache::flush();

        return (new AdminPostResource($post->load(['categories', 'tags', 'user', 'media'])))->response()->setStatusCode(201);
    }

    public function update(Request $request, Post $post): AdminPostResource
    {
        $data = $request->validate($this->rules());

        // Le slug n'est pas modifié : changer le titre ne casse pas l'URL déjà publiée
        $post->update([
            'title' => $data['title'],
            'content' => $data['content'],
            'excerpt' => $data['excerpt'] ?? null ?: Str::limit(trim(strip_tags($data['content'])), 160),
            'meta_title' => $data['meta_title'] ?? null ?: $data['title'],
            'meta_keywords' => $data['meta_keywords'] ?? '',
            'meta_description' => $data['meta_description'] ?? null ?: Str::limit(trim(strip_tags($data['content'])), 160),
        ]);

        $this->syncRelations($post, $request, $data);
        BlogCache::flush($post->slug);

        return new AdminPostResource($post->load(['categories', 'tags', 'user', 'media']));
    }

    public function destroy(Post $post): JsonResponse
    {
        $slug = $post->slug;
        // InteractsWithMedia supprime aussi les images de l'article
        $post->delete();
        BlogCache::flush($slug);

        return response()->json(['message' => 'Article supprimé.']);
    }

    private function syncRelations(Post $post, Request $request, array $data): void
    {
        $post->categories()->sync($data['categories'] ?? []);
        $post->tags()->sync($data['tags'] ?? []);

        if ($request->hasFile('image')) {
            $post->clearMediaCollection('posts');
            $filename = 'post-'.time().'.'.$request->file('image')->getClientOriginalExtension();
            $post->addMediaFromRequest('image')->usingFileName($filename)->toMediaCollection('posts');
        }
    }
}
