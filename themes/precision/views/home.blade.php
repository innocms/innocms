@extends('layouts.app')
@section('body-class', 'page-home')
@section('content')

  @push('header')
    <script src="{{ asset('vendor/aos/aos.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('vendor/aos/aos.css') }}">
  @endpush

  @hookinsert('page.content.top')

  {{-- Hero banner --}}
  <div class="home-banner"
       style="background-image: linear-gradient(rgba(244, 247, 253, .92), rgba(240, 243, 254, .88)), url('{{ asset('images/demo/industries/manufacturing.jpg') }}')">
    <div class="home-banner-info">
      <div class="container">
        <div class="row">
          <div class="col-md-6">
            <div class="home-banner-left">
              <h1 data-aos="fade-up" data-aos-duration="1000">{{ __('theme-precision::home.hero_title') }}</h1>
              <p class="sub-title" data-aos="fade-up" data-aos-duration="1500">
                <span class="text-primary" style="color: {{ '#f4772e' }} !important;">{{ __('theme-precision::home.hero_highlight') }}</span>
              </p>
              <p class="sub-title-2" data-aos="fade-up" data-aos-duration="1800">
                - {{ __('theme-precision::home.hero_point_1') }}<br/>
                - {{ __('theme-precision::home.hero_point_2') }}<br/>
                - {{ __('theme-precision::home.hero_point_3') }}<br/>
                - {{ __('theme-precision::home.hero_point_4') }}<br>
              </p>
              <div data-aos="fade-up" data-aos-duration="2000" class="left-btn">
                <a href="{{ front_route('pages.slug_show', ['slug' => 'contact']) }}"
                   class="btn btn-lg btn-accent">{{ __('theme-precision::home.cta_quote') }}</a>
                @if($productsCatalog ?? null)
                  <a href="{{ front_route('catalogs.slug_show', ['slug' => $productsCatalog->slug]) }}"
                     class="btn btn-lg btn-outline-primary ms-md-3 mt-3 mt-md-0">{{ __('theme-precision::home.cta_products') }}</a>
                @endif
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="home-banner-right">
              <img src="{{ asset('images/demo/company/factory.jpg') }}" class="img-fluid rounded shadow"
                   data-aos="fade-up" data-aos-duration="2000" alt="{{ __('theme-precision::home.hero_highlight') }}">
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="bottom-bg">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1280 140" preserveAspectRatio="none">
        <path d="M320 28c320 0 320 84 640 84 160 0 240-21 320-42v70H0V70c80-21 160-42 320-42z"></path>
      </svg>
    </div>
  </div>

  {{-- Core manufacturing services --}}
  @if(($serviceCatalogs ?? null) && $serviceCatalogs->isNotEmpty())
    @php
      $serviceIcons = [
          'cnc-machining'     => 'bi-gear-wide-connected',
          'sheet-metal'       => 'bi-layers-half',
          'die-casting'       => 'bi-droplet-half',
          'surface-finishing' => 'bi-paint-bucket',
      ];
    @endphp
    <div class="home-business">
      <div class="container">
        <div class="business-top">
          <div class="module-title" data-aos="fade-up">{{ __('theme-precision::home.services_title') }}</div>
          <div class="module-sub-title" data-aos="fade-up">{{ __('theme-precision::home.services_sub') }}</div>
        </div>
        <div class="row g-4">
          @foreach($serviceCatalogs as $catalog)
            <div class="col-12 col-md-6 col-lg-3">
              <a class="business-item" data-aos="fade-up" data-aos-duration="{{ 300 + $loop->index * 300 }}"
                 href="{{ front_route('catalogs.slug_show', ['slug' => $catalog->slug]) }}">
                <div class="icon"><i class="bi {{ $serviceIcons[$catalog->slug] ?? 'bi-box-seam' }}"></i></div>
                <div class="title">{{ $catalog->translation->title ?? $catalog->slug }}</div>
                <div class="sub-title">{{ $catalog->translation->summary ?? '' }}</div>
              </a>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  @endif

  {{-- Featured products --}}
  @if(($featuredProducts ?? null) && $featuredProducts->isNotEmpty())
    <div class="container mt-5 pt-4">
      <div class="module-title" data-aos="fade-up">{{ __('theme-precision::home.featured_title') }}</div>
      <div class="module-sub-title" data-aos="fade-up">{{ __('theme-precision::home.featured_sub') }}</div>
      <div class="row g-4">
        @foreach($featuredProducts as $article)
          <div class="col-12 col-md-6 col-lg-3" data-aos="fade-up" data-aos-duration="{{ 300 + $loop->index * 300 }}">
            <div class="card h-100 shadow-sm rounded-3 overflow-hidden">
              <a href="{{ $article->url }}">
                <img src="{{ image_resize($article->translation->image ?? '', 600, 450) }}"
                     class="card-img-top" alt="{{ $article->translation->title ?? '' }}">
              </a>
              <div class="card-body d-flex flex-column">
                <h3 class="h6 fw-bold mb-2">
                  <a href="{{ $article->url }}" class="text-reset text-decoration-none">{{ $article->translation->title ?? '' }}</a>
                </h3>
                <p class="text-muted small mb-3 flex-grow-1">{{ \Illuminate\Support\Str::limit($article->translation->summary ?? '', 60) }}</p>
                <a href="{{ $article->url }}" class="small text-decoration-none">{{ __('theme-precision::home.learn_more') }} <i class="bi bi-arrow-right"></i></a>
              </div>
            </div>
          </div>
        @endforeach
      </div>
      <div class="text-center mt-4">
        @if($productsCatalog ?? null)
          <a href="{{ front_route('catalogs.slug_show', ['slug' => $productsCatalog->slug]) }}"
             class="btn btn-outline-primary">{{ __('theme-precision::home.view_more') }}</a>
        @endif
      </div>
    </div>
  @endif

  {{-- Industries --}}
  @if(($industryArticles ?? null) && $industryArticles->isNotEmpty())
    <div class="bg-light mt-5 pt-5 pb-5">
      <div class="container">
        <div class="module-title" data-aos="fade-up">{{ __('theme-precision::home.industries_title') }}</div>
        <div class="module-sub-title" data-aos="fade-up">{{ __('theme-precision::home.industries_sub') }}</div>
        <div class="row g-4">
          @foreach($industryArticles as $article)
            <div class="col-12 col-md-6 col-lg-3" data-aos="fade-up" data-aos-duration="{{ 300 + $loop->index * 300 }}">
              <a href="{{ front_route('catalogs.slug_show', ['slug' => $article->catalog->slug]) }}" class="text-reset text-decoration-none">
                <div class="card h-100 shadow-sm rounded-3 overflow-hidden">
                  <img src="{{ image_resize($article->translation->image ?? '', 600, 450) }}"
                       class="card-img-top" alt="{{ $article->catalog->translation->title ?? '' }}">
                  <div class="card-body">
                    <h3 class="h6 fw-bold mb-1">{{ $article->catalog->translation->title ?? '' }}</h3>
                    <p class="text-muted small mb-0">{{ \Illuminate\Support\Str::limit($article->translation->summary ?? '', 50) }}</p>
                  </div>
                </div>
              </a>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  @endif

  {{-- Why choose us --}}
  <div class="home-core-function">
    <div class="container">
      <div class="module-title" data-aos="fade-up">{{ __('theme-precision::home.strengths_title') }}</div>
      @php
        $strengths = [
            ['icon' => 'bi-bullseye', 'title' => __('theme-precision::home.strength_1_title'), 'sub' => __('theme-precision::home.strength_1_sub')],
            ['icon' => 'bi-lightning-charge-fill', 'title' => __('theme-precision::home.strength_2_title'), 'sub' => __('theme-precision::home.strength_2_sub')],
            ['icon' => 'bi-patch-check-fill', 'title' => __('theme-precision::home.strength_3_title'), 'sub' => __('theme-precision::home.strength_3_sub')],
            ['icon' => 'bi-globe-americas', 'title' => __('theme-precision::home.strength_4_title'), 'sub' => __('theme-precision::home.strength_4_sub')],
        ];
      @endphp
      <div class="row g-4">
        @foreach($strengths as $strength)
          <div class="col-12 col-md-6 col-lg-3" data-aos="fade-up" data-aos-duration="{{ 300 + $loop->index * 300 }}">
            <div class="core-function-item">
              <div class="icon"><i class="bi {{ $strength['icon'] }}"></i></div>
              <div class="title">{{ $strength['title'] }}</div>
              <div class="sub-title">{{ $strength['sub'] }}</div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>

  {{-- Latest news --}}
  @if(($latestNews ?? null) && $latestNews->isNotEmpty())
    <div class="container mt-5 pt-4">
      <div class="module-title" data-aos="fade-up">{{ __('theme-precision::home.news_title') }}</div>
      <div class="module-sub-title" data-aos="fade-up">{{ __('theme-precision::home.news_sub') }}</div>
      <div class="row g-4">
        @foreach($latestNews as $news)
          <div class="col-12 col-md-4" data-aos="fade-up" data-aos-duration="{{ 300 + $loop->index * 300 }}">
            <div class="card h-100 shadow-sm rounded-3 overflow-hidden">
              <a href="{{ $news->url }}">
                <img src="{{ image_resize($news->translation->image ?? '', 800, 450) }}"
                     class="card-img-top" alt="{{ $news->translation->title ?? '' }}">
              </a>
              <div class="card-body d-flex flex-column">
                <div class="text-muted small mb-2">
                  <i class="bi bi-calendar3"></i> {{ $news->created_at->format('Y-m-d') }}
                  @if($news->catalog && $news->catalog->translation)
                    <span class="ms-2"><i class="bi bi-folder2"></i> {{ $news->catalog->translation->title }}</span>
                  @endif
                </div>
                <h3 class="h6 fw-bold mb-2">
                  <a href="{{ $news->url }}" class="text-reset text-decoration-none">{{ $news->translation->title ?? '' }}</a>
                </h3>
                <p class="text-muted small mb-0 flex-grow-1">{{ \Illuminate\Support\Str::limit($news->translation->summary ?? '', 80) }}</p>
              </div>
            </div>
          </div>
        @endforeach
      </div>
      <div class="text-center mt-4">
        @if($newsCatalog ?? null)
          <a href="{{ front_route('catalogs.slug_show', ['slug' => $newsCatalog->slug]) }}"
             class="btn btn-outline-primary">{{ __('theme-precision::home.view_more') }}</a>
        @endif
      </div>
    </div>
  @endif

  {{-- CTA banner --}}
  <div class="home-customized"
       style="background-image: linear-gradient(rgba(255, 255, 255, .9), rgba(255, 255, 255, .9)), url('{{ asset('images/demo/company/team.jpg') }}')">
    <div class="home-banner-info">
      <div class="container">
        <div class="row">
          <div class="col-md-8">
            <h1 data-aos="fade-up" data-aos-duration="1000">{{ __('theme-precision::home.cta_banner_title') }}</h1>
            <p class="sub-title-2" data-aos="fade-up" data-aos-duration="1500">{{ __('theme-precision::home.cta_banner_sub') }}</p>
            <div data-aos="fade-up" data-aos-duration="2000" class="left-btn">
              <a href="{{ front_route('pages.slug_show', ['slug' => 'contact']) }}"
                 class="btn btn-lg btn-accent">{{ __('theme-precision::home.cta_banner_btn') }}</a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="bottom-bg">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1280 140" preserveAspectRatio="none">
        <path d="M320 28c320 0 320 84 640 84 160 0 240-21 320-42v70H0V70c80-21 160-42 320-42z"></path>
      </svg>
    </div>
  </div>

  {{-- Contact --}}
  <div class="home-contact" id="contactUsContent">
    <div class="container">
      <div class="title" data-aos="fade-up">{{ __('theme-precision::home.contact_title') }}</div>
      <div class="row">
        <div class="col-12 col-lg-3">
          <div class="contact-item" data-aos="fade-up">
            <div class="icon"><i class="bi bi-telephone-fill"></i></div>
            <div class="right">
              <div class="text-1">{{ __('theme-precision::home.contact_phone') }}</div>
              <div class="text-2"><a href="tel:{{ preg_replace('/[^0-9+]/', '', system_setting('contact_phone')) }}">{{ system_setting('contact_phone') }}</a></div>
            </div>
          </div>
        </div>
        <div class="col-12 col-lg-3">
          <div class="contact-item" data-aos="fade-up">
            <div class="icon"><i class="bi bi-envelope-fill"></i></div>
            <div class="right">
              <div class="text-1">{{ __('theme-precision::home.contact_email') }}</div>
              <div class="text-2"><a href="mailto:{{ system_setting('contact_email') }}">{{ system_setting('contact_email') }}</a></div>
            </div>
          </div>
        </div>
        <div class="col-12 col-lg-3">
          <div class="contact-item" data-aos="fade-up">
            <div class="icon"><i class="bi bi-geo-alt-fill"></i></div>
            <div class="right">
              <div class="text-1">{{ __('theme-precision::home.contact_address') }}</div>
              <div class="text-2 text-muted">{{ system_setting('contact_address') }}</div>
            </div>
          </div>
        </div>
        <div class="col-12 col-lg-3">
          <div class="contact-item" data-aos="fade-up">
            <div class="icon"><i class="bi bi-clock-fill"></i></div>
            <div class="right">
              <div class="text-1">{{ __('theme-precision::home.contact_hours') }}</div>
              <div class="text-2 text-muted">{{ system_setting('contact_hours') }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  @hookinsert('page.content.bottom')

@endsection

@push('footer')
  <script>
    AOS.init({ duration: 300, easing: 'ease-in-out', once: true, mirror: false });
  </script>
@endpush
