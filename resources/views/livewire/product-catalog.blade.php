<div>
    @if ($products->isEmpty() && $category === 'all')
        <x-empty-state :eyebrow="__('home.products.coming_soon_title')">
            <h3>{{ __('home.products.coming_soon_heading') }}</h3>
            <p>{{ __('home.products.coming_soon_lead') }}</p>
        </x-empty-state>
    @else
        <div class="filters" role="group" aria-label="Filter products by category">
            @foreach ($this->categories() as $key)
                <button
                    type="button"
                    class="filter-btn {{ $category === $key ? 'is-active' : '' }}"
                    wire:click="setCategory('{{ $key }}')"
                >{{ __('home.products.filters.'.$key) }}</button>
            @endforeach
        </div>
        <div class="grid" wire:loading.class="is-loading">
            @foreach ($products as $product)
                <x-product-card :product="$product" wire:key="product-{{ $product->id }}" />
            @endforeach
        </div>
        <a class="view-all" href="{{ route('home') }}#products">{{ __('home.products.view_all') }}</a>
    @endif
</div>
