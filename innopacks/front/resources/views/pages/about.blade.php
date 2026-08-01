@extends('layouts.app')

@section('body-class', 'page-about')
@section('title', $page->translation?->meta_title ?: __('front::common.about_label'))

@php
  $products = \InnoCMS\Common\Models\Product::query()
      ->with(['translations', 'categories'])
      ->where('active', true)
      ->whereHas('categories', fn($q) => $q->where('slug', 'software-products'))
      ->orderBy('position')
      ->get();
@endphp

@section('content')
  {{-- Hero --}}
  <section class="about-hero">
    <div class="container">
      <span class="about-eyebrow" data-aos="fade-up">{{ __('front::common.about_label') }}</span>
      <h1 class="about-hero-reveal" data-aos="fade-up" data-aos-duration="300">
        <span class="about-char">
          <span class="char char--blue">{{ __('front::common.about_char_fan') }}</span>
          <span class="char-mean"><i class="bi bi-wind"></i>{{ __('front::common.about_char_fan_mean') }}</span>
        </span>
        <span class="about-char">
          <span class="char char--accent">{{ __('front::common.about_char_lian') }}</span>
          <span class="char-mean"><i class="bi bi-globe2"></i>{{ __('front::common.about_char_lian_mean') }}</span>
        </span>
        <span class="about-brand-en">FunnLink</span>
      </h1>
      <p class="about-hero-accent" data-aos="fade-up" data-aos-duration="400">{{ __('front::common.about_accent') }}</p>
      <p class="about-hero-sub" data-aos="fade-up" data-aos-duration="500">
        {{ __('front::common.about_sub') }}
      </p>
      <div class="about-hero-meta" data-aos="fade-up" data-aos-duration="600">
        <span><i class="bi bi-geo-alt-fill"></i>{{ __('front::common.about_based') }}</span>
        <span><i class="bi bi-calendar-event-fill"></i>{{ __('front::common.about_founded') }}</span>
        <span><i class="bi bi-code-slash"></i>{{ __('front::common.about_open_source') }}</span>
      </div>
      <a href="#about-story" class="about-hero-cta" data-aos="fade-up" data-aos-duration="700">
        {{ __('front::common.learn_more') }} <i class="bi bi-arrow-down"></i>
      </a>
    </div>
  </section>

  {{-- Story --}}
  <section class="about-story" id="about-story">
    <div class="container">
      {{-- Intro (owned by this blade; CMS rich-text box is NOT rendered for custom-blade pages) --}}
      <p class="about-intro" data-aos="fade-up">{{ __('front::common.about_intro') }}</p>

      {{-- Product matrix --}}
      <div class="about-block-head" data-aos="fade-up">
        <span class="about-block-label">{{ __('front::common.about_matrix_label') }}</span>
        <h2 class="about-section-title">{{ __('front::common.about_matrix_heading') }}</h2>
        <p class="about-section-sub">{{ __('front::common.about_matrix_sub') }}</p>
      </div>
      <div class="about-matrix">
        @foreach($products as $product)
          @php
            $t = $product->translations->firstWhere('locale', app()->getLocale()) ?? $product->translations->first();
            $slug = $product->slug ?? '';
            $pmap = [
              'innocms'     => ['bi-code-square',        'blue'],
              'innoshop'    => ['bi-bag-check-fill',     'orange'],
              'innocrm'     => ['bi-people-fill',        'teal'],
              'innocard'    => ['bi-credit-card-2-front-fill', 'violet'],
              'tianfutrade' => ['bi-box-seam-fill',      'green'],
            ];
            $pm = $pmap[strtolower($slug)] ?? ['bi-box-fill', 'blue'];
          @endphp
          <a href="{{ front_route('products.show', ['slug' => $slug]) }}" class="about-mcard about-mcard--{{ $pm[1] }}" data-aos="fade-up" data-aos-duration="{{ 300 + $loop->index * 90 }}">
            <span class="about-mcard__icon"><i class="bi {{ $pm[0] }}"></i></span>
            <h3 class="about-mcard__name">{{ $t?->name ?? '' }}</h3>
            <p class="about-mcard__desc">{{ $t?->summary ?? '' }}</p>
            <span class="about-mcard__more">{{ __('front::common.about_product_view') }} <i class="bi bi-arrow-right"></i></span>
          </a>
        @endforeach
      </div>

      {{-- Philosophy + Contact --}}
      <div class="about-story-row">
        <div class="about-philosophy" data-aos="fade-up">
          <span class="about-mark" aria-hidden="true">&ldquo;</span>
          <h3 class="about-card-title">{{ __('front::common.about_philosophy_heading') }}</h3>
          <p>{{ __('front::common.about_sub') }}</p>
        </div>
        <div class="about-contact" data-aos="fade-up" data-aos-duration="300">
          <h3 class="about-card-title">{{ __('front::common.about_contact_heading') }}</h3>
          <a class="about-contact-row" href="mailto:team@innoshop.com">
            <span class="about-contact-row__icon"><i class="bi bi-envelope-fill"></i></span>
            <span class="about-contact-row__body">
              <span class="about-contact-row__label">{{ __('front::common.about_email_label') }}</span>
              <span class="about-contact-row__value">team@innoshop.com</span>
            </span>
          </a>
          <a class="about-contact-row" href="tel:+8613648089236">
            <span class="about-contact-row__icon"><i class="bi bi-telephone-fill"></i></span>
            <span class="about-contact-row__body">
              <span class="about-contact-row__label">{{ __('front::common.about_phone_label') }}</span>
              <span class="about-contact-row__value">+86 136-4808-9236</span>
            </span>
          </a>
        </div>
      </div>

      {{-- Stats strip with count-up --}}
      <div class="about-stats-strip" data-aos="fade-up">
        <div class="about-stat-cell">
          <i class="bi bi-boxes"></i>
          <div class="about-stat-num" data-count="5" data-suffix="+">0</div>
          <div class="about-stat-label">{{ __('front::common.about_stat_products') }}</div>
        </div>
        <div class="about-stat-cell">
          <i class="bi bi-download"></i>
          <div class="about-stat-num" data-count="20" data-suffix="K+">0</div>
          <div class="about-stat-label">{{ __('front::common.about_stat_downloads') }}</div>
        </div>
        <div class="about-stat-cell">
          <i class="bi bi-building"></i>
          <div class="about-stat-num" data-count="1000" data-suffix="+" data-format="comma">0</div>
          <div class="about-stat-label">{{ __('front::common.about_stat_clients') }}</div>
        </div>
        <div class="about-stat-cell">
          <i class="bi bi-globe2"></i>
          <div class="about-stat-num" data-count="30" data-suffix="+">0</div>
          <div class="about-stat-label">{{ __('front::common.about_stat_countries') }}</div>
        </div>
      </div>
    </div>
  </section>

  {{-- CTA --}}
  <section class="about-cta">
    <div class="container">
      <h2 data-aos="fade-up">{{ __('front::common.about_cta_title') }}</h2>
      <p data-aos="fade-up" data-aos-duration="300">{{ __('front::common.about_cta_sub') }}</p>
      <div class="about-cta-btns" data-aos="fade-up" data-aos-duration="500">
        <a href="{{ front_route('pages.slug_show', ['slug' => 'contact']) }}" class="btn btn-primary btn-lg">
          <i class="bi bi-envelope me-2"></i>{{ __('front::common.contact_us') }}
        </a>
        <a href="{{ front_route('products.index') }}" class="btn btn-outline-primary btn-lg ms-md-2 mt-2 mt-md-0">
          {{ __('front::common.products_title') }} <i class="bi bi-arrow-right ms-1"></i>
        </a>
      </div>
    </div>
  </section>
