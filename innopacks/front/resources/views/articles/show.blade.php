@extends('layouts.app')

@section('body-class', 'page-news-details')

@section('content')
  @include('shared.page-head', ['title' => $article->translation->title ?? ''])

  <section class="article-detail-section">
    <div class="container">
      <div class="row">
        <div class="col-12 col-md-9">
          <div class="newest-box">
            <div class="newes-title">{{ $article->translation->title ?? '' }}</div>
            @if ($article->tags->count())
            <div class="newes-tags mb-3 mt-n2">
              <i class="bi bi-tags me-1"></i>
              <div class="d-flex">
                @foreach($article->tags as $tag)
                  <a href="{{ $tag->url }}">{{ $tag->translation->name ?? '' }}</a>
                @endforeach
              </div>
            </div>
            @endif
            <div class="newes-top">
              <div class="newes-time"><i class="bi bi-clock"></i> {{ $article->created_at->format('Y-m-d') }}</div>
              @if($article->author)<div class="newes-author"><i class="bi bi-person-square"></i> {{ $article->author }}</div>@endif
              @if($article->catalog)<div class="newes-author"><i class="bi bi-ui-radios-grid"></i> {{ $article->catalog->translation->title ?? '' }}</div>@endif
              <div class="newes-author"><i class="bi bi-eye"></i> {{ $article->viewed }}</div>
            </div>
            @if($article->image)
              <div class="newes-cover">
                <img src="{{ image_resize($article->image, 1200, 675) }}" class="img-fluid rounded" alt="{{ $article->translation->title ?? '' }}">
              </div>
            @endif
            <div class="content">
              {!! $article->translation->content ?? '' !!}
            </div>
          </div>
        </div>
        <div class="col-12 col-md-3">
          @include('shared.articles-sidebar', ['currentCatalogId' => $article->catalog_id ?? null])
        </div>
      </div>
    </div>
  </section>
@endsection
