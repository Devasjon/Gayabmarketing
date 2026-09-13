@extends('layouts.app')
@section('title', __('legal.terms.title').' | Gaya B Marketing')
@section('content')
<x-legal-page :title="__('legal.terms.title')">
 <p>{{ __('legal.terms.body') }}</p>
</x-legal-page>
@endsection
