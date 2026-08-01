@extends('layouts.app')

@section('body-class', 'page-product-detail')
@section('title', $product->translation?->meta_title ?: ($product->translation?->name ?? ''))

@section('content')
  @include('shared.page-head', ['title' => $product->translation?->name ?? ''])

  <div class="container">
    <div class="row g-5">
      <div class="col-lg-6">
        <div class="product-detail-img">
          <img src="{{ image_resize($product->image ?? '', 800, 500) }}" alt="{{ $product->translation?->name ?? '' }}" class="img-fluid rounded-3">
        </div>
      </div>
      <div class="col-lg-6">
        @if($product->spu_code)
          <div class="product-spu text-muted small mb-2">{{ $product->spu_code }}</div>
        @endif
        <div class="content">
          {!! $product->translation?->content ?? '' !!}
        </div>
        @if($product->link)
          <a href="{{ $product->link }}" target="_blank" rel="noopener" class="btn btn-primary btn-lg mt-4">
            {{ __('front::common.visit_site') }} <i class="bi bi-box-arrow-up-right ms-1"></i>
          </a>
        @endif
      </div>
    </div>
  </div>

  @if(($related ?? null) && $related->isNotEmpty())
    <div class="home-section">
      <div class="container">
        <div class="module-title" data-aos="fade-up">{{ __('front::common.related_products') }}</div>
        <div class="row g-4 mt-3">
          @foreach($related as $rp)
            @php $rt = $rp->translations->firstWhere('locale', app()->getLocale()) ?? $rp->translations->first() @endphp
            <div class="col-12 col-md-4" data-aos="fade-up" data-aos-duration="{{ 300 + $loop->index * 200 }}">
              <a href="{{ front_route('products.show', ['slug' => $rp->slug]) }}" class="text-reset text-decoration-none h-100 d-flex flex-column">
                <div class="card h-100 shadow-sm rounded-3 overflow-hidden product-card">
                  <div class="product-card-img">
                    <img src="{{ image_resize($rp->image ?? '', 800, 500) }}" alt="{{ $rt?->name ?? '' }}" class="card-img-top" loading="lazy">
                  </div>
                  <div class="card-body d-flex flex-column">
                    <h3 class="h5 fw-bold mb-2">{{ $rt?->name ?? '' }}</h3>
                    <p class="text-muted small flex-grow-1 mb-0">{{ $rt?->summary ?? '' }}</p>
                  </div>
                </div>
              </a>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  @endif
@endsection
