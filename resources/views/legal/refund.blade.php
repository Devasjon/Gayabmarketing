@extends('layouts.app')
@section('title', __('legal.refund.title').' | Gaya B Marketing')
@section('content')
<x-legal-page :title="__('legal.refund.title')">
 <p>{{ __('legal.refund.body') }}</p>
</x-legal-page>
@endsection
