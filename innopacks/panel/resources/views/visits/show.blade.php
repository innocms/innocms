@extends('panel::layouts.app')
@section('body-class', 'page-visits')

@section('title', __('panel/visit.visit_detail'))

@section('page-title-right')
  <a href="{{ panel_route('visits.index') }}" class="btn btn-sm btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> {{ __('panel/common.back') }}
  </a>
@endsection

@section('content')
{{-- Visit Info Card --}}
<div class="card mb-3">
  <div class="card-header">
    <h6 class="mb-0 fw-semibold">{{ __('panel/visit.session_info') }}</h6>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-3 mb-2">
        <div class="text-muted small">{{ __('panel/visit.ip_address') }}</div>
        <div>{{ $visit->ip_address }}</div>
      </div>
      <div class="col-md-3 mb-2">
        <div class="text-muted small">{{ __('panel/visit.country_name') }}</div>
        <div>{{ $visit->country_name ?: '-' }}</div>
      </div>
      <div class="col-md-3 mb-2">
        <div class="text-muted small">{{ __('panel/visit.city') }}</div>
        <div>{{ $visit->city ?: '-' }}</div>
      </div>
      <div class="col-md-3 mb-2">
        <div class="text-muted small">{{ __('panel/visit.device_type') }}</div>
        <div>
          @if($visit->device_type === 'desktop')
            <span class="badge bg-primary">{{ __('panel/visit.device_desktop') }}</span>
          @elseif($visit->device_type === 'mobile')
            <span class="badge bg-success">{{ __('panel/visit.device_mobile') }}</span>
          @elseif($visit->device_type === 'tablet')
            <span class="badge bg-info">{{ __('panel/visit.device_tablet') }}</span>
          @else
            {{ $visit->device_type ?: '-' }}
          @endif
        </div>
      </div>
      <div class="col-md-3 mb-2">
        <div class="text-muted small">{{ __('panel/visit.browser') }}</div>
        <div>{{ $visit->browser ?: '-' }}</div>
      </div>
      <div class="col-md-3 mb-2">
        <div class="text-muted small">{{ __('panel/visit.os') }}</div>
        <div>{{ $visit->os ?: '-' }}</div>
      </div>
      <div class="col-md-3 mb-2">
        <div class="text-muted small">{{ __('panel/visit.referrer') }}</div>
        <div>{{ $visit->referrer ?: '-' }}</div>
      </div>
      <div class="col-md-3 mb-2">
        <div class="text-muted small">{{ __('panel/visit.user_agent') }}</div>
        <div style="word-break:break-all;font-size:0.85em">{{ $visit->user_agent ?: '-' }}</div>
      </div>
      <div class="col-md-3 mb-2">
        <div class="text-muted small">{{ __('panel/visit.first_visited_at') }}</div>
        <div>{{ $visit->first_visited_at?->format('Y-m-d H:i:s') }}</div>
      </div>
      <div class="col-md-3 mb-2">
        <div class="text-muted small">{{ __('panel/visit.last_visited_at') }}</div>
        <div>{{ $visit->last_visited_at?->format('Y-m-d H:i:s') }}</div>
      </div>
    </div>
  </div>
</div>

{{-- Events Table --}}
<div class="card">
  <div class="card-header">
    <h6 class="mb-0 fw-semibold">
      {{ __('panel/visit.browsing_history') }}
      <span class="badge bg-secondary ms-2">{{ $visit->visitEvents->count() }}</span>
    </h6>
  </div>
  <div class="card-body">
    @if($visit->visitEvents->count())
      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <td>#</td>
              <td>{{ __('panel/visit.event_type') }}</td>
              <td>URL</td>
              <td>{{ __('panel/visit.referrer') }}</td>
              <td>{{ __('panel/visit.event_time') }}</td>
            </tr>
          </thead>
          <tbody>
            @foreach($visit->visitEvents as $i => $event)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                  @php
                    $typeColors = [
                      'page_view'      => 'bg-secondary',
                      'article_view'   => 'bg-primary',
                      'catalog_view'   => 'bg-info',
                      'home_view'      => 'bg-success',
                      'search'         => 'bg-warning text-dark',
                    ];
                    $color = $typeColors[$event->event_type] ?? 'bg-secondary';
                  @endphp
                  <span class="badge {{ $color }}">{{ $event->event_type }}</span>
                </td>
                <td>
                  @if($event->page_url)
                    <a href="{{ $event->page_url }}" target="_blank" class="text-decoration-none"
                       data-bs-toggle="tooltip" title="{{ $event->page_url }}">
                      {{ \Illuminate\Support\Str::limit($event->page_url, 50) }}
                    </a>
                  @else
                    -
                  @endif
                </td>
                <td>
                  @if($event->referrer)
                    <span data-bs-toggle="tooltip" title="{{ $event->referrer }}">
                      {{ \Illuminate\Support\Str::limit($event->referrer, 30) }}
                    </span>
                  @else
                    -
                  @endif
                </td>
                <td>{{ $event->created_at?->format('Y-m-d H:i:s') }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @else
      <x-common-no-data :width="200" />
    @endif
  </div>
</div>
@endsection
