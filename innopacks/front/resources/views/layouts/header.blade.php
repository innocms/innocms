@php
  $allLocales = locales();
  $currentLocaleCode = front_locale_code();
  $contactUrl = has_front_route('pages.slug_show') ? front_route('pages.slug_show', ['slug' => 'contact']) : null;
@endphp

<div class="site-header">
<div class="header-topbar">
  <div class="container d-flex justify-content-between align-items-center">
    <div class="topbar-left">
      <a href="tel:{{ preg_replace('/[^0-9+]/', '', __('front::common.contact_phone_v')) }}"><i class="bi bi-telephone-fill"></i> {{ __('front::common.contact_phone_v') }}</a>
      <a href="mailto:{{ __('front::common.contact_email_v') }}" class="ms-4"><i class="bi bi-envelope-fill"></i> {{ __('front::common.contact_email_v') }}</a>
    </div>
    <div class="topbar-right">
      <span class="me-3"><i class="bi bi-geo-alt-fill"></i> {{ __('front::common.contact_address_v') }}</span>
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
               href="{{ front_route('home.index') }}">{{ __('front::common.home') }}</a>
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
          <i class="bi bi-send-fill me-1"></i> {{ __('front::common.get_quote') }}
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
               href="{{ front_route('home.index') }}">{{ __('front::common.home') }}</a>
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
          @if($contactUrl)
            <li class="nav-item p-3">
              <a href="{{ $contactUrl }}" class="btn btn-accent w-100">{{ __('front::common.get_quote') }}</a>
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
