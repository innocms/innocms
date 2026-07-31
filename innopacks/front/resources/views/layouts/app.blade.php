<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <base href="{{ front_route('home.index') }}">
  <title>@yield('title', system_setting_locale('meta_title', 'InnoCMS - 轻量级企业官网建站系统'))</title>
  <meta name="keywords" content="@yield('keywords', system_setting_locale('meta_keywords', 'InnoCMS, CMS, 企业官网, 快速建站, 开源, 多语言'))">
  <meta name="description" content="@yield('description', system_setting_locale('meta_description', 'InnoCMS 是一款专为企业官网快速建站而设计的轻量级内容管理系统，简洁、高效、易用，并支持通过插件便捷扩展。'))">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="shortcut icon" href="{{ image_origin(system_setting('favicon', 'images/favicon.png')) }}">   
  <link rel="stylesheet" href="{{ asset('themes/default/css/app.css') }}">
  <script src="{{ asset('themes/default/js/app.js') }}"></script>
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