<div class="max-w-2xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">{{ __('cart.checkout.title') }}</h1>

    @if ($items->isEmpty())
        <p class="text-gray-600">{{ __('cart.checkout.empty_redirect') }}</p>
        <a href="{{ route('home') }}#products" class="text-brand-600 font-semibold">{{ __('cart.cart.continue_shopping') }}</a>
    @else
        <div class="bg-white shadow rounded-lg p-6 space-y-3">
            <h2 class="font-semibold">{{ __('cart.checkout.order_summary') }}</h2>
            @foreach ($items as $item)
                <div class="flex justify-between text-sm">
                    <span>{{ $item->product->localizedName() }} × {{ $item->quantity }}</span>
                    <span>RM{{ number_format($item->quantity * $item->product->price_cents / 100, 2) }}</span>
                </div>
            @endforeach
            <div class="flex justify-between font-bold border-t pt-3">
                <span>{{ __('cart.checkout.total') }}</span>
                <span>RM{{ number_format($totalCents / 100, 2) }}</span>
            </div>
        </div>

        @if (config('services.billplz.checkout_enabled'))
            <form wire:submit="pay" class="bg-white shadow rounded-lg p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('cart.checkout.phone') }}</label>
                    <input type="text" wire:model="phone" placeholder="+60 12-345 6789" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    @error('phone') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white px-5 py-3 rounded font-semibold w-full">{{ __('cart.checkout.pay') }}</button>
            </form>
        @else
            <div class="bg-white shadow rounded-lg p-6">
                <p class="text-gray-600">{{ __('cart.checkout.not_open') }}</p>
            </div>
        @endif
    @endif
</div>
