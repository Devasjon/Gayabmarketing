<nav>
    <a href="{{ route('home') }}#products">{{ __('layout.nav.products') }}</a>
    <a href="{{ route('home') }}#publishing">{{ __('layout.nav.publishing') }}</a>
    <a href="{{ route('home') }}#free">{{ __('layout.nav.free_resources') }}</a>
    <a href="{{ route('home') }}#about">{{ __('layout.nav.about') }}</a>
    <a href="{{ route('home') }}#faq">{{ __('layout.nav.faq') }}</a>
</nav>
<div class="controls">
    <div class="lang-switch" role="group" aria-label="{{ __('layout.controls.change_language') }}">
        @foreach (config('localization.supported') as $code => $label)
            <a href="{{ route('locale.update', $code) }}"
               class="{{ app()->getLocale() === $code ? 'is-active' : '' }}"
               aria-label="{{ $label }}">{{ strtoupper($code) }}</a>
        @endforeach
    </div>
    <button id="theme" type="button" aria-label="{{ __('layout.controls.change_theme') }}">{{ ($theme ?? 'light') === 'dark' ? '☀' : '☾' }}</button>
    <button class="ghost" type="button" disabled title="{{ __('messages.coming_soon') }}">{{ __('layout.controls.account') }}</button>
    <button class="ghost" type="button" disabled title="{{ __('messages.coming_soon') }}">{{ __('layout.controls.cart', ['count' => 0]) }}</button>
</div>
