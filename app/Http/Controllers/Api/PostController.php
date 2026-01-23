<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function update(Request $request, Post $post)
    {
        // Validation
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'image' => 'nullable|image|max:2048', // max 2MB
        ]);

        // Upload image si présente
        if ($request->hasFile('image')) {

            // Supprimer l'ancienne image
            $post->clearMediaCollection('posts');

            $filename = 'post-' . time() . '.' . $request->file('image')->getClientOriginalExtension();

            $post
                ->addMediaFromRequest('image')
                ->usingFileName($filename)
                ->toMediaCollection('posts');
        }


        // Slug automatique
        $validated['slug'] = Str::slug($validated['title']);

        // Mise à jour du post
        $post->update($validated);

        // Sync categories & tags (pivot)
        if (isset($validated['categories'])) {
            $post->categories()->sync($validated['categories']);
        }
        if (isset($validated['tags'])) {
            $post->tags()->sync($validated['tags']);
        }
        // Rafraîchir cache
        Cache::forget("post:{$post->slug}");
        Cache::forget('posts:latest');
        return new PostResource($post->load('categories', 'tags'));
    }
    public function uploadEditorImage(Request $request, Post $post)
    {
        $request->validate([
            'image' => 'required|image|max:5120',
        ]);

        $media = $post
            ->addMediaFromRequest('image')
            ->toMediaCollection('post-content');

        return response()->json([
            'url' => $media->getUrl()
        ]);
    }

    public function index(Request $request)
    {
        $allowedColumns = ['id', 'title', 'created_at'];
        $orderColumn = in_array($request->order_column, $allowedColumns) ? $request->order_column : 'created_at';
        $orderDirection = in_array($request->order_direction, ['asc', 'desc']) ? $request->order_direction : 'desc';

        $limit = (int) $request->get('limit', 10);

        $postsQuery = Post::with(['media', 'categories', 'tags', 'user'])
            ->when($request->search_category, function ($query) use ($request) {
                $ids = array_map('intval', explode(',', $request->search_category));
                $query->whereHas('categories', fn($q) => $q->whereIn('id', $ids));
            })
            ->when($request->search_id, fn($q) => $q->where('id', (int)$request->search_id))
            ->when($request->search_title, fn($q) => $q->where('title', 'like', "%{$request->search_title}%"))
            ->when($request->search_content, fn($q) => $q->where('content', 'like', "%{$request->search_content}%"))
            ->when($request->search_global, function ($q) use ($request) {
                $s = strip_tags($request->search_global);
                $q->where(function ($inner) use ($s) {
                    $inner->where('id', $s)
                        ->orWhere('title', 'like', "%$s%")
                        ->orWhere('content', 'like', "%$s%");
                });
            })
            ->orderBy($orderColumn, $orderDirection);

        $posts = $postsQuery->paginate($limit);

        return PostResource::collection($posts)
            ->additional([
                'meta' => [
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                ],
            ]);
    }


    /**
     * GET /posts/all
     * Tous les posts sans filtre
     */
    public function getPosts()
    {
        $posts = Post::with(['categories', 'media', 'tags', 'user'])
            ->latest()
            ->paginate(20);

        return PostResource::collection($posts);
    }

    /**
     * GET /posts/category/{id}
     * Posts d'une catégorie
     */
    public function getCategoryByPosts($id)
    {
        $posts = Post::whereHas('categories', fn($q) => $q->where('id', $id))
            ->with(['media', 'categories', 'tags', 'user'])
            ->paginate(20);

        return PostResource::collection($posts);
    }

    /**
     * GET /posts/{slug}
     * Détail d'un post avec cache
     * @param string $slug
     * @return JsonResponse
     */
    public function getPost(string $slug)
    {

        // 🔹 Limite par défaut des articles récents
        $latestLimit = request()->get('limit', 5);

        // 🔹 Post principal (cache 1h)
        $post =
            Post::with(['categories', 'tags', 'user', 'media'])
                ->where('slug', $slug)
                ->firstOrFail();


        // 🔹 Derniers articles (cache 30 min)
        $latestPosts = Cache::remember("latest_posts:{$latestLimit}", 1800, function () use ($latestLimit) {
            return Post::with(['media', 'categories', 'tags', 'user'])
                ->latest()
                ->limit($latestLimit)
                ->get();
        });

        // 🔹 Catégories (cache long)
        $categories = Cache::remember("categories:all", 86400, function () {
            return Category::all();
        });

        return response()->json([
            'post'         => new PostResource($post),
            'latest_posts' => PostResource::collection($latestPosts),
            'categories'   => $categories,
            'seo' => [
                'title' => $post->title,
                'description' => $post->excerpt,
                'image' => optional($post->media->first())->url,
            ]

        ]);
    }

    /**
     * GET /posts/{id}
     * Détail d'un post avec cache
     * @param $id
     */
    public function getPostByID($id)
    {
        $post =  Post::with(['categories', 'tags', 'user', 'media'])
                ->where('id', $id)
                ->firstOrFail();
        return new PostResource($post);
    }
    /**
     * GET /posts/latest
     * Pour afficher les derniers posts sur la homepage
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function latest(Request $request)
    {
        $limit = $request->get('limit', 3);
        $posts = Post::with(['media', 'categories', 'tags', 'user'])
            ->latest()
            ->take($limit)
            ->get();

        return PostResource::collection($posts);
    }
}


