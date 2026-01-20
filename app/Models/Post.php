<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Post extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'title',
        'slug',
        'user_id',
        'excerpt',
        'content',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    // -------------------
    // Relations
    // -------------------

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_post');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tag');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // -------------------
    // Scopes SEO & Published
    // -------------------
    public function scopePublished($query)
    {
        return $query->where('created_at', '<=', now());
    }

    public function registerMediaConversions(Media $media = null): void
    {
        // Thumbnail WebP
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(250)
            ->format('webp')
            ->quality(80)
            ->sharpen(10)
            ->nonQueued();

        // Image moyenne WebP
        $this->addMediaConversion('medium')
            ->width(800)
            ->format('webp')
            ->quality(85)
            ->nonQueued();

    }
}
