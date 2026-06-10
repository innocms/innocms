@extends('panel::layouts.app')
@section('body-class', 'page-home')

@push('header')
<script src="{{ asset('vendor/echarts/echarts.min.js') }}"></script>
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

<div class="row g-3 mb-3">
  <div class="col-12 col-lg-8">
    <div class="card dashboard-chart-card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold">流量趋势</h6>
      </div>
      <div class="card-body">
        <div id="chart-visit-trend" style="height:320px;"></div>
      </div>
    </div>
  </div>
  <div class="col-12 col-lg-4">
    <div class="card dashboard-chart-card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold">设备分布</h6>
      </div>
      <div class="card-body d-flex align-items-center justify-content-center">
        <div id="chart-device" style="height:320px;width:100%;"></div>
      </div>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-12 col-md-7">
    <div class="card dashboard-chart-card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold">浏览器分布</h6>
      </div>
      <div class="card-body">
        <div id="chart-browser" style="height:320px;"></div>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-5">
    <div class="card dashboard-top-card-list">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold">{{ __('panel/dashboard.top_articles') }}</h6>
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
(function() {
  const colors = {
    primary: '#2563EB',
    primaryLight: '#3B82F6',
    success: '#10B981',
    successLight: '#34D399',
    warning: '#F59E0B',
    danger: '#EF4444',
    text: '#475569',
    textLight: '#94A3B8',
    border: '#E2E8F0',
  };

  // ===== 1. Visit Trend (Area Chart) =====
  const trendEl = document.getElementById('chart-visit-trend');
  if (trendEl) {
    const trendChart = echarts.init(trendEl);
    const periods = @json($visit_trend['period']);
    const labels = periods.map(p => p.substring(5));

    trendChart.setOption({
      tooltip: {
        trigger: 'axis',
        backgroundColor: '#0F172A',
        borderColor: 'transparent',
        textStyle: { color: '#fff', fontSize: 12 },
        axisPointer: { type: 'cross', crossStyle: { color: '#94A3B8' } },
      },
      legend: {
        top: 0,
        right: 0,
        itemWidth: 16,
        itemHeight: 3,
        itemGap: 16,
        textStyle: { color: colors.text, fontSize: 12 },
      },
      grid: { left: 8, right: 8, top: 36, bottom: 8, containLabel: true },
      xAxis: {
        type: 'category',
        data: labels,
        boundaryGap: false,
        axisLine: { show: false },
        axisTick: { show: false },
        axisLabel: { color: colors.textLight, fontSize: 11 },
      },
      yAxis: {
        type: 'value',
        splitLine: { lineStyle: { color: '#F1F5F9', type: 'dashed' } },
        axisLine: { show: false },
        axisTick: { show: false },
        axisLabel: { color: colors.textLight, fontSize: 11 },
      },
      series: [
        {
          name: 'PV',
          type: 'line',
          smooth: true,
          symbol: 'circle',
          symbolSize: 6,
          showSymbol: false,
          lineStyle: { width: 2.5, color: colors.primary },
          itemStyle: { color: colors.primary, borderWidth: 2, borderColor: '#fff' },
          areaStyle: {
            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
              { offset: 0, color: 'rgba(37,99,235,0.2)' },
              { offset: 1, color: 'rgba(37,99,235,0.01)' }
            ])
          },
          emphasis: { focus: 'series' },
          data: @json($visit_trend['pv']),
        },
        {
          name: 'UV',
          type: 'line',
          smooth: true,
          symbol: 'circle',
          symbolSize: 6,
          showSymbol: false,
          lineStyle: { width: 2.5, color: colors.success },
          itemStyle: { color: colors.success, borderWidth: 2, borderColor: '#fff' },
          areaStyle: {
            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
              { offset: 0, color: 'rgba(16,185,129,0.2)' },
              { offset: 1, color: 'rgba(16,185,129,0.01)' }
            ])
          },
          emphasis: { focus: 'series' },
          data: @json($visit_trend['uv']),
        },
      ],
    });
    window.addEventListener('resize', () => trendChart.resize());
  }

  // ===== 2. Device Distribution (Doughnut) =====
  const deviceEl = document.getElementById('chart-device');
  if (deviceEl) {
    const deviceChart = echarts.init(deviceEl);
    const rawDevices = @json($device_data);
    const deviceMap = { desktop: '桌面端', mobile: '移动端', tablet: '平板' };
    const deviceColors = [colors.primary, colors.success, colors.warning];
    const deviceData = rawDevices.map((d, i) => ({
      name: deviceMap[d.device_type] || d.device_type,
      value: d.page_views || d.visits || 0,
      itemStyle: { color: deviceColors[i % deviceColors.length] },
    }));
    const total = deviceData.reduce((s, d) => s + d.value, 0);

    deviceChart.setOption({
      tooltip: {
        trigger: 'item',
        backgroundColor: '#0F172A',
        borderColor: 'transparent',
        textStyle: { color: '#fff', fontSize: 12 },
        formatter: '{b}: {c} ({d}%)',
      },
      graphic: [{
        type: 'text',
        left: 'center',
        top: '38%',
        style: {
          text: total.toLocaleString(),
          fontSize: 22,
          fontWeight: 700,
          fill: '#0F172A',
          textAlign: 'center',
        }
      }, {
        type: 'text',
        left: 'center',
        top: '52%',
        style: {
          text: '总访问',
          fontSize: 12,
          fill: '#94A3B8',
          textAlign: 'center',
        }
      }],
      series: [{
        type: 'pie',
        radius: ['50%', '72%'],
        center: ['50%', '48%'],
        avoidLabelOverlap: true,
        padAngle: 3,
        itemStyle: { borderRadius: 6 },
        label: {
          show: true,
          position: 'outside',
          formatter: '{b}\n{d}%',
          fontSize: 11,
          color: colors.text,
          lineHeight: 16,
        },
        labelLine: { length: 12, length2: 8 },
        emphasis: {
          label: { show: true, fontSize: 13, fontWeight: 600 },
          itemStyle: { shadowBlur: 10, shadowColor: 'rgba(0,0,0,0.1)' },
        },
        data: deviceData,
      }],
    });
    window.addEventListener('resize', () => deviceChart.resize());
  }

  // ===== 3. Browser Distribution (Horizontal Bar) =====
  const browserEl = document.getElementById('chart-browser');
  if (browserEl) {
    const browserChart = echarts.init(browserEl);
    const rawBrowsers = @json($browser_data);
    const browserNames = rawBrowsers.map(b => b.browser || 'Unknown').reverse();
    const browserValues = rawBrowsers.map(b => b.visits || 0).reverse();

    browserChart.setOption({
      tooltip: {
        trigger: 'axis',
        backgroundColor: '#0F172A',
        borderColor: 'transparent',
        textStyle: { color: '#fff', fontSize: 12 },
        axisPointer: { type: 'shadow' },
      },
      grid: { left: 8, right: 32, top: 8, bottom: 8, containLabel: true },
      xAxis: {
        type: 'value',
        splitLine: { lineStyle: { color: '#F1F5F9', type: 'dashed' } },
        axisLine: { show: false },
        axisTick: { show: false },
        axisLabel: { color: colors.textLight, fontSize: 11 },
      },
      yAxis: {
        type: 'category',
        data: browserNames,
        axisLine: { show: false },
        axisTick: { show: false },
        axisLabel: { color: colors.text, fontSize: 12 },
      },
      series: [{
        type: 'bar',
        barWidth: 16,
        itemStyle: {
          borderRadius: [0, 4, 4, 0],
          color: new echarts.graphic.LinearGradient(0, 0, 1, 0, [
            { offset: 0, color: '#3B82F6' },
            { offset: 1, color: '#2563EB' },
          ]),
        },
        emphasis: {
          itemStyle: {
            color: new echarts.graphic.LinearGradient(0, 0, 1, 0, [
              { offset: 0, color: '#2563EB' },
              { offset: 1, color: '#1E40AF' },
            ]),
          }
        },
        data: browserValues,
      }],
    });
    window.addEventListener('resize', () => browserChart.resize());
  }
})();
</script>
@endpush
