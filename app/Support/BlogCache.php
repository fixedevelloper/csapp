<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

// Invalide les caches lus par l'API publique du blog (article, derniers articles, catégories)
class BlogCache
{
    public static function flush(?string $slug = null): void
    {
        if ($slug) {
            Cache::forget("post:{$slug}");
        }
        Cache::forget('posts:latest');
        Cache::forget('categories:all');
        for ($limit = 1; $limit <= 20; $limit++) {
            Cache::forget("latest_posts:{$limit}");
        }
    }
}
