@hookinsert('layouts.footer.top')

@php
  $footerMenus    = $menus ?? [];
  $catalogMenus   = array_values(array_filter($footerMenus, fn ($m) => ! empty($m['children'])));
  $pageMenus      = array_values(array_filter($footerMenus, fn ($m) => empty($m['children'])));
@endphp

<footer class="footer-box">
  <div class="container">
    <div class="footer-main">
      <div class="row">
        <div class="col-lg-4 mb-4 mb-lg-0">
          <div class="footer-logo">
            <img src="{{ asset('images/logo-white.png') }}" class="img-fluid" alt="Apex Precision">
          </div>
          <p class="footer-about">{{ __('theme-precision::footer.about') }}</p>
          <div class="footer-certs">
            <i class="bi bi-patch-check-fill"></i> {{ __('theme-precision::footer.certs') }}
          </div>
        </div>

        @foreach($catalogMenus as $catalogMenu)
          <div class="col-6 col-lg-2 mb-4 mb-lg-0">
            <div class="footer-title"><a href="{{ $catalogMenu['url'] }}">{{ $catalogMenu['name'] }}</a></div>
            <ul class="footer-links">
              @foreach($catalogMenu['children'] as $child)
                <li><a href="{{ $child['url'] }}">{{ $child['name'] }}</a></li>
              @endforeach
            </ul>
          </div>
        @endforeach

        <div class="col-6 col-lg-2 mb-4 mb-lg-0">
          <div class="footer-title">{{ __('theme-precision::footer.quick_links') }}</div>
          <ul class="footer-links">
            @foreach($pageMenus as $pageMenu)
              <li><a href="{{ $pageMenu['url'] }}">{{ $pageMenu['name'] }}</a></li>
            @endforeach
          </ul>
        </div>

        <div class="col-lg-2">
          <div class="footer-title">{{ __('theme-precision::footer.contact_us') }}</div>
          <ul class="footer-contact">
            @if($phone = system_setting('contact_phone'))
              <li><i class="bi bi-telephone-fill"></i> {{ $phone }}</li>
            @endif
            @if($email = system_setting('contact_email'))
              <li><i class="bi bi-envelope-fill"></i> {{ $email }}</li>
            @endif
            @if($address = system_setting('contact_address'))
              <li><i class="bi bi-geo-alt-fill"></i> {{ $address }}</li>
            @endif
          </ul>
        </div>
      </div>
    </div>

    <div class="footer-bottom">
      <div class="row align-items-center">
        <div class="col-md-6">
          <div class="left-links">Powered By <a href="https://www.innocms.com" target="_blank">INNOCMS</a></div>
        </div>
        <div class="col-md-6 copyright-text">
          Apex Precision &copy; {{ date('Y') }} {{ __('theme-precision::footer.rights') }}
          @if(system_setting('icp_number'))
            <a href="https://beian.miit.gov.cn" class="ms-2" target="_blank">{{ system_setting('icp_number') }}</a>
          @endif
        </div>
      </div>
    </div>
  </div>
</footer>

@hookinsert('layouts.footer.bottom')

@if (system_setting('js_code'))
  {!! system_setting('js_code') !!}
@endif
