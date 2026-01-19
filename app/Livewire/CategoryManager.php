<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryManager extends Component
{
    use WithPagination;

    public $categoryId;
    public $name;
    public $slug;
    public $isEditing = false;

    protected $rules = [
        'name' => 'required|min:2',
    ];

    public function render()
    {
        return view('livewire.category-manager', [
            'categories' => Category::latest()->paginate(10),
        ]);
    }

    public function resetForm()
    {
        $this->reset(['name', 'slug', 'categoryId']);
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

        Category::create([
            'name' => $this->name,
            'slug' => Str::slug($this->name),
        ]);

        session()->flash('message', 'Catégorie créée avec succès.');
        $this->dispatch('closeModal');
        $this->resetForm();
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);

        $this->categoryId = $category->id;
        $this->name = $category->name;

        $this->isEditing = true;

        $this->dispatch('openModal');
    }

    public function update()
    {
        $this->validate();
        $category = Category::findOrFail($this->categoryId);

        $category->update([
            'name' => $this->name,
            'slug' => Str::slug($this->name),
        ]);

        session()->flash('message', 'Catégorie mise à jour.');
        $this->dispatch('closeModal');
        $this->resetForm();
    }

    public function delete($id)
    {
        Category::findOrFail($id)->delete();
        session()->flash('message', 'Catégorie supprimée.');
    }
}
