<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\TagResource;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;

class HookController extends Controller
{
    public function getCategories()
    {
        $posts = Category::query()
            ->latest()->get();

        return CategoryResource::collection($posts);
    }
    public function getTags()
    {
        $posts = Tag::query()
            ->latest()->get();

        return TagResource::collection($posts);
    }

}
