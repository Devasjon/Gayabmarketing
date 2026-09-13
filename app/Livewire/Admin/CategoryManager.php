<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;

class CategoryManager extends Component
{
    public ?int $editingId = null;

    #[Validate('required|string|max:120')]
    public string $name = '';

    public function mount(): void
    {
        $this->authorize('viewAny', Category::class);
    }

    public function edit(Category $category): void
    {
        $this->authorize('update', $category);

        $this->editingId = $category->id;
        $this->name = $category->name;
    }

    public function cancel(): void
    {
        $this->reset(['editingId', 'name']);
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editingId) {
            $category = Category::findOrFail($this->editingId);
            $this->authorize('update', $category);
            $category->update(['name' => $this->name, 'slug' => Str::slug($this->name)]);
        } else {
            $this->authorize('create', Category::class);
            Category::create(['name' => $this->name, 'slug' => Str::slug($this->name)]);
        }

        session()->flash('status', __('admin.categories.saved'));
        $this->reset(['editingId', 'name']);
    }

    public function delete(Category $category): void
    {
        $this->authorize('delete', $category);

        if ($category->products()->exists()) {
            session()->flash('error', __('admin.categories.delete_blocked'));

            return;
        }

        $category->delete();
        session()->flash('status', __('admin.categories.deleted'));
    }

    public function render(): View
    {
        return view('livewire.admin.category-manager', [
            'categories' => Category::withCount('products')->orderBy('name')->get(),
        ])->layout('layouts.authenticated');
    }
}
