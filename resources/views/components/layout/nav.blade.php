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
    @auth
        <a href="{{ route('dashboard') }}" class="ghost">{{ __('layout.controls.account') }}</a>
        <livewire:cart-count />
    @else
        <a href="{{ route('login') }}" class="ghost">{{ __('layout.controls.account') }}</a>
        <a href="{{ route('register') }}" class="ghost">{{ __('layout.controls.sign_up') }}</a>
    @endauth
</div>
