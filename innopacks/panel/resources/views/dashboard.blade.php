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
        <h6 class="mb-0 fw-semibold">{{ __('panel::dashboard.article_trends') }}</h6>
      </div>
      <div class="card-body">
        <canvas id="chart-new-quantity" height="320"></canvas>
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
  const ctx1 = document.getElementById('chart-new-quantity').getContext('2d');
  const options = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: false,
      tooltip: {
        backgroundColor: '#0F172A',
        titleFont: { size: 13, weight: '500' },
        bodyFont: { size: 12 },
        padding: 12,
        cornerRadius: 8,
        displayColors: false,
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
        },
        border: { display: false }
      }
    },
  };

  const orderGradient = ctx1.createLinearGradient(0, 0, 0, 320);
  orderGradient.addColorStop(0, 'rgba(37,99,235,0.2)');
  orderGradient.addColorStop(1, 'rgba(37,99,235,0)');

  const chart1 = new Chart(ctx1, {
    type: 'line',
    data: {
      labels: @json($article['latest_week']['period']),
      datasets: [{
        label: '发布数量',
        data: @json($article['latest_week']['totals']),
        responsive: true,
        backgroundColor: orderGradient,
        borderColor: '#2563EB',
        borderWidth: 2.5,
        fill: true,
        pointBackgroundColor: '#fff',
        pointBorderColor: '#2563EB',
        pointBorderWidth: 2,
        pointRadius: 4,
        pointHoverRadius: 6,
        pointHoverBorderWidth: 3,
        tension: 0.4
      }]
    },
    options: options
  });
</script>
@endpush
