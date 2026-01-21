<?php

namespace App\Livewire;


use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Support\Facades\Cache;
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



    public function render()
    {
        return view('livewire.post-manager', [
            'posts' => Post::with(['categories', 'tags'])->latest()->paginate(10),
            'availableCategories' => Category::all(),
            'availableTags' => Tag::all(),
        ]);
    }

    public function create()
    {
        $this->isEditing = false;
        $this->postId = null;
       return $this->redirect('create_edit');
    }


    public function edit($id)
    {
        return redirect()->route('post.edit', ['id' => $id]);
    }

    public function delete($id)
    {
        $post = Post::findOrFail($id);
        $post->clearMediaCollection('image');
        $post->delete();

        session()->flash('message', 'Post supprimé.');
    }
}
