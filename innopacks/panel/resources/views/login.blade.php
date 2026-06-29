<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <base href="{{ panel_route('home.index') }}">
  <title>@yield('title', __('panel/login.title'))</title>
  <meta name="keywords" content="@yield('keywords', __('panel/login.keywords'))">
  <meta name="description" content="@yield('description', __('panel/login.description'))">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}">
  <link rel="stylesheet" href="{{ asset('build/panel/css/app.css') }}">
  <script src="{{ asset('vendor/jquery/jquery-3.7.1.min.js') }}"></script>
  <script src="{{ asset('build/panel/js/app.js') }}"></script>
  <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('vendor/layer/3.5.1/layer.js') }}"></script>
  @stack('header')
</head>
<body class="page-login">
<div class="login-container">
  <div class="login-left">
    <div class="login-hero">
      <h1 class="hero-name">{{ system_setting('panel_name') ?: 'InnoCMS' }}</h1>
      <p class="hero-tagline">@yield('hero_tagline', __('panel/login.tagline'))</p>
    </div>
    <div class="login-decoration">
      <div class="deco-circle deco-circle-1"></div>
      <div class="deco-circle deco-circle-2"></div>
      <div class="deco-circle deco-circle-3"></div>
      <div class="deco-dots"></div>
    </div>
  </div>
  <div class="login-right">
    <div class="login-header">
      <div class="login-brand">
        @if (system_setting('panel_logo'))
          <img src="{{ image_origin(system_setting('panel_logo')) }}" alt="Logo" class="brand-logo">
        @else
          <img src="{{ image_origin('images/logo-panel.svg') }}" alt="Logo" class="brand-logo">
        @endif
      </div>
      <div class="locale-wrap">
        <div class="d-flex align-items-center locale">
          <div class="wh-20 me-2"><img src="{{ image_origin('images/flags/'. panel_locale_code().'.svg') }}" class="img-fluid"></div>
          <span class="">{{ current_panel_locale()['name'] }} <i class="bi bi-chevron-down"></i></span>
          <ul class="dropdown-menu">
            @foreach (panel_locales() as $locale)
            <li>
              <a class="dropdown-item d-flex" href="{{ panel_route('login.index', ['locale'=> $locale['code']]) }}">
                <div class="wh-20 me-2"><img src="{{ image_origin($locale['image']) }}" class="img-fluid"></div>
                {{ $locale['name'] }}
              </a>
            </li>
            @endforeach
          </ul>
        </div>
      </div>
    </div>
    <div class="login-form-wrap">
      <div class="login-form-inner">
        <h2 class="login-title">{{ __('panel/login.login_index') }}</h2>
        <p class="login-subtitle">{{ __('panel/login.description') }}</p>

        <form action="{{ panel_route('login.store') }}" method="post">
          @csrf

          <div class="form-group mb-3">
            <label for="email-input" class="form-label">{{ __('panel/login.email') }}</label>
            <div class="input-icon-wrap">
              <i class="bi bi-envelope"></i>
              <input type="text" name="email" class="form-control" id="email-input" value="{{ old('email', $admin_email ?? '') }}" placeholder="{{ __('panel/login.email') }}" autocomplete="email">
            </div>
            @error('email')
            <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <div class="form-group mb-4">
            <label for="password-input" class="form-label">{{ __('panel/login.password') }}</label>
            <div class="input-icon-wrap">
              <i class="bi bi-lock"></i>
              <input type="password" name="password" class="form-control" id="password-input" value="{{ old('password', $admin_password ?? '') }}" placeholder="{{ __('panel/login.password') }}" autocomplete="current-password">
            </div>
            @error('password')
            <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          @if (session('error'))
            <div class="alert alert-danger">
              {{ session('error') }}
            </div>
          @endif

          <button type="submit" class="btn btn-login">{{ __('panel/login.btn_login') }}</button>
        </form>
      </div>
      <p class="text-center text-footer">
        {!! innocms_brand_link() !!}
        {{ innocms_version() }} &copy; {{ date('Y') }}
      </p>
    </div>
  </div>
</div>
</body>
</html>
