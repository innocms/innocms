@extends('layouts.app')

@section('title', __('common/base.server_error'))

@section('body-class', 'page-error')

@section('content')
  <div class="container py-5">
    <div class="row align-items-center justify-content-center" style="min-height: calc(100vh - 400px);">
      <div class="col-md-6 text-center">
        <div class="mb-4">
          <div style="font-size:120px;line-height:1;color:var(--bs-danger,#dc3545);font-weight:300;text-shadow:4px 4px 10px rgba(220,53,69,.1);">500</div>
          <h2 class="h4 mb-3 mt-4">{{ __('common/base.server_error') }}</h2>
          <p class="text-secondary mb-4">{{ __('common/base.server_error_description') }}</p>
        </div>
        <div class="d-flex gap-3 justify-content-center">
          <a href="javascript:location.reload()" class="btn btn-outline-secondary"><i class="bi bi-arrow-clockwise me-1"></i>{{ __('common/base.refresh') }}</a>
          <a href="{{ front_route('home.index') }}" class="btn btn-primary"><i class="bi bi-house me-1"></i>{{ __('common/base.home') }}</a>
        </div>
      </div>
    </div>
  </div>
@endsection
