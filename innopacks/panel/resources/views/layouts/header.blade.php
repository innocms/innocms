<div class="header-box d-flex justify-content-between align-items-center px-3">
  <div></div>
  <div class="d-flex">

    <div class="header-item dropdown d-flex align-items-center d-none d-lg-flex">
      <div class="wh-20 me-2"><img src="{{ image_origin('images/flags/'. panel_locale_code().'.svg') }}" class="img-fluid"></div>
      <span class="">{{ current_panel_locale()['name'] }} <i class="bi bi-chevron-down"></i></span>
      <ul class="dropdown-menu">
        @foreach (panel_locales() as $locale)
          <li>
            <a class="dropdown-item d-flex" href="{{ panel_route('locale.switch', ['code'=> $locale['code']]) }}">
              <div class="wh-20 me-2"><img src="{{ image_origin($locale['image']) }}" class="img-fluid"></div>
              {{ $locale['name'] }}
            </a>
          </li>
        @endforeach
      </ul>
    </div>

    <div class="header-item dropdown d-flex align-items-center">
      <span class=""><i class="bi bi-person-circle me-1"></i>{{ current_admin()->name }} <i class="bi bi-chevron-down ms-1"></i></span>

      <ul class="dropdown-menu">
        <li>
          <a class="dropdown-item" href="{{ route('front.home.index') }}" target="_blank">
            <i class="bi bi-window-stack me-2"></i>{{ __('panel/dashboard.frontend') }}
          </a>
        </li>
        <li>
          <a class="dropdown-item" href="{{ panel_route('account.index') }}">
            <i class="bi bi-person me-2"></i>{{ __('panel/dashboard.profile') }}
          </a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
          <a class="dropdown-item text-danger" href="{{ panel_route('logout.index') }}">
            <i class="bi bi-box-arrow-right me-2"></i>{{ __('panel/dashboard.sign_out') }}
          </a>
        </li>
      </ul>
    </div>
  </div>
</div>