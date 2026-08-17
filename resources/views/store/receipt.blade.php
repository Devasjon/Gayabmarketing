@extends('layouts.app')
@section('title','Order '.$order->reference)
@section('content')<section class="receipt"><p class="eyebrow">PAYMENT STATUS</p><h1>{{ $order->status==='paid'?'Thank you!':'Payment pending' }}</h1><p>Order <b>{{ $order->reference }}</b></p><dl><dt>Merchant</dt><dd>GAYA BORNEO ENTERPRISE</dd><dt>Registration</dt><dd>202603150299 (KT0615457-D)</dd><dt>Product</dt><dd>{{ $order->product->name_en }}</dd><dt>Amount</dt><dd>RM{{ number_format($order->amount_cents/100,2) }}</dd></dl><p>A receipt and download link will be sent to {{ $order->customer_email }} after payment confirmation.</p></section>@endsection

