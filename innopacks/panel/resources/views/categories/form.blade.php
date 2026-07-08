@extends('panel::layouts.app')

@section('title', __('panel/menu.categories'))

@section('content')
  <div class="card h-min-600">
    <div class="card-body">
      <form class="needs-validation" novalidate
            action="{{ $category->id ? panel_route('categories.update', [$category->id]) : panel_route('categories.store') }}"
            method="POST">
        @csrf
        @method($category->id ? 'PUT' : 'POST')

        <ul class="nav nav-tabs mb-4" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#cat-tab-content" type="button">{{ __('panel/category.content_tab') }}</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#cat-tab-extra" type="button">{{ __('panel/category.extra_tab') }}</button>
          </li>
        </ul>

        <div class="tab-content">
          {{-- 分类内容（多语言手风琴） --}}
          <div class="tab-pane fade show active" id="cat-tab-content">
            <div class="accordion accordion-flush locales-accordion" id="cat-locales">
              @foreach (locales() as $locale)
                @php($localeCode = $locale->code)
                <div class="accordion-item">
                  <h2 class="accordion-header">
                    <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button"
                            data-bs-toggle="collapse" data-bs-target="#cat-locale-{{ $localeCode }}"
                            aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                            aria-controls="cat-locale-{{ $localeCode }}">
                      <div class="wh-20 me-2">
                        <img src="{{ image_origin($locale->image) }}" class="img-fluid">
                      </div>
                      {{ $locale->name }}
                    </button>
                  </h2>
                  <div id="cat-locale-{{ $localeCode }}"
                       class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                       data-bs-parent="#cat-locales">
                    <div class="accordion-body">
                      <input name="translations[{{$localeCode}}][locale]" value="{{$localeCode}}" class="d-none">

                      <x-panel-form-input title="{{ __('panel/category.name') }}" name="translations[{{$localeCode}}][name]"
                                          :value="old('translations.' . $localeCode . '.name', $category->translate($localeCode, 'name'))"
                                          required placeholder="{{ __('panel/category.name') }}"/>

                      <x-panel-form-input title="{{ __('panel/category.summary') }}" name="translations[{{$localeCode}}][summary]"
                                          :value="old('translations.' . $localeCode . '.summary', $category->translate($localeCode, 'summary'))"
                                          placeholder="{{ __('panel/category.summary') }}"/>

                      <div class="mb-3">
                        <div class="col-form-label">{{ __('panel/category.content') }}</div>
                        <x-panel-form-rich-text title="" name="translations[{{$localeCode}}][content]"
                                                :value="old('translations.' . $localeCode . '.content', $category->translate($localeCode, 'content'))"
                                                placeholder="{{ __('panel/category.content') }}"/>
                      </div>

                      <x-panel-form-input title="{{ __('panel/common.meta_title') }}" name="translations[{{$localeCode}}][meta_title]"
                                          :value="old('translations.' . $localeCode . '.meta_title', $category->translate($localeCode, 'meta_title'))"
                                          placeholder="{{ __('panel/common.meta_title') }}"/>

                      <x-panel-form-input title="{{ __('panel/common.meta_keywords') }}" name="translations[{{$localeCode}}][meta_keywords]"
                                          :value="old('translations.' . $localeCode . '.meta_keywords', $category->translate($localeCode, 'meta_keywords'))"
                                          placeholder="{{ __('panel/common.meta_keywords') }}"/>

                      <x-panel-form-input title="{{ __('panel/common.meta_description') }}" name="translations[{{$localeCode}}][meta_description]"
                                          :value="old('translations.' . $localeCode . '.meta_description', $category->translate($localeCode, 'meta_description'))"
                                          placeholder="{{ __('panel/common.meta_description') }}"/>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>

          {{-- 其他信息 --}}
          <div class="tab-pane fade" id="cat-tab-extra">
            @php($parentId = old('parent_id', $category->parent_id ?? 0))
            <div class="mb-3">
              <div class="col-form-label">{{ __('panel/category.parent') }}</div>
              <select class="form-select" name="parent_id">
                <option value="0" {{ $parentId == 0 ? 'selected' : '' }}>{{ __('panel/category.top_level') }}</option>
                @foreach ($categories as $cat)
                  <option value="{{ $cat['id'] }}" {{ $parentId == $cat['id'] ? 'selected' : '' }}>{{ $cat['name'] }}</option>
                @endforeach
              </select>
            </div>

            <x-panel-form-input title="{{ __('panel/common.slug') }}" name="slug"
                                :value="old('slug', $category->slug ?? '')"
                                placeholder="{{ __('panel/common.slug') }}"/>

            <div class="mb-3">
              <div class="col-form-label">{{ __('panel/common.image') }}</div>
              <x-panel-form-image title="" name="image" value="{{ old('image', $category->image ?? '') }}"/>
            </div>

            <x-panel-form-input title="{{ __('panel/common.position') }}" name="position"
                                :value="old('position', $category->position ?? 0)"
                                placeholder="{{ __('panel/common.position') }}"/>

            <x-panel-form-switch-radio title="{{ __('panel/common.whether_enable') }}" name="active"
                                :value="old('active', $category->active ?? true)"/>
          </div>
        </div>

        <x-panel::form.bottom-btns />
      </form>
    </div>
  </div>
@endsection
