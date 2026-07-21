@extends('panel::layouts.app')
@section('body-class', 'page-catalogs')

@section('title', __('panel/menu.catalogs'))

@section('page-title-right')
  <a href="{{ panel_route('catalogs.create') }}" class="btn btn-primary btn-sm">
    <i class="bi bi-plus-square"></i> {{ __('panel/common.create') }}
  </a>
@endsection

@section('content')
<div class="card h-min-600">
  <div class="card-body">
    <x-panel-data-search
      :action="panel_route('catalogs.index')"
      :searchFields="$searchFields ?? []"
      :filters="$filterButtons ?? []"
    />

    @if ($catalogs->count())
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <td class="col-drag"></td>
            <td>{{ __('panel/common.id') }}</td>
            <td>{{ __('panel/common.name') }}</td>
            <td>{{ __('panel/menu.catalogs') }}</td>
            <td>{{ __('panel/common.slug') }}</td>
            <td>{{ __('panel/common.position') }}</td>
            <td>{{ __('panel/common.status') }}</td>
            <td>{{ __('panel/common.actions') }}</td>
          </tr>
        </thead>
        <tbody>
        @foreach($catalogs as $item)
          <tr data-id="{{ $item->id }}">
            <td class="text-center col-drag">
              <i class="bi bi-grip-vertical drag-handle text-muted" title="{{ __('panel/common.drag_sort_hint') }}"></i>
            </td>
            <td>{{ $item->id }}</td>
            <td>{{ $item->title }}</td>
            <td>{{ $item->parent->title ?? '-' }}</td>
            <td>{{ $item->slug }}</td>
            <td>{{ $item->position }}</td>
            <td>@include('panel::shared.list_switch', ['value' => $item->active, 'url' => panel_route('catalogs.active', $item->id)])</td>
            <td>
              <a href="{{ panel_route('catalogs.edit', [$item->id]) }}" class="btn btn-sm btn-outline-primary">{{ __('panel/common.edit') }}</a>
              <form action="{{ panel_route('catalogs.destroy', [$item->id]) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('panel/common.delete') }}</button>
              </form>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    {{ $catalogs->withQueryString()->links('panel::vendor/pagination/bootstrap-4') }}
    @else
      <x-common-no-data :width="200" />
    @endif
  </div>
</div>
@endsection

@push('footer')
    <script src="{{ asset('vendor/vuedraggable/sortable.min.js') }}"></script>
    <script>
    function initCatalogSortable() {
      if (typeof Sortable === 'undefined') return;
      const tbody = document.querySelector('table tbody');
      if (!tbody || tbody.dataset.sortableInit) return;
      tbody.dataset.sortableInit = '1';
      new Sortable(tbody, {
        handle: '.drag-handle',
        animation: 150,
        ghostClass: 'sortable-ghost',
        onEnd: function(evt) {
          const ids = [];
          Array.prototype.forEach.call(evt.to.querySelectorAll(':scope > tr'), function(tr) {
            const id = tr.getAttribute('data-id');
            if (id) ids.push(parseInt(id));
          });
          if (ids.length < 1) return;
          axios.post(@json(panel_route('catalogs.reorder')), { ids: ids })
            .then(function(res) { inno.msg(res.message); })
            .catch(function(err) {
              if (err.response && err.response.data && err.response.data.message) {
                inno.msg(err.response.data.message);
              }
            });
        }
      });
    }
    initCatalogSortable();
    </script>
@endpush
