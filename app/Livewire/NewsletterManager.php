<?php

namespace App\Livewire;

use App\Models\NewsletterSubscriber;
use Livewire\Component;
use Livewire\WithPagination;

class NewsletterManager extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.newsletter-manager', [
            'subscribers' => NewsletterSubscriber::query()
                ->when($this->search, fn ($q) => $q->where('email', 'like', "%{$this->search}%"))
                ->latest()
                ->paginate(20),
            'total' => NewsletterSubscriber::count(),
        ]);
    }

    // Désinscription sur demande (droit à l'effacement RGPD)
    public function delete($id)
    {
        NewsletterSubscriber::findOrFail($id)->delete();
        session()->flash('message', 'Inscrit supprimé.');
    }
}
