@extends('layouts.app')
@section('title', __('cart.invoice.title').' '.$order->invoice->invoice_number)
@section('content')
<section class="legal">
 <h1>{{ __('cart.invoice.title') }}</h1>
 <dl>
  <dt>{{ __('cart.invoice.number') }}</dt><dd>{{ $order->invoice->invoice_number }}</dd>
  <dt>{{ __('cart.invoice.date') }}</dt><dd>{{ $order->invoice->issued_at->format('d M Y') }}</dd>
  <dt>{{ __('product.receipt.merchant') }}</dt><dd>GAYA BORNEO ENTERPRISE</dd>
  <dt>{{ __('product.receipt.registration') }}</dt><dd>202603150299 (KT0615457-D)</dd>
 </dl>
 <table style="width:100%;border-collapse:collapse;margin-top:20px">
  <thead>
   <tr style="text-align:left;border-bottom:2px solid var(--ink,#171813)">
    <th style="padding:8px 0">{{ __('cart.invoice.item') }}</th>
    <th style="padding:8px 0">{{ __('cart.invoice.quantity') }}</th>
    <th style="padding:8px 0">{{ __('cart.invoice.unit_price') }}</th>
    <th style="padding:8px 0">{{ __('cart.invoice.line_total') }}</th>
   </tr>
  </thead>
  <tbody>
   @foreach ($order->items as $item)
   <tr style="border-bottom:1px solid #d8d2c6">
    <td style="padding:8px 0">{{ $item->product_name }}</td>
    <td style="padding:8px 0">{{ $item->quantity }}</td>
    <td style="padding:8px 0">RM{{ number_format($item->unit_price_cents/100,2) }}</td>
    <td style="padding:8px 0">RM{{ number_format($item->line_total_cents/100,2) }}</td>
   </tr>
   @endforeach
  </tbody>
 </table>
 <p style="text-align:right;font-weight:800;margin-top:12px">{{ __('cart.checkout.total') }}: RM{{ number_format($order->total_cents/100,2) }}</p>
 <a class="link" href="{{ route('library.index') }}">← {{ __('cart.library.title') }}</a>
</section>
@endsection
