@extends('layouts.app')
@section('title','Order '.$order->reference)
@section('content')
<section class="receipt">
 <p class="eyebrow">{{ __('product.receipt.eyebrow') }}</p>
 <h1>{{ $order->status === 'paid' ? __('product.receipt.paid_heading') : __('product.receipt.pending_heading') }}</h1>
 <p>{{ __('product.receipt.order') }} <b>{{ $order->reference }}</b></p>
 <dl>
  <dt>{{ __('product.receipt.merchant') }}</dt><dd>GAYA BORNEO ENTERPRISE</dd>
  <dt>{{ __('product.receipt.registration') }}</dt><dd>202603150299 (KT0615457-D)</dd>
  <dt>{{ __('product.receipt.product') }}</dt><dd>{{ $order->product->localizedName() }}</dd>
  <dt>{{ __('product.receipt.amount') }}</dt><dd>RM{{ number_format($order->amount_cents/100,2) }}</dd>
 </dl>
 <p>{{ __('product.receipt.note', ['email' => $order->customer_email]) }}</p>
</section>
@endsection
