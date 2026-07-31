@php
  $allLocales = locales();
  $currentLocaleCode = front_locale_code();
  $currentLocale = $allLocales->firstWhere('code', $currentLocaleCode);
  $currentLocaleName = $currentLocale?->name ?? $currentLocaleCode;
  $contactUrl = has_front_route('pages.slug_show') ? front_route('pages.slug_show', ['slug' => 'contact']) : null;
@endphp

<div class="site-header">
<div class="header-topbar">
  <div class="container d-flex justify-content-between align-items-center">
    <div class="topbar-left">
      @if($phone = system_setting('telephone'))
        <a href="tel:{{ preg_replace('/[^0+]/', '', $phone) }}"><i class="bi bi-telephone-fill"></i> {{ $phone }}</a>
      @endif
      @if($email = system_setting('email'))
        <a href="mailto:{{ $email }}" class="ms-4"><i class="bi bi-envelope-fill"></i> {{ $email }}</a>
      @endif
    </div>
    <div class="topbar-right">
      @if($allLocales->count() > 1)
        <div class="topbar-lang dropdown">
          <button type="button" class="topbar-lang__btn" data-bs-toggle="dropdown" aria-expanded="false">
            @if($currentLocale?->image)
              <img class="topbar-lang__flag" src="{{ image_origin($currentLocale->image) }}" alt="">
            @endif
            <span>{{ $currentLocaleName }}</span>
            <i class="bi bi-chevron-down topbar-lang__caret" aria-hidden="true"></i>
          </button>
          <ul class="dropdown-menu dropdown-menu-end topbar-lang__menu">
            @foreach($allLocales as $locale)
              <li>
                <a class="dropdown-item topbar-lang__item {{ $locale->code === $currentLocaleCode ? 'is-active' : '' }}"
                   href="{{ front_route('locales.switch', ['code' => $locale->code]) }}">
                  @if($locale->image)
                    <img class="topbar-lang__flag" src="{{ image_origin($locale->image) }}" alt="">
                  @endif
                  <span>{{ $locale->name }}</span>
                </a>
              </li>
            @endforeach
          </ul>
        </div>
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
              <li class="nav-item has-mega">
                <a class="nav-link {{ equal_url($menu['url']) ? 'active' : '' }}"
                   href="{{ $menu['url'] }}" aria-haspopup="true" aria-expanded="false">{{ $menu['name'] }}
                  <i class="bi bi-chevron-down has-mega__caret" aria-hidden="true"></i>
                </a>
                <ul class="mega-panel">
                  @foreach($menu['children'] as $child)
                    <li><a class="mega-panel__item" href="{{ $child['url'] }}">{{ $child['name'] }}</a></li>
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
              <li class="nav-item has-children">
                <a class="nav-link {{ equal_url($menu['url']) ? 'active' : '' }}"
                   href="{{ $menu['url'] }}">{{ $menu['name'] }}</a>
                <ul class="sub-menu">
                  @foreach($menu['children'] as $child)
                    <li><a class="nav-link {{ equal_url($child['url']) ? 'active' : '' }}" href="{{ $child['url'] }}">{{ $child['name'] }}</a></li>
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
