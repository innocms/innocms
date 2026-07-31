@php
  $sidebarList = [];
  if (! empty($sidebarCatalog)) {
      $children = $sidebarCatalog->children ?? collect();
      if ($children->isNotEmpty()) {
          // Parent catalog: list itself + its children.
          $sidebarList = collect([$sidebarCatalog])->merge($children);
      } elseif ($sidebarCatalog->parent) {
          // Leaf catalog: list its siblings (incl. itself) under the parent.
          $sidebarList = collect([$sidebarCatalog->parent])->merge($sidebarCatalog->parent->children);
      } else {
          $sidebarList = collect([$sidebarCatalog]);
      }
  }
@endphp

<div class="container mt-3 mt-md-5">
  <div class="row">
    <div class="col-12 col-md-9">
      @if ($articles->count())
        <div class="newest-box">
          @foreach($articles as $article)
          <div class="newest-item">
            <div class="item-img">
              <a href="{{ $article->url }}">
                <img src="{{ image_resize($article->translation->image ?? '', 200, 150) }}" class="img-fluid">
              </a>
            </div>
            <div class="item-content d-flex flex-column justify-content-between">
              <div class="content-top">
                <div class="item-title"><a href="{{ $article->url }}">{{ $article->translation->title ?? '' }}</a></div>
                @if ($article->tags->count())
                <div class="newes-tags">
                  <i class="bi bi-tags me-1"></i>
                  <div class="d-flex">
                    @foreach($article->tags as $tag)
                      <a href="{{ front_route('tags.show', ['slug' => $tag->slug]) }}">{{ $tag->translation->name ?? '' }}</a>
                    @endforeach
                  </div>
                </div>
                @endif
                <div class="item-summary">{{ $article->translation->summary ?? '' }}</div>
              </div>
              <div class="item-date text-secondary">
                <span><i class="bi bi-clock"></i> {{ $article->created_at->format('Y-m-d') }}</span>
                <span class="ms-3"><i class="bi bi-eye"></i> {{ $article->viewed }}</span>
              </div>
            </div>
          </div>
          @endforeach
        </div>
        <div class="d-flex justify-content-center mt-4">{{ $articles->links() }}</div>
      @else
        @include('shared.no-data', ['text' => __('front::common.no_data')])
      @endif
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

        @if(isset($tags) && $tags && $tags->count())
          <div class="sidebar-item">
            <div class="sidebar-title">{{ __('front::common.tags') }}</div>
            <div class="sidebar-list">
              <ul>
                @foreach($tags as $tag)
                  <li><a href="{{ front_route('tags.show', ['slug' => $tag->slug ?? '']) }}">{{ $tag->translation->name ?? '' }}</a></li>
                @endforeach
              </ul>
            </div>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

@push('footer')
  <script>
    $(function () {
      $('.search-box button').click(function () {
        var keyword = $('.search-box input').val();
        if (keyword) {
          window.location.href = is.updateQueryStringParameter(window.location.href, 'keyword', keyword);
          return;
        }

        window.location.href = is.removeURLParameters(window.location.href, 'keyword')
      });

      $('.search-box input').keydown(function (e) {
        if (e.keyCode === 13) {
          $('.search-box button').trigger('click');
        }
      });
    });
  </script>
@endpush
