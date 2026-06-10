@extends('panel::layouts.app')
@section('body-class', 'page-articles')

@section('title', __('panel/menu.articles'))
@section('page-title-right')
  <a href="{{ panel_route('articles.create') }}" class="btn btn-primary btn-sm">
    <i class="bi bi-plus-square"></i> {{ __('common/base.create') }}
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

    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
        <tr>
          <td>{{ __('common/base.id') }}</td>
          <td>{{ __('panel/article.image') }}</td>
          <td>{{ __('panel/article.title') }}</td>
          <td class="d-none d-md-table-cell">{{ __('panel/article.catalog') }}</td>
          <td>{{ __('common/base.position') }}</td>
          <td>{{ __('panel/common.active') }}</td>
          <td>{{ __('panel/common.actions') }}</td>
        </tr>
        </thead>
        @if ($articles->count())
          <tbody>
          @foreach($articles as $item)
            <tr>
              <td>{{ $item->id }}</td>
              <td><img src="{{ image_resize($item->image, 30, 30) }}" class="wh-30"></td>
              <td>
                <a href="{{ $item->url }}" target="_blank" class="text-decoration-none"
                   data-bs-toggle="tooltip" title="{{ $item->title }}">
                  {{ sub_string($item->title, 32) }}
                  <i class="bi bi-box-arrow-up-right small"></i>
                </a>
              </td>
              <td class="d-none d-md-table-cell">{{ $item->catalog->title }}</td>
              <td>{{ $item->position }}</td>
              <td>@include('panel::shared.list_switch', ['value' => $item->active, 'url' => panel_route('articles.active', $item->id)])</td>
              <td>
                <div class="d-flex gap-1">
                  <a href="{{ panel_route('articles.edit', [$item->id]) }}" class="btn btn-sm btn-outline-primary">{{ __('common/base.edit') }}</a>
                  <form action="{{ panel_route('articles.destroy', [$item->id]) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-sm btn-outline-danger btn-delete">{{ __('common/base.delete') }}</button>
                  </form>
                  @hookinsert('panel.articles.index.row_actions')
                </div>
              </td>
            </tr>
          @endforeach
          </tbody>
        @else
          <tbody>
          <tr>
            <td colspan="7">
              <x-common-no-data :width="200" />
            </td>
          </tr>
          </tbody>
        @endif
      </table>
    </div>
    {{ $articles->withQueryString()->links('panel::vendor/pagination/bootstrap-4') }}
  </div>
</div>
@endsection

@push('footer')
<script>
document.querySelectorAll('.btn-delete').forEach(btn => {
  btn.addEventListener('click', function(e) {
    e.preventDefault();
    const form = this.closest('form');
    layer.confirm('{{ __("common/base.hint_delete") }}', {
      btn: ['{{ __("common/base.confirm") }}', '{{ __("common/base.cancel") }}'],
      title: false,
    }, function(index) {
      form.submit();
      layer.close(index);
    });
  });
});
</script>
@endpush
