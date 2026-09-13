@php $theme = request()->cookie(config('localization.theme_cookie'), 'light'); @endphp
<!doctype html>
<html lang="{{ app()->getLocale() }}" data-theme="{{ $theme }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title','Gaya B Marketing')</title>
<meta name="description" content="Premium digital products and publishing from Borneo, Malaysia.">
<meta name="theme-color" content="#f05a24">
<link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
<link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">
<link rel="icon" href="{{ asset('icons/icon-192.png') }}">
<script>
(function(){
    var stored = document.cookie.replace(/(?:(?:^|.*;\s*)gbm_theme\s*=\s*([^;]*).*$)|^.*$/, '$1');
    if (!stored) {
        var preferred = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        document.documentElement.dataset.theme = preferred;
        document.cookie = 'gbm_theme=' + preferred + ';path=/;max-age=31536000;samesite=lax';
    }
})();
</script>
@vite(['resources/css/app.css','resources/js/app.js'])
@livewireStyles
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
