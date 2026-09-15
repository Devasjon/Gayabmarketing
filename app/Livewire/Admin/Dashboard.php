<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Dashboard extends Component
{
    public function render(): View
    {
        return view('livewire.admin.dashboard', [
            'totalProducts' => Product::count(),
            'publishedProducts' => Product::published()->count(),
            'totalCategories' => Category::count(),
        ])->layout('layouts.authenticated');
    }
}
