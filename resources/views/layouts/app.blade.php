@php $theme = request()->cookie(config('localization.theme_cookie'), 'light'); @endphp
<!doctype html>
<html lang="{{ app()->getLocale() }}" data-theme="{{ $theme }}">
<head>
@include('partials.head')
</head>
<body>
<header>
    <a class="brand" href="{{ route('home') }}">GB <span>GAYA B<small>MARKETING</small></span></a>
    <x-layout.nav :theme="$theme" />
</header>
<main>@yield('content')</main>
<footer>
    <x-layout.footer />
</footer>
@livewireScripts
</body>
</html>
