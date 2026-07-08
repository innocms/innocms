@extends('panel::layouts.app')
@section('body-class', 'page-products')

@section('title', __('panel/menu.products'))

@section('page-title-right')
  <a href="{{ panel_route('products.create') }}" class="btn btn-primary btn-sm">
    <i class="bi bi-plus-square"></i> {{ __('panel/common.create') }}
  </a>
@endsection

@section('content')
<div class="card h-min-600">
  <div class="card-body">

    <x-panel-data-search
      :action="panel_route('products.index')"
      :searchFields="$searchFields ?? []"
      :filters="$filterButtons ?? []"
    />

    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
        <tr>
          <td>{{ __('panel/common.id') }}</td>
          <td>{{ __('panel/product.image') }}</td>
          <td>{{ __('panel/product.name') }}</td>
          <td class="d-none d-md-table-cell">{{ __('panel/product.category') }}</td>
          <td>{{ __('panel/product.price') }}</td>
          <td>{{ __('panel/common.position') }}</td>
          <td>{{ __('panel/common.active') }}</td>
          <td>{{ __('panel/common.actions') }}</td>
        </tr>
        </thead>
        @if ($products->count())
          <tbody>
          @foreach($products as $item)
            @php($categoryNames = $item->categories->map(fn($c) => $c->fallbackName())->filter()->take(3))
            <tr>
              <td>{{ $item->id }}</td>
              <td><img src="{{ image_resize($item->image, 30, 30) }}" class="wh-30"></td>
              <td>
                <a href="{{ panel_route('products.edit', [$item->id]) }}" class="text-decoration-none"
                   data-bs-toggle="tooltip" title="{{ $item->fallbackName() }}">
                  {{ sub_string($item->fallbackName(), 32) }}
                </a>
                @if ($item->spu_code)
                  <div class="small text-secondary">{{ $item->spu_code }}</div>
                @endif
              </td>
              <td class="d-none d-md-table-cell">
                {{ $categoryNames->implode(', ') }}
                @if ($item->categories->count() > 3)
                  <span class="badge bg-light text-dark">+{{ $item->categories->count() - 3 }}</span>
                @endif
              </td>
              <td>{{ $item->price }}</td>
              <td>{{ $item->position }}</td>
              <td>@include('panel::shared.list_switch', ['value' => $item->active, 'url' => panel_route('products.active', $item->id)])</td>
              <td>
                <div class="d-flex gap-1">
                  <form action="{{ panel_route('products.copy', [$item->id]) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-secondary">{{ __('panel/product.copy') }}</button>
                  </form>
                  <a href="{{ panel_route('products.edit', [$item->id]) }}" class="btn btn-sm btn-outline-primary">{{ __('panel/common.edit') }}</a>
                  <form action="{{ panel_route('products.destroy', [$item->id]) }}" method="POST" class="d-inline">
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
            <td colspan="8">
              <x-common-no-data :width="200" />
            </td>
          </tr>
          </tbody>
        @endif
      </table>
    </div>
    {{ $products->withQueryString()->links('panel::vendor/pagination/bootstrap-4') }}
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
