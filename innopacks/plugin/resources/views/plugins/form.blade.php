@extends('panel::layouts.app')

@section('title', '插件详情')

@section('content')
<div class="card h-min-600">
  <div class="card-body">
    <h6 class="border-bottom pb-3 mb-4">{{ $plugin->getLocaleName() }}</h6>

    <form class="needs-validation" novalidate action="{{ panel_route('plugins.update', $plugin->getCode()) }}" method="POST">
      @csrf
      {{ method_field('put') }}

      @foreach ($columns as $column)
        @if ($column['type'] == 'image')
          <x-panel-form-image
            :name="$column['name']"
            :title="$column['label']"
            :description="$column['description'] ?? ''"
            :error="$errors->first($column['name'])"
            :required="$column['required'] ? true : false"
            :value="old($column['name'], $column['value'] ?? '')">
            @if ($column['recommend_size'] ?? false)
            <div class="help-text font-size-12 lh-base">{{ __('common.recommend_size') }} {{ $column['recommend_size'] }}</div>
            @endif
          </x-panel-form-image>
        @endif

        @if ($column['type'] == 'string')
          <x-panel-form-input
            :name="$column['name']"
            :title="$column['label']"
            :placeholder="$column['placeholder'] ?? ''"
            :description="$column['description'] ?? ''"
            :error="$errors->first($column['name'])"
            :required="$column['required'] ? true : false"
            :value="old($column['name'], $column['value'] ?? '')" />
        @endif

        {{-- @if ($column['type'] == 'string-multiple')
          <x-panel-form-input-locale
            :name="$column['name'].'.*'"
            :title="$column['label']"
            :placeholder="$column['placeholder'] ?? ''"
            :error="$errors->first($column['name'])"
            :required="$column['required'] ? true : false"
            :value="old($column['name'], $column['value'] ?? '')" />
        @endif --}}

        @if ($column['type'] == 'select')
          <x-panel-form-select
            :name="$column['name']"
            :title="$column['label']"
            :value="old($column['name'], $column['value'] ?? '')"
            :options="$column['options']">
            @if (isset($column['description']))
              <div class="help-text font-size-12 lh-base">{{ $column['description'] }}</div>
            @endif
          </x-panel-form-select>
        @endif

        @if ($column['type'] == 'bool')
          <x-panel-form-switch-radio
            :name="$column['name']"
            :title="$column['label']"
            :value="old($column['name'], $column['value'] ?? '')">
            @if (isset($column['description']))
              <div class="help-text font-size-12 lh-base">{{ $column['description'] }}</div>
            @endif
          </x-panel-form-switch-radio>
        @endif

        @if ($column['type'] == 'textarea')
          <x-panel-form-textarea
            :name="$column['name']"
            :title="$column['label']"
            :required="$column['required'] ? true : false"
            :value="old($column['name'], $column['value'] ?? '')">
            @if (isset($column['description']))
              <div class="help-text font-size-12 lh-base">{{ $column['description'] }}</div>
            @endif
          </x-panel-form-textarea>
        @endif

        @if ($column['type'] == 'rich-text')
          <x-panel-form-rich-text
            :name="$column['name']"
            :title="$column['label']"
            :value="old($column['name'], $column['value'] ?? '')"
            :required="$column['required'] ? true : false"
            :multiple="$column['multiple'] ?? false"
            >
            @if (isset($column['description']))
              <div class="help-text font-size-12 lh-base">{{ $column['description'] }}</div>
            @endif
          </x-panel-form-rich-text>
        @endif

        @if ($column['type'] == 'checkbox')
          <x-panel::form.row :title="$column['label']" :required="$column['required'] ? true : false">
            <div class="form-checkbox">
              @foreach ($column['options'] as $item)
              <div class="form-check d-inline-block mt-2 me-3">
                <input
                  class="form-check-input"
                  name="{{ $column['name'] }}[]"
                  type="checkbox"
                  value="{{ old($column['name'], $item['value']) }}"
                  {{ in_array($item['value'], old($column['name'], json_decode($column['value'] ?? '[]', true))) ? 'checked' : '' }}
                  id="flexCheck-{{ $column['name'] }}-{{ $loop->index }}">
                <label class="form-check-label" for="flexCheck-{{ $column['name'] }}-{{ $loop->index }}">
                  {{ $item['label'] }}
                </label>
              </div>
              @endforeach
            </div>
            @if (isset($column['description']))
              <div class="help-text font-size-12 lh-base">{{ $column['description'] }}</div>
            @endif
          </x-panel::form.row>
        @endif
      @endforeach

      <x-panel::form.bottom-btns/>
    </form>
  </div>
</div>
@endsection

@push('footer')
  <script></script>
@endpush
