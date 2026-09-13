<?php

namespace App\Livewire;

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

    public function categories(): array
    {
        return ['all', 'Ebook', 'Workbook', 'Template'];
    }

    public function products(): Collection
    {
        return Product::published()
            ->latest()
            ->get()
            ->when($this->category !== 'all', fn (Collection $products) => $products->where('category', $this->category));
    }

    public function render(): View
    {
        return view('livewire.product-catalog', [
            'products' => $this->products(),
        ]);
    }
}
