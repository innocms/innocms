@if($links->count())
  <section class="module-blogroll">
    <div class="container">
      <ul class="inform-wrap">
        <li>
          <div>
            <span><i class="bi bi-diamond-fill"></i> 友情链接：</span>
            @foreach($links as $link)
              @if($link->logo)
                <a href="{{ $link->url }}" target="_blank">
                  <img src="{{ image_resize($link->logo) }}">
                </a>
              @else
                <a href="{{ $link->url }}" target="_blank">{{ $link->name }}</a>
              @endif
            @endforeach
          </div>
        </li>
      </ul>
    </div>
  </section>
@endif