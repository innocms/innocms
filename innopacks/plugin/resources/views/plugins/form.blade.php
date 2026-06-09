@extends('panel::layouts.app')

@section('title', $plugin->getLocaleName())

@if($plugin->checkInstalled())
<x-panel::form.right-btns />
@endif

@section('content')
<div class="card h-min-600">
  <div class="card-body">
    {{-- Plugin Header --}}
    <div class="row mb-4 align-items-center pb-3 plugin-header-bar">
      <div class="col-auto">
        @if($plugin->getIconUrl())
          <img src="{{ $plugin->getIconUrl() }}" alt="logo" class="img-fluid rounded me-4 plugin-header-logo">
        @endif
      </div>
      <div class="col ps-0">
        <div class="fw-bold fs-4 mb-1">
          {{ $plugin->getLocaleName() }}
          @if($plugin->getVersion())
            <small class="text-muted ms-2">{{ $plugin->getVersion() }}</small>
          @endif
        </div>
        @if($plugin->getLocaleDescription())
          <div class="mb-2 text-secondary plugin-header-desc">{{ $plugin->getLocaleDescription() }}</div>
        @endif
        <div class="d-flex flex-wrap gap-3 align-items-center">
          <div class="text-secondary small">
            <i class="bi bi-person"></i> {{ __('panel/plugin.author') }}: {{ $plugin->getAuthor() }}
          </div>
          @if($plugin->getType())
            <div class="text-secondary small"><i class="bi bi-tag"></i> {{ __('panel/plugin.type') }}: {{ $plugin->getType() }}</div>
          @endif
        </div>
      </div>
    </div>

    @php
      $readmeHtml = $plugin->getReadmeHtml();
    @endphp

    @if(!$plugin->checkInstalled())
      <div class="text-center py-5">
        <p class="text-secondary mb-3">{{ trans('panel/plugin.not_installed_hint') }}</p>
        <button type="button" class="btn btn-primary btn-install-plugin" data-code="{{ $plugin->getCode() }}">
          <i class="bi bi-puzzle-fill"></i> {{ __('panel/common.install') }}
        </button>
      </div>
    @else
    <ul class="nav nav-tabs mt-4" role="tablist">
      <li class="nav-item">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#config-tab" role="tab">
          <i class="bi bi-gear"></i> {{ trans('panel/plugin.config_settings') }}
        </button>
      </li>
      @if(!empty($readmeHtml) && trim($readmeHtml) !== '')
      <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#readme-tab" role="tab">
          <i class="bi bi-book"></i> {{ trans('panel/plugin.usage_documentation') }}
        </button>
      </li>
      @endif
      @if($plugin->hasSeeders())
      <li class="nav-item ms-auto">
        <button type="button" class="btn btn-outline-primary btn-sm mt-1" data-bs-toggle="modal" data-bs-target="#seederConfirmModal">
          <i class="bi bi-database-fill-add"></i> 导入初始数据
        </button>
      </li>
      @endif
    </ul>

    <div class="tab-content mt-3">
      <div class="tab-pane fade show active" id="config-tab" role="tabpanel">
        @if(!empty($view) && $view !== 'plugin::plugins.form')
          @includeIf($view, ['plugin' => $plugin, 'fields' => $fields ?? [], 'errors' => $errors ?? []])
        @else
        <form class="needs-validation" id="app-form" novalidate action="{{ panel_route('plugins.update', [$plugin->getCode()]) }}" method="POST">
          @csrf
          {{ method_field('put') }}
          <div class="row">
            <div class="col-12 col-md-7">
              @foreach ($fields as $field)
                @if ($field['type'] == 'image')
                  <x-common-form-image
                    :name="$field['name']"
                    :title="$field['label']"
                    :description="$field['description'] ?? ''"
                    :error="$errors->first($field['name'])"
                    :required="(bool)$field['required']"
                    :value="old($field['name'], $field['value'] ?? '')">
                  </x-common-form-image>
                @endif

                @if ($field['type'] == 'string')
                  <x-common-form-input
                    :name="$field['name']"
                    :title="$field['label']"
                    :placeholder="$field['placeholder'] ?? ''"
                    :description="$field['description'] ?? ''"
                    :error="$errors->first($field['name'])"
                    :required="(bool)$field['required']"
                    :value="old($field['name'], $field['value'] ?? '')" />
                @endif

                @if ($field['type'] == 'multi-string')
                  <x-common-form-input
                    :name="$field['name']"
                    :title="$field['label']"
                    :placeholder="$field['placeholder'] ?? ''"
                    :description="$field['description'] ?? ''"
                    :error="$errors->first($field['name'])"
                    :required="(bool)$field['required']"
                    :is-locales="true"
                    :value="old($field['name'], $field['value'] ?? '')" />
                @endif

                @if ($field['type'] == 'select')
                  <x-common-form-select
                    :name="$field['name']"
                    :title="$field['label']"
                    :value="old($field['name'], $field['value'] ?? '')"
                    :options="$field['options']">
                    @if (isset($field['description']))
                      <div class="help-text font-size-12 lh-base">{{ $field['description'] }}</div>
                    @endif
                  </x-common-form-select>
                @endif

                @if ($field['type'] == 'bool')
                  <x-common-form-switch-radio
                    :name="$field['name']"
                    :title="$field['label']"
                    :value="old($field['name'], $field['value'] ?? '')">
                    @if (isset($field['description']))
                      <div class="help-text font-size-12 lh-base">{{ $field['description'] }}</div>
                    @endif
                  </x-common-form-switch-radio>
                @endif

                @if ($field['type'] == 'textarea')
                  <x-common-form-textarea
                    :name="$field['name']"
                    :title="$field['label']"
                    :required="(bool)$field['required']"
                    :value="old($field['name'], $field['value'] ?? '')">
                    @if (isset($field['description']))
                      <div class="help-text font-size-12 lh-base">{{ $field['description'] }}</div>
                    @endif
                  </x-common-form-textarea>
                @endif

                @if ($field['type'] == 'multi-textarea')
                  <x-common-form-textarea
                    :name="$field['name']"
                    :title="$field['label']"
                    :required="(bool)$field['required']"
                    :is-locales="true"
                    :value="old($field['name'], $field['value'] ?? '')">
                    @if (isset($field['description']))
                      <div class="help-text font-size-12 lh-base">{{ $field['description'] }}</div>
                    @endif
                  </x-common-form-textarea>
                @endif

                @if ($field['type'] == 'rich-text')
                  <x-common-form-rich-text
                    :name="$field['name']"
                    :title="$field['label']"
                    :value="old($field['name'], $field['value'] ?? '')"
                    :required="(bool)$field['required']"
                    >
                    @if (isset($field['description']))
                      <div class="help-text font-size-12 lh-base">{{ $field['description'] }}</div>
                    @endif
                  </x-common-form-rich-text>
                @endif

                @if ($field['type'] == 'multi-rich-text')
                  <x-common-form-rich-text
                    :name="$field['name']"
                    :title="$field['label']"
                    :value="old($field['name'], $field['value'] ?? '')"
                    :required="(bool)$field['required']"
                    :is-locales="true"
                    >
                    @if (isset($field['description']))
                      <div class="help-text font-size-12 lh-base">{{ $field['description'] }}</div>
                    @endif
                  </x-common-form-rich-text>
                @endif

                @if ($field['type'] == 'checkbox')
                  <x-panel::form.row :title="$field['label']" :required="(bool)$field['required']">
                    <div class="form-checkbox">
                      @foreach ($field['options'] as $item)
                      <div class="form-check d-inline-block mt-2 me-3">
                        <input
                          class="form-check-input"
                          name="{{ $field['name'] }}[]"
                          type="checkbox"
                          value="{{ old($field['name'], $item['value']) }}"
                          {{ in_array($item['value'], old($field['name'], json_decode($field['value'] ?? '[]', true))) ? 'checked' : '' }}
                          id="flexCheck-{{ $field['name'] }}-{{ $loop->index }}">
                        <label class="form-check-label" for="flexCheck-{{ $field['name'] }}-{{ $loop->index }}">
                          {{ $item['label'] }}
                        </label>
                      </div>
                      @endforeach
                    </div>
                    @if (isset($field['description']))
                      <div class="help-text font-size-12 lh-base">{{ $field['description'] }}</div>
                    @endif
                  </x-panel::form.row>
                @endif
              @endforeach
            </div>
          </div>
        </form>
        @endif
      </div>
      @if(!empty($readmeHtml) && trim($readmeHtml) !== '')
      <div class="tab-pane fade" id="readme-tab" role="tabpanel">
        <div class="markdown-body">
          {!! $readmeHtml !!}
        </div>
      </div>
      @endif
    </div>
    @endif
  </div>
