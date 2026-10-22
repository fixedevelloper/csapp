<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminPostResource extends JsonResource
{
    public function toArray($request): array
    {
        $media = $this->getFirstMedia('posts');
        $full = $request->routeIs('*.show') || $request->routeIs('*.store') || $request->routeIs('*.update');

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'author' => $this->user?->name,
            'comments_count' => $this->comments_count ?? $this->comments()->count(),
            'thumb_url' => $media && $media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : null,
            'categories' => $this->categories->map(fn ($c) => ['id' => $c->id, 'name' => $c->name])->values(),
            'tags' => $this->tags->map(fn ($t) => ['id' => $t->id, 'name' => $t->name])->values(),
            $this->mergeWhen($full, fn () => [
                'content' => $this->content,
                'meta_title' => $this->meta_title,
                'meta_description' => $this->meta_description,
                'meta_keywords' => $this->meta_keywords,
                'image_url' => $media ? $media->getUrl('medium') : null,
            ]),
        ];
    }
}
