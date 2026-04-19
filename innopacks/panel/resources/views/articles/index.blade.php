@extends('panel::layouts.app')
@section('body-class', 'page-articles')

@section('title', __('panel::menu.articles'))

@section('page-title-right')
  <a href="{{ panel_route('articles.create') }}" class="btn btn-primary btn-sm">
    <i class="bi bi-plus-square"></i> {{ __('panel::common.create') }}
  </a>
@endsection

@section('content')
<div class="card h-min-600">
  <div class="card-body">
    <x-panel-data-search
      :action="panel_route('articles.index')"
      :searchFields="$searchFields ?? []"
      :filters="$filterButtons ?? []"
    />

    @if ($articles->count())
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <td>{{ __('panel::common.id') }}</td>
            <td>{{ __('panel::common.image') }}</td>
            <td>{{ __('panel::common.name') }}</td>
            <td>{{ __('panel::menu.catalogs') }}</td>
            <td>{{ __('panel::menu.tags') }}</td>
            <td>{{ __('panel::common.slug') }}</td>
            <td>{{ __('panel::common.actions') }}</td>
          </tr>
        </thead>
        <tbody>
        @foreach($articles as $item)
          <tr>
            <td>{{ $item->id }}</td>
            <td><img src="{{ image_resize($item->translation->image ?? '', 30, 30) }}" style="width: 30px; height: 30px" alt=""></td>
            <td>{{ sub_string($item->translation->title ?? '', 30) }}</td>
            <td>{{ $item->catalog->translation->title ?? '-' }}</td>
            <td>{{ $item->tagNames ?? '' }}</td>
            <td>{{ $item->slug }}</td>
            <td>
              <a href="{{ panel_route('articles.edit', [$item->id]) }}" class="btn btn-sm btn-outline-primary">{{ __('panel::common.edit') }}</a>
              <form action="{{ panel_route('articles.destroy', [$item->id]) }}" method="POST" class="d-inline">
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
    {{ $articles->withQueryString()->links('panel::vendor/pagination/bootstrap-4') }}
    @else
      <x-common-no-data :width="200" />
    @endif
  </div>
</div>
@endsection
