<?php

namespace App\Livewire;

use App\Models\Comment;
use Livewire\Component;
use Livewire\WithPagination;

class CommentManager extends Component
{
    use WithPagination;

    // pending | approved | all
    public $filter = 'pending';
    public $search = '';

    public function updatingFilter()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.comment-manager', [
            'comments' => Comment::with('post:id,title,slug')
                ->when($this->filter === 'pending', fn ($q) => $q->where('approved', false))
                ->when($this->filter === 'approved', fn ($q) => $q->where('approved', true))
                ->when($this->search, fn ($q) => $q->where(fn ($q) => $q
                    ->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('comment', 'like', "%{$this->search}%")))
                ->latest()
                ->paginate(20),
            'pendingCount' => Comment::where('approved', false)->count(),
        ]);
    }

    public function approve($id)
    {
        Comment::findOrFail($id)->update(['approved' => true]);
        session()->flash('message', 'Commentaire publié.');
    }

    // Retire un commentaire du site sans le supprimer
    public function unapprove($id)
    {
        Comment::findOrFail($id)->update(['approved' => false]);
        session()->flash('message', 'Commentaire retiré du site.');
    }

    public function delete($id)
    {
        Comment::findOrFail($id)->delete();
        session()->flash('message', 'Commentaire supprimé.');
    }
}
