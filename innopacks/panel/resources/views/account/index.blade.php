@extends('panel::layouts.app')
@section('body-class', 'account')

@section('title', __('panel/menu.account'))

<x-panel::form.right-btns />

@section('content')
{{-- {{ $admin->name }}, 你好。 修改个人信息页面, 待完善。 --}}
<div class="card h-min-600">
  <div class="card-body">
    <form class="needs-validation" id="app-form" novalidate action="{{ panel_route('account.update') }}" method="POST">
      @csrf
      @method('put')

      <x-panel-form-input title="{{ __('panel/common.name') }}" name="name" value="{{ old('name', $admin->name) }}" required />
      <x-panel-form-input title="{{ __('panel/common.email') }}" name="email" value="{{ old('email', $admin->email) }}" required />
      <x-panel-form-input title="{{ __('panel/common.password') }}" name="password" value="" type="password" description="{{ __('panel/dashboard.password_keep') }}" />

      <button type="submit" class="d-none"></button>
    </form>
  </div>
</div>
@endsection

@push('footer')
<script>
</script>
@endpush