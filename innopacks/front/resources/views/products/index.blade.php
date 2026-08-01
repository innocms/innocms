@extends('layouts.app')

@section('body-class', 'page-products')
@section('title', $category?->translation?->meta_title ?: __('front::common.products_title'))

@section('content')
  @include('shared.page-head', ['title' => $category?->translation?->name ?? __('front::common.products_title')])

  @if(($products ?? null) && $products->isNotEmpty())
    <section class="products-list-section">
      <div class="container">
        <div class="row g-4">
          @foreach($products as $product)
            @php $t = $product->translations->firstWhere('locale', app()->getLocale()) ?? $product->translations->first() @endphp
            <div class="col-12 col-md-6 col-lg-4" data-aos="fade-up" data-aos-duration="{{ 300 + $loop->index * 200 }}">
              <a href="{{ front_route('products.show', ['slug' => $product->slug]) }}" class="product-card text-reset text-decoration-none">
                <div class="product-card-img">
                  <img src="{{ image_resize($product->image ?? '', 800, 500) }}" alt="{{ $t?->name ?? '' }}" loading="lazy">
                  @if($product->spu_code)
                    <span class="product-card-badge">{{ $product->spu_code }}</span>
                  @endif
                </div>
                <div class="product-card-body">
                  <h3 class="h5 fw-bold mb-2">{{ $t?->name ?? '' }}</h3>
                  <p class="text-muted small mb-0">{{ $t?->summary ?? '' }}</p>
                  <div class="product-card-footer mt-3">
                    {{ __('front::common.learn_more') }} <i class="bi bi-arrow-right"></i>
                  </div>
                </div>
              </a>
            </div>
          @endforeach
        </div>
      </div>
    </section>
  @endif
@endsection
