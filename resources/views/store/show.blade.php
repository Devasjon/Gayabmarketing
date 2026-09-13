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
  <form method="post" action="{{ route('checkout.store') }}">
   @csrf
   <input type="hidden" name="product_id" value="{{ $product->id }}">
   <input name="name" placeholder="{{ __('product.form.full_name') }}" required>
   <input type="email" name="email" placeholder="{{ __('product.form.email') }}" required>
   <input name="phone" placeholder="{{ __('product.form.phone') }}" required>
   <button>{{ __('product.form.pay') }}</button>
  </form>
  <a class="link" href="{{ route('home') }}#products">{{ __('product.back') }}</a>
 </div>
</section>
@endsection
