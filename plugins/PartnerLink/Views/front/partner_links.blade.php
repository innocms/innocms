@if($links->count())
  <section class="module-blogroll">
    <div class="container">
      <ul class="inform-wrap">
        <li>
          <div>
            <span><i class="bi bi-diamond-fill"></i> 友情链接：</span>
            @foreach($links as $link)
              <a href="{{ $link->url }}" target="_blank">{{ $link->name }}</a>
            @endforeach
          </div>
        </li>
      </ul>
    </div>
  </section>
@endif