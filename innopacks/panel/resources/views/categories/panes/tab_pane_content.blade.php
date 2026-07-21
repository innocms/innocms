<div class="tab-pane fade mt-3" id="content-tab-pane" role="tabpanel" aria-labelledby="content-tab" tabindex="0">

  {{-- 富文本详情：按语言切换（同产品「内容」标签页） --}}
  <div class="mb-3">
    <div class="col-form-label">{{ __('panel/category.content') }}</div>
    <ul class="nav nav-tabs mb-3" id="locales-content-tab" role="tablist">
      @foreach (locales() as $locale)
        <li class="nav-item" role="presentation">
          <button class="nav-link d-flex {{ $loop->first ? 'active' : '' }}" id="locale-{{ $locale->code }}-content-tab"
            data-bs-toggle="tab" data-bs-target="#locale-{{ $locale->code }}-content-pane" type="button"
            role="tab" aria-controls="locale-{{ $locale->code }}-content-pane"
            aria-selected="{{ $loop->first ? 'true' : 'false' }}">
            <div class="wh-20 me-2">
              <img src="{{ image_origin($locale->image) }}" class="img-fluid">
            </div>
            {{ $locale->name }}
          </button>
        </li>
      @endforeach
    </ul>

    <div class="tab-content" id="locales-content-tabContent">
      @foreach (locales() as $locale)
        @php($localeCode = $locale->code)
        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
          id="locale-{{ $localeCode }}-content-pane" role="tabpanel"
          aria-labelledby="locale-{{ $localeCode }}-content-tab" tabindex="0">
          <input type="hidden" name="translations[{{ $localeCode }}][locale]" value="{{ $localeCode }}">
          <x-panel-form-rich-text title="" name="translations[{{ $localeCode }}][content]"
                                  value="{{ old('translations.' . $localeCode . '.content', $category->translate($localeCode, 'content')) }}"
                                  placeholder="{{ __('panel/category.content') }}"/>
        </div>
      @endforeach
    </div>
  </div>
</div>

@hookinsert('panel.category.edit.content.bottom')
