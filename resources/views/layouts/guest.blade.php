@php $theme = request()->cookie(config('localization.theme_cookie'), 'light'); @endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-theme="{{ $theme }}">
    <head>
        @include('partials.head')
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-cream">
            <div>
                <a href="/" class="font-black text-2xl flex items-center gap-2 text-ink" style="font-family: Georgia, serif;">
                    GB <span class="font-black text-base">GAYA B<small class="block text-[8px] tracking-[.25em]">MARKETING</small></span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
