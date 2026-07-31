<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <base href="{{ front_route('home.index') }}">
  <title>@yield('title', system_setting('meta_title', 'Apex Precision - Precision Parts, One-Stop Manufacturing'))</title>
  <meta name="keywords" content="@yield('keywords', system_setting('meta_keywords', 'CNC machining, precision parts, sheet metal, die casting, OEM'))">
  <meta name="description" content="@yield('description', system_setting('meta_description', 'Apex Precision delivers one-stop precision parts manufacturing to customers worldwide.'))">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="shortcut icon" href="{{ image_origin(system_setting('favicon', 'images/favicon.png')) }}">
  <link rel="stylesheet" href="{{ theme_asset('css/bootstrap.css') }}">
  <link rel="stylesheet" href="{{ theme_asset('css/app.css') }}">
  <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  @stack('header')
</head>

<body class="@yield('body-class')">
  @include('layouts.header')

  @yield('content')

  @include('layouts.footer')

  @stack('footer')
</body>
</html>
