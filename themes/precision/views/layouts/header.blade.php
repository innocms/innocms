@php
  $allLocales = locales();
  $currentLocaleCode = front_locale_code();
  $contactUrl = has_front_route('pages.slug_show') ? front_route('pages.slug_show', ['slug' => 'contact']) : null;
@endphp

<div class="site-header">
<div class="header-topbar">
  <div class="container d-flex justify-content-between align-items-center">
    <div class="topbar-left">
      @if($phone = system_setting('contact_phone'))
        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}"><i class="bi bi-telephone-fill"></i> {{ $phone }}</a>
      @endif
      @if($email = system_setting('contact_email'))
        <a href="mailto:{{ $email }}" class="ms-4"><i class="bi bi-envelope-fill"></i> {{ $email }}</a>
      @endif
    </div>
    <div class="topbar-right">
      @if($address = system_setting('contact_address'))
        <span class="me-3"><i class="bi bi-geo-alt-fill"></i> {{ $address }}</span>
      @endif
      @if($allLocales->count() > 1)
        <span class="topbar-lang">
          <i class="bi bi-globe2"></i>
          @foreach($allLocales as $locale)
            <a href="{{ front_route('locales.switch', ['code' => $locale->code]) }}"
               class="{{ $locale->code === $currentLocaleCode ? 'active' : '' }}">{{ $locale->name }}</a>
            @unless($loop->last)<span class="divider">/</span>@endunless
          @endforeach
        </span>
      @endif
    </div>
  </div>
</div>

<div class="header-box">
  <div class="container d-flex justify-content-between align-items-center">
    <div class="logo">
      <h1 class="mb-0">
        <a href="{{ front_route('home.index') }}">
          <img src="{{ image_origin(system_setting('front_logo', 'images/logo.png')) }}" class="img-fluid">
        </a>
      </h1>
    </div>
    <div class="header-menu d-flex align-items-center">
      <nav class="navbar navbar-expand-md navbar-light">
        @hookupdate('layouts.header.menu')
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link {{ equal_route_name('home.index') ? 'active' : '' }}" aria-current="page"
               href="{{ front_route('home.index') }}">{{ __('theme-precision::header.home') }}</a>
          </li>
          @foreach($menus as $menu)
            @if($menu['children'] ?? [])
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle {{ equal_url($menu['url']) ? 'active' : '' }}" href="{{ $menu['url'] }}"
                   data-bs-toggle="dropdown" role="button" aria-expanded="false">{{ $menu['name'] }}</a>
                <ul class="dropdown-menu">
                  @foreach($menu['children'] as $child)
                    <li><a class="dropdown-item" href="{{ $child['url'] }}">{{ $child['name'] }}</a></li>
                  @endforeach
                </ul>
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

      @if($contactUrl)
        <a href="{{ $contactUrl }}"
           class="btn btn-accent d-none d-lg-inline-flex align-items-center ms-3">
          <i class="bi bi-send-fill me-1"></i> {{ __('theme-precision::header.get_quote') }}
        </a>
      @endif

      <div class="offcanvas offcanvas-start" tabindex="-1" id="mobile-menu-offcanvas">
        <div class="offcanvas-header">
          <div class="mb-logo"><img src="{{ image_origin(system_setting('front_logo', 'images/logo.png')) }}"
                                    class="img-fluid"></div>
        </div>
        <div class="close-offcanvas" data-bs-dismiss="offcanvas"><i class="bi bi-chevron-compact-left"></i></div>
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link {{ equal_route_name('home.index') ? 'active' : '' }}" aria-current="page"
               href="{{ front_route('home.index') }}">{{ __('theme-precision::header.home') }}</a>
          </li>
          @foreach($menus as $menu)
            @if($menu['children'] ?? [])
              <li class="nav-item">
                <a class="nav-link {{ equal_url($menu['url']) ? 'active' : '' }}"
                   href="{{ $menu['url'] }}">{{ $menu['name'] }}</a>
                <ul class="dropdown-menu">
                  @foreach($menu['children'] as $child)
                    <li><a class="dropdown-item" href="{{ $child['url'] }}">{{ $child['name'] }}</a></li>
                  @endforeach
                </ul>
              </li>
            @else
              <li class="nav-item">
                <a class="nav-link {{ equal_url($menu['url']) ? 'active' : '' }}"
                   href="{{ $menu['url'] }}">{{ $menu['name'] }}</a>
              </li>
            @endif
          @endforeach
          @if($contactUrl)
            <li class="nav-item p-3">
              <a href="{{ $contactUrl }}" class="btn btn-accent w-100">{{ __('theme-precision::header.get_quote') }}</a>
            </li>
          @endif
        </ul>
      </div>
      <div class="mb-icon" data-bs-toggle="offcanvas" data-bs-target="#mobile-menu-offcanvas"
           aria-controls="mobile-menu-offcanvas"><i class="bi bi-list"></i></div>
    </div>
  </div>
</div>
</div>
