@if($links->count())
<section class="module-blogroll">
  <div class="container">
    @php
      $linksLogo = $links->filter(function($link) {
        return $link->logo;
      });

      $linksNoLogo = $links->filter(function($link) {
        return !$link->logo;
      });
    @endphp

    @if ($linksNoLogo->count())
    <ul class="inform-wrap mb-4 list-unstyled d-flex flex-wrap align-items-center">
      <li><span><i class="bi bi-link-45deg"></i> 友情链接：</span></li>
      @foreach($linksLogo as $link)
      <li class="me-2">
        @if ($link->logo)
          <a href="{{ $link->url }}" target="_blank">
            <img src="{{ $link->logo }}" alt="{{ $link->name }}" title="{{ $link->name }}" class="img-fluid w-max-200 h-max-100">
          </a>
        @else
          <a href="{{ $link->url }}" target="_blank">{{ $link->name }}</a>
        @endif
      </li>
      @endforeach
    </ul>
    @endif

    @if ($linksNoLogo->count())
    <ul class="inform-wrap mb-4 list-unstyled d-flex flex-wrap align-items-center">
      <li><span><i class="bi bi-link-45deg"></i> 友情链接：</span></li>
      @foreach($linksNoLogo as $link)
      <li class="me-2">
        @if ($link->logo)
          <a href="{{ $link->url }}" target="_blank">
            <img src="{{ $link->logo }}" alt="{{ $link->name }}" title="{{ $link->name }}" class="img-fluid w-max-200 h-max-100">
          </a>
        @else
          <a href="{{ $link->url }}" target="_blank">{{ $link->name }}</a>
        @endif
      </li>
      @endforeach
    </ul>
    @endif
  </div>
</section>
@endif