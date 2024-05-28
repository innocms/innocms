@extends('panel::layouts.app')
@section('body-class', 'page-home')

@section('title', __('友情链接'))

@section('content')
<div class="card h-min-600">
  <div class="card-body">
    <div class="d-flex justify-content-between mb-4">
      <a href="{{ panel_route('partner_links.create') }}" class="btn btn-primary"><i class="bi bi-plus-square"></i> 添加</a>
    </div>

    <table class="table">
      <thead>
        <tr>
          <td>ID</td>
          <td>网站图标</td>
          <td>网站名称</td>
          <td>URL地址</td>
          <td>排序</td>
          <td>是否启用</td>
          <td>操作</td>
        </tr>
      </thead>
      @if ($items->count())
      <tbody>
        @foreach($items as $item)
          <tr>
            <td>{{ $item->id }}</td>
            <td><img src="{{ image_resize($item->logo, 30, 30) }}" style="width: 30px; height: 30px" alt=""></td>
            <td>{{ sub_string($item->name, 30) }}</td>
            <td>{{ $item->url }}</td>
            <td>{{ $item->position }}</td>
            <td>{{ $item->active }}</td>
            <td>
              <a href="{{ panel_route('partner_links.edit', [$item->id]) }}" class="btn btn-sm btn-outline-primary">编辑</a>
              <form action="{{ panel_route('partner_links.destroy', [$item->id]) }}" method="POST" class="d-inline">
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