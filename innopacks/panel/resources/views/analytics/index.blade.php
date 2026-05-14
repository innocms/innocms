@extends('panel::layouts.app')
@section('body-class', 'page-analytics')

@section('title', __('panel/menu.analytics'))

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
          'all'          => __('panel/analytics.filter_all'),
          'today'        => __('panel/analytics.filter_today'),
          'yesterday'    => __('panel/analytics.filter_yesterday'),
          'this_week'    => __('panel/analytics.filter_this_week'),
          'this_month'   => __('panel/analytics.filter_this_month'),
          'last_7_days'  => __('panel/analytics.filter_last_7_days'),
          'last_30_days' => __('panel/analytics.filter_last_30_days'),
          'custom'       => __('panel/analytics.filter_custom'),
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
        <button type="submit" class="btn btn-sm btn-primary">{{ __('panel/common.filter') }}</button>
      @endif
    </form>
  </div>
</div>

{{-- KPI Cards --}}
<div class="row g-3 mb-3">
  <div class="col-6 col-md-3">
    <div class="card">
      <div class="card-body py-3 px-4">
        <div class="text-muted small mb-1">{{ __('panel/analytics.page_views') }}</div>
        <div class="fs-4 fw-bold">{{ $statistics['page_views'] ?? 0 }}</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card">
      <div class="card-body py-3 px-4">
        <div class="text-muted small mb-1">{{ __('panel/analytics.total_visits') }}</div>
        <div class="fs-4 fw-bold">{{ $statistics['total_visits'] ?? 0 }}</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card">
      <div class="card-body py-3 px-4">
        <div class="text-muted small mb-1">{{ __('panel/analytics.unique_visitors') }}</div>
        <div class="fs-4 fw-bold">{{ $statistics['unique_visitors'] ?? 0 }}</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card">
      <div class="card-body py-3 px-4">
        <div class="text-muted small mb-1">{{ __('panel/analytics.unique_sessions') }}</div>
        <div class="fs-4 fw-bold">{{ $statistics['unique_sessions'] ?? 0 }}</div>
      </div>
    </div>
  </div>
</div>

@php
  $hasDailyData = collect($dailyStats)->pluck('page_views')->some(fn($v) => $v > 0);
  $hasDeviceData = collect($deviceData)->pluck('page_views')->some(fn($v) => $v > 0);
  $hasBrowserData = collect($browserData)->pluck('visits')->some(fn($v) => $v > 0);
  $hasOSData = collect($osData)->pluck('visits')->some(fn($v) => $v > 0);
@endphp

{{-- Charts Row --}}
<div class="row g-3">
  <div class="col-12 col-md-8">
    <div class="card" style="min-height:400px">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-semibold">{{ __('panel/analytics.daily_trends') }}</h6>
        <div class="d-flex align-items-center gap-2">
          <div class="btn-group btn-group-sm" role="group">
            <button type="button" class="btn btn-outline-primary active" data-metric="pv" onclick="switchMetric('pv', this)">PV</button>
            <button type="button" class="btn btn-outline-primary" data-metric="uv" onclick="switchMetric('uv', this)">UV</button>
            <button type="button" class="btn btn-outline-primary" data-metric="ip" onclick="switchMetric('ip', this)">IP</button>
          </div>
          <button type="button" class="btn btn-sm btn-outline-secondary" onclick="reaggregate()" title="{{ __('panel/analytics.reaggregate') }}">
            <i class="bi bi-arrow-clockwise"></i>
          </button>
        </div>
      </div>
      <div class="card-body">
        @if($hasDailyData)
          <div style="position:relative;height:320px;">
            <canvas id="chart-daily"></canvas>
          </div>
        @else
          <div class="d-flex flex-column align-items-center justify-content-center py-5 text-muted">
            <i class="bi bi-bar-chart-line fs-1 mb-2"></i>
            <span>{{ __('panel/common.no_data') }}</span>
          </div>
        @endif
      </div>
    </div>
  </div>
  <div class="col-12 col-md-4">
    <div class="card" style="min-height:400px">
      <div class="card-header">
        <h6 class="mb-0 fw-semibold">{{ __('panel/analytics.by_device') }}</h6>
      </div>
      <div class="card-body">
        @if($hasDeviceData)
          <div style="position:relative;height:200px;">
            <canvas id="chart-device"></canvas>
          </div>
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
            <span>{{ __('panel/common.no_data') }}</span>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

