@extends('panel::layouts.app')
@section('body-class', 'page-home')

@section('title', '系统设置')

<x-panel::form.right-btns />

@section('content')
<form class="needs-validation" novalidate action="{{ panel_route('settings.update') }}" method="POST" id="app-form">
  @csrf
  @method('put')
  <div class="row">
    <div class="col-md-3">
      <div class="card" id="setting-menu">
        <div class="card-header">系统设置</div>
        <div class="card-body">
          <ul class="nav flex-column settings-nav">
            <a class="nav-link active" href="#" data-bs-toggle="tab" data-bs-target="#tab-setting-basics">基本设置</a>
            <a class="nav-link" href="#" data-bs-toggle="tab" data-bs-target="#tab-setting-storage">{{ __('panel/setting.storage_settings') }}</a>
          </ul>
        </div>
      </div>
    </div>
    <div class="col-md-9">
      <div class="card h-min-600">
        <div class="card-header setting-header">基本设置</div>
        <div class="card-body">
          <div class="tab-content">
            <!-- Basic Settings -->
            <div class="tab-pane fade show active" id="tab-setting-basics">
              <x-panel-form-image title="前台 Logo" name="front_logo" value="{{ old('front_logo', system_setting('front_logo')) }}"/>
              <x-panel-form-image title="后台 Logo" name="panel_logo" value="{{ old('panel_logo', system_setting('panel_logo')) }}"/>
              <x-panel-form-image title="缺省图" name="placeholder" value="{{ old('placeholder', system_setting('placeholder')) }}"/>
              <x-panel-form-image title="浏览器小图标" name="favicon" value="{{ old('favicon', system_setting('favicon')) }}"/>
              <x-panel-form-input title="Meta 标题" name="meta_title"
                                  value="{{ old('meta_title', system_setting('meta_title')) }}" required
                                  placeholder="Meta 标题"/>
              <x-panel-form-input title="Meta 关键词" name="meta_keywords"
                                  value="{{ old('meta_keywords', system_setting('meta_keywords')) }}"
                                  placeholder="Meta 关键词"/>
              <x-panel-form-textarea title="Meta 描述" name="meta_description"
                                     value="{{ old('meta_description', system_setting('meta_description')) }}"
                                     placeholder="Meta 描述"/>
              <x-panel-form-switch-radio title="启用 html 后缀" name="has_suffix" :value="old('has_suffix', system_setting('has_suffix', 0))"
                                         placeholder="启用 html 后缀"/>
              <x-panel-form-input title="ICP备案号" name="icp_number" value="{{ old('icp_number', system_setting('icp_number')) }}"/>
              <x-panel-form-textarea title="第三方JS代码" name="js_code"
                                     value="{{ old('js_code', system_setting('js_code')) }}"
                                     placeholder="第三方JS代码"/>
            </div>

            @include('panel::settings._storage_setting')
          </div>
        </div>
      </div>
    </div>
  </div>

  <button type="submit" class="d-none"></button>
</form>
@endsection

@push('footer')
<script>
  // Switch to tab from URL query param
  var tabParam = new URLSearchParams(window.location.search).get('tab');
  if (tabParam) {
    $('a[data-bs-target="#' + tabParam + '"]').tab('show');
  }
</script>
@endpush
