@extends('panel::layouts.app')
@section('body-class', 'page-product-categories')

@section('title', __('panel/menu.categories'))

@section('page-title-right')
  <a href="{{ panel_route('categories.create') }}" class="btn btn-primary btn-sm">
    <i class="bi bi-plus-square"></i> {{ __('panel/common.create') }}
  </a>
@endsection

@section('content')
<div class="card h-min-600">
  <div class="card-body">

    <x-panel-data-search
      :action="panel_route('categories.index')"
      :searchFields="$searchFields ?? []"
      :filters="$filterButtons ?? []"
    />

    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
        <tr>
          <td>{{ __('panel/common.id') }}</td>
          <td>{{ __('panel/common.name') }}</td>
          <td class="d-none d-md-table-cell">{{ __('panel/category.parent') }}</td>
          <td>{{ __('panel/common.slug') }}</td>
          <td>{{ __('panel/common.position') }}</td>
          <td>{{ __('panel/common.active') }}</td>
          <td>{{ __('panel/common.actions') }}</td>
        </tr>
        </thead>
        @if ($categories->count())
          <tbody>
          @foreach($categories as $item)
            <tr>
              <td>{{ $item->id }}</td>
              <td>{{ $item->fallbackName() }}</td>
              <td class="d-none d-md-table-cell">{{ $item->parent?->fallbackName() ?: '-' }}</td>
              <td>{{ $item->slug }}</td>
              <td>{{ $item->position }}</td>
              <td>@include('panel::shared.list_switch', ['value' => $item->active, 'url' => panel_route('categories.active', $item->id)])</td>
              <td>
                <div class="d-flex gap-1">
                  <a href="{{ panel_route('categories.edit', [$item->id]) }}" class="btn btn-sm btn-outline-primary">{{ __('panel/common.edit') }}</a>
                  <form action="{{ panel_route('categories.destroy', [$item->id]) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-sm btn-outline-danger btn-delete">{{ __('panel/common.delete') }}</button>
                  </form>
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
    {{ $categories->withQueryString()->links('panel::vendor/pagination/bootstrap-4') }}
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
