<div>
    <h5>{{'新增轮播图片'. $carouselImages->count() .'/10'}}</h5>
    <p class="text-danger">请上传1920*600或1320*400的图片，以保证最佳的显示效果，我们会在后续版本增加裁剪功能。</p>
    <div class="is-up-file" data-type="carousel_images" data-event="{{ $item->id }}">
        <div class="img-upload-item bg-light wh-80 rounded border d-flex justify-content-center align-items-center me-2 mb-2 position-relative cursor-pointer overflow-hidden">
            <div class="position-absolute tool-wrap {{ !$item->image_url ? 'd-none' : '' }} d-flex top-0 start-0 w-100 bg-primary bg-opacity-75">
                <div class="show-img w-100 text-center">
                    <i class="bi bi-eye text-white"></i>
                </div>
                <div class="w-100 delete-img text-center">
                    <i class="bi bi-trash text-white"></i>
                </div>
            </div>
            <div class="position-absolute bg-white d-none img-loading">
                <div class="spinner-border opacity-50"></div>
            </div>

            <div class="img-info rounded w-100 d-flex justify-content-center align-items-center">
                @if ($item->image_url)
                    <img src="{{ image_resize($item->image_url,1080,1920) }}" data-origin-img="{{ image_origin($item->image_url) }}" class="img-fluid">
                @else
                    <i class="bi bi-plus fs-1 text-secondary opacity-75"></i>
                @endif
            </div>
            <input type="hidden" id="imageVal" value="{{ old('carousel_image', $item->image_url ?? '') }}" name="image_url">
            <input type="hidden" id='eventId' value="{{ $item->id }}" name="carousel_id">
        </div>
    </div>

    <button id="btnAddImage" class="btn btn-primary disabled">添加</button>
</div>


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
        $('.is-up-file .img-upload-item').click(function () {
            const _self = $(this);
            $('#form-upload').remove();
            $('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" accept="image/*" name="file" /></form>');
            $('#form-upload input[name=\'file\']').trigger('click');
            $('#form-upload input[name=\'file\']').change(function () {
                let file = $(this).prop('files')[0];
                imgUploadAjax(file, _self);
            });
        })

        // 允许拖拽上传
        $('.is-up-file .img-upload-item').on('dragover', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).addClass('border-primary');
        });

        // dragleave
        $('.is-up-file .img-upload-item').on('dragleave', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).removeClass('border-primary');
        });

        $('.is-up-file .img-upload-item').on('drop', function (e) {
            e.stopPropagation();
            let file = e.originalEvent.dataTransfer.files[0];
            imgUploadAjax(file, $(this));
            $(this).removeClass('border-primary');
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

        function imgUploadAjax(file, _self) {
            if (file.type.indexOf('image') === -1) {
                alert('请上传图片文件');
                return;
            }

            let formData = new FormData();
            formData.append('image', file);
            formData.append('type', _self.parents('.is-up-file').data('type'));
            formData.append('event', _self.parents('.is-up-file').data('event'));
            _self.find('.img-loading').removeClass('d-none');
            axios.post('{{route('front.upload.images')}}', formData, {}).then(function (res) {
                let url = res.data.url;
                let val = res.data.value;
                _self.find('#imageVal').val(val);
                _self.find('.tool-wrap').removeClass('d-none');
                $('#btnAddImage').removeClass('disabled');
                $('#btnAddImage').click(addCarouselImage);
                _self.find('.img-info').html('<img src="' + url + '" class="img-fluid" data-origin-img="' + url + '">');
            }).catch(function (err) {
                layer.msg(err.response.data.message, {icon: 2});
            }).finally(function () {
                _self.find('.img-loading').addClass('d-none');
            });
        }

        function addCarouselImage() {
            // if (!carousel_id  || !image_url) {
            //     alert('请上传图片文件');
            //     return;
            // }
            let formData = new FormData();
            formData.append('_token',"{{csrf_token()}}");
            formData.append('carousel_id',$('#eventId').val());
            formData.append('image_url',$('#imageVal').val());
            axios.post("{{panel_route('images.store',$item->id)}}",formData,{}).then(function (res){
                layer.msg('上传成功！', {icon: 1});
                location.reload();
            }).catch(function (err) {
                layer.msg(err.response.data.message, {icon: 2});
            }).finally(function () {
                $('#btnAddImage').addClass('disabled');
            });
        }
    </script>
@endPushOnce
