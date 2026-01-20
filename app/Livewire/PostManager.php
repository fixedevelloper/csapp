<?php

namespace App\Livewire;


use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class PostManager extends Component
{
    use WithPagination, WithFileUploads;

    public $postId;
    public $title, $content, $category_id, $status = 'draft';
    public $tags = [];
    public $categories = [];
    public $image;
    public $isEditing = false;

    protected $rules = [
        'title' => 'required|min:3',
        'content' => 'required|min:10',
        'categories' => 'array',
        'tags' => 'array',
        'image' => 'nullable|image|max:2048'
    ];

    public function render()
    {
        return view('livewire.post-manager', [
            'posts' => Post::with(['categories', 'tags'])->latest()->paginate(10),
            'availableCategories' => Category::all(),
            'availableTags' => Tag::all(),
        ]);
    }

    public function resetForm()
    {
        $this->reset(['title', 'content', 'categories', 'status', 'tags', 'image']);
        $this->isEditing = false;
        $this->postId = null;
    }

    public function create()
    {
        $this->resetForm();
        $this->dispatch('openModal');
    }

    public function store()
    {
        $this->validate();

        $post = Post::create([
            'title' => $this->title,
            'content' => $this->content,
            'status' => $this->status,
        ]);

        $post->tags()->sync($this->tags);
        $post->categories()->sync($this->categories);

        if ($this->image) {
            $post->addMedia($this->image->getRealPath())
                ->usingFileName('post-'.time().'.jpg')
                ->toMediaCollection('posts');
        }

        $this->resetForm();
        $this->dispatch('closeModal');
        session()->flash('message', 'Post créé avec succès.');
    }

    public function edit($id)
    {
        $post = Post::with('tags')->findOrFail($id);
        $this->postId = $post->id;
        $this->title = $post->title;
        $this->content = $post->content;
        $this->categories = $post->categories->pluck('id')->toArray();
        $this->status = $post->status;
        $this->tags = $post->tags->pluck('id')->toArray();
        $this->isEditing = true;
        $this->dispatch('openModal');
    }

    public function update()
    {
        $this->validate();

        $post = Post::findOrFail($this->postId);
        $post->update([
            'title' => $this->title,
            'content' => $this->content,
            'status' => $this->status,
        ]);

        $post->tags()->sync($this->tags);
        $post->categories()->sync($this->categories);
        if ($this->image) {
            $post->clearMediaCollection('posts');
            $post->addMedia($this->image->getRealPath())
                ->usingFileName('post-'.time().'.jpg')
                ->toMediaCollection('posts');
        }

        $this->resetForm();
        $this->dispatch('closeModal');
        session()->flash('message', 'Post mis à jour.');
    }

    public function delete($id)
    {
        $post = Post::findOrFail($id);
        $post->clearMediaCollection('image');
        $post->delete();

        session()->flash('message', 'Post supprimé.');
    }
}
