<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class OrderManager extends Component
{
    use WithPagination;

    public function mount(): void
    {
        $this->authorize('viewAny', Order::class);
    }

    public function render(): View
    {
        return view('livewire.admin.order-manager', [
            'orders' => Order::with(['user', 'items', 'payment'])->latest()->paginate(20),
        ])->layout('layouts.authenticated');
    }
}
