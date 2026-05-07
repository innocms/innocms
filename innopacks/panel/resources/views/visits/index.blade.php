@extends('panel::layouts.app')
@section('body-class', 'page-visits')

@section('title', __('panel::menu.visits'))

@section('content')
<div class="card h-min-600">
  <div class="card-body">
    <x-panel-data-search
      :action="panel_route('visits.index')"
      :searchFields="$searchFields ?? []"
      :filters="$filterButtons ?? []"
    />

    @if ($visits->count())
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <td>{{ __('panel::common.id') }}</td>
            <td>{{ __('panel::visit.ip_address') }}</td>
            <td>{{ __('panel::visit.country_name') }}</td>
            <td>{{ __('panel::visit.city') }}</td>
            <td>{{ __('panel::visit.device_type') }}</td>
            <td>{{ __('panel::visit.browser') }}</td>
            <td>{{ __('panel::visit.os') }}</td>
            <td>{{ __('panel::visit.referrer') }}</td>
            <td>{{ __('panel::visit.first_visited_at') }}</td>
            <td>{{ __('panel::visit.last_visited_at') }}</td>
          </tr>
        </thead>
        <tbody>
        @foreach($visits as $item)
          <tr>
            <td>{{ $item->id }}</td>
            <td>{{ $item->ip_address }}</td>
            <td>{{ $item->country_name ?: '-' }}</td>
            <td>{{ $item->city ?: '-' }}</td>
            <td>
              @if($item->device_type === 'desktop')
                <span class="badge bg-primary">{{ __('panel::visit.device_desktop') }}</span>
              @elseif($item->device_type === 'mobile')
                <span class="badge bg-success">{{ __('panel::visit.device_mobile') }}</span>
              @elseif($item->device_type === 'tablet')
                <span class="badge bg-info">{{ __('panel::visit.device_tablet') }}</span>
              @else
                <span class="badge bg-secondary">{{ $item->device_type ?: '-' }}</span>
              @endif
            </td>
            <td>{{ $item->browser ?: '-' }}</td>
            <td>{{ $item->os ?: '-' }}</td>
            <td>{{ $item->referrer ? Str::limit($item->referrer, 30) : '-' }}</td>
            <td>{{ $item->first_visited_at?->format('Y-m-d H:i') }}</td>
            <td>{{ $item->last_visited_at?->format('Y-m-d H:i') }}</td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    {{ $visits->withQueryString()->links('panel::vendor.pagination.bootstrap-4') }}
    @else
      <x-common-no-data :width="200" />
    @endif
  </div>
</div>
@endsection
