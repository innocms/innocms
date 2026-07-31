@extends('layouts.app')

@section('body-class', 'page-news-details')

@section('content')
  @include('shared.page-head', ['title' => $article->translation->title ?? ''])

  @php
    $sidebarList = [];
    if (! empty($sidebarCatalog)) {
        $children = $sidebarCatalog->children ?? collect();
        if ($children->isNotEmpty()) {
            $sidebarList = collect([$sidebarCatalog])->merge($children);
        } elseif ($sidebarCatalog->parent) {
            $sidebarList = collect([$sidebarCatalog->parent])->merge($sidebarCatalog->parent->children);
        } else {
            $sidebarList = collect([$sidebarCatalog]);
        }
    }
  @endphp

  <div class="container mt-3 mt-md-5">
    <div class="row">
      <div class="col-12 col-md-9">
        <div class="newest-box">
          <div class="newes-title">{{ $article->translation->title ?? '' }}</div>
          @if ($article->tags->count())
          <div class="newes-tags mb-3 mt-n2">
            <i class="bi bi-tags me-1"></i>
            <div class="d-flex">
              @foreach($article->tags as $tag)
                <a href="{{ front_route('tags.show', ['slug' => $tag->slug]) }}">{{ $tag->translation->name ?? '' }}</a>
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
        <div class="newes-sidebar">
          <div class="search-box">
            <div class="input-group input-group-lg">
              <input type="text" class="form-control" value="{{ request('keyword') }}" placeholder="{{ __('front::common.search_ph') }}">
              <button class="btn btn-primary" type="button">{{ __('front::common.search') }}</button>
            </div>
          </div>
          @if($sidebarList)
            <div class="sidebar-item">
              <div class="sidebar-title">{{ __('front::common.categories') }}</div>
              <div class="sidebar-list">
                <ul>
                  @foreach($sidebarList as $cat)
                    <li><a href="{{ $cat->url }}">{{ $cat->translation->title ?? '' }}</a></li>
                  @endforeach
                </ul>
              </div>
            </div>
          @endif
          @if(isset($sidebarHot) && $sidebarHot->count())
            <div class="sidebar-item">
              <div class="sidebar-title">{{ __('front::common.read_more') }}</div>
              <div class="sidebar-list">
                <ul>
                  @foreach($sidebarHot as $hot)
                    <li><a href="{{ $hot->url }}">{{ $hot->translation->title ?? '' }}</a></li>
                  @endforeach
                </ul>
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
@endsection
