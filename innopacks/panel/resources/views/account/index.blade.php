@extends('panel::layouts.app')
@section('body-class', 'account')

@section('title', __('panel::menu.account'))

<x-panel::form.right-btns />

@section('content')
{{-- {{ $admin->name }}, 你好。 修改个人信息页面, 待完善。 --}}
<div class="card h-min-600">
  <div class="card-body">
    <form class="needs-validation" id="app-form" novalidate action="{{ panel_route('account.update') }}" method="POST">
      @csrf
      @method('put')

      <x-panel-form-input title="名称" name="name" value="{{ old('name', $admin->name) }}" required />
      <x-panel-form-input title="邮箱" name="email" value="{{ old('email', $admin->email) }}" required />
      <x-panel-form-input title="密码" name="password" value="" type="password" description="密码留空则不修改" />

      <button type="submit" class="d-none"></button>
    </form>
  </div>
</div>
@endsection

@push('footer')
<script>
</script>
@endpush