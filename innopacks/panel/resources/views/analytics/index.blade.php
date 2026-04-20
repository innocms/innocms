@extends('panel::layouts.app')
@section('body-class', 'page-analytics')

@section('title', __('panel::menu.analytics'))

@push('header')
<script src="{{ asset('vendor/chart/chart.min.js') }}"></script>
@endpush

@section('content')

{{-- Date Range Filter --}}
<div class="card mb-3">
  <div class="card-body py-2">
    <form action="{{ panel_route('analytics.index') }}" method="GET" class="d-flex align-items-center gap-2 flex-wrap">
      <span class="text-secondary"><i class="bi bi-calendar3"></i></span>
      @php
        $dateOptions = [
          'all'          => __('panel::analytics.filter_all'),
          'today'        => __('panel::analytics.filter_today'),
          'yesterday'    => __('panel::analytics.filter_yesterday'),
          'this_week'    => __('panel::analytics.filter_this_week'),
          'this_month'   => __('panel::analytics.filter_this_month'),
          'last_7_days'  => __('panel::analytics.filter_last_7_days'),
          'last_30_days' => __('panel::analytics.filter_last_30_days'),
          'custom'       => __('panel::analytics.filter_custom'),
        ];
        $currentFilter = $dateFilter ?? 'last_30_days';
      @endphp
      @foreach($dateOptions as $value => $label)
        <button type="submit" name="date_filter" value="{{ $value }}"
          class="btn btn-sm {{ $currentFilter === $value ? 'btn-primary' : 'btn-outline-secondary' }}">
          {{ $label }}
        </button>
      @endforeach
      @if($currentFilter === 'custom')
        <input type="date" name="start_date" class="form-control form-control-sm" style="width:140px" value="{{ $startDate }}">
        <span class="text-muted">-</span>
        <input type="date" name="end_date" class="form-control form-control-sm" style="width:140px" value="{{ $endDate }}">
        <button type="submit" class="btn btn-sm btn-primary">{{ __('panel::common.filter') }}</button>
      @endif
    </form>
  </div>
</div>

{{-- KPI Cards --}}
<div class="row g-3 mb-3">
  <div class="col-6 col-md-3">
    <div class="card">
      <div class="card-body py-3 px-4">
        <div class="text-muted small mb-1">{{ __('panel::analytics.total_visits') }}</div>
        <div class="fs-4 fw-bold">{{ $statistics['total_visits'] ?? 0 }}</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card">
      <div class="card-body py-3 px-4">
        <div class="text-muted small mb-1">{{ __('panel::analytics.unique_visitors') }}</div>
        <div class="fs-4 fw-bold">{{ $statistics['unique_visitors'] ?? 0 }}</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card">
      <div class="card-body py-3 px-4">
        <div class="text-muted small mb-1">{{ __('panel::analytics.page_views') }}</div>
        <div class="fs-4 fw-bold">{{ $statistics['page_views'] ?? 0 }}</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card">
      <div class="card-body py-3 px-4">
        <div class="text-muted small mb-1">{{ __('panel::analytics.unique_sessions') }}</div>
        <div class="fs-4 fw-bold">{{ $statistics['unique_sessions'] ?? 0 }}</div>
      </div>
    </div>
  </div>
</div>

@php
  $hasDailyData = collect($dailyStats)->pluck('page_views')->some(fn($v) => $v > 0);
  $hasDeviceData = collect($deviceData)->pluck('page_views')->some(fn($v) => $v > 0);
@endphp

