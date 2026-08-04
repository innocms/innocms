{{--
  Shared article sidebar (list + detail pages).
  Expects: $sidebarCatalog (Catalog|null), $tags, optional $sidebarHot, optional $currentCatalogId
--}}
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
  $activeCatalogId = $currentCatalogId ?? ($sidebarCatalog->id ?? null);
@endphp

<div class="newes-sidebar">
  <div class="search-box">
    <div class="input-group">
      <input type="text" class="form-control" value="{{ request('keyword') }}" placeholder="{{ __('front::common.search_ph') }}">
      <button class="btn btn-primary" type="button"><i class="bi bi-search"></i></button>
    </div>
  </div>

  @if($sidebarList)
    <div class="sidebar-item">
      <div class="sidebar-title">{{ __('front::common.categories') }}</div>
      <ul class="sidebar-cat-list">
        @foreach($sidebarList as $cat)
          <li>
            <a href="{{ $cat->url }}" class="{{ $cat->id === $activeCatalogId ? 'active' : '' }}">
              <i class="bi bi-chevron-right"></i>{{ $cat->translation->title ?? '' }}
            </a>
          </li>
        @endforeach
      </ul>
    </div>
  @endif

  @if(isset($sidebarHot) && $sidebarHot->count())
    <div class="sidebar-item">
      <div class="sidebar-title">{{ __('front::common.hot_articles') }}</div>
      <div class="sidebar-hot-list">
        @foreach($sidebarHot as $hot)
          <a href="{{ $hot->url }}" class="hot-item">
            <div class="hot-item-img">
              <img src="{{ image_resize($hot->translation->image ?? '', 128, 128) }}" alt="{{ $hot->translation->title ?? '' }}" loading="lazy">
            </div>
            <div class="hot-item-info">
              <div class="hot-item-title">{{ $hot->translation->title ?? '' }}</div>
              <div class="hot-item-meta">
                <span><i class="bi bi-clock"></i>{{ $hot->created_at->format('Y-m-d') }}</span>
                <span><i class="bi bi-eye"></i>{{ $hot->viewed }}</span>
              </div>
            </div>
          </a>
        @endforeach
      </div>
    </div>
  @endif

  @if(isset($tags) && $tags && $tags->count())
    <div class="sidebar-item">
      <div class="sidebar-title">{{ __('front::common.tags') }}</div>
      <div class="sidebar-tag-list">
        @foreach($tags as $tag)
          <a href="{{ $tag->url }}" class="tag-chip">{{ $tag->translation->name ?? '' }}</a>
        @endforeach
      </div>
    </div>
  @endif
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
