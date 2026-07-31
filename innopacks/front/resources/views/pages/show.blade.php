@extends('layouts.app')

@section('content')
  @php $pageTitle = $page->translation->title ?? ''; @endphp
  <div class="page-head">
    <div class="container">
      <div class="page-title">{{ $pageTitle }}</div>
      <nav>
        <ol class="breadcrumb d-flex justify-content-center">
          <li class="breadcrumb-item"><a href="{{ front_route('home.index') }}"><i class="bi bi-house-door-fill"></i> {{ __('front::common.home') }}</a></li>
          <li class="breadcrumb-item active" aria-current="page">{{ $pageTitle }}</li>
        </ol>
      </nav>
    </div>
  </div>

  @if(isset($result))
    {!! $result !!}
  @else
    @hookinsert('page.content.top')
    <div class="page-service-content">
      <div class="container">
        <div class="row">
          <div class="col-12">
            {!! $page->translation->content ?? '' !!}
          </div>
        </div>
      </div>
    </div>
    @hookinsert('page.content.bottom')
  @endif
@endsection
