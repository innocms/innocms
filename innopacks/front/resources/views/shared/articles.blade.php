<section class="articles-list-section">
<div class="container">
  <div class="row">
    <div class="col-12 col-md-9">
      @if ($articles->count())
        <div class="newest-box">
          @foreach($articles as $article)
          <div class="newest-item">
            <div class="item-img">
              <a href="{{ $article->url }}">
                <img src="{{ image_resize($article->translation->image ?? '', 400, 225) }}" class="img-fluid">
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
                      <a href="{{ $tag->url }}">{{ $tag->translation->name ?? '' }}</a>
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
      @include('shared.articles-sidebar')
    </div>
  </div>
</div>
</section>
