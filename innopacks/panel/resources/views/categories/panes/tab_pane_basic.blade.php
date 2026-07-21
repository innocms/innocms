<div class="tab-pane fade show active mt-3" id="basic-tab-pane" role="tabpanel" aria-labelledby="basic-tab" tabindex="0">

  {{-- 分类名称（多语言） --}}
  <div class="mb-3 col-12 col-md-6">
    <x-common-form-locale-input
      name="name"
      type="input"
      :translations="locale_field_data($category, 'name')"
      :required="true"
      :label="__('panel/category.name')"
      :placeholder="__('panel/category.name')"
    />
  </div>

  {{-- 主图片（统一，不区分语言） --}}
  <div class="mb-3 col-12 col-md-6">
    <x-panel-form-image title="{{ __('panel/common.image') }}" name="image"
                        value="{{ old('image', $category->image ?? '') }}"/>
  </div>

  {{-- 启用状态 --}}
  <div class="mb-3 col-12 col-md-6">
    <x-panel-form-switch-radio title="{{ __('panel/common.whether_enable') }}" name="active"
                               :value="old('active', $category->active ?? true)"/>
  </div>
</div>

@hookinsert('panel.category.edit.basic.bottom')
