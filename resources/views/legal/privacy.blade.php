@extends('layouts.app')
@section('title', __('legal.privacy.title').' | Gaya B Marketing')
@section('content')
<x-legal-page :title="__('legal.privacy.title')">
 <p>{{ __('legal.privacy.body') }}</p>
</x-legal-page>
@endsection
