<div class="tab-pane fade show active mt-3" id="basic-tab-pane" role="tabpanel" aria-labelledby="basic-tab" tabindex="0">

  <div class="col-12 col-md-6">
    <x-common-form-locale-input
      name="name"
      type="input"
      :translations="locale_field_data($product, 'name')"
      :required="true"
      :label="__('panel/product.name')"
      :placeholder="__('panel/product.name')"
    />
  </div>

  <div class="col-12">
    <x-common-form-images
      title="{{ __('panel/product.images') }}"
      name="images"
      :values="old('images', $product->images ?? [])"
    />
  </div>

  <div class="row">
    <div class="col-12 col-md-6">
      {{-- 产品视频：手填 URL 或从文件管理选择本地视频 --}}
      <div class="mb-3">
        <div class="col-form-label">{{ __('panel/product.video') }}</div>
        <div class="input-group">
          <input type="text" class="form-control" name="video[url]" id="product-video-url"
                 value="{{ old('video.url', $product->video['url'] ?? '') }}"
                 placeholder="{{ __('panel/product.video_placeholder') }}">
          <button type="button" class="btn btn-outline-secondary" id="product-video-pick" title="{{ __('panel/product.select_file') }}">
            <i class="bi bi-folder2-open"></i>
          </button>
        </div>
        <div class="mt-2" id="product-video-preview"></div>
      </div>
    </div>
    <div class="col-12 col-md-3">
      <x-panel-form-input title="{{ __('panel/product.price') }}" name="price"
                          :value="old('price', $product->price ?? 0)"
                          placeholder="0.00"/>
    </div>
    <div class="col-12 col-md-3">
      <x-panel-form-input title="{{ __('panel/product.model') }}" name="spu_code"
                          :value="old('spu_code', $product->spu_code ?? '')"
                          placeholder="{{ __('panel/product.model') }}"/>
    </div>
  </div>

  <x-panel-form-switch-radio title="{{ __('panel/common.whether_enable') }}" name="active" :value="old('active', $product->active ?? true)" />

</div>

@hookinsert('panel.product.edit.basic.bottom')

@push('footer')
<script>
(function () {
  var VIDEO_EXT = /\.(mp4|webm|ogg|ogv|mov|m4v|avi|mkv)(\?|$)/i;

  function isSafeMediaUrl(u) {
    // 仅允许 http(s) 或站内绝对路径，拦截 javascript:/data: 等危险协议
    return /^(https?:\/\/|\/)/i.test(u);
  }

  function renderVideoPreview(val) {
    var $box = $('#product-video-preview');
    $box.empty();
    if (!val) return;

    if (!isSafeMediaUrl(val)) {
      // 非安全协议：仅以纯文本提示，不渲染为属性，避免 XSS
      $box.append($('<span>').addClass('badge bg-warning text-dark').text(val));
      return;
    }

    if (VIDEO_EXT.test(val)) {
      // 本地或直链视频文件，用 DOM API 构造 <video>（属性自动转义）
      $('<video>').attr('src', val).attr('controls', '').prop('playsinline', true)
        .css({ maxWidth: '100%', maxHeight: '220px', borderRadius: '6px', background: '#000' })
        .appendTo($box);
    } else {
      // 外部平台链接，用 DOM API 构造 <a>（属性与文本自动转义）
      var $a = $('<a>').attr('href', val).attr('target', '_blank')
        .addClass('badge bg-light text-dark text-decoration-none border px-2 py-1');
      $a.append($('<i>').addClass('bi bi-box-arrow-up-right me-1'));
      $a.append(document.createTextNode(val));
      $box.append($a);
    }
  }

  // 手动输入/粘贴 URL
  $('#product-video-url').on('input', function () {
    renderVideoPreview($(this).val());
  });

  // 从文件管理器选择本地视频文件
  $('#product-video-pick').on('click', function () {
    window.inno.fileManagerIframe(function (file) {
      var url = file.url || file.origin_url || file.path;
      $('#product-video-url').val(url).trigger('input');
    }, { type: 'video', multiple: false });
  });

  // 首次渲染（编辑页回显）
  renderVideoPreview($('#product-video-url').val());
})();
</script>
@endpush
