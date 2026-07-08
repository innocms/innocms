<div class="tab-pane fade mt-3" id="extra-tab-pane" role="tabpanel" aria-labelledby="extra-tab" tabindex="0">
  <div class="row">
    <div class="col-12 col-md-6">
      <x-panel-form-autocomplete-list
        name="categories[]"
        :value="old('categories', $product->categories->pluck('id')->toArray())"
        title="{{ __('panel/product.category') }}"
        placeholder="{{ __('panel/product.category_search') }}"
        api="{{ url('api/panel/categories') }}" />
    </div>

    <div class="col-12 col-md-6">
      <x-panel-form-input title="{{ __('panel/common.position') }}" name="position"
                          :value="old('position', $product->position ?? 0)"
                          placeholder="{{ __('panel/common.position') }}"/>

      <x-panel-form-input title="{{ __('panel/common.viewed') }}" name="viewed"
                          :value="old('viewed', $product->viewed ?? 0)"
                          placeholder="{{ __('panel/common.viewed') }}"/>

      <x-panel-form-input title="{{ __('panel/product.link') }}" name="link"
                          :value="old('link', $product->link ?? '')"
                          placeholder="{{ __('panel/product.link_placeholder') }}"/>
    </div>
  </div>
</div>

@hookinsert('panel.product.edit.extra.bottom')