@endsection

@push('footer')
  <script>
    (function () {
      var strip = document.querySelector('.about-stats-strip');
      if (!strip) return;
      var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      var fmt = function (n, comma) {
        n = Math.round(n);
        return comma ? n.toLocaleString('en-US') : String(n);
      };
      var run = function (el) {
        var target = parseFloat(el.getAttribute('data-count')) || 0;
        var suffix = el.getAttribute('data-suffix') || '';
        var comma = el.getAttribute('data-format') === 'comma';
        if (reduce) { el.textContent = fmt(target, comma) + suffix; return; }
        var dur = 1300, t0 = null;
        var step = function (ts) {
          if (!t0) t0 = ts;
          var p = Math.min((ts - t0) / dur, 1);
          var e = 1 - Math.pow(1 - p, 3);
          el.textContent = fmt(target * e, comma) + suffix;
          if (p < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
      };
      if (!('IntersectionObserver' in window)) {
        strip.querySelectorAll('.about-stat-num').forEach(run);
        return;
      }
      var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (en) {
          if (en.isIntersecting) { run(en.target); io.unobserve(en.target); }
        });
      }, { threshold: 0.5 });
      strip.querySelectorAll('.about-stat-num').forEach(function (el) { io.observe(el); });
    })();
  </script>
@endpush
