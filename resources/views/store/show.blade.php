@extends('layouts.app')
@section('title',$product->localizedName().' | Gaya B Marketing')
@section('content')
<section class="product-detail">
 <div class="cover large"><span>{{ strtoupper($product->category?->name ?? '') }}</span><strong>{{ $product->localizedName() }}</strong></div>
 <div>
  <p class="eyebrow">{{ strtoupper($product->category?->name ?? '') }}</p>
  <h1>{{ $product->localizedName() }}</h1>
  <p>{{ $product->localizedDescription() }}</p>
  <h2>RM{{ number_format($product->price_cents/100,2) }}</h2>
  <ul class="trust-list">
   @foreach (__('product.trust') as $item)
   <li>✓ {{ $item }}</li>
   @endforeach
  </ul>
  <livewire:add-to-cart-button :product="$product" />
  <a class="link" href="{{ route('home') }}#products">{{ __('product.back') }}</a>
 </div>
</section>
@endsection
