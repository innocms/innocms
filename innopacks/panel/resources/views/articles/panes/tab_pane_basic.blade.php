<div class="tab-pane fade show active mt-3" id="basic-tab-pane" role="tabpanel" aria-labelledby="basic-tab" tabindex="0">

  <div class="col-12 col-md-6">
    <x-common-form-locale-input
      name="title"
      type="input"
      :translations="locale_field_data($article, 'title')"
      :required="true"
      :label="__('panel/article.title')"
      :placeholder="__('panel/article.title')"
    />
  </div>

  <div class="col-12 col-md-8">
    <x-common-form-image title="{{ __('panel/article.main_image') }}" name="image"
                        value="{{ old('image', $article->image ?? '') }}"/>
  </div>

  <x-panel-form-switch-radio title="{{ __('panel/common.whether_enable') }}" name="active" :value="old('active', $article->active ?? true)" />

</div>

@hookinsert('panel.article.edit.basic.bottom')
