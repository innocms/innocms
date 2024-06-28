@extends('panel::layouts.app')

@section('title', __('panel::menu.admins'))

<x-panel::form.right-btns/>

@section('content')
  <div class="card h-min-600">
    <div class="card-header">
      <h5 class="card-title mb-0">{{ __('panel::menu.admins') }}</h5>
    </div>
    <div class="card-body">
      <form class="needs-validation" novalidate id="app-form"
            action="{{ $admin->id ? panel_route('admins.update', [$admin->id]) : panel_route('admins.store') }}"
            method="POST">
        @csrf
        @method($admin->id ? 'PUT' : 'POST')

        <x-common-form-input title="姓名" name="name" value="{{ old('name', $admin->name) }}" required
                             placeholder="姓名"/>

        <x-common-form-input title="密码" name="password" value="{{ old('email') }}"/>

        <x-common-form-input title="Email" name="email" value="{{ old('email', $admin->email) }}" required/>

        <x-common-form-input title="系统语言" name="locale" value="{{ old('locale', $admin->locale) }}" required
                             placeholder="系统语言"/>

        <x-common-form-switch-radio title="是否启用" name="active" :value="old('active', $page->active ?? true)"
                                    placeholder="是否启用"/>

        <span>角色:</span>
        <div class="pt-2 w-max-600">
          @foreach ($roles as $item)
            <div class="form-check me-4 d-inline-block mb-2">
              <label class="form-check-label">
                <input class="form-check-input" type="checkbox" value="{{ $item->id }}" name="roles[]"
                    {{ in_array($item->id, $admin->roles->pluck('id')->toArray()) ? 'checked' : '' }}>
                {{ $item->name }}
              </label>
            </div>
          @endforeach
        </div>

        <button type="submit" class="d-none"></button>
      </form>
    </div>
  </div>
@endsection

@push('footer')
  <script></script>
@endpush