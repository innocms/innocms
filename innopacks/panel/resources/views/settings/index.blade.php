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
            <a class="nav-link" href="#" data-bs-toggle="tab" data-bs-target="#tab-setting-tools">{{ __('panel/setting.tools_setting') }}</a>
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
              <div class="row g-3">
                <div class="col-md-3">
                  <x-panel-form-image title="前台 Logo" name="front_logo" value="{{ old('front_logo', system_setting('front_logo')) }}"/>
                </div>
                <div class="col-md-3">
                  <x-panel-form-image title="后台 Logo" name="panel_logo" value="{{ old('panel_logo', system_setting('panel_logo')) }}"/>
                </div>
                <div class="col-md-3">
                  <x-panel-form-image title="缺省图" name="placeholder" value="{{ old('placeholder', system_setting('placeholder')) }}"/>
                </div>
                <div class="col-md-3">
                  <x-panel-form-image title="浏览器小图标" name="favicon" value="{{ old('favicon', system_setting('favicon')) }}"/>
                </div>
              </div>
              <div class="mb-3"></div>

              {{-- 店铺 / 公司基础信息（文本类支持多语言） --}}
              <x-common-form-locale-input
                  name="store_name"
                  type="input"
                  nameFormat="single"
                  label="{{ __('panel/setting.store_name') }}"
                  :translations="system_setting_translations('store_name')"
                  placeholder="{{ __('panel/setting.store_name') }}"/>
              <x-common-form-locale-input
                  name="store_description"
                  type="textarea"
                  nameFormat="single"
                  :rows="4"
                  label="{{ __('panel/setting.store_description') }}"
                  :translations="system_setting_translations('store_description')"
                  placeholder="{{ __('panel/setting.store_description') }}"/>
              <x-common-form-locale-input
                  name="address"
                  type="input"
                  nameFormat="single"
                  label="{{ __('panel/setting.address') }}"
                  :translations="system_setting_translations('address')"
                  placeholder="{{ __('panel/setting.address') }}"/>
              <x-panel-form-input title="{{ __('panel/setting.telephone') }}" name="telephone"
                                  value="{{ old('telephone', system_setting('telephone')) }}"
                                  placeholder="{{ __('panel/setting.telephone') }}"/>
              <x-panel-form-input title="{{ __('panel/setting.email') }}" name="email"
                                  value="{{ old('email', system_setting('email')) }}"
                                  placeholder="{{ __('panel/setting.email') }}"/>

              <div class="mb-3"></div>
              <x-common-form-locale-input
                  name="meta_title"
                  type="input"
                  nameFormat="single"
                  :required="true"
                  label="{{ __('panel/setting.meta_title') }}"
                  :translations="system_setting_translations('meta_title')"
                  placeholder="{{ __('panel/setting.meta_title') }}"/>
              <x-common-form-locale-input
                  name="meta_keywords"
                  type="input"
                  nameFormat="single"
                  label="{{ __('panel/setting.meta_keywords') }}"
                  :translations="system_setting_translations('meta_keywords')"
                  placeholder="{{ __('panel/setting.meta_keywords') }}"/>
              <x-common-form-locale-input
                  name="meta_description"
                  type="textarea"
                  nameFormat="single"
                  :rows="4"
                  label="{{ __('panel/setting.meta_description') }}"
                  :translations="system_setting_translations('meta_description')"
                  placeholder="{{ __('panel/setting.meta_description') }}"/>
              <x-panel-form-switch-radio title="启用 html 后缀" name="has_suffix" :value="old('has_suffix', system_setting('has_suffix', 0))"
                                         placeholder="启用 html 后缀"/>
              <x-panel-form-switch-radio title="隐藏 URL 语言标识" name="hide_url_locale" :value="old('hide_url_locale', system_setting('hide_url_locale', 0))"
                                         placeholder="仅一种语言时，URL 中不包含语言代码"/>
              <x-panel-form-input title="ICP备案号" name="icp_number" value="{{ old('icp_number', system_setting('icp_number')) }}"/>
              <x-panel-form-textarea title="第三方JS代码" name="js_code"
                                     value="{{ old('js_code', system_setting('js_code')) }}"
                                     placeholder="第三方JS代码"/>

              <h6 class="mt-4 mb-3 text-muted">{{ __('panel/setting.contact_info') }}</h6>
              <x-panel-form-input title="{{ __('panel/setting.contact_phone') }}" name="contact_phone"
                                  value="{{ old('contact_phone', system_setting('contact_phone')) }}"
                                  placeholder="136-0000-0000"/>
              <x-panel-form-input title="{{ __('panel/setting.contact_email') }}" name="contact_email"
                                  value="{{ old('contact_email', system_setting('contact_email')) }}"
                                  placeholder="team@example.com"/>
              <x-panel-form-input title="{{ __('panel/setting.contact_address') }}" name="contact_address"
                                  value="{{ old('contact_address', system_setting('contact_address')) }}"
                                  placeholder="成都市高新区…"/>
              <x-panel-form-input title="{{ __('panel/setting.contact_hours') }}" name="contact_hours"
                                  value="{{ old('contact_hours', system_setting('contact_hours')) }}"
                                  placeholder="周一至周五 9:00-18:00"/>
            </div>

            @include('panel::settings._storage_setting')

            @include('panel::settings._tools_setting')
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
