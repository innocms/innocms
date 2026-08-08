@extends('layouts.app')

@section('body-class', 'page-categories')
@section('title', __('front::common.categories'))

@section('content')
  @include('shared.page-head', ['title' => __('front::common.categories')])

  @if($categories->isNotEmpty())
    <section class="products-list-section">
      <div class="container">
        <div class="row g-4">
          @foreach($categories as $category)
            <div class="col-12 col-md-6 col-lg-4" data-aos="fade-up" data-aos-duration="{{ 300 + $loop->index * 200 }}">
              <a href="{{ $category->url }}" class="product-card text-reset text-decoration-none">
                <div class="product-card-img">
                  <img src="{{ image_resize($category->image ?? '', 800, 500) }}" alt="{{ $category->fallbackName() }}" loading="lazy">
                </div>
                <div class="product-card-body">
                  <h3 class="h5 fw-bold mb-2">{{ $category->fallbackName() }}</h3>
                  <div class="product-card-footer mt-3">
                    {{ __('front::common.view_more') }} <i class="bi bi-arrow-right"></i>
                  </div>
                </div>
              </a>
            </div>
          @endforeach
        </div>
      </div>
    </section>
  @else
    <section class="products-list-section">
      <div class="container">
        <p class="text-muted text-center py-5">{{ __('front::common.no_data') }}</p>
      </div>
    </section>
  @endif
@endsection
