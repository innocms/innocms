<x-panel::form.row :title="$title">
  <div class="is-up-file" data-type="{{ $type }}">
    <div class="img-upload-item bg-light wh-80 rounded border d-flex justify-content-center align-items-center me-2 mb-2 position-relative cursor-pointer overflow-hidden">
      <div class="position-absolute tool-wrap {{ !$value ? 'd-none' : '' }} d-flex top-0 start-0 w-100 bg-primary bg-opacity-75"><div class="show-img w-100 text-center"><i class="bi bi-eye text-white"></i></div><div class="w-100 delete-img text-center"><i class="bi bi-trash text-white"></i></div></div>
      <div class="position-absolute bg-white d-none img-loading"><div class="spinner-border opacity-50"></div></div>

      <div class="img-info rounded h-100 w-100 d-flex justify-content-center align-items-center">
        @if ($value)
        <img src="{{ image_resize($value) }}" data-origin-img="{{ image_origin($value) }}" class="img-fluid">
        @else
        <i class="bi bi-plus fs-1 text-secondary opacity-75"></i>
        @endif
      </div>
      <input type="hidden" value="{{ $value }}" name="{{ $name }}">
    </div>
  </div>
  @if ($description)
  <div class="help-text font-size-12 lh-base">{!! $description !!}</div>
  @endif
  {{ $slot }}
</x-panel::form.row>


@pushOnce('footer')
<div class="modal fade" id="modal-show-img">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body"></div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">关闭</button>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).on('click', '.is-up-file .img-upload-item', function (e) {
    if ($(e.target).closest('.delete-img, .show-img').length) {
      return;
    }
    const _self = $(this);

    window.inno.fileManagerIframe((file) => {
      let val = file.path;
      let url = file.url || file.origin_url;
      let originUrl = file.origin_url || file.url;
      _self.find('input').val(val);
      _self.find('.tool-wrap').removeClass('d-none');
      _self.find('.img-info').html('<img src="' + url + '" class="img-fluid" data-origin-img="' + originUrl + '">');
      _self.find('input').trigger('change');
    }, {
      multiple: false,
      type: 'image'
    });
  });

  $('.is-up-file .delete-img').on('click', function (e) {
      e.stopPropagation();
      let _self = $(this).parent().parent();
      _self.find('input').val('');
      _self.find('.tool-wrap').addClass('d-none');
      _self.find('.img-info').html('<i class="bi bi-plus-lg fs-3 text-secondary"></i>');
  });

  $('.is-up-file .show-img').on('click', function (e) {
      e.stopPropagation();
      let src = $(this).parent().siblings('.img-info').find('img').data('origin-img');
      let img = '<img src="' + src + '" class="img-fluid">';
      $('#modal-show-img .modal-body').html(img);
      $('#modal-show-img').modal('show');
  });
</script>
@endPushOnce
