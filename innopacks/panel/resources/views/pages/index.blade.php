@extends('panel::layouts.app')
@section('body-class', 'page-pages')

@section('title', __('panel::menu.pages'))

@section('page-title-right')
  <a href="{{ panel_route('pages.create') }}" class="btn btn-primary btn-sm">
    <i class="bi bi-plus-square"></i> {{ __('panel::common.create') }}
  </a>
@endsection

@section('content')
<div class="card h-min-600">
  <div class="card-body">
    <x-panel-data-search
      :action="panel_route('pages.index')"
      :searchFields="$searchFields ?? []"
      :filters="$filterButtons ?? []"
    />

    @if ($pages->count())
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <td>{{ __('panel::common.id') }}</td>
            <td>{{ __('panel::common.slug') }}</td>
            <td>{{ __('panel::common.name') }}</td>
            <td>{{ __('panel::common.viewed') }}</td>
            <td>{{ __('panel::common.status') }}</td>
            <td>{{ __('panel::common.actions') }}</td>
          </tr>
        </thead>
        <tbody>
        @foreach($pages as $item)
          <tr>
            <td>{{ $item->id }}</td>
            <td>{{ $item->slug }}</td>
            <td>{{ $item->translation->title ?? '' }}</td>
            <td>{{ $item->viewed }}</td>
            <td>{{ $item->active ? __('panel::common.active') : __('panel::common.inactive') }}</td>
            <td>
              <a href="{{ panel_route('pages.edit', [$item->id]) }}" class="btn btn-sm btn-outline-primary">{{ __('panel::common.edit') }}</a>
              <form action="{{ panel_route('pages.destroy', [$item->id]) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('panel::common.delete') }}</button>
              </form>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    {{ $pages->withQueryString()->links('panel::vendor/pagination/bootstrap-4') }}
    @else
      <x-common-no-data :width="200" />
    @endif
  </div>
</div>
@endsection
