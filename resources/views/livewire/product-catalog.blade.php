<div>
    @if ($products->isEmpty() && $category === 'all')
        <x-empty-state :eyebrow="__('home.products.coming_soon_title')">
            <h3>{{ __('home.products.coming_soon_heading') }}</h3>
            <p>{{ __('home.products.coming_soon_lead') }}</p>
        </x-empty-state>
    @else
        <div class="filters" role="group" aria-label="Filter products by category">
            <button type="button" class="filter-btn {{ $category === 'all' ? 'is-active' : '' }}" wire:click="setCategory('all')">{{ __('home.products.filters.all') }}</button>
            @foreach ($categories as $cat)
                <button
                    type="button"
                    class="filter-btn {{ $category === $cat->slug ? 'is-active' : '' }}"
                    wire:click="setCategory('{{ $cat->slug }}')"
                >{{ $cat->name }}</button>
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
