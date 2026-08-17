@extends('layouts.app')
@section('title',$product->name_en.' | Gaya B Marketing')
@section('content')
<section class="product-detail">
 <div class="cover large"><span>{{ strtoupper($product->category) }}</span><strong>{{ $product->name_en }}</strong></div>
 <div>
  <p class="eyebrow">{{ strtoupper($product->category) }}</p>
  <h1 data-en="{{ $product->name_en }}" data-bm="{{ $product->name_bm }}">{{ $product->name_en }}</h1>
  <p data-en="{{ $product->description_en }}" data-bm="{{ $product->description_bm }}">{{ $product->description_en }}</p>
  <h2>RM{{ number_format($product->price_cents/100,2) }}</h2>
  <ul class="trust-list">
   <li data-en="✓ Instant download" data-bm="✓ Muat turun segera">✓ Instant download</li>
   <li data-en="✓ Secure payment" data-bm="✓ Pembayaran selamat">✓ Secure payment</li>
  </ul>
  <form method="post" action="{{ route('checkout.store') }}">
   @csrf
   <input type="hidden" name="product_id" value="{{ $product->id }}">
   <input name="name" data-en-placeholder="Full name" data-bm-placeholder="Nama penuh" placeholder="Full name" required>
   <input type="email" name="email" data-en-placeholder="Email address" data-bm-placeholder="Alamat e-mel" placeholder="Email address" required>
   <input name="phone" placeholder="+60 12-345 6789" required>
   <button data-en="Pay securely with Billplz →" data-bm="Bayar dengan selamat melalui Billplz →">Pay securely with Billplz →</button>
  </form>
  <a class="link" href="{{ route('home') }}#products" data-en="← Back to all products" data-bm="← Kembali ke semua produk">← Back to all products</a>
 </div>
</section>
@endsection
