@extends('layouts.app')
@section('content')
<section class="hero">
 <div>
  <p class="eyebrow">{{ __('home.hero.eyebrow') }}</p>
  <h1>{{ __('home.hero.title_line') }}<br><em>{{ __('home.hero.title_emphasis') }}</em></h1>
  <p>{{ __('home.hero.lead') }}</p>
  <div class="hero-cta">
   <x-button href="#products">{{ __('home.hero.cta_primary') }}</x-button>
   <x-button href="#about" variant="link">{{ __('home.hero.cta_secondary') }}</x-button>
  </div>
  <ul class="trust-list">
   @foreach (__('home.hero.trust') as $item)
   <li>✓ {{ $item }}</li>
   @endforeach
  </ul>
 </div>
 <div class="hero-art"><span>WORLDS<br><em>WORTH</em><br>WRITING</span></div>
</section>

<section class="trust-bar">
 <p>{{ __('home.trust_bar.lead') }}</p>
 <div class="trust-chips">
  @foreach (__('home.trust_bar.chips') as $chip)
  <span>{{ $chip }}</span>
  @endforeach
 </div>
</section>

<section id="products" class="products">
 <p class="eyebrow">{{ __('home.products.eyebrow') }}</p>
 <h2>{{ __('home.products.title_prefix') }}<em>{{ __('home.products.title_emphasis') }}</em></h2>
 <p class="section-lead">{{ __('home.products.lead') }}</p>

 <livewire:product-catalog />
</section>

<section id="publishing" class="publishing">
 <p class="eyebrow">{{ __('home.publishing.eyebrow') }}</p>
 <h2>{{ __('home.publishing.title_prefix') }}<em>{{ __('home.publishing.title_emphasis') }}</em></h2>
 <p>{{ __('home.publishing.lead') }}</p>
 <ol class="steps">
  @foreach (__('home.publishing.steps') as $step)
  <li>{{ $step }}</li>
  @endforeach
 </ol>
 <x-button href="mailto:admin@gayabmarketing.com" variant="link">{{ __('home.publishing.link') }}</x-button>
</section>

<section id="free" class="free-resources">
 <p class="eyebrow">{{ __('home.free_resources.eyebrow') }}</p>
 <h2>{{ __('home.free_resources.title_prefix') }}<em>{{ __('home.free_resources.title_emphasis') }}</em></h2>
 <p>{{ __('home.free_resources.lead') }}</p>
 <x-button href="mailto:admin@gayabmarketing.com?subject=Free%20Resource%20-%20Gaya%20B">{{ __('home.free_resources.cta') }}</x-button>
 <ol class="steps">
  @foreach (__('home.free_resources.steps') as $step)
  <li>{{ $step }}</li>
  @endforeach
 </ol>
</section>

<section id="about" class="about">
 <p class="eyebrow">{{ __('home.about.eyebrow') }}</p>
 <h2>{{ __('home.about.title_prefix') }}<em>{{ __('home.about.title_emphasis') }}</em></h2>
 <p>{{ __('home.about.paragraph_one') }}</p>
 <p>{{ __('home.about.paragraph_two') }}</p>
 <div class="stats">
  @foreach (__('home.about.stats') as $stat)
  <div><b>{{ $stat['value'] }}</b><span>{{ $stat['label'] }}</span></div>
  @endforeach
 </div>
</section>

<section id="faq" class="faq">
 <p class="eyebrow">{{ __('home.faq.eyebrow') }}</p>
 <h2>{{ __('home.faq.title_prefix') }}<em>{{ __('home.faq.title_emphasis') }}</em></h2>
 <x-button href="mailto:admin@gayabmarketing.com" variant="link">{{ __('home.faq.link') }}</x-button>
 <div class="faq-list">
  @foreach (__('home.faq.items') as $index => $item)
  <article class="faq-item" x-data="{ open: {{ $index === 0 ? 'true' : 'false' }} }" :class="{ 'is-open': open }">
   <button type="button" @click="open = !open" :aria-expanded="open">{{ $item['question'] }}<span x-text="open ? '−' : '+'"></span></button>
   <p>{{ $item['answer'] }}</p>
  </article>
  @endforeach
 </div>
</section>

<section class="newsletter">
 <p class="eyebrow">{{ __('home.newsletter.eyebrow') }}</p>
 <h2>{{ __('home.newsletter.title_prefix') }}<em>{{ __('home.newsletter.title_emphasis') }}</em></h2>
 <form id="newsletter-form">
  <input type="email" required placeholder="{{ __('home.newsletter.placeholder') }}">
  <button type="submit" data-thank-you="{{ __('home.newsletter.thank_you') }}">{{ __('home.newsletter.submit') }}</button>
 </form>
 <p class="newsletter-note">{{ __('home.newsletter.note') }}</p>
</section>
@endsection
