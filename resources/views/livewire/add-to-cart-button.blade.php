<div>
    @auth
        @if ($added)
            <a href="{{ route('cart.index') }}" class="btn">{{ __('cart.view_cart') }}</a>
        @else
            <button type="button" wire:click="add" class="btn">{{ __('cart.add_to_cart') }}</button>
        @endif
    @else
        <a href="{{ route('login') }}" class="btn">{{ __('cart.login_to_buy') }}</a>
    @endauth
</div>
