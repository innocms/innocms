@extends('panel::layouts.app')

@section('title', '友情链接')

@section('content')
  <div class="card h-min-600">
    <div class="card-body">
      <form class="needs-validation" novalidate
            action="{{ $item->id ? panel_route('partner_links.update', [$item->id]) : panel_route('partner_links.store') }}"
            method="POST">
        @csrf
        @method($item->id ? 'PUT' : 'POST')

        <x-panel-form-input title="网站名称" name="name" :value="old('name', $item->name ?? '')"
                            placeholder="网站名称"/>
        <x-panel-form-input title="URL地址" name="url" :value="old('url', $item->url ?? '')"
                            placeholder="URL地址"/>
        <x-panel-form-image title="网站图标" name="logo" :value="old('logo', $item->logo ?? '')"
                            placeholder="网站图标"/>
        <x-panel-form-input title="排序" name="position" :value="old('position', $item->position ?? 0)"
                            placeholder="排序"/>
        <x-panel-form-switch-radio title="是否启用" name="active" :value="old('active', $item->active ?? true)"
                                   placeholder="是否启用"/>
        <x-panel::form.bottom-btns/>
      </form>
    </div>
  </div>
  </div>
@endsection

@push('footer')
  <script>
  </script>
@endpush