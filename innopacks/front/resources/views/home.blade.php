@extends('layouts.app')
@section('body-class', 'page-home')
@section('content')

  @hookinsert('page.content.top')

  {{-- Hero --}}
  <div class="home-banner">
    <div class="home-banner-info">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-7">
            <div class="home-banner-left">
              <h1 data-aos="fade-up">{{ __('front::common.hero_title') }}</h1>
              <p class="sub-title" data-aos="fade-up" data-aos-duration="300">
                <span class="text-accent">{{ __('front::common.hero_highlight') }}</span>
              </p>
              <p class="sub-title-2" data-aos="fade-up" data-aos-duration="500">{{ __('front::common.hero_sub') }}</p>
              <div data-aos="fade-up" data-aos-duration="700" class="left-btn">
                <a href="{{ front_route('pages.slug_show', ['slug' => 'get-started']) }}" class="btn btn-lg btn-accent">{{ __('front::common.hero_cta_start') }}</a>
                <a href="{{ front_route('pages.slug_show', ['slug' => 'features']) }}" class="btn btn-lg btn-outline-primary ms-md-3 mt-3 mt-md-0">{{ __('front::common.hero_cta_features') }}</a>
              </div>
            </div>
          </div>
          <div class="col-md-5 d-none d-md-block">
            <div class="home-hero-visual" data-aos="fade-left" data-aos-duration="700">
              <div class="hero-card hero-card--1"><i class="bi bi-boxes"></i><span>{{ __('front::common.hero_card_1') }}</span></div>
              <div class="hero-card hero-card--2"><i class="bi bi-plug-fill"></i><span>{{ __('front::common.hero_card_2') }}</span></div>
              <div class="hero-card hero-card--3"><i class="bi bi-palette-fill"></i><span>{{ __('front::common.hero_card_3') }}</span></div>
              <div class="hero-card hero-card--4"><i class="bi bi-translate"></i><span>{{ __('front::common.hero_card_4') }}</span></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="bottom-bg">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1280 140" preserveAspectRatio="none"><path d="M320 28c320 0 320 84 640 84 160 0 240-21 320-42v70H0V70c80-21 160-42 320-42z"></path></svg>
    </div>
  </div>

  {{-- Features --}}
  <div class="home-business">
    <div class="container">
      <div class="business-top">
        <div class="module-title" data-aos="fade-up">{{ __('front::common.features_title') }}</div>
        <div class="module-sub-title" data-aos="fade-up">{{ __('front::common.features_sub') }}</div>
      </div>
      @php
        $features = [
            ['bi-boxes', __('front::common.feature_1_title'), __('front::common.feature_1_sub')],
            ['bi-plug-fill', __('front::common.feature_2_title'), __('front::common.feature_2_sub')],
            ['bi-palette-fill', __('front::common.feature_3_title'), __('front::common.feature_3_sub')],
            ['bi-translate', __('front::common.feature_4_title'), __('front::common.feature_4_sub')],
        ];
      @endphp
      <div class="row g-4">
        @foreach($features as $i => $f)
          <div class="col-12 col-md-6 col-lg-3">
            <div class="business-item" data-aos="fade-up" data-aos-duration="{{ 300 + $i * 200 }}">
              <div class="icon"><i class="bi {{ $f[0] }}"></i></div>
              <div class="title">{{ $f[1] }}</div>
              <div class="sub-title">{{ $f[2] }}</div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>

  {{-- Stats --}}
  <div class="home-stats">
    <div class="container">
      <div class="module-title" data-aos="fade-up">{{ __('front::common.stats_title') }}</div>
      <div class="row g-4 text-center">
        <div class="col-6 col-md-3" data-aos="fade-up" data-aos-duration="300">
          <div class="stat-number">500+</div>
          <div class="stat-label">{{ __('front::common.stat_businesses') }}</div>
        </div>
        <div class="col-6 col-md-3" data-aos="fade-up" data-aos-duration="400">
          <div class="stat-number">30+</div>
          <div class="stat-label">{{ __('front::common.stat_countries') }}</div>
        </div>
        <div class="col-6 col-md-3" data-aos="fade-up" data-aos-duration="500">
          <div class="stat-number">99.9%</div>
          <div class="stat-label">{{ __('front::common.stat_uptime') }}</div>
        </div>
        <div class="col-6 col-md-3" data-aos="fade-up" data-aos-duration="600">
          <div class="stat-number">10{{ __('front::common.unit_min') }}</div>
          <div class="stat-label">{{ __('front::common.stat_launch') }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- How It Works --}}
  <div class="home-workflow">
    <div class="container">
      <div class="module-title" data-aos="fade-up">{{ __('front::common.workflow_title') }}</div>
      <div class="module-sub-title" data-aos="fade-up">{{ __('front::common.workflow_sub') }}</div>
      <div class="row g-4 align-items-center">
        <div class="col-12 col-md-4" data-aos="fade-up" data-aos-duration="300">
          <div class="workflow-step">
            <div class="workflow-icon"><i class="bi bi-layout-text-window"></i></div>
            <div class="workflow-num">01</div>
            <div class="workflow-title">{{ __('front::common.workflow_1_title') }}</div>
            <div class="workflow-desc">{{ __('front::common.workflow_1_desc') }}</div>
          </div>
        </div>
        <div class="col-12 col-md-4" data-aos="fade-up" data-aos-duration="500">
          <div class="workflow-step">
            <div class="workflow-icon"><i class="bi bi-pencil-square"></i></div>
            <div class="workflow-num">02</div>
            <div class="workflow-title">{{ __('front::common.workflow_2_title') }}</div>
            <div class="workflow-desc">{{ __('front::common.workflow_2_desc') }}</div>
          </div>
        </div>
        <div class="col-12 col-md-4" data-aos="fade-up" data-aos-duration="700">
          <div class="workflow-step">
            <div class="workflow-icon"><i class="bi bi-rocket-takeoff"></i></div>
            <div class="workflow-num">03</div>
            <div class="workflow-title">{{ __('front::common.workflow_3_title') }}</div>
            <div class="workflow-desc">{{ __('front::common.workflow_3_desc') }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Solutions --}}
  @if(($serviceCatalogs ?? null) && $serviceCatalogs->isNotEmpty())
    <div class="home-section home-section--alt">
      <div class="container">
        <div class="module-title" data-aos="fade-up">{{ __('front::common.solutions_title') }}</div>
        <div class="module-sub-title" data-aos="fade-up">{{ __('front::common.solutions_sub') }}</div>
        <div class="row g-4">
          @foreach($serviceCatalogs as $catalog)
            <div class="col-12 col-md-6 col-lg-4" data-aos="fade-up" data-aos-duration="{{ 300 + $loop->index * 200 }}">
              <a href="{{ front_route('catalogs.slug_show', ['slug' => $catalog->slug]) }}" class="solution-card text-reset text-decoration-none">
                <h3 class="h5 fw-bold mb-2">{{ $catalog->translation->title ?? '' }}</h3>
                <p class="text-muted small mb-0">{{ $catalog->translation->summary ?? '' }}</p>
                <span class="solution-more mt-3 d-inline-block small">{{ __('front::common.read_more') }} <i class="bi bi-arrow-right"></i></span>
              </a>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  @endif

  {{-- Resources / latest articles --}}
  @if(($latestNews ?? null) && $latestNews->isNotEmpty())
    <div class="home-section">
      <div class="container">
        <div class="module-title" data-aos="fade-up">{{ __('front::common.resources_title') }}</div>
        <div class="module-sub-title" data-aos="fade-up">{{ __('front::common.resources_sub') }}</div>
        <div class="row g-4">
          @foreach($latestNews as $article)
            <div class="col-12 col-md-6 col-lg-4" data-aos="fade-up" data-aos-duration="{{ 300 + $loop->index * 200 }}">
              <div class="card h-100 shadow-sm rounded-3 overflow-hidden">
                <a href="{{ $article->url }}">
                  <img src="{{ image_resize($article->translation->image ?? '', 800, 450) }}" class="card-img-top" alt="{{ $article->translation->title ?? '' }}">
                </a>
                <div class="card-body d-flex flex-column">
                  <div class="text-muted small mb-2">
                    @if($article->catalog && $article->catalog->translation)<i class="bi bi-folder2"></i> {{ $article->catalog->translation->title }}@endif
                  </div>
                  <h3 class="h6 fw-bold mb-2"><a href="{{ $article->url }}" class="text-reset text-decoration-none">{{ $article->translation->title ?? '' }}</a></h3>
                  <p class="text-muted small mb-0 flex-grow-1">{{ \Illuminate\Support\Str::limit($article->translation->summary ?? '', 90) }}</p>
                </div>
              </div>
            </div>
          @endforeach
        </div>
        <div class="text-center mt-4">
          @if($newsCatalog ?? null)
            <a href="{{ front_route('catalogs.slug_show', ['slug' => $newsCatalog->slug]) }}" class="btn btn-outline-primary">{{ __('front::common.view_more') }}</a>
          @endif
        </div>
      </div>
    </div>
  @endif

  @hookinsert('page.content.bottom')

@endsection
