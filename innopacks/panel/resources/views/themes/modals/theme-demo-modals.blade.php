{{-- Detail: all valid themes --}}
<div class="modal fade" id="themeDetailModal-{{ $theme['code'] }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ $theme['name'] }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-5">
            <div class="ratio ratio-4x3 border rounded overflow-hidden bg-light">
              <img src="{{ theme_image($theme['preview'] ?? '', $theme['code'] ?? '', 640, 480) }}"
                   class="object-fit-cover w-100 h-100"
                   alt="{{ $theme['name'] ?? '' }}">
            </div>
          </div>
          <div class="col-md-7">
            @if(!empty($theme['has_demo']))
              <span class="badge text-bg-info mb-2">{{ __('panel/themes.theme_badge_demo') }}</span>
            @endif
            <div class="small text-muted">{{ $theme['code'] }}
              @if(!empty($theme['version']))
                · v{{ $theme['version'] }}
              @endif
            </div>
            @if(is_array($theme['author'] ?? null))
              <div class="small text-muted mt-1">
                {{ $theme['author']['name'] ?? '' }}
                @if(!empty($theme['author']['email']))
                  <span class="ms-1">({{ $theme['author']['email'] }})</span>
                @endif
              </div>
            @endif
            @if(!empty($theme['description']))
              <p class="mt-3 mb-0">{{ strip_tags($theme['description']) }}</p>
            @endif
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between flex-wrap gap-2">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('panel/common.close') }}</button>
      </div>
    </div>
  </div>
</div>

@if(!empty($theme['has_demo']))
  <div class="modal fade" id="themeImportModal-{{ $theme['code'] }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ __('panel/themes.confirm_import_demo_title', ['name' => $theme['name']]) }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="text-muted small mb-3">{{ __('panel/themes.confirm_import_demo_intro') }}</p>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" value="1"
                   id="theme-import-clear-{{ $theme['code'] }}">
            <label class="form-check-label" for="theme-import-clear-{{ $theme['code'] }}">
              {{ __('panel/themes.clear_before_import') }}
            </label>
          </div>
          <p class="text-danger small mt-3 mb-0">{{ __('panel/themes.clear_before_import_warning') }}</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('panel/themes.demo_import_cancel') }}</button>
          <button type="button"
                  class="btn btn-primary btn-theme-demo-import-submit"
                  data-theme-code="{{ $theme['code'] }}"
                  data-url="{{ panel_route('themes.import_demo', ['code' => $theme['code']]) }}">
            {{ __('panel/themes.confirm_import_demo') }}
          </button>
        </div>
      </div>
    </div>
  </div>
@endif
