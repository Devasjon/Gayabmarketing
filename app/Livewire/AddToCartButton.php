<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class AddToCartButton extends Component
{
    public Product $product;

    public bool $added = false;

    public function add(): void
    {
        $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);

        $item = $cart->items()->firstOrNew(['product_id' => $this->product->id]);
        $item->quantity = ($item->quantity ?? 0) + 1;
        $item->save();

        $this->added = true;
        $this->dispatch('cart-updated');
    }

    public function render(): View
    {
        return view('livewire.add-to-cart-button');
    }
}
