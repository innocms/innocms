<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <base href="{{ front_route('home.index') }}">
  <title>@yield('title', system_setting_locale('meta_title', 'InnoCMS - 外贸 B2B 独立站建站平台'))</title>
  <meta name="keywords" content="@yield('keywords', system_setting_locale('meta_keywords', 'InnoCMS, B2B 独立站, 外贸建站, 品牌出海, 询盘独立站, 跨境电商, 开源, 多语言'))">
  <meta name="description" content="@yield('description', system_setting_locale('meta_description', 'InnoCMS 是一款专为外贸 B2B 独立站与品牌出海打造的高性能建站系统，基于 Laravel，模块化架构、Hook 插件、主题一键切换、内置多语言，快速搭建高转化的询盘独立站。'))">
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