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
            <img src="{{ asset('images/logo-white.png') }}" class="img-fluid" alt="InnoCMS">
          </div>
          <p class="footer-about">{{ __('front::common.footer_about') }}</p>
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
          <div class="footer-title">{{ __('front::common.footer_quick_links') }}</div>
          <ul class="footer-links">
            @foreach($pageMenus as $pageMenu)
              <li><a href="{{ $pageMenu['url'] }}">{{ $pageMenu['name'] }}</a></li>
            @endforeach
          </ul>
        </div>

        <div class="col-lg-2">
          <div class="footer-title">{{ __('front::common.footer_contact') }}</div>
          <ul class="footer-contact">
            @if($phone = system_setting('telephone'))
              <li><i class="bi bi-telephone-fill"></i> {{ $phone }}</li>
            @endif
            @if($email = system_setting('email'))
              <li><i class="bi bi-envelope-fill"></i> {{ $email }}</li>
            @endif
            @if($address = system_setting_locale('address'))
              <li><i class="bi bi-geo-alt-fill"></i> {{ $address }}</li>
            @endif
          </ul>
        </div>
      </div>
    </div>

    @php
      $funnlinkProducts = [
          ['name' => 'InnoCMS',    'url' => 'https://www.innocms.com'],
          ['name' => 'InnoShop',    'url' => 'https://www.innoshop.cn'],
          ['name' => 'InnoCRM',     'url' => 'https://www.innocrm.com.cn'],
          ['name' => 'InnoCard',    'url' => 'https://www.innocard.cn'],
      ];
    @endphp
    <div class="footer-bottom">
      <div class="footer-bottom__row">
        <div class="footer-bottom__company">
          <a class="footer-bottom__company-name" href="https://www.funnlink.cn" target="_blank" rel="noopener">
            {{ __('front::common.company_name') }}
          </a>
        </div>
        <ul class="footer-bottom__products">
          @foreach($funnlinkProducts as $product)
            <li><a href="{{ $product['url'] }}" target="_blank" rel="noopener">{{ $product['name'] }}</a></li>
          @endforeach
        </ul>
      </div>
      <div class="footer-bottom__row footer-bottom__row--legal">
        <div class="copyright-text">
          &copy; {{ date('Y') }} {{ __('front::common.footer_rights') }}
          @if(system_setting('icp_number'))
            <a href="https://beian.miit.gov.cn" class="ms-2" target="_blank">{{ system_setting('icp_number') }}</a>
          @endif
        </div>
        <div class="powered-by">Powered By <a href="https://www.innocms.com" target="_blank" rel="noopener">INNOCMS</a></div>
      </div>
    </div>
  </div>
</footer>

@hookinsert('layouts.footer.bottom')

@if (system_setting('js_code'))
  {!! system_setting('js_code') !!}
@endif
