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
          <tr>
            <td>{{ $item->id }}</td>
            <td>{{ $item->ip_address }}</td>
            <td>{{ $item->country_name ?: '-' }}</td>
            <td>{{ $item->city ?: '-' }}</td>
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
            <td>{{ $item->browser ?: '-' }}</td>
            <td>{{ $item->os ?: '-' }}</td>
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

    // Per-row refresh icons
    var table = document.querySelector('.page-visits table tbody');
    if (!table) return;

    var COL_COUNTRY = 2, COL_CITY = 3, COL_BROWSER = 5, COL_OS = 6;

    function createIcon(title) {
        var btn = document.createElement('a');
        btn.href = 'javascript:void(0)';
        btn.className = 'text-secondary text-decoration-none';
        btn.title = title;
        btn.innerHTML = '<i class="bi bi-arrow-repeat"></i>';
        return btn;
    }

    function setSpin(icons) {
        icons.forEach(function(b) {
            b.classList.add('disabled');
            b.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        });
    }

    function setError(icons) {
        icons.forEach(function(b) {
            b.innerHTML = '<i class="bi bi-x-circle text-danger"></i>';
            b.classList.remove('disabled');
        });
    }

    table.querySelectorAll('tr').forEach(function(row) {
        var cells = row.querySelectorAll('td');
        if (cells.length < 7) return;

        var id = cells[0].textContent.trim();
        var countryEmpty = cells[COL_COUNTRY].textContent.trim() === '-';
        var cityEmpty    = cells[COL_CITY].textContent.trim() === '-';
        var browserEmpty = cells[COL_BROWSER].textContent.trim() === '-';
        var osEmpty      = cells[COL_OS].textContent.trim() === '-';

        // Geo icons
        if (countryEmpty || cityEmpty) {
            var geoIcons = [];
            if (countryEmpty) {
                cells[COL_COUNTRY].textContent = '';
                var ic = createIcon('获取地理位置');
                cells[COL_COUNTRY].appendChild(ic);
                geoIcons.push(ic);
            }
            if (cityEmpty) {
                cells[COL_CITY].textContent = '';
                var ic = createIcon('获取地理位置');
                cells[COL_CITY].appendChild(ic);
                geoIcons.push(ic);
            }
            geoIcons.forEach(function(icon) {
                icon.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (icon.classList.contains('disabled')) return;
                    setSpin(geoIcons);
                    axios.post(locateUrl.replace('__ID__', id)).then(function(data) {
                        if (data.success) {
                            cells[COL_COUNTRY].textContent = data.country_name || '';
                            cells[COL_CITY].textContent = data.city || '';
                            geoIcons.forEach(function(b) { b.remove(); });
                        } else { setError(geoIcons); }
                    }).catch(function(err) {
                        var msg = (err.response && err.response.data && err.response.data.error) || '';
                        if (msg) inno.msg(msg);
                        setError(geoIcons);
                    });
                });
            });
        }

        // UA icons
        if (browserEmpty || osEmpty) {
            var uaIcons = [];
            if (browserEmpty) {
                cells[COL_BROWSER].textContent = '';
                var ic = createIcon('解析浏览器');
                cells[COL_BROWSER].appendChild(ic);
                uaIcons.push(ic);
            }
            if (osEmpty) {
                cells[COL_OS].textContent = '';
                var ic = createIcon('解析操作系统');
                cells[COL_OS].appendChild(ic);
                uaIcons.push(ic);
            }
            uaIcons.forEach(function(icon) {
                icon.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (icon.classList.contains('disabled')) return;
                    setSpin(uaIcons);
                    axios.post(parseUaUrl.replace('__ID__', id)).then(function(data) {
                        if (data.success) {
                            cells[COL_BROWSER].textContent = data.browser || '';
                            cells[COL_OS].textContent = data.os || '';
                            uaIcons.forEach(function(b) { b.remove(); });
                        } else { setError(uaIcons); }
                    }).catch(function() { setError(uaIcons); });
                });
            });
        }
    });
})();
</script>
@endpush
@endsection
