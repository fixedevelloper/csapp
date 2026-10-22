<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Support\BlogCache;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CommentController extends Controller
{
    private function present(Comment $c): array
    {
        return [
            'id' => $c->id,
            'name' => $c->name,
            'email' => $c->email,
            'comment' => $c->comment,
            'approved' => (bool) $c->approved,
            'created_at' => $c->created_at?->toIso8601String(),
            'post' => $c->post ? ['id' => $c->post->id, 'title' => $c->post->title, 'slug' => $c->post->slug] : null,
        ];
    }

    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'filter' => ['nullable', Rule::in(['pending', 'approved', 'all'])],
            'q' => ['nullable', 'string', 'max:100'],
            'per_page' => ['nullable', 'integer'],
        ]);

        $perPage = min(max((int) $request->get('per_page', 20), 1), 100);

        $page = Comment::with('post:id,title,slug')
            ->when($request->filter === 'pending', fn ($q) => $q->where('approved', false))
            ->when($request->filter === 'approved', fn ($q) => $q->where('approved', true))
            ->when($request->q, function ($q, $term) {
                $term = '%'.str_replace(['%', '_'], ['\%', '\_'], strip_tags($term)).'%';
                $q->where(fn ($i) => $i->where('name', 'like', $term)->orWhere('email', 'like', $term)->orWhere('comment', 'like', $term));
            })
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'data' => $page->getCollection()->map(fn ($c) => $this->present($c))->values(),
            'meta' => ['current_page' => $page->currentPage(), 'last_page' => $page->lastPage(), 'total' => $page->total()],
            'counts' => [
                'all' => Comment::count(),
                'pending' => Comment::where('approved', false)->count(),
                'approved' => Comment::where('approved', true)->count(),
            ],
        ]);
    }

    public function update(Request $request, Comment $comment): JsonResponse
    {
        $data = $request->validate(['approved' => ['required', 'boolean']]);
        $comment->update($data);
        BlogCache::flush($comment->post?->slug);

        return response()->json(['data' => $this->present($comment->load('post:id,title,slug'))]);
    }

    public function destroy(Comment $comment): JsonResponse
    {
        $slug = $comment->post?->slug;
        $comment->delete();
        BlogCache::flush($slug);

        return response()->json(['message' => 'Commentaire supprimé.']);
    }
}
