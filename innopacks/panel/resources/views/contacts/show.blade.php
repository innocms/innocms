@extends('panel::layouts.app')
@section('body-class', 'page-contacts')

@section('title', __('panel/contacts.detail'))

@section('page-title-right')
  <a href="{{ panel_route('contacts.index') }}" class="btn btn-outline-secondary btn-sm">
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
            <td class="fw-semibold text-nowrap" style="width: 120px;">{{ __('panel/contacts.contact_name') }}</td>
            <td>{{ $contact->name ?: '-' }}</td>
          </tr>
          <tr>
            <td class="fw-semibold">{{ __('panel/common.email') }}</td>
            <td>{{ $contact->email }}</td>
          </tr>
          <tr>
            <td class="fw-semibold">{{ __('panel/contacts.phone') }}</td>
            <td>{{ $contact->phone ?: '-' }}</td>
          </tr>
          <tr>
            <td class="fw-semibold">{{ __('panel/contacts.company') }}</td>
            <td>{{ $contact->company ?: '-' }}</td>
          </tr>
          <tr>
            <td class="fw-semibold">{{ __('panel/common.status') }}</td>
            <td>
              @if($contact->status)
                <span class="badge bg-success">{{ __('panel/contacts.read') }}</span>
              @else
                <span class="badge bg-warning text-dark">{{ __('panel/contacts.unread') }}</span>
              @endif
            </td>
          </tr>
          <tr>
            <td class="fw-semibold">{{ __('panel/common.created_at') }}</td>
            <td>{{ $contact->created_at->format('Y-m-d H:i:s') }}</td>
          </tr>
        </table>
      </div>
      <div class="col-md-6">
        <div class="mb-2 fw-semibold">{{ __('panel/contacts.content') }}</div>
        <div class="p-3 bg-light rounded" style="white-space: pre-wrap;">{{ $contact->content }}</div>
      </div>
    </div>
  </div>
</div>
@endsection