</div>

@if($plugin->checkInstalled() && $plugin->hasSeeders())
<div class="modal fade" id="seederConfirmModal" tabindex="-1" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">确认导入初始数据</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="mb-3">即将导入插件的初始数据，是否继续？</p>
        <div class="form-check mb-0">
          <input class="form-check-input seeder-clear-data" type="checkbox" value="1" id="seederClearData">
          <label class="form-check-label" for="seederClearData">
            导入前清空插件相关数据
          </label>
        </div>
        <p class="text-muted small mt-2 mb-0">勾选后将清空该插件所有数据后再重新导入，请谨慎操作。</p>
        <div class="alert alert-danger mt-3 d-none seeder-error-wrap" role="alert">
          <i class="bi bi-exclamation-triangle-fill me-2"></i>
          <span class="seeder-error-msg"></span>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">取消</button>
        <button type="button" class="btn btn-primary btn-confirm-seeder" data-code="{{ $plugin->getCode() }}">
          <span class="spinner-border spinner-border-sm d-none me-2 seeder-spinner" role="status" aria-hidden="true"></span>
          确认导入
        </button>
      </div>
    </div>
  </div>
</div>
@endif
@endsection

@push('header')
  <style>
    .markdown-body {
      max-width: 900px;
      margin: 0 auto;
      padding: 24px;
      background: #fff;
      border-radius: 8px;
      font-size: 14px;
    }
    .markdown-body h1 { font-size: 1.6em; border-bottom: 1px solid #eee; padding-bottom: 0.3em; }
    .markdown-body h2 { font-size: 1.4em; border-bottom: 1px solid #eee; padding-bottom: 0.3em; }
    .markdown-body h3 { font-size: 1.2em; }
    .markdown-body code { background: #f6f8fa; padding: 2px 6px; border-radius: 3px; font-size: 0.9em; }
    .markdown-body pre { background: #f6f8fa; padding: 16px; border-radius: 6px; overflow-x: auto; }
    .markdown-body pre code { background: none; padding: 0; }
    .markdown-body blockquote { border-left: 4px solid #ddd; padding: 0 16px; color: #666; }
    .markdown-body table { border-collapse: collapse; width: 100%; }
    .markdown-body th, .markdown-body td { border: 1px solid #ddd; padding: 8px 12px; }
    .markdown-body img { max-width: 100%; }
    .plugin-header-bar { border-bottom: 1.5px solid #eee; }
    .plugin-header-logo { max-height: 100px; border: 1px solid #e9e9e9; }
    .plugin-header-desc { font-size: 15px; }
  </style>
@endpush

@push('footer')
  <script>
    $(function () {
      $('.btn-install-plugin').click(function () {
        var code = $(this).data('code');
        axios.post('/{{ panel_name() }}/plugins', {code: code}).then(function (res) {
          if (res && res.data && res.data.success) {
            window.location.reload();
          } else {
            inno.msg(res.data ? res.data.message : 'Install failed');
          }
        }).catch(function (error) {
          var data = error.response ? error.response.data : {};
          inno.msg(data.message || error.message || 'Install failed');
        });
      });

      var $seederModal = $('#seederConfirmModal');
      $seederModal.on('show.bs.modal', function () {
        $(this).find('.seeder-clear-data').prop('checked', false);
        $(this).find('.seeder-error-wrap').addClass('d-none');
      });

      $seederModal.on('click', '.btn-confirm-seeder', function () {
        var btn = $(this);
        var code = btn.data('code');
        var clearData = $seederModal.find('.seeder-clear-data').is(':checked');
        var spinner = btn.find('.seeder-spinner');

        btn.prop('disabled', true);
        spinner.removeClass('d-none');
        $seederModal.find('.seeder-error-wrap').addClass('d-none');

        axios.post('/{{ panel_name() }}/plugins/seeders', {code: code, clear_data: clearData}).then(function (data) {
          btn.prop('disabled', false);
          spinner.addClass('d-none');
          if (data.success) {
            $seederModal.modal('hide');
            inno.msg(data.message);
          } else {
            $seederModal.find('.seeder-error-msg').text(data.message || 'Failed');
            $seederModal.find('.seeder-error-wrap').removeClass('d-none');
          }
        }).catch(function (error) {
          btn.prop('disabled', false);
          spinner.addClass('d-none');
          var resp = error.response ? error.response.data : {};
          $seederModal.find('.seeder-error-msg').text(resp.message || error.message || 'Failed');
          $seederModal.find('.seeder-error-wrap').removeClass('d-none');
        });
      });
    });
  </script>
@endpush
