@extends('layouts.app')

@section('body-class', 'page-product-detail')
@section('title', $product->translation?->meta_title ?: ($product->translation?->name ?? ''))

@php
  $t = $product->translation;
  $content = $t?->content ?? '';
  // Plain text → wrap in <p> for typography; HTML → render as-is
  if ($content && ! preg_match('/<[a-z][^>]*>/i', $content)) {
      $content = '<p>' . nl2br(e($content)) . '</p>';
  }
  $sellingPoints = array_filter(array_map('trim', preg_split('/[·,，、|]/u', $t?->selling_point ?? '')));
@endphp

@section('content')
  @include('shared.page-head', ['title' => $product->translation?->name ?? ''])

  <section class="product-hero">
    <div class="container">
      <div class="row g-5 align-items-center">
        <div class="col-lg-6" data-aos="fade-right">
          <div class="product-hero-img">
            <img src="{{ image_resize($product->image ?? '', 1000, 563) }}" alt="{{ $t?->name ?? '' }}" class="img-fluid">
          </div>
        </div>
        <div class="col-lg-6" data-aos="fade-left">
          <div class="product-hero-info">
            @if($product->spu_code)
              <span class="product-spu-badge">{{ $product->spu_code }}</span>
            @endif

            <h1 class="product-hero-title">{{ $t?->name ?? '' }}</h1>

            @if($t?->summary)
              <p class="product-hero-summary">{{ $t->summary }}</p>
            @endif

            @if($sellingPoints)
              <div class="product-hero-tags">
                @foreach($sellingPoints as $sp)
                  <span class="product-tag"><i class="bi bi-check-circle-fill"></i> {{ $sp }}</span>
                @endforeach
              </div>
            @endif

            @if($product->link)
              <div class="product-hero-cta">
                <a href="{{ $product->link }}" target="_blank" rel="noopener" class="btn btn-primary btn-lg">
                  <i class="bi bi-globe2 me-2"></i>{{ __('front::common.visit_site') }}
                </a>
                <a href="{{ front_route('products.index') }}" class="btn btn-outline-secondary btn-lg ms-2">
                  <i class="bi bi-arrow-left me-2"></i>{{ __('front::common.products_title') }}
                </a>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </section>

  @if($content)
    <section class="product-content-section">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-10">
            <div class="content product-detail-content">
              {!! $content !!}
            </div>
          </div>
        </div>
      </div>
    </section>
  @endif

  @if(($related ?? null) && $related->isNotEmpty())
    <section class="product-related">
      <div class="container">
        <h2 class="product-section-title" data-aos="fade-up">{{ __('front::common.related_products') }}</h2>
        <div class="row g-4 mt-1">
          @foreach($related as $rp)
            @php
              $rt = $rp->translations->firstWhere('locale', app()->getLocale()) ?? $rp->translations->first();
              $rpContent = $rt?->content ?? '';
            @endphp
            <div class="col-12 col-md-4" data-aos="fade-up" data-aos-duration="{{ 300 + $loop->index * 200 }}">
              <a href="{{ $rp->front_url }}" class="product-related-card text-reset text-decoration-none h-100 d-flex flex-column">
                <div class="product-related-img">
                  <img src="{{ image_resize($rp->image ?? '', 800, 500) }}" alt="{{ $rt?->name ?? '' }}" loading="lazy">
                </div>
                <div class="product-related-body">
                  <h3 class="h5 fw-bold mb-2">{{ $rt?->name ?? '' }}</h3>
                  <p class="text-muted small mb-0">{{ $rt?->summary ?? '' }}</p>
                  <span class="product-related-link mt-3">
                    {{ __('front::common.learn_more') }} <i class="bi bi-arrow-right"></i>
                  </span>
                </div>
              </a>
            </div>
          @endforeach
        </div>
      </div>
    </section>
  @endif
@endsection
