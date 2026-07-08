<div class="tab-pane fade mt-3" id="seo-tab-pane" role="tabpanel" aria-labelledby="seo-tab" tabindex="0">

  <x-panel-form-input title="{{ __('panel/common.slug') }}" name="slug"
                      :value="old('slug', $product->slug ?? '')"
                      placeholder="{{ __('panel/common.slug') }}"/>

  <x-common-form-locale-input
    name="meta_title"
    type="input"
    :translations="locale_field_data($product, 'meta_title')"
    :label="__('panel/common.meta_title')"
    :placeholder="__('panel/common.meta_title')"
  />

  <x-common-form-locale-input
    name="meta_description"
    type="textarea"
    :translations="locale_field_data($product, 'meta_description')"
    :label="__('panel/common.meta_description')"
    :placeholder="__('panel/common.meta_description')"
    :rows="3"
  />

  <x-common-form-locale-input
    name="meta_keywords"
    type="input"
    :translations="locale_field_data($product, 'meta_keywords')"
    :label="__('panel/common.meta_keywords')"
    :placeholder="__('panel/common.meta_keywords')"
  />
</div>

@hookinsert('panel.product.edit.seo.bottom')
