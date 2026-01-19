<?php
namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Post;

class PostPage extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // QueryString = sauvegarde dans l’URL
    public $search_category;
    public $search_id;
    public $search_title;
    public $search_content;
    public $search_global;
    public $order_column = 'created_at';
    public $order_direction = 'desc';

    protected $updatesQueryString = [
        'search_category',
        'search_id',
        'search_title',
        'search_content',
        'search_global',
        'order_column',
        'order_direction'
    ];

    public function updating($property)
    {
        // reset pagination quand on filtre
        $this->resetPage();
    }

    public function sortBy($column)
    {
        if ($this->order_column === $column) {
            $this->order_direction = $this->order_direction === 'asc' ? 'desc' : 'asc';
        } else {
            $this->order_column = $column;
            $this->order_direction = 'asc';
        }
    }

    public function render()
    {
        $posts = Post::with('media')
            ->whereHas('categories', function ($query) {
                if ($this->search_category) {
                    $categories = explode(",", $this->search_category);
                    $query->whereIn('id', $categories);
                }
            })
            ->when($this->search_id, fn($q) =>
            $q->where('id', $this->search_id)
            )
            ->when($this->search_title, fn($q) =>
            $q->where('title', 'like', '%' . $this->search_title . '%')
            )
            ->when($this->search_content, fn($q) =>
            $q->where('content', 'like', '%' . $this->search_content . '%')
            )
            ->when($this->search_global, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('id', $this->search_global)
                        ->orWhere('title', 'like', '%' . $this->search_global . '%')
                        ->orWhere('content', 'like', '%' . $this->search_global . '%');
                });
            })
            ->orderBy($this->order_column, $this->order_direction)
            ->paginate(50);

        return view('livewire.post-page', [
            'items' => $posts
        ]);
    }
}

