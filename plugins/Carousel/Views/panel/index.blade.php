@extends('panel::layouts.app')
@section('body-class', 'page-home')

@section('title', __('轮播图'))

@section('content')
<div class="card h-min-600">
  <div class="card-body">
    <div class="d-flex justify-content-between mb-4">
      <a href="{{ panel_route('carousels.create') }}" class="btn btn-primary"><i class="bi bi-plus-square"></i> 添加</a>
    </div>

    <table class="table">
      <thead>
        <tr>
          <td>ID</td>
          <td>轮播图名称</td>
          <td>轮播图页面</td>
          <td>轮播图位置</td>
          <td>轮播图数量</td>
          <td>轮播图尺寸</td>
          <td>是否启用</td>
          <td>操作</td>
        </tr>
      </thead>
      @if ($items->count())
      <tbody>

        @foreach($items as $item)
          <tr>
            <td>{{ $item->id }}</td>
            <td>{{ $item->name }}</td>
            <td>{{ $item->page->slug ?? 'home' }}</td>
            <td>{{ $item->position }}</td>
            <td>{{ $item->images()->count() }}</td>
            <td>{{ $item->style }}</td>
            <td>{{ $item->active ? '是' : '否' }}</td>
            <td>
              <a href="{{ panel_route('carousels.edit', [$item->id]) }}" class="btn btn-sm btn-outline-primary">编辑</a>
              <form action="{{ panel_route('carousels.destroy', [$item->id]) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger">删除</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
      @else
      <tbody><tr><td colspan="5"><x-panel-no-data /></td></tr></tbody>
      @endif
    </table>
    {{ $items->withQueryString()->links('panel::vendor/pagination/bootstrap-4') }}
  </div>
</div>
@endsection

@push('footer')
  <script>
  </script>
@endpush
