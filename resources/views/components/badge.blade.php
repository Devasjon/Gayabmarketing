@props(['class' => 'badge'])
<p {{ $attributes->merge(['class' => $class]) }}>{{ $slot }}</p>
