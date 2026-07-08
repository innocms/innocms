<div class="tab-pane fade mt-3" id="related-tab-pane" role="tabpanel" aria-labelledby="related-tab" tabindex="0">
  <div class="col-12 col-md-8">
    <x-panel-form-autocomplete-list
      name="related_ids[]"
      :value="old('related_ids', $product->relationProducts->pluck('id')->toArray())"
      title="{{ __('panel/product.related_products') }}"
      placeholder="{{ __('panel/product.related_search') }}"
      api="{{ url('api/panel/products') }}" />
  </div>
</div>

@hookinsert('panel.product.edit.related.bottom')
