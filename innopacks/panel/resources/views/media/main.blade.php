@prepend('header')
  <script src="{{ asset('vendor/vue/3.5/vue.global' . (config('app.debug') ? '' : '.prod') . '.js') }}"></script>
  <script src="{{ asset('vendor/element-plus/index.full.js') }}"></script>
  <link rel="stylesheet" href="{{ asset('vendor/element-plus/index.css') }}">
  <script src="{{ asset('vendor/element-plus/icons.min.js') }}"></script>
  @if(str_starts_with(panel_locale_code(), 'zh'))
  <script src="{{ asset('vendor/element-plus/zh-cn.js') }}"></script>
  @endif
@endprepend

@prepend('header')
  @php($enabledDrivers = $enabled_drivers ?? ['local'])
  @php($mediaConfig = $config ?? [])
  <meta name="api-token" content="{{ auth()->user()->api_token ?? '' }}">
  <script>
    window.mediaConfig = Object.freeze({
      driver: '{{ $mediaConfig['driver'] ?? 'local' }}',
      endpoint: '{{ $mediaConfig['endpoint'] ?? '' }}',
      bucket: '{{ $mediaConfig['bucket'] ?? '' }}',
      baseUrl: '{{ $mediaConfig['baseUrl'] ?? config('app.url') }}',
      enabledDrivers: @json($enabledDrivers),
      multiple: {{ ($multiple ?? false) ? 'true' : 'false' }},
      type: '{{ $type ?? 'all' }}',
      uploadMaxFileSize: '{{ $uploadMaxFileSize ?? "unknown" }}',
      postMaxSize: '{{ $postMaxSize ?? "unknown" }}'
    });
  </script>
@endprepend

@include('panel::media.partials._header')

@include('panel::media.partials._content')

@include('panel::media.partials._footer')
