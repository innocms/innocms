@extends('panel::layouts.app')

@section('title', __('panel/menu.catalogs'))

<x-panel::form.right-btns formid="catalog-form" />

@section('content')
  <div class="card h-min-600">
    <div class="card-body">
      <form class="needs-validation" novalidate
            id="catalog-form"
            action="{{ $catalog->id ? panel_route('catalogs.update', [$catalog->id]) : panel_route('catalogs.store') }}"
            method="POST">
        @csrf
        @method($catalog->id ? 'PUT' : 'POST')

        <ul class="nav nav-tabs mb-4" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-data" type="button">{{ __('panel/catalog.content_tab') }}</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-info" type="button">{{ __('panel/catalog.extra_tab') }}</button>
          </li>
        </ul>

        <div class="tab-content">
          {{-- 分类内容：与产品编辑一致的多语言方式 --}}
          <div class="tab-pane fade show active" id="tab-data">
            <div class="row">
              <div class="col-12 col-md-6">
                <x-common-form-locale-input
                  name="title"
                  type="input"
                  :translations="locale_field_data($catalog, 'title')"
                  :required="true"
                  :label="__('panel/catalog.title')"
                  :placeholder="__('panel/catalog.title')"
                />
              </div>
            </div>

            <x-common-form-locale-input
              name="summary"
              type="textarea"
              :translations="locale_field_data($catalog, 'summary')"
              :label="__('panel/catalog.summary')"
              :placeholder="__('panel/catalog.summary')"
              :rows="3"
            />

            <x-common-form-locale-input
              name="meta_title"
              type="input"
              :translations="locale_field_data($catalog, 'meta_title')"
              :label="__('panel/common.meta_title')"
              :placeholder="__('panel/common.meta_title')"
            />

            <x-common-form-locale-input
              name="meta_keywords"
              type="input"
              :translations="locale_field_data($catalog, 'meta_keywords')"
              :label="__('panel/common.meta_keywords')"
              :placeholder="__('panel/common.meta_keywords')"
            />

            <x-common-form-locale-input
              name="meta_description"
              type="textarea"
              :translations="locale_field_data($catalog, 'meta_description')"
              :label="__('panel/common.meta_description')"
              :placeholder="__('panel/common.meta_description')"
              :rows="3"
            />
          </div>

          <div class="tab-pane fade" id="tab-info">
            <x-panel-form-select title="{{ __('panel/category.parent') }}" name="parent_id" :value="old('parent_id', $catalog->parent_id ?? 0)"
                                :options="$catalogs" key="id" label="name" />
            <x-panel-form-input title="{{ __('panel/common.slug') }}" name="slug" :value="old('slug', $catalog->slug ?? '')"
                                placeholder="{{ __('panel/common.slug') }}" />
            <x-panel-form-input title="{{ __('panel/common.position') }}" name="position" :value="old('position', $catalog->position ?? 0)"
                                placeholder="{{ __('panel/common.position') }}"/>
            <x-panel-form-switch-radio title="{{ __('panel/common.whether_enable') }}" name="active" :value="old('active', $catalog->active ?? true)"
              placeholder="{{ __('panel/common.whether_enable') }}"/>
          </div>
        </div>

        <button type="submit" class="d-none"></button>
      </form>
    </div>
  </div>
@endsection
