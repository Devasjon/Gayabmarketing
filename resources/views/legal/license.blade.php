@extends('layouts.app')
@section('title', __('legal.license.title').' | Gaya B Marketing')
@section('content')
<x-legal-page :title="__('legal.license.title')">
 <p>{{ __('legal.license.body') }}</p>
</x-legal-page>
@endsection
