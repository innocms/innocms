@extends('panel::layouts.app')
@section('body-class', 'page-visits')

@section('title', __('panel/menu.visits'))

@section('page-title-right')
  <button type="button" class="btn btn-outline-primary btn-sm" id="btn-batch-locate">
    <i class="bi bi-lightning-charge"></i> 补全数据
  </button>
@endsection

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
            <td>{{ __('panel/common.id') }}</td>
            <td>{{ __('panel/visit.ip_address') }}</td>
            <td>{{ __('panel/visit.country_name') }}</td>
            <td>{{ __('panel/visit.city') }}</td>
            <td>{{ __('panel/visit.device_type') }}</td>
            <td>{{ __('panel/visit.browser') }}</td>
            <td>{{ __('panel/visit.os') }}</td>
            <td>{{ __('panel/visit.referrer') }}</td>
            <td>PV</td>
            <td>{{ __('panel/visit.first_visited_at') }}</td>
            <td>{{ __('panel/visit.last_visited_at') }}</td>
          </tr>
        </thead>
        <tbody>
        @foreach($visits as $item)
          <tr data-id="{{ $item->id }}">
            <td>{{ $item->id }}</td>
            <td>{{ $item->ip_address }}</td>
            <td class="col-country">
              {{ $item->country_name ?: '-' }}
              <a href="javascript:void(0)" class="text-secondary text-decoration-none btn-refresh-geo ms-1" title="刷新位置"><i class="bi bi-arrow-repeat"></i></a>
            </td>
            <td class="col-city">{{ $item->city ?: '-' }}</td>
            <td>
              @if($item->device_type === 'desktop')
                <span class="badge bg-primary">{{ __('panel/visit.device_desktop') }}</span>
              @elseif($item->device_type === 'mobile')
                <span class="badge bg-success">{{ __('panel/visit.device_mobile') }}</span>
              @elseif($item->device_type === 'tablet')
                <span class="badge bg-info">{{ __('panel/visit.device_tablet') }}</span>
              @else
                <span class="badge bg-secondary">{{ $item->device_type ?: '-' }}</span>
              @endif
            </td>
            <td class="col-browser">{{ $item->browser ?: '-' }}</td>
            <td class="col-os">{{ $item->os ?: '-' }}</td>
            <td>{{ $item->referrer ? Str::limit($item->referrer, 30) : '-' }}</td>
            <td>
              <a href="{{ panel_route('visits.show', $item->id) }}" class="badge bg-secondary text-decoration-none">
                {{ $item->visit_events_count ?? 0 }}
              </a>
            </td>
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

@push('footer')
<script>
(function() {
    var locateUrl  = '{{ panel_route("visits.locate", ["visit" => "__ID__"]) }}';
    var parseUaUrl = '{{ panel_route("visits.parse_ua", ["visit" => "__ID__"]) }}';
    var batchUrl   = '{{ panel_route("visits.batch_locate") }}';

    // Batch locate button
    var batchBtn = document.getElementById('btn-batch-locate');
    if (batchBtn) {
        batchBtn.addEventListener('click', function() {
            if (batchBtn.disabled) return;
            batchBtn.disabled = true;
            batchBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> 处理中...';
            axios.post(batchUrl).then(function(data) {
                if (data.success) {
                    inno.msg('已补全 ' + data.updated + ' 条记录');
                    setTimeout(function() { location.reload(); }, 800);
                }
            }).catch(function(err) {
                var msg = (err.response && err.response.data && err.response.data.error) || '补全失败，请重试';
                inno.msg(msg);
                batchBtn.disabled = false;
                batchBtn.innerHTML = '<i class="bi bi-lightning-charge"></i> 补全数据';
            });
        });
    }

    // Per-row refresh geo button
    document.querySelectorAll('.btn-refresh-geo').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var row = btn.closest('tr');
            var id  = row.dataset.id;
            if (btn.disabled) return;

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            var countryCell = row.querySelector('.col-country');
            var cityCell    = row.querySelector('.col-city');
            var browserCell = row.querySelector('.col-browser');
            var osCell      = row.querySelector('.col-os');

            // Refresh geo
            axios.post(locateUrl.replace('__ID__', id)).then(function(data) {
                if (data.success) {
                    countryCell.textContent = data.country_name || '-';
                    cityCell.textContent    = data.city || '-';
                }
            }).catch(function(err) {
                var msg = (err.response && err.response.data && err.response.data.error) || '';
                if (msg) inno.msg(msg);
            }).finally(function() {
                // Also refresh UA
                axios.post(parseUaUrl.replace('__ID__', id)).then(function(data) {
                    if (data.success) {
                        browserCell.textContent = data.browser || '-';
                        osCell.textContent      = data.os || '-';
                    }
                }).catch(function() {}).finally(function() {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-arrow-repeat"></i>';
                });
            });
        });
    });
})();
</script>
@endpush
@endsection
