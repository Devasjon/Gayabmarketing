<?php

namespace App\Livewire;

use App\Models\Cart;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class CartCount extends Component
{
    public int $count = 0;

    public function mount(): void
    {
        $this->count = $this->currentCount();
    }

    #[On('cart-updated')]
    public function refreshCount(): void
    {
        $this->count = $this->currentCount();
    }

    private function currentCount(): int
    {
        if (! auth()->check()) {
            return 0;
        }

        return (int) (Cart::where('user_id', auth()->id())->first()?->items()->sum('quantity') ?? 0);
    }

    public function render(): View
    {
        return view('livewire.cart-count');
    }
}
