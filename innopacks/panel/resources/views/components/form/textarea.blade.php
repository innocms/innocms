<x-panel::form.row :title="$title" :required="$required">
  <div>
    <textarea rows="4" type="text" name="{{ $name }}" class="form-control" @if ($required) required @endif placeholder="{{ $title }}">{{ $value }}</textarea>
    <span class="invalid-feedback" role="alert">
      {{ __('panel/common.field_required', ['title' => $title]) }}
    </span>
  </div>
  {{ $slot }}
</x-panel::form.row>
