<x-panel::form.row :title="$title">
  <div class="multi-image-wrap" data-name="{{ $name }}">
    <div class="d-flex flex-wrap gap-2 multi-image-list">
      @foreach ($values as $img)
        @php
          $imgPath = is_array($img) ? ($img['path'] ?? ($img['url'] ?? '')) : $img;
        @endphp
        @if ($imgPath)
          <div class="multi-image-item img-upload-item bg-light wh-80 rounded border d-flex justify-content-center align-items-center position-relative cursor-pointer overflow-hidden">
            <div class="position-absolute tool-wrap d-flex top-0 start-0 w-100 bg-primary bg-opacity-75">
              <div class="w-100 text-center show-img"><i class="bi bi-eye text-white"></i></div>
              <div class="w-100 text-center delete-img"><i class="bi bi-trash text-white"></i></div>
            </div>
            <div class="img-info rounded h-100 w-100 d-flex justify-content-center align-items-center">
              <img src="{{ image_resize($imgPath) }}" data-origin-img="{{ image_origin($imgPath) }}" class="img-fluid">
            </div>
            <input type="hidden" name="{{ $name }}[]" value="{{ $imgPath }}">
          </div>
        @endif
      @endforeach

      <div class="multi-image-add img-upload-item bg-light wh-80 rounded border d-flex justify-content-center align-items-center cursor-pointer">
        <i class="bi bi-plus fs-1 text-secondary opacity-75"></i>
      </div>
    </div>
  </div>
  {{ $slot }}
</x-panel::form.row>

@pushOnce('footer', 'multi-image-init')
<div class="modal fade" id="modal-multi-image-preview">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('panel/common.close') }}</button>
      </div>
    </div>
  </div>
</div>

<script>
// 多实例安全：通用脚本只输出一次，每个 wrap 从自身 data-name 读字段名
function miEscapeHtml(value) {
  return String(value == null ? '' : value)
    .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
}

$(function () {
  $('.multi-image-wrap').each(function () {
    var $wrap     = $(this);
    var fieldName = $wrap.data('name');

    function buildItem(path, url, originUrl) {
      url = url || path;
      originUrl = originUrl || url;
      return '' +
        '<div class="multi-image-item img-upload-item bg-light wh-80 rounded border d-flex justify-content-center align-items-center position-relative cursor-pointer overflow-hidden">' +
          '<div class="position-absolute tool-wrap d-flex top-0 start-0 w-100 bg-primary bg-opacity-75">' +
            '<div class="w-100 text-center show-img"><i class="bi bi-eye text-white"></i></div>' +
            '<div class="w-100 text-center delete-img"><i class="bi bi-trash text-white"></i></div>' +
          '</div>' +
          '<div class="img-info rounded h-100 w-100 d-flex justify-content-center align-items-center">' +
            '<img src="' + miEscapeHtml(url) + '" data-origin-img="' + miEscapeHtml(originUrl) + '" class="img-fluid">' +
          '</div>' +
          '<input type="hidden" name="' + miEscapeHtml(fieldName) + '[]" value="' + miEscapeHtml(path) + '">' +
        '</div>';
    }

    $wrap.on('click', '.multi-image-add', function () {
      var $add = $(this);
      window.inno.fileManagerIframe(function (file) {
        var files = Array.isArray(file) ? file : [file];
        files.forEach(function (f) {
          var path = f.path;
          var url  = f.url || f.origin_url;
          var originUrl = f.origin_url || f.url;
          $add.before(buildItem(path, url, originUrl));
        });
      }, { multiple: true, type: 'image' });
    });

    $wrap.on('click', '.delete-img', function (e) {
      e.stopPropagation();
      $(this).closest('.multi-image-item').remove();
    });

    $wrap.on('click', '.show-img', function (e) {
      e.stopPropagation();
      var src = $(this).closest('.multi-image-item').find('img').data('origin-img');
      $('#modal-multi-image-preview .modal-body').html('<img src="' + miEscapeHtml(src) + '" class="img-fluid">');
      $('#modal-multi-image-preview').modal('show');
    });
  });
});
</script>
@endPushOnce
