<div class="tab-pane fade mt-3" id="seo-tab-pane" role="tabpanel" aria-labelledby="seo-tab" tabindex="0">

  <x-panel-form-input title="{{ __('panel/common.slug') }}" name="slug"
                      :value="old('slug', $category->slug ?? '')"
                      placeholder="{{ __('panel/common.slug') }}"/>

  <x-common-form-locale-input
    name="summary"
    type="textarea"
    :translations="locale_field_data($category, 'summary')"
    :label="__('panel/category.summary')"
    :placeholder="__('panel/category.summary')"
    :rows="3"
  />

  <x-common-form-locale-input
    name="meta_title"
    type="input"
    :translations="locale_field_data($category, 'meta_title')"
    :label="__('panel/common.meta_title')"
    :placeholder="__('panel/common.meta_title')"
  />

  <x-common-form-locale-input
    name="meta_description"
    type="textarea"
    :translations="locale_field_data($category, 'meta_description')"
    :label="__('panel/common.meta_description')"
    :placeholder="__('panel/common.meta_description')"
    :rows="3"
  />

  <x-common-form-locale-input
    name="meta_keywords"
    type="textarea"
    :translations="locale_field_data($category, 'meta_keywords')"
    :label="__('panel/common.meta_keywords')"
    :placeholder="__('panel/common.meta_keywords')"
    :rows="3"
  />
</div>
