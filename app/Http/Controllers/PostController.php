<?php


namespace App\Http\Controllers;


use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Category;
use App\Models\Post;

class PostController extends Controller
{

    public function index()
    {
        return view('product.index');
    }

    public function store(StorePostRequest $request)
    {
        $this->authorize('post-create');

        $validatedData = $request->validated();
        $validatedData['user_id'] = auth()->id();
        $post = Post::create($validatedData);

        $categories = explode(",", $request->categories);
        $category = Category::findMany($categories);
        $post->categories()->attach($category);
//        try {
        if ($request->hasFile('thumbnail')) {
            $post->addMediaFromRequest('thumbnail')->preservingOriginal()->toMediaCollection('images');
        }
//        } catch (Exception $e) {
//            error_log($e->getMessage());
//        }
        return new PostResource($post);
    }
}
