<?php

namespace App\Livewire;


use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Tag;
use Illuminate\Support\Str;

class TagManager extends Component
{
    use WithPagination;

    public $tagId;
    public $name;
    public $slug;
    public $isEditing = false;

    protected $rules = [
        'name' => 'required|min:2',
    ];

    public function render()
    {
        return view('livewire.tag-manager', [
            'tags' => Tag::latest()->paginate(10),
        ]);
    }

    public function resetForm()
    {
        $this->reset(['name', 'slug', 'tagId']);
        $this->isEditing = false;
    }

    public function create()
    {
        $this->resetForm();
        $this->dispatch('openModal');
    }

    public function store()
    {
        $this->validate();

        Tag::create([
            'name' => $this->name,
            'slug' => Str::slug($this->name),
        ]);

        session()->flash('message', 'Tag créé avec succès.');
        $this->dispatch('closeModal');
        $this->resetForm();
    }

    public function edit($id)
    {
        $tag = Tag::findOrFail($id);

        $this->tagId = $tag->id;
        $this->name = $tag->name;

        $this->isEditing = true;

        $this->dispatch('openModal');
    }

    public function update()
    {
        $this->validate();
        $tag = Tag::findOrFail($this->tagId);

        $tag->update([
            'name' => $this->name,
            'slug' => Str::slug($this->name),
        ]);

        session()->flash('message', 'Tag mis à jour.');
        $this->dispatch('closeModal');
        $this->resetForm();
    }

    public function delete($id)
    {
        Tag::findOrFail($id)->delete();
        session()->flash('message', 'Tag supprimé.');
    }
}
