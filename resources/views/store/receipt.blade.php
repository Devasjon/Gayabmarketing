@extends('layouts.app')
@section('title','Order '.$order->reference)
@section('content')
<section class="receipt">
 <p class="eyebrow">{{ __('product.receipt.eyebrow') }}</p>
 <h1>{{ $order->status === \App\Enums\OrderStatus::Paid ? __('product.receipt.paid_heading') : __('product.receipt.pending_heading') }}</h1>
 <p>{{ __('product.receipt.order') }} <b>{{ $order->reference }}</b></p>
 <dl>
  <dt>{{ __('product.receipt.merchant') }}</dt><dd>GAYA BORNEO ENTERPRISE</dd>
  <dt>{{ __('product.receipt.registration') }}</dt><dd>202603150299 (KT0615457-D)</dd>
  <dt>{{ __('product.receipt.amount') }}</dt><dd>RM{{ number_format($order->total_cents/100,2) }}</dd>
 </dl>
 <ul class="trust-list">
  @foreach ($order->items as $item)
   <li>{{ $item->product_name }} × {{ $item->quantity }}</li>
  @endforeach
 </ul>
 @if ($order->status === \App\Enums\OrderStatus::Paid)
  <p>{{ __('product.receipt.note', ['email' => $order->user->email]) }}</p>
  <a class="link" href="{{ route('library.index') }}">{{ __('cart.library.title') }} →</a>
 @endif
</section>
@endsection
