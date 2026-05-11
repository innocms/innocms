@extends('panel::layouts.app')
@section('body-class', 'theme')

@section('title', __('panel/menu.themes'))

@section('content')
  <div class="card h-min-600">
    <div class="card-body p-4">
      <div class="d-flex flex-wrap align-items-center gap-2 text-muted small mb-3 pb-3 border-bottom">
        <span><i class="bi bi-palette me-1"></i>{{ __('panel/themes.available_themes_count', ['count' => $themes_count ?? 0]) }}</span>
        <span class="text-secondary">·</span>
        <span><i class="bi bi-collection-play me-1"></i>{{ __('panel/themes.themes_stat_demo', ['count' => $themes_with_demo_count ?? 0]) }}</span>
        <span class="text-secondary">·</span>
        <span>{{ __('panel/themes.themes_stat_current') }}: {{ $selected_theme_name ?? __('panel/themes.themes_stat_none') }}</span>
      </div>

      @if(!empty($errors))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
          <h6 class="alert-heading mb-2">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ __('panel/themes.error_theme_validation') }}
          </h6>
          <ul class="mb-0 ps-3">
            @foreach($errors as $error)
              <li><strong>{{ $error['name'] }}</strong> ({{ $error['folder'] }}): {{ $error['error'] }}</li>
            @endforeach
          </ul>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      @if($themes && $themes->isNotEmpty())
        <div class="themes-wrap">
          <div class="row g-4">
            @foreach($themes as $theme)
              <div class="col-6 col-lg-4 col-xxl-3">
                <div class="card themes-item overflow-hidden h-100 border @if(!empty($theme['selected'])) border-primary shadow-sm @endif">
                  <div class="ratio ratio-4x3 border-bottom bg-light">
                    <img src="{{ theme_image($theme['preview'] ?? '', $theme['code'] ?? '', 900, 600) }}"
                         class="object-fit-cover"
                         alt="{{ $theme['name'] ?? '' }}">
                  </div>
                  <div class="card-body d-flex flex-column">
                    <h6 class="fw-semibold mb-1">{{ $theme['name'] ?? '' }}</h6>
                    @if(!empty($theme['version']))
                      <div class="text-muted small mb-2"><i class="bi bi-tag me-1"></i>{{ $theme['version'] }}</div>
                    @endif
                    @if(!empty($theme['description']))
                      <p class="text-muted small flex-grow-1 mb-3" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;" title="{{ strip_tags($theme['description']) }}">{{ \Illuminate\Support\Str::limit(strip_tags($theme['description']), 120) }}</p>
                    @endif
                    @if(!empty($theme['has_demo']))
                      <div class="mb-2">
                        <span class="badge rounded-pill text-bg-info">{{ __('panel/themes.theme_badge_demo') }}</span>
                      </div>
                    @endif
                    <div class="text-muted small mb-2">{{ $theme['code'] ?? '' }}</div>
                    <div class="d-flex flex-wrap align-items-center gap-2 justify-content-between mt-auto">
                      <div class="d-flex flex-wrap gap-1">
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                data-bs-target="#themeDetailModal-{{ $theme['code'] }}">
                          {{ __('panel/themes.view_detail') }}
                        </button>
                        @if(!empty($theme['has_demo']))
                          <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                  data-bs-target="#themeImportModal-{{ $theme['code'] }}">
                            {{ __('panel/themes.import_demo_data') }}
                          </button>
                        @endif
                      </div>
                      @include('panel::shared.list_switch', [
                        'value' => $theme['selected'] ?? false,
                        'url' => panel_route('themes.active', ['code' => $theme['code']]),
                        'reload' => true,
                      ])
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      @else
        <x-common-no-data :text="__('panel/themes.no_custom_theme')" />
      @endif

      @if($themes && $themes->isNotEmpty())
        @foreach($themes as $theme)
          @include('panel::themes.modals.theme-demo-modals', ['theme' => $theme])
        @endforeach
      @endif
    </div>
  </div>
@endsection

@push('footer')
  <script>
    $(function () {
      $(document).on('click', '.btn-theme-demo-import-submit', function () {
        const code = $(this).data('theme-code');
        const url = $(this).data('url');
        const clear = $('#theme-import-clear-' + code).prop('checked') ? 1 : 0;
        axios.post(url, { clear_default_catalog: clear }).then(function (res) {
          inno.msg(res.message);
          const el = document.getElementById('themeImportModal-' + code);
          if (el && window.bootstrap) {
            const instance = bootstrap.Modal.getInstance(el);
            instance && instance.hide();
          }
          location.reload();
        }).catch(function (err) {
          const msg = (err.response && err.response.data && err.response.data.message) ? err.response.data.message : err.message;
          inno.msg(msg);
        });
      });
    });
  </script>
@endpush
