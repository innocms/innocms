@extends('panel::layouts.app')
@section('body-class', '')

@section('title', __('panel::menu.roles'))
@section('page-title-right')
  <a href="{{ panel_route('roles.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-square"></i> {{ __('panel::common.create') }}
  </a>
@endsection

@section('content')
  <div class="card h-min-600">
    <div class="card-body">
      @if ($roles->count())
      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
          <tr>
            <th>{{ __('panel::common.id') }}</th>
            <th>{{ __('panel::common.name') }}</th>
            <th>{{ __('panel::common.actions') }}</th>
          </tr>
          </thead>
          <tbody>
          @foreach ($roles as $product)
            <tr>
              <td>{{ $product->id }}</td>
              <td>{{ $product->name }}</td>
              <td>
                <a href="{{ panel_route('roles.edit', [$product->id]) }}"
                   class="btn btn-outline-primary btn-sm">{{ __('panel::common.edit')}}</a>
                <button class="btn btn-outline-danger btn-sm" type="button">{{ __('panel::common.delete')}}</button>
              </td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
      {{ $roles->withQueryString()->links('panel::vendor/pagination/bootstrap-4') }}
      @else
      <x-common-no-data />
      @endif
    </div>
  </div>
@endsection

@push('footer')
  <script>
  </script>
@endpush