<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

class ProductCatalog extends Component
{
    public string $category = 'all';

    public function setCategory(string $category): void
    {
        $this->category = $category;
    }

    public function categories(): Collection
    {
        return Category::query()->orderBy('name')->get();
    }

    public function products(): Collection
    {
        return Product::published()
            ->with(['category', 'translations'])
            ->latest()
            ->when($this->category !== 'all', fn ($query) => $query->whereHas('category', fn ($q) => $q->where('slug', $this->category)))
            ->get();
    }

    public function render(): View
    {
        return view('livewire.product-catalog', [
            'products' => $this->products(),
            'categories' => $this->categories(),
        ]);
    }
}
