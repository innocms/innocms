@extends('panel::layouts.app')

@section('title', __('panel/menu.tags'))

@section('content')
  <div class="card h-min-600">
    <div class="card-body">
      <form class="needs-validation" novalidate
            action="{{ $tag->id ? panel_route('tags.update', [$tag->id]) : panel_route('tags.store') }}"
            method="POST">
        @csrf
        @method($tag->id ? 'PUT' : 'POST')

        <ul class="nav nav-tabs mb-4" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-data" type="button">{{ __('panel/tag.tag_content') }}
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-info" type="button">{{ __('panel/common.extra_info') }}</button>
          </li>
        </ul>

        <div class="tab-content">
          <div class="tab-pane fade show active" id="tab-data">
            <div class="accordion accordion-flush locales-accordion" id="data-locales">
              @foreach (locales() as $locale)
                @php($localeCode = $locale->code)
                @php($localeName = $locale->name)
                <div class="accordion-item">
                  <h2 class="accordion-header">
                    <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button"
                            data-bs-toggle="collapse" data-bs-target="#data-locale-{{ $localeCode }}"
                            aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                            aria-controls="data-locale-{{ $localeCode }}">
                      <div class="wh-20 me-2">
                        <img src="{{ image_origin($locale->image) }}" class="img-fluid">
                      </div>
                      {{ $localeName }}
                    </button>
                  </h2>
                  <div id="data-locale-{{ $localeCode }}"
                       class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                       data-bs-parent="#data-locales">
                    <div class="accordion-body">
                      <input name="translations[{{$localeCode}}][locale]" value="{{$localeCode}}" class="d-none">

                      <x-panel-form-input title="{{ __('panel/tag.title') }}" name="translations[{{$localeCode}}][name]"
                                          value="{{ old('translations.' . $localeCode . '.name', $tag->translate($localeCode, 'name')) }}"
                                          required placeholder="{{ __('panel/tag.title') }}"/>

                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>

          <div class="tab-pane fade" id="tab-info">
            <x-panel-form-input title="{{ __('panel/common.slug') }}" name="slug" :value="old('slug', $tag->slug ?? '')"
                                placeholder="{{ __('panel/common.slug') }}" />
            <x-panel-form-input title="{{ __('panel/tag.position') }}" name="position" :value="old('position', $tag->position ?? 0)"
                                placeholder="{{ __('panel/tag.position') }}"/>
            <x-panel-form-switch-radio title="{{ __('panel/common.whether_enable') }}" name="active" :value="old('active', $tag->active ?? true)"
              placeholder="{{ __('panel/common.whether_enable') }}"/>
          </div>
        </div>

        <x-panel::form.bottom-btns />
      </form>
    </div>
  </div>
@endsection

@push('footer')
  <script>
  </script>
@endpush