@php $theme = request()->cookie(config('localization.theme_cookie'), 'light'); @endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-theme="{{ $theme }}">
<head>
@include('partials.head')
</head>
<body class="font-sans antialiased bg-gray-100">
<div class="min-h-screen">
    @include('layouts.navigation')

    @if (isset($header))
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endif

    <main>
        {{ $slot }}
    </main>
</div>
@livewireScripts
</body>
</html>
