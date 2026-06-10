@extends('panel::layouts.app')
@section('body-class', 'page-my-plugins')

@section('title', __('panel/menu.plugins'))

@section('content')
<div class="card h-min-600">
  <div class="card-header">{{ __('panel/menu.plugins') }}</div>

  <div class="card-body">

    <div class="row">
      @foreach ($plugins as $plugin)
      <div class="col-6 col-lg-3 mb-4">
        <div class="plugin-item" data-code="{{ $plugin['code'] }}" data-installed="{{ $plugin['installed'] ? 1 : 0 }}">
          <div class="image-wrap">
            <div class="image"><img src="{{ $plugin['icon'] }}" alt="{{ $plugin['name'] }}" class="img-fluid"></div>
            <div class="title-wrap">
              <div class="title">{{ $plugin['name'] }}</div>
              <div class="plugin-meta">
                <span class="font-monospace">{{ $plugin['code'] }}</span>
                @if(!empty($plugin['version']))
                  <span class="plugin-meta-dot">&middot;</span>
                  <span>{{ $plugin['version'] }}</span>
                @endif
              </div>
            </div>
          </div>

          <div class="plugin-info">
            <div class="description">{{ $plugin['description'] }}</div>
            <div class="d-flex justify-content-between align-items-center">
              <div class="version">
                <div class="d-flex align-items-center">
                  <div class="form-switch plugin-enabled-switch cursor-pointer">
                    <input class="form-check-input" type="checkbox" {{ !$plugin['installed'] ? 'disabled' : '' }} role="switch" {{ $plugin['enabled'] ? 'checked' : '' }}>
                  </div>
                </div>
              </div>
              <div class="btns">
                @if ($plugin['installed'])
                  <a href="{{ $plugin['edit_url'] }}" class="btn btn-primary btn-sm">{{ __('panel/common.edit') }}</a>
                  <div class="btn btn-danger btn-sm uninstall-plugin">{{ __('panel/common.uninstall') }}</div>
                @else
                  <div class="btn btn-primary btn-sm install-plugin">{{ __('panel/common.install') }}</div>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</div>
@endsection

@push('footer')
<script>
var pluginLabels = {
  edit: '{{ __("panel/common.edit") }}',
  uninstall: '{{ __("panel/common.uninstall") }}',
  install: '{{ __("panel/common.install") }}'
};

$(function() {
  $(document).on('click', '.install-plugin', function() {
    var $item = $(this).closest('.plugin-item');
    var code = $item.data('code');
    pluginUpdate($item, code, 'install');
  });

  $(document).on('click', '.uninstall-plugin', function() {
    var $item = $(this).closest('.plugin-item');
    var code = $item.data('code');
    pluginUpdate($item, code, 'uninstall');
  });
});

$(document).on('change', '.plugin-enabled-switch input', function() {
  var $item = $(this).closest('.plugin-item');
  var code = $item.data('code');
  var enabled = $(this).prop('checked') ? 1 : 0;
  var $switch = $(this);

  axios.post('/{{ panel_name() }}/plugins/enabled', { code: code, enabled: enabled }).then(function(data) {
    if (data.success) {
      updatePluginCard($item, data.data);
      inno.msg(data.message);
    } else {
      $switch.prop('checked', !enabled);
      inno.msg(data.message || '{{ __("panel/common.error") }}');
    }
  }).catch(function() {
    $switch.prop('checked', !enabled);
  });
});

function updatePluginCard($item, plugin) {
  $item.attr('data-installed', plugin.installed ? 1 : 0);
  var $btns = $item.find('.btns');
  var $switch = $item.find('.plugin-enabled-switch input');

  if (plugin.installed) {
    var html = '<a href="' + plugin.edit_url + '" class="btn btn-primary btn-sm">' + pluginLabels.edit + '</a> ';
    html += '<div class="btn btn-danger btn-sm uninstall-plugin">' + pluginLabels.uninstall + '</div>';
    $btns.html(html);
    $switch.prop('disabled', false).prop('checked', !!plugin.enabled);
  } else {
    $btns.html('<div class="btn btn-primary btn-sm install-plugin">' + pluginLabels.install + '</div>');
    $switch.prop('disabled', true).prop('checked', false);
  }
}

function pluginUpdate($item, code, type) {
  var url = type === 'install' ? '/{{ panel_name() }}/plugins' : '/{{ panel_name() }}/plugins/' + code;
  var method = type === 'install' ? 'post' : 'delete';

  axios[method](url, { code: code }).then(function(data) {
    if (data.success) {
      updatePluginCard($item, data.data);
      inno.msg(data.message);
    } else {
      inno.msg(data.message || '{{ __("panel/common.error") }}');
    }
  }).catch(function(error) {
    var resp = error.response && error.response.data;
    inno.msg((resp && resp.message) || '{{ __("panel/common.error") }}');
  });
}
</script>
@endpush
