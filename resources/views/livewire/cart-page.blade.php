<div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">{{ __('cart.cart.title') }}</h1>

    @if ($items->isEmpty())
        <p class="text-gray-600">{{ __('cart.cart.empty') }}</p>
        <a href="{{ route('home') }}#products" class="text-brand-600 font-semibold">{{ __('cart.cart.continue_shopping') }}</a>
    @else
        <div class="bg-white shadow rounded-lg divide-y">
            @foreach ($items as $item)
                <div class="p-4 flex items-center justify-between gap-4" wire:key="cart-item-{{ $item->id }}">
                    <div>
                        <p class="font-semibold text-gray-900">{{ $item->product->localizedName() }}</p>
                        <p class="text-sm text-gray-500">RM{{ number_format($item->product->price_cents / 100, 2) }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <input
                            type="number" min="1" max="99" value="{{ $item->quantity }}"
                            wire:change="updateQuantity({{ $item->id }}, $event.target.value)"
                            class="w-16 rounded-md border-gray-300 shadow-sm text-center"
                        >
                        <button wire:click="remove({{ $item->id }})" class="text-red-600 text-sm font-semibold">{{ __('cart.cart.remove') }}</button>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex items-center justify-between bg-white shadow rounded-lg p-6">
            <div>
                <p class="text-sm text-gray-500">{{ __('cart.cart.subtotal') }}</p>
                <p class="text-2xl font-bold text-ink">RM{{ number_format($subtotalCents / 100, 2) }}</p>
            </div>
            <a href="{{ route('checkout.index') }}" class="bg-brand-600 hover:bg-brand-700 text-white px-5 py-3 rounded font-semibold">{{ __('cart.cart.checkout') }}</a>
        </div>
    @endif
</div>
