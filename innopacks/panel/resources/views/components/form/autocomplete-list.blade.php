<x-panel::form.row :title="$title" :required="$required">
  <div class="autocomplete-group-wrapper" data-api="{{ $api }}" data-name="{{ $name }}">
    <div class="autocomplete-input-box">
      <input type="text" class="form-control input-autocomplete"
      placeholder="{{ $placeholder }}" @if ($required) required @endif />
      <div class="invalid-feedback text-start">{{ $placeholder }}</div>
    </div>
    <div class="autocomplete-list p-2 autocomplete-list-{{ $id }}">
      <ul class="list-group list-group-flush"></ul>
    </div>
  </div>
</x-panel::form.row>

@pushOnce('footer', 'autocomplete-list-init')
<script>
// 多实例安全：通用脚本只输出一次，每个 wrapper 从自身 data 属性读配置
function acEscapeHtml(value) {
  return String(value == null ? '' : value)
    .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
}

$(function () {
  $('.autocomplete-group-wrapper').each(function () {
    var $wrap = $(this);
    var api   = $wrap.data('api');
    var name  = $wrap.data('name');
    var $list = $wrap.find('.autocomplete-list ul');

    $wrap.find('.input-autocomplete').autocomplete({
      'source': function (request, response) {
        axios.get(api + '/autocomplete?keyword=' + encodeURIComponent(request)).then(function (res) {
          response($.map(res.data, function (item) {
            return { label: item['name'], value: item['id'] };
          }));
        });
      },
      'select': function (item) {
        // 去重：已存在相同值则跳过
        var alreadyPicked = $list.find('input[type="hidden"]').filter(function () {
          return String($(this).val()) === String(item['value']);
        }).length > 0;
        if (alreadyPicked) {
          return;
        }
        $list.append(
          '<li class="list-group list-group-item">' +
            '<span class="autocomplete-name">' + acEscapeHtml(item['label']) + '</span>' +
            '<button type="button" class="btn-close"></button>' +
            '<input name="' + name + '" type="hidden" value="' + acEscapeHtml(item['value']) + '" />' +
          '</li>'
        );
      }
    });
  });

  $(document).on('click', '.autocomplete-list .btn-close', function () {
    $(this).closest('li').remove();
  });
});
</script>
@endPushOnce

@push('footer')
<script>
// 预加载已选项（每个实例独立，用 {{ $id }} 限定目标列表）
(function () {
  var values = @json($value);
  if (!values || !values.length) return;
  axios.get('{{ $api }}?tag_ids=' + values.join(',')).then(function (res) {
    var data  = res.data;
    var $list = $('.autocomplete-list-{{ $id }} ul');
    for (var i = 0; i < values.length; i++) {
      var value = values[i];
      for (var j = 0; j < data.length; j++) {
        var item = data[j];
        if (String(item['id']) === String(value)) {
          $list.append(
            '<li class="list-group list-group-item">' +
              '<span class="autocomplete-name">' + acEscapeHtml(item['name']) + '</span>' +
              '<button type="button" class="btn-close"></button>' +
              '<input name="{{ $name }}" type="hidden" value="' + acEscapeHtml(item['id']) + '" />' +
            '</li>'
          );
          break;
        }
      }
    }
  });
})();
</script>
@endpush
