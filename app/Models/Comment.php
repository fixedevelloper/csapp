<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'comment', 'post_id', 'approved'];

    protected function casts(): array
    {
        return [
            'approved' => 'boolean',
        ];
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    // Scope pour ne récupérer que les commentaires approuvés
    public function scopeApproved($query)
    {
        return $query->where('approved', true);
    }
}
