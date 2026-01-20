<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        // Récupère le premier média de la collection 'featured' (ou adapte selon ton modèle)
        $media = $this->getFirstMedia('posts');

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'comments_count' => $this->comments()->count(),

            'comments' => $this->comments,
            'thumb_url' => $media && $media->hasGeneratedConversion('thumb')
                ? $media->getUrl('thumb')
                : '/images/blog/default-thumb.webp',

            'image_url' => $media
                ? $media->getUrl('medium')
                : '/images/blog/default.webp',

            // Relations si nécessaire
            'categories' => $this->categories()->pluck('name'),
            'tags' => TagResource::collection($this->tags),
            'author' => [
                'id' => $this->user->id ?? null,
                'name' => $this->user->name ?? 'Inconnu',
                'avatar' => $this->user && $this->user->getFirstMedia('avatars')
                    ? $this->user->getFirstMediaUrl('avatars', 'thumb')
                    : '/images/default-avatar.jpg',
            ],
        ];
    }
}
