@extends('layouts.app')

@section('content')
    @if($page->show_breadcrumb)
      <div class="page-head">
        <div class="container">
          <div class="page-title">{{ $page->translation->title }}</div>
          <nav>
            <ol class="breadcrumb d-flex justify-content-center">
              <li class="breadcrumb-item"><a href="{{ front_route('home.index') }}"><i class="bi bi-house-door-fill"></i> 首页</a></li>
              <li class="breadcrumb-item active" aria-current="page">{{ $page->translation->title }}</li>
            </ol>
          </nav>
        </div>
      </div>
    @endif

  @if(isset($result))
    {!! $result !!}
  @else

      @hookinsert('page.content.top')

    <div class="page-service-content">
      <div class="container">
        <div class="row">
          {!! $page->translation->content !!}
        </div>
      </div>
    </div>
  @endif

    @hookinsert('page.content.bottom')

@endsection
