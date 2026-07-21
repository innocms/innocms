<div class="tab-pane fade mt-3" id="extra-tab-pane" role="tabpanel" aria-labelledby="extra-tab" tabindex="0">
  <div class="row">
    <div class="col-12 col-md-6">
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
    </div>

    <div class="col-12 col-md-6">
      <x-panel-form-input title="{{ __('panel/common.position') }}" name="position"
                          :value="old('position', $category->position ?? 0)"
                          placeholder="{{ __('panel/common.position') }}"/>
    </div>
  </div>
</div>

@hookinsert('panel.category.edit.settings.bottom')