{{-- Charts Row --}}
<div class="row g-3">
  <div class="col-12 col-md-8">
    <div class="card" style="min-height:400px">
      <div class="card-header">
        <h6 class="mb-0 fw-semibold">{{ __('panel::analytics.daily_trends') }}</h6>
      </div>
      <div class="card-body">
        @if($hasDailyData)
          <canvas id="chart-daily" height="320"></canvas>
        @else
          <div class="d-flex flex-column align-items-center justify-content-center py-5 text-muted">
            <i class="bi bi-bar-chart-line fs-1 mb-2"></i>
            <span>{{ __('panel::common.no_data') }}</span>
          </div>
        @endif
      </div>
    </div>
  </div>
  <div class="col-12 col-md-4">
    <div class="card" style="min-height:400px">
      <div class="card-header">
        <h6 class="mb-0 fw-semibold">{{ __('panel::analytics.by_device') }}</h6>
      </div>
      <div class="card-body">
        @if($hasDeviceData)
          <canvas id="chart-device" height="200"></canvas>
          <table class="table table-sm mt-3 mb-0">
            @foreach($deviceData as $device)
              @if($device['page_views'] > 0)
              <tr>
                <td>{{ $device['device_type'] }}</td>
                <td class="text-end">{{ $device['page_views'] }}</td>
              </tr>
              @endif
            @endforeach
          </table>
        @else
          <div class="d-flex flex-column align-items-center justify-content-center py-5 text-muted">
            <i class="bi bi-pie-chart fs-1 mb-2"></i>
            <span>{{ __('panel::common.no_data') }}</span>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

@if(count($countryData) > 0)
<div class="row g-3 mt-0">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h6 class="mb-0 fw-semibold">{{ __('panel::analytics.by_country') }}</h6>
      </div>
      <div class="card-body">
        <table class="table table-sm">
          <thead>
            <tr>
              <th>{{ __('panel::analytics.country') }}</th>
              <th class="text-end">{{ __('panel::analytics.visits') }}</th>
              <th class="text-end">{{ __('panel::analytics.unique_visitors') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach($countryData as $item)
              <tr>
                <td>{{ $item['country_name'] ?: $item['country_code'] }}</td>
                <td class="text-end">{{ $item['visits'] }}</td>
                <td class="text-end">{{ $item['unique_visitors'] }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endif

@endsection

@push('footer')
<script>
  // Daily trends chart
  const dailyCtx = document.getElementById('chart-daily').getContext('2d');
  const dailyLabels = @json(collect($dailyStats)->pluck('date')->map(fn($d) => substr($d, 5)));
  const pvData = @json(collect($dailyStats)->pluck('page_views'));
  const uvData = @json(collect($dailyStats)->pluck('unique_visitors'));
  const hasDailyData = pvData.some(v => v > 0);

  if (hasDailyData) {
    new Chart(dailyCtx, {
      type: 'line',
      data: {
        labels: dailyLabels,
        datasets: [
          {
            label: 'PV',
            data: pvData,
            borderColor: '#2563EB',
            backgroundColor: 'rgba(37,99,235,0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            pointRadius: 2,
            pointHoverRadius: 5,
          },
          {
            label: 'UV',
            data: uvData,
            borderColor: '#10B981',
            backgroundColor: 'rgba(16,185,129,0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            pointRadius: 2,
            pointHoverRadius: 5,
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: false,
        plugins: {
          legend: {
            position: 'top',
            align: 'end',
            labels: { boxWidth: 12, padding: 16, font: { size: 12 } }
          },
          tooltip: {
            backgroundColor: '#0F172A',
            padding: 10,
            cornerRadius: 8,
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: { borderDash: [4,4], color: '#E2E8F0' },
            ticks: { color: '#94A3B8', font: { size: 11 } },
            border: { display: false }
          },
          x: {
            grid: { display: false },
            ticks: { color: '#94A3B8', font: { size: 11 } },
            border: { display: false }
          }
        }
      }
    });
  }

  // Device distribution chart
  const deviceLabels = @json(collect($deviceData)->pluck('device_type'));
  const deviceValues = @json(collect($deviceData)->pluck('page_views'));
  const hasDeviceData = deviceValues.some(v => v > 0);

  if (hasDeviceData) {
    const deviceCtx = document.getElementById('chart-device').getContext('2d');
    new Chart(deviceCtx, {
      type: 'doughnut',
      data: {
        labels: deviceLabels,
        datasets: [{
          data: deviceValues,
          backgroundColor: ['#2563EB', '#10B981', '#F59E0B'],
          borderWidth: 0,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: false,
        plugins: {
          legend: {
            position: 'bottom',
            labels: { boxWidth: 12, padding: 12, font: { size: 12 } }
          }
        }
      }
    });
  }
</script>
@endpush
