@extends('panel::layouts.app')
@section('body-class', 'page-consultations')

@section('title', __('panel/consultations.detail'))

@section('page-title-right')
  <a href="{{ panel_route('consultations.index') }}" class="btn btn-outline-secondary btn-sm">
    <i class="bi bi-arrow-left"></i> {{ __('panel/common.list') }}
  </a>
@endsection

@section('content')
<div class="card">
  <div class="card-body">
    <div class="row">
      <div class="col-md-6">
        <table class="table table-borderless">
          <tr>
            <td class="fw-semibold text-nowrap" style="width: 120px;">{{ __('panel/consultations.contact_name') }}</td>
            <td>{{ $consultation->name ?: '-' }}</td>
          </tr>
          <tr>
            <td class="fw-semibold">{{ __('panel/common.email') }}</td>
            <td>{{ $consultation->email }}</td>
          </tr>
          <tr>
            <td class="fw-semibold">{{ __('panel/consultations.phone') }}</td>
            <td>{{ $consultation->phone ?: '-' }}</td>
          </tr>
          <tr>
            <td class="fw-semibold">{{ __('panel/consultations.company') }}</td>
            <td>{{ $consultation->company ?: '-' }}</td>
          </tr>
          <tr>
            <td class="fw-semibold">{{ __('panel/common.status') }}</td>
            <td>
              @if($consultation->status)
                <span class="badge bg-success">{{ __('panel/consultations.read') }}</span>
              @else
                <span class="badge bg-warning text-dark">{{ __('panel/consultations.unread') }}</span>
              @endif
            </td>
          </tr>
          <tr>
            <td class="fw-semibold">{{ __('panel/common.created_at') }}</td>
            <td>{{ $consultation->created_at->format('Y-m-d H:i:s') }}</td>
          </tr>
        </table>
      </div>
      <div class="col-md-6">
        <div class="mb-2 fw-semibold">{{ __('panel/consultations.content') }}</div>
        <div class="p-3 bg-light rounded" style="white-space: pre-wrap;">{{ $consultation->content }}</div>
      </div>
    </div>
  </div>
</div>
@endsection
