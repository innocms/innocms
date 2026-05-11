@props([
    'name'          => '',
    'type'          => 'input',
    'translations'  => [],
    'required'      => false,
    'placeholder'   => '',
    'label'         => '',
    'rows'          => 4,
    'nameFormat'    => 'translations',
    'description'   => '',
    'translate'     => true,
])

@php
$panelLocale    = panel_locale_code();
$frontLocale    = setting_locale_code();
$allLocales     = locales();
$isSameLocale   = $panelLocale === $frontLocale;

$sortedLocales = $allLocales->sortBy(function ($loc) use ($panelLocale, $frontLocale) {
    if ($loc->code === $panelLocale) return 0;
    if ($loc->code === $frontLocale) return 1;
    return 2;
})->values();

$primaryLocale = $sortedLocales->first();
$primaryCode   = $primaryLocale ? $primaryLocale->code : $panelLocale;
$primaryValue  = $translations[$primaryCode] ?? '';

$otherLocales = $sortedLocales->slice(1);
$hasOthers    = $otherLocales->count() > 0;

$uid = 'lf-' . md5($name . '-' . ($label ?: $placeholder) . uniqid());
$modalId = 'locale-modal-' . $uid;
@endphp

<div class="locale-field-wrapper mb-3" id="locale-field-{{ $uid }}"
     data-field-name="{{ $name }}"
     data-name-format="{{ $nameFormat }}"
     data-panel-locale="{{ $primaryCode }}"
     data-front-locale="{{ $frontLocale }}"
     data-modal-id="{{ $modalId }}"
     data-field-label="{{ $label ?: $name }}">

  @if($label)
    <div class="col-form-label {{ $required ? 'required' : '' }}">{{ $label }}</div>
  @endif

  {{-- Main input: primary language --}}
  <div class="input-group mb-2">
    <span class="input-group-text p-1">
      <div class="d-flex align-items-center wh-20">
        @if($primaryLocale)
          <img src="{{ image_origin($primaryLocale->image) }}"
               class="img-fluid {{ default_locale_class($primaryCode) }}"
               alt="{{ $primaryLocale->name }}">
        @endif
      </div>
    </span>

    @if($type === 'textarea')
      <textarea class="form-control locale-primary-input"
                name="@if($nameFormat === 'translations')translations[{{ $primaryCode }}][{{ $name }}]@else{{ $name }}[{{ $primaryCode }}]@endif"
                rows="{{ $rows }}"
                {{ $required ? 'required' : '' }}
                placeholder="{{ $placeholder ?: $label }}"
                data-locale="{{ $primaryCode }}"
      >{{ $primaryValue }}</textarea>
    @else
      <input type="text" class="form-control locale-primary-input"
             name="@if($nameFormat === 'translations')translations[{{ $primaryCode }}][{{ $name }}]@else{{ $name }}[{{ $primaryCode }}]@endif"
             value="{{ $primaryValue }}"
             {{ $required ? 'required' : '' }}
             placeholder="{{ $placeholder ?: $label }}"
             data-locale="{{ $primaryCode }}">
    @endif

    @if($hasOthers)
      <button type="button" class="btn btn-outline-secondary"
              onclick="$('#{{ $modalId }}').modal('show')"
              title="{{ __('panel/common.all_languages') }}">
        <i class="bi bi-translate"></i>
      </button>
    @endif
  </div>

  {{-- Hidden locale field for primary --}}
  @if($nameFormat === 'translations')
    <input type="hidden" name="translations[{{ $primaryCode }}][locale]" value="{{ $primaryCode }}">
  @endif

  @if($description)
    <div class="text-secondary mb-2"><small>{!! $description !!}</small></div>
  @endif

  {{-- Modal: all languages --}}
  @if($hasOthers)
    <div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ $label ?: $placeholder }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            @foreach($sortedLocales as $locale)
              @php
                $localeCode  = $locale->code;
                $localeValue = $translations[$localeCode] ?? '';
                $isPanel     = $localeCode === $panelLocale;
                $isFront     = $localeCode === $frontLocale;
                $borderClass = $isPanel ? 'border border-primary' : ($isFront ? 'border border-success' : 'border');
              @endphp
              <div class="locale-modal-row mb-3 p-2 rounded {{ $borderClass }}" data-locale="{{ $localeCode }}">
                <div class="d-flex align-items-center gap-2 mb-1">
                  <img src="{{ image_origin($locale->image) }}" class="img-fluid" width="22" height="16" alt="{{ $locale->name }}">
                  <span class="fw-medium">{{ $locale->name }}</span>
                  @if($isPanel)
                    <span class="badge bg-primary">{{ __('panel/common.panel_locale') }}</span>
                  @endif
                  @if($isFront && ! $isSameLocale)
                    <span class="badge bg-success">{{ __('panel/common.front_locale') }}</span>
                  @endif
                </div>
                @if($type === 'textarea')
                  <textarea class="form-control" rows="{{ min($rows, 3) }}"
                            name="@if($nameFormat === 'translations')translations[{{ $localeCode }}][{{ $name }}]@else{{ $name }}[{{ $localeCode }}]@endif"
                            placeholder="{{ $placeholder ?: $label }}"
                            data-locale="{{ $localeCode }}"
                  >{{ $localeValue }}</textarea>
                @else
                  <input type="text" class="form-control"
                         name="@if($nameFormat === 'translations')translations[{{ $localeCode }}][{{ $name }}]@else{{ $name }}[{{ $localeCode }}]@endif"
                         value="{{ $localeValue }}"
                         placeholder="{{ $placeholder ?: $label }}"
                         data-locale="{{ $localeCode }}">
                @endif
                @if($nameFormat === 'translations')
                  <input type="hidden" name="translations[{{ $localeCode }}][locale]" value="{{ $localeCode }}">
                @endif
              </div>
            @endforeach
          </div>
          <div class="modal-footer d-flex justify-content-between">
            <button type="button" class="btn btn-outline-secondary locale-smart-fill-btn"
                    data-primary-locale="{{ $primaryCode }}"
                    data-msg-no-empty="{{ __('panel/common.no_empty_languages') }}"
                    data-msg-copied="{{ __('panel/common.copied_languages') }}">
              <i class="bi bi-arrow-right-circle me-1"></i>{{ __('panel/common.copy_empty') }}
            </button>
            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">{{ __('common/base.confirm') }}</button>
          </div>
        </div>
      </div>
    </div>
  @endif
</div>

@include('common::components.form.locale-input-js')
