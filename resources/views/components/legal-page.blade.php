@props(['title'])
<section class="legal">
    <h1>{{ $title }}</h1>
    <p class="draft-notice">{{ __('legal.draft_notice') }}</p>
    {{ $slot }}
    <a class="link" href="{{ route('home') }}">{{ __('legal.back') }}</a>
</section>
