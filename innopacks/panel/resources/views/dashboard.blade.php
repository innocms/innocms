@extends('panel::layouts.app')
@section('body-class', 'page-home')

@push('header')
<script src="{{ asset('vendor/chart/chart.min.js') }}"></script>
@endpush

@section('content')

<div class="mb-4 mt-n2">
  <div class="dashboard-top-card">
    <div class="row g-3">
      @foreach ($cards as $card)
      <div class="col-6 col-md-3">
        <div class="dashboard-item">
          <div class="d-flex justify-content-between align-items-center">
            <div class="left">
              <div class="quantity">{{ $card['quantity'] }}</div>
              <span class="title">{{ $card['title'] }}</span>
            </div>
            <div class="right">
              <div class="icon-wrap"><i class="{{ $card['icon'] }} icon"></i></div>
            </div>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-12 col-md-7">
    <div class="card dashboard-chart-card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold">流量趋势</h6>
      </div>
      <div class="card-body">
        <canvas id="chart-visit-trend" height="320"></canvas>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-5">
    <div class="card dashboard-top-card-list">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold">{{ __('panel::dashboard.top_articles') }}</h6>
      </div>
      <div class="card-body">
        @if ($top_viewed_articles)
          <div class="top-articles-list">
          @foreach($top_viewed_articles as $item)
            <a class="article-row d-flex align-items-center text-decoration-none" href="{{ panel_route('articles.edit', $item['id']) }}">
              <div class="rank">
                @if ($loop->iteration <= 3)
                  <span class="rank-badge rank-{{ $loop->iteration }}">{{ $loop->iteration }}</span>
                @else
                  <span class="rank-badge rank-default">{{ $loop->iteration }}</span>
                @endif
              </div>
              <div class="article-thumb rounded overflow-hidden flex-shrink-0">
                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}">
              </div>
              <div class="article-info flex-grow-1 mx-3">
                <div class="article-title">{{ $item['summary'] }}</div>
              </div>
              <div class="article-views text-end">
                <i class="bi bi-eye me-1"></i>{{ $item['viewed'] }}
              </div>
            </a>
          @endforeach
          </div>
        @else
          <x-common-no-data :width="200" />
        @endif
      </div>
    </div>
  </div>
</div>
<img src="https://www.innocms.com/install/dashboard.jpg?version={{ config('innocms.version') }}&build_date={{ config('innocms.build') }}" class="d-none" alt=""/>
@endsection

@push('footer')
<script>
  const ctx1 = document.getElementById('chart-visit-trend').getContext('2d');
  const options = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        display: true,
        position: 'top',
        align: 'end',
        labels: {
          boxWidth: 12,
          boxHeight: 12,
          borderRadius: 3,
          useBorderRadius: true,
          padding: 16,
          font: { size: 12, weight: '500' },
          color: '#64748B',
        }
      },
      tooltip: {
        backgroundColor: '#0F172A',
        titleFont: { size: 13, weight: '500' },
        bodyFont: { size: 12 },
        padding: 12,
        cornerRadius: 8,
        displayColors: true,
      }
    },
    interaction: {
      mode: 'index',
      intersect: false,
    },
    scales: {
      y: {
        beginAtZero: true,
        grid: {
          drawBorder: false,
          borderDash: [4, 4],
          color: '#E2E8F0',
        },
        ticks: {
          color: '#94A3B8',
          font: { size: 12 },
          padding: 8,
        },
        border: { display: false }
      },
      x: {
        grid: {
          drawBorder: false,
          display: false,
        },
        ticks: {
          color: '#94A3B8',
          font: { size: 12 },
          padding: 8,
          callback: function(value, index) {
            const label = this.getLabelForValue(value);
            return label ? label.substring(5) : label;
          }
        },
        border: { display: false }
      }
    },
  };

  const pvGradient = ctx1.createLinearGradient(0, 0, 0, 320);
  pvGradient.addColorStop(0, 'rgba(37,99,235,0.15)');
  pvGradient.addColorStop(1, 'rgba(37,99,235,0)');

  const uvGradient = ctx1.createLinearGradient(0, 0, 0, 320);
  uvGradient.addColorStop(0, 'rgba(16,185,129,0.15)');
  uvGradient.addColorStop(1, 'rgba(16,185,129,0)');

  const chart1 = new Chart(ctx1, {
    type: 'line',
    data: {
      labels: @json($visit_trend['period']),
      datasets: [
        {
          label: 'PV',
          data: @json($visit_trend['pv']),
          backgroundColor: pvGradient,
          borderColor: '#2563EB',
          borderWidth: 2.5,
          fill: true,
          pointBackgroundColor: '#fff',
          pointBorderColor: '#2563EB',
          pointBorderWidth: 2,
          pointRadius: 3,
          pointHoverRadius: 5,
          tension: 0.4
        },
        {
          label: 'UV',
          data: @json($visit_trend['uv']),
          backgroundColor: uvGradient,
          borderColor: '#10B981',
          borderWidth: 2.5,
          fill: true,
          pointBackgroundColor: '#fff',
          pointBorderColor: '#10B981',
          pointBorderWidth: 2,
          pointRadius: 3,
          pointHoverRadius: 5,
          tension: 0.4
        }
      ]
    },
    options: options
  });
</script>
@endpush
