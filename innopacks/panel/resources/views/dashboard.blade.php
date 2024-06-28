@extends('panel::layouts.app')
@section('body-class', 'page-home')

@push('header')
<script src="{{ asset('vendor/chart/chart.min.js') }}"></script>
@endpush

@section('content')

<div class="mb-4 mt-n3">
  <div class="card dashboard-top-card">
    <div class="card-body">
      <div class="row">
        @foreach ($cards as $card)
        <div class="col-6 col-md-3">
          <div class="card dashboard-item">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div class="left">
                  <div class="quantity">{{ $card['quantity'] }}</div>
                  <span class="title">{{ $card['title'] }}</span>
                </div>
                <div class="right"><i class="{{ $card['icon'] }} icon"></i></div>
              </div>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12 col-md-6">
    <div class="card">
      <div class="card-header">{{ __('panel::dashboard.article_trends') }}</div>
      <div class="card-body">
        <canvas id="chart-new-quantity" height="380"></canvas>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-6 mb-3">
    <div class="card top-sale-products">
      <div class="card-header">{{ __('panel::dashboard.top_articles') }}</div>
      <div class="card-body pb-0">
        @if ($top_viewed_articles)
          <table class="table table-last-no-border align-middle mt-n3 mb-0">
            <tbody>
            @foreach($top_viewed_articles as $item)
              <tr>
                <td class="text-center">
                  @if ($loop->iteration <= 3)
                    <img src="{{ asset('icon/grade-'. $loop->iteration .'.svg') }}" alt="{{ $item['name'] }}" class="img-fluid wh-30">
                  @else
                    <span class="badge bg-secondary">{{ $loop->iteration }}</span>
                  @endif
                </td>
                <td>
                  <a class="d-flex align-items-center text-dark text-decoration-none" href="{{ panel_route('articles.edit', $item['id']) }}">
                    <div class="wh-30 rounded-circle overflow-hidden border border-1 me-2"><img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="img-fluid"></div>
                    {{ $item['summary'] }}
                  </a>
                </td>
                <td class="text-center">{{ $item['viewed'] }}</td>
              </tr>
            @endforeach
            </tbody>
          </table>
        @else
          <x-common-no-data :width="240" />
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
        legend: false // Hide legend
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
          borderDash: [3],
        },
      },
      x: {
        beginAtZero: true,
        grid: {
          drawBorder: false,
          display: false
        },
      }
    },
  };

  const orderGradient = ctx1.createLinearGradient(0, 0, 0, 380);
  orderGradient.addColorStop(0, 'rgba(76,122,247,0.5)');
  orderGradient.addColorStop(1, 'rgba(76,122,247,0)');

  const chart1 = new Chart(ctx1, {
    type: 'line',
    data: {
      labels: @json($article['latest_week']['period']),
      datasets: [{
        label: '发布数量',
        data: @json($article['latest_week']['totals']),
        responsive: true,
        backgroundColor : orderGradient,
        borderColor : "#3c7af7",
        fill: true,
        lineTension: 0.4,
        datasetStrokeWidth: 3,
        pointBackgroundColor: '#3c7af7',
        pointDotStrokeWidth: 4,
        pointHoverBorderWidth: 8,
        tension: 0.1
      }]
    },
    options: options
  });
</script>
@endpush
