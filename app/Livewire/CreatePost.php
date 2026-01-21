<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
class CreatePost extends Component
{
    use WithFileUploads;

    public $postId;
    public $title, $content, $status = 'draft';
    public $categories = [];
    public $tags = [];
    public $image;
    public $meta_title, $meta_keywords, $meta_description;

    public $availableCategories = [];
    public $availableTags = [];
    public $isEditing = false;

    protected $rules = [
        'title' => 'required|min:3',
        'content' => 'required|min:10',
        'categories' => 'array',
        'tags' => 'array',
        'image' => 'nullable|image|max:2048',
        'meta_title' => 'nullable|min:3',
        'meta_keywords' => 'nullable|min:3',
        'meta_description' => 'nullable|min:10',
    ];

    public function mount($id = null)
    {
        $this->availableCategories = Category::all();
        $this->availableTags = Tag::all();

        if ($id) {
            $this->loadPost($id);
        }
    }

    public function loadPost($id)
    {
        $post = Post::with(['categories', 'tags'])->findOrFail($id);

        $this->postId = $post->id;
        $this->title = $post->title;
        $this->content = $post->content;
        $this->status = $post->status;
        $this->categories = $post->categories->pluck('id')->toArray();
        $this->tags = $post->tags->pluck('id')->toArray();
        $this->meta_title = $post->meta_title;
        $this->meta_keywords = $post->meta_keywords;
        $this->meta_description = $post->meta_description;

        $this->isEditing = true;
    }

    public function save()
    {
        $this->validate();

        $slug = Str::slug($this->title);

        if ($this->isEditing) {
            $post = Post::findOrFail($this->postId);
            $post->update([
                'user_id'=>auth()->id(),
                'title' => $this->title,
                'slug' => $slug,
                'content' => $this->content,
                'status' => $this->status,
                'meta_title' => $this->meta_title ?? $this->title,
                'meta_keywords' => $this->meta_keywords ?? '',
                'meta_description' => $this->meta_description ?? Str::limit(strip_tags($this->content), 160),
            ]);
        } else {
            $post = Post::create([
                'user_id'=>auth()->id(),
                'title' => $this->title,
                'slug' => $slug,
                'content' => $this->content,
                'status' => $this->status,
                'meta_title' => $this->meta_title ?? $this->title,
                'meta_keywords' => $this->meta_keywords ?? '',
                'meta_description' => $this->meta_description ?? Str::limit(strip_tags($this->content), 160),
            ]);
            $this->postId = $post->id;
            $this->isEditing = true;
        }

        // Sync categories et tags
        $post->categories()->sync($this->categories);
        $post->tags()->sync($this->tags);

        // Image
        if ($this->image) {
            $post->clearMediaCollection('posts');
            $filename = 'post-' . time() . '.' . $this->image->getClientOriginalExtension();
            $post->addMedia($this->image->getRealPath())->usingFileName($filename)->toMediaCollection('posts');
        }

        // Rafraîchir cache
        Cache::forget("post:{$post->slug}");
        Cache::forget('posts:latest');

        session()->flash('message', $this->isEditing ? 'Post mis à jour avec succès.' : 'Post créé avec succès.');
        return redirect()->route('post.index');
    }

    public function render()
    {
        return view('livewire.create-post');
    }
}


