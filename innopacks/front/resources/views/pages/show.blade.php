@extends('layouts.app')

@section('body-class', 'page-service-content')

@section('content')
  @include('shared.page-head', ['title' => $page->translation->title ?? ''])

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
