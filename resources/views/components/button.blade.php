@props(['href' => null, 'variant' => 'primary'])
@php $class = $variant === 'link' ? 'link' : 'btn'; @endphp
@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $class]) }}>{{ $slot }}</a>
@else
    <button {{ $attributes->merge(['class' => $class, 'type' => 'submit']) }}>{{ $slot }}</button>
@endif