{{-- Browser & OS Row --}}
@if($hasBrowserData || $hasOSData)
<div class="row g-3 mt-0">
  @if($hasBrowserData)
  <div class="col-12 col-md-6">
    <div class="card">
      <div class="card-header">
        <h6 class="mb-0 fw-semibold">{{ __('panel/analytics.by_browser') }}</h6>
      </div>
      <div class="card-body">
        <div style="position:relative;height:240px;">
          <canvas id="chart-browser"></canvas>
        </div>
      </div>
    </div>
  </div>
  @endif
  @if($hasOSData)
  <div class="col-12 {{ $hasBrowserData ? 'col-md-6' : 'col-md-12' }}">
    <div class="card">
      <div class="card-header">
        <h6 class="mb-0 fw-semibold">{{ __('panel/analytics.by_os') }}</h6>
      </div>
      <div class="card-body">
        <div style="position:relative;height:240px;">
          <canvas id="chart-os"></canvas>
        </div>
      </div>
    </div>
  </div>
  @endif
</div>
@endif

@if(count($countryData) > 0)
<div class="row g-3 mt-0">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h6 class="mb-0 fw-semibold">{{ __('panel/analytics.by_country') }}</h6>
      </div>
      <div class="card-body">
        <table class="table table-sm">
          <thead>
            <tr>
              <th>{{ __('panel/analytics.country') }}</th>
              <th class="text-end">{{ __('panel/analytics.visits') }}</th>
              <th class="text-end">{{ __('panel/analytics.unique_visitors') }}</th>
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
function reaggregate() {
  axios.post('{{ panel_route('analytics.reaggregate') }}').then(function(data) {
    inno.msg(data.message);
    setTimeout(function() { location.reload(); }, 1000);
  });
}

  // Daily trends chart - tab switching
  const dailyLabels = @json(collect($dailyStats)->pluck('date')->map(fn($d) => substr($d, 5)));
  const metricData = {
    pv: @json(collect($dailyStats)->pluck('page_views')),
    uv: @json(collect($dailyStats)->pluck('visits')),
    ip: @json(collect($dailyStats)->pluck('unique_visitors')),
  };
  const metricMeta = {
    pv: { label: 'PV', borderColor: '#2563EB', bgColor: 'rgba(37,99,235,0.1)' },
    uv: { label: 'UV', borderColor: '#10B981', bgColor: 'rgba(16,185,129,0.1)' },
    ip: { label: 'IP', borderColor: '#F59E0B', bgColor: 'rgba(245,158,11,0.1)' },
  };

  const hasDailyData = metricData.pv.some(v => v > 0);
  let dailyChart = null;

  function renderDailyChart(metric) {
    const canvas = document.getElementById('chart-daily');
    if (!canvas) return;
    const meta = metricMeta[metric];
    if (dailyChart) { dailyChart.destroy(); }
    dailyChart = new Chart(canvas.getContext('2d'), {
      type: 'line',
      data: {
        labels: dailyLabels,
        datasets: [{
          label: meta.label,
          data: metricData[metric],
          borderColor: meta.borderColor,
          backgroundColor: meta.bgColor,
          borderWidth: 2,
          fill: true,
          tension: 0.4,
          pointRadius: 2,
          pointHoverRadius: 5,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: false,
        plugins: {
          legend: { display: false },
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

  function switchMetric(metric, btn) {
    document.querySelectorAll('.btn-group .btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    renderDailyChart(metric);
  }

  if (hasDailyData) {
    renderDailyChart('pv');
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

  // Browser distribution chart
  const hasBrowserData = {{ $hasBrowserData ? 'true' : 'false' }};
  if (hasBrowserData) {
    const browserLabels = @json(collect($browserData)->pluck('browser'));
    const browserValues = @json(collect($browserData)->pluck('visits'));
    const browserCtx = document.getElementById('chart-browser').getContext('2d');
    new Chart(browserCtx, {
      type: 'doughnut',
      data: {
        labels: browserLabels,
        datasets: [{
          data: browserValues,
          backgroundColor: ['#2563EB', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#06B6D4', '#84CC16', '#F97316', '#6366F1'],
          borderWidth: 0,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: false,
        plugins: {
          legend: {
            position: 'right',
            labels: { boxWidth: 12, padding: 10, font: { size: 12 } }
          }
        }
      }
    });
  }

  // OS distribution chart
  const hasOSData = {{ $hasOSData ? 'true' : 'false' }};
  if (hasOSData) {
    const osLabels = @json(collect($osData)->pluck('os'));
    const osValues = @json(collect($osData)->pluck('visits'));
    const osCtx = document.getElementById('chart-os').getContext('2d');
    new Chart(osCtx, {
      type: 'doughnut',
      data: {
        labels: osLabels,
        datasets: [{
          data: osValues,
          backgroundColor: ['#10B981', '#2563EB', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#06B6D4', '#84CC16'],
          borderWidth: 0,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: false,
        plugins: {
          legend: {
            position: 'right',
            labels: { boxWidth: 12, padding: 10, font: { size: 12 } }
          }
        }
      }
    });
  }
</script>
@endpush
