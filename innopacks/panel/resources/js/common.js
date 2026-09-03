export default {
  msg(params = {}, callback = null) {
    let msg = typeof params === 'string' ? params : params.msg || '';
    let time = params.time || 2000;
    layer.msg(msg, {time}, callback);
  },

  // Aligned with InnoShop panel-inno.js: centered layer.msg popup with shade,
  // so feedback appears in the middle of the screen instead of a corner toast.
  alert(params = {}, callback = null) {
    let msg = typeof params === 'string' ? params : params.msg || '';
    let type = params.type || 'success';
    let icon = type === 'success' ? 1 : 2;

    layer.msg(msg, {
      icon: icon,
      shade: 0.3,
      shadeClose: true,
      time: 5000,
    }, callback);
  },

  imgUploadAjax(file, _self, callback = null) {
    if (file.type.indexOf('image') === -1) {
        alert('请上传图片文件');
        return;
    }

    let formData = new FormData();
    formData.append('image', file);
    formData.append('type', _self.parents('.is-up-file').data('type'));
    _self.find('.img-loading').removeClass('d-none');
    axios.post(urls.upload_images, formData, {}).then(function (res) {
      callback(res);
    }).catch(function (err) {
      inno.msg(err.response.data.message);
    }).finally(function () {
      _self.find('.img-loading').addClass('d-none');
    });
  },

  // bootstrap 表单验证, js 验证
  validateAndSubmitForm(form, callback) {
    $(document).on('click', `${form} .form-submit`, function(event) {
      if ($(form)[0].checkValidity() === false) {
        event.preventDefault();
        event.stopPropagation();
      }

      $(form).addClass('was-validated');

      if ($(form)[0].checkValidity() === true) {
        callback($(form).serialize());
      }
    })

    $(document).on('keypress', `${form} input`, function(event) {
      if (event.keyCode === 13) {
        $(`${form} .form-submit`).trigger('click');
      }
    })
  },
};