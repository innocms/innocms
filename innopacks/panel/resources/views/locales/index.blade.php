@extends('panel::layouts.app')
@section('body-class', 'page-locales')

@section('title', __('panel::menu.locale'))

@section('content')
<div class="card h-min-600">
  <div class="card-body">
    @if ($locales)
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <td>{{ __('panel::common.id') }}</td>
            <td>{{ __('panel::common.image') }}</td>
            <td>{{ __('panel::common.name') }}</td>
            <td>{{ __('panel::common.slug') }}</td>
            <td>{{ __('panel::common.position') }}</td>
            <td>{{ __('panel::common.actions') }}</td>
          </tr>
        </thead>
        <tbody>
        @foreach($locales as $item)
          <tr>
            <td>{{ $item['id'] }}</td>
            <td><img src="{{ image_resize($item['image']) }}" style="width: 30px;"></td>
            <td>{{ $item['name'] }}</td>
            <td>{{ $item['code'] }}</td>
            <td>{{ $item['position'] }}</td>
            <td>
              @if ($item['id'])
                <button type="button" class="btn btn-sm btn-outline-danger leng-unload" data-code="{{ $item['code'] }}">{{ __('panel::common.uninstall') }}</button>
              @else
                <button type="button" class="btn btn-sm btn-outline-primary leng-install" data-code="{{ $item['code'] }}">{{ __('panel::common.install') }}</button>
              @endif
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    @else
      <x-common-no-data :width="200" />
    @endif
  </div>
</div>
@endsection

@push('footer')
<script>
  $(function () {
    $('.leng-install').click(function () {
      axios.post('panel/locales/install', {code: $(this).data('code')}).then(function (res) {
        window.location.reload()
      })
    });

    $('.leng-unload').click(function () {
      axios.post(`panel/locales/${$(this).data('code')}/uninstall`).then(function (res) {
        window.location.reload()
      })
    });
  });
</script>
@endpush
