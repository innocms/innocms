@extends('panel::layouts.app')
@section('body-class', 'page-my-plugins')

@section('title', __('panel::menu.plugin'))

@section('content')
<div class="card h-min-600">
  <div class="card-header">插件列表</div>

  <div class="card-body">

    <div class="row">
      @foreach ($plugins as $plugin)
      <div class="col-6 col-md-3 mb-4">
        <div class="plugin-item" data-code="{{ $plugin['code'] }}" data-installed="{{ $plugin['installed'] ? 1 : 0 }}">
          <div class="image">
            <img src="{{ $plugin['icon'] }}" alt="{{ $plugin['name'] }}" class="img-fluid">
          </div>

          <div class="plugin-info">
            <div class="title">{{ $plugin['name'] }}</div>

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
                  <a href="{{ $plugin['edit_url'] }}" class="btn btn-primary btn-sm">{{ __('panel::common.edit') }}</a>
                  <div class="btn btn-danger btn-sm uninstall-plugin">{{ __('panel::common.uninstall') }}</div>
                  @else
                  <div class="btn btn-primary btn-sm install-plugin">{{ __('panel::common.install') }}</div>
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
    $(function() {
      $('.install-plugin').click(function() {
        var code = $(this).parents('.plugin-item').data('code');
        pluginsUpdate(code, 'install');
      });

      $('.uninstall-plugin').click(function() {
        var code = $(this).parents('.plugin-item').data('code');
        pluginsUpdate(code, 'uninstall');
      });
    });

    $('.plugin-enabled-switch input').change(function() {
      var code = $(this).parents('.plugin-item').data('code');
      var enabled = $(this).prop('checked') ? 1 : 0;
      axios.post('/panel/plugins/enabled', { code: code, enabled: enabled }).then(function(response) {
        if (response.data.status === 'success') {
          window.location.reload();
        } else {
          is.alert(response.data.message);
        }
      });
    });

    function pluginsUpdate(code, type) {
      const url = type === 'install' ? '/panel/plugins' : '/panel/plugins/' + code;
      const method = type === 'install' ? 'post' : 'delete';

      axios[method](url, { code: code }).then(function(response) {
        if (response.data.status === 'success') {
          window.location.reload();
        } else {
          is.alert(response.data.message);
        }
      });
    }
  </script>
@endpush