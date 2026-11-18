<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <base href="{{ panel_route('dashboard.index') }}">
  <title>绑定两步验证 - {{ system_setting('panel_name') ?: 'InnoAdmin' }}</title>
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
      <h1 class="hero-name">{{ system_setting('panel_name') ?: 'InnoAdmin' }}</h1>
      <p class="hero-tagline">两步验证（2FA）为后台账号再加固一道锁</p>
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
          <img src="{{ image_origin('images/logo-panel.png') }}" alt="Logo" class="brand-logo">
        @endif
      </div>
    </div>
    <div class="login-form-wrap">
      <div class="login-form-inner">
        <h2 class="login-title">绑定两步验证</h2>
        <p class="login-subtitle">后台已启用强制两步验证。用身份验证器 App（Google Authenticator / 1Password 等）扫描二维码或手动添加密钥，输入 6 位动态码完成绑定。</p>

        @if (session('error'))
          <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="text-center mb-3">
          <img src="{{ $qr }}" alt="TOTP 二维码" width="180" height="180"
               class="img-fluid border rounded-3 bg-white p-2">
        </div>

        <details class="mb-3">
          <summary class="text-muted small">无法扫码？手动复制密钥或 otpauth 链接</summary>
          <div class="input-group mt-2">
            <input type="text" class="form-control font-monospace" value="{{ $secret }}" readonly id="totp-secret">
            <button type="button" class="btn btn-outline-secondary"
                    onclick="navigator.clipboard.writeText(document.getElementById('totp-secret').value).then(() => this.textContent = '已复制')">复制</button>
          </div>
          <textarea class="form-control font-monospace small mt-2" rows="2" readonly onclick="this.select()">{{ $otpauth }}</textarea>
        </details>

        <form method="POST" action="{{ panel_route('login.enable') }}">
          @csrf
          <div class="form-group mb-4">
            <label for="totp-code" class="form-label">6 位动态码</label>
            <input type="text" name="code" id="totp-code" class="form-control form-control-lg text-center"
                   inputmode="numeric" autocomplete="one-time-code" maxlength="7"
                   pattern="[0-9\- ]*" placeholder="000000" required autofocus>
          </div>
          <button type="submit" class="btn btn-login w-100">确认绑定并登录</button>
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
