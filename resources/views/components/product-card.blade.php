@props(['product'])
@php
    $kicker = __('home.products.kickers')[$product->slug] ?? ['sym' => '✦', 'label' => strtoupper($product->category)];
@endphp
<article>
    <div class="cover">
        <span>{{ $kicker['sym'] }}</span>
        <strong>{{ $product->localizedName() }}</strong>
    </div>
    <x-badge class="kicker">{{ $kicker['label'] }}</x-badge>
    <x-badge>{{ strtoupper($product->category) }}</x-badge>
    <h3>{{ $product->localizedName() }}</h3>
    <p>{{ $product->localizedDescription() }}</p>
    <b>RM{{ number_format($product->price_cents / 100, 2) }}</b>
    <a href="{{ route('products.show', $product) }}">{{ __('home.products.view') }}</a>
</article>
