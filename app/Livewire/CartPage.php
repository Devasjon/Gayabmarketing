<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CartPage extends Component
{
    public function updateQuantity(int $itemId, int $quantity): void
    {
        $item = $this->ownedItem($itemId);

        if ($quantity < 1) {
            $item->delete();

            return;
        }

        $item->update(['quantity' => min($quantity, 99)]);
    }

    public function remove(int $itemId): void
    {
        $this->ownedItem($itemId)->delete();
    }

    private function ownedItem(int $itemId): CartItem
    {
        return CartItem::whereHas('cart', fn ($q) => $q->where('user_id', auth()->id()))
            ->findOrFail($itemId);
    }

    public function render(): View
    {
        $cart = Cart::with(['items.product.translations', 'items.product.category'])
            ->where('user_id', auth()->id())
            ->first();

        return view('livewire.cart-page', [
            'cart' => $cart,
            'items' => $cart?->items ?? collect(),
            'subtotalCents' => $cart?->subtotalCents() ?? 0,
        ])->layout('layouts.authenticated');
    }
}
