<div class="foot-grid">
    <div class="foot-brand">
        <a class="brand" href="{{ route('home') }}">GB <span>GAYA B<small>MARKETING</small></span></a>
        <p>{{ __('layout.footer.tagline') }}</p>
    </div>
    <div>
        <b>{{ __('layout.footer.explore') }}</b>
        <a href="{{ route('home') }}#products">{{ __('layout.nav.products') }}</a>
        <a href="{{ route('home') }}#publishing">{{ __('layout.nav.publishing') }}</a>
        <a href="{{ route('home') }}#free">{{ __('layout.nav.free_resources') }}</a>
    </div>
    <div>
        <b>{{ __('layout.footer.help') }}</b>
        <a href="{{ route('home') }}#faq">{{ __('layout.nav.faq') }}</a>
        <a href="mailto:admin@gayabmarketing.com">{{ __('layout.footer.contact') }}</a>
        <a href="{{ route('refund') }}">{{ __('layout.footer.refund_policy') }}</a>
    </div>
    <div>
        <b>{{ __('layout.footer.contact') }}</b>
        <a href="mailto:admin@gayabmarketing.com">admin@gayabmarketing.com</a>
        <span>{{ __('layout.footer.location') }}</span>
    </div>
</div>
<p class="legal-line">GAYA BORNEO ENTERPRISE · 202603150299 (KT0615457-D)</p>
<div class="foot-bottom">
    <small>{{ __('layout.footer.copyright', ['year' => date('Y')]) }}</small>
    <span>
        <a href="{{ route('privacy') }}">{{ __('layout.footer.privacy') }}</a>
        <a href="{{ route('terms') }}">{{ __('layout.footer.terms') }}</a>
        <a href="{{ route('license') }}">{{ __('layout.footer.license') }}</a>
    </span>
</div>
