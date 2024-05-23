<div class="header-box">
  <div class="container d-flex justify-content-between align-items-center">
    <div class="logo">
      <h1 class="mb-0">
        <a href="{{ front_route('home.index') }}">
          <img src="{{ image_origin(system_setting('front_logo', 'images/logo.svg')) }}" class="img-fluid">
        </a>
      </h1>
    </div>
    <div class="header-menu">
      <nav class="navbar navbar-expand-md navbar-light">
        @hookupdate('layouts.header.menu')
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link {{ equal_route_name('home.index') ? 'active' : '' }}" aria-current="page"
               href="{{ front_route('home.index') }}">首页</a>
          </li>
          @foreach($menus as $menu)
            @if($menu['children'] ?? [])
              <li class="nav-item">
                <div class="dropdown">
                  <a class="nav-link {{ equal_url($menu['url']) ? 'active' : '' }}"
                     href="{{ $menu['url'] }}">{{ $menu['name'] }}</a>
                  <ul class="dropdown-menu">
                    @foreach($menu['children'] as $child)
                      <li><a class="dropdown-item" href="{{ $child['url'] }}">{{ $child['name'] }}</a></li>
                    @endforeach
                  </ul>
                </div>
              </li>
            @else
              <li class="nav-item">
                <a class="nav-link {{ equal_url($menu['url']) ? 'active' : '' }}"
                   href="{{ $menu['url'] }}">{{ $menu['name'] }}</a>
              </li>
            @endif
          @endforeach
        </ul>
        @endhookupdate
      </nav>

      <div class="offcanvas offcanvas-start" tabindex="-1" id="mobile-menu-offcanvas">
        <div class="offcanvas-header">
          <div class="mb-logo"><img src="{{ asset('images/logo.svg') }}" class="img-fluid"></div>
        </div>
        <div class="close-offcanvas" data-bs-dismiss="offcanvas"><i class="bi bi-chevron-compact-left"></i></div>
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link {{ equal_route_name('home.index') ? 'active' : '' }}" aria-current="page"
               href="{{ front_route('home.index') }}">首页</a>
          </li>
          @foreach($menus as $menu)
            @if($menu['children'] ?? [])
              <li class="nav-item">
                <div class="dropdown">
                  <a class="nav-link {{ equal_url($menu['url']) ? 'active' : '' }}"
                     href="{{ $menu['url'] }}">{{ $menu['name'] }}</a>
                  <ul class="dropdown-menu">
                    @foreach($menu['children'] as $child)
                      <li><a class="dropdown-item" href="{{ $child['url'] }}">{{ $child['name'] }}</a></li>
                    @endforeach
                  </ul>
                </div>
              </li>
            @else
              <li class="nav-item">
                <a class="nav-link {{ equal_url($menu['url']) ? 'active' : '' }}"
                   href="{{ $menu['url'] }}">{{ $menu['name'] }}</a>
              </li>
            @endif
          @endforeach
        </ul>
      </div>
      <div class="mb-icon" data-bs-toggle="offcanvas" data-bs-target="#mobile-menu-offcanvas"
           aria-controls="offcanvasExample"><i class="bi bi-list"></i></div>
    </div>
  </div>
</div>