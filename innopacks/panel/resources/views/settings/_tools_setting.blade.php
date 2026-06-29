<!-- Tools Settings -->
<div class="tab-pane fade" id="tab-setting-tools">
  <ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" type="button" role="tab"
              data-bs-toggle="tab" data-bs-target="#tools-geolite2"
              aria-controls="tools-geolite2" aria-selected="true">
        {{ __('panel/setting_geolite2.geolite2_setting') }}
      </button>
    </li>
  </ul>

  <div class="tab-content">
    <!-- GeoLite2 Sub-tab -->
    <div class="tab-pane fade show active" id="tools-geolite2" role="tabpanel">
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="card-title mb-0">{{ __('panel/setting_geolite2.geolite2_setting') }}</h5>
          <p class="text-muted small mb-0">{{ __('panel/setting_geolite2.geolite2_setting_desc') }}</p>
        </div>
        <div class="card-body">
          <!-- Database Info -->
          <div class="card bg-light mb-4">
            <div class="card-body">
              <h6 class="mb-3">{{ __('panel/setting_geolite2.geolite2_database_info') }}</h6>
              <div class="row mb-2">
                <div class="col-md-3">
                  <strong>{{ __('panel/setting_geolite2.database_status') }}:</strong>
                  <span id="geolite2-status" class="ms-2">
                    @if($geolite2_info['exists'])
                      <span class="badge bg-success">{{ __('panel/setting_geolite2.database_exists') }}</span>
                    @else
                      <span class="badge bg-warning">{{ __('panel/setting_geolite2.database_not_exists') }}</span>
                    @endif
                  </span>
                </div>
                <div class="col-md-3">
                  <strong>{{ __('panel/setting_geolite2.database_size') }}:</strong>
                  <span id="geolite2-size" class="ms-2">{{ $geolite2_info['size_formatted'] }}</span>
                </div>
                <div class="col-md-3">
                  <strong>{{ __('panel/setting_geolite2.database_updated') }}:</strong>
                  <span id="geolite2-modified" class="ms-2">{{ $geolite2_info['modified_formatted'] }}</span>
                </div>
                <div class="col-md-3">
                  <strong>{{ __('panel/setting_geolite2.database_version') }}:</strong>
                  <span id="geolite2-version" class="ms-2">{{ $geolite2_info['version'] ?: '-' }}</span>
                </div>
              </div>
              <div class="row mt-2">
                <div class="col-md-12">
                  <strong>{{ __('panel/setting_geolite2.database_path') }}:</strong>
                  <code id="geolite2-path" class="ms-2 small d-block mt-1">{{ $geolite2_info['path'] }}</code>
                </div>
              </div>
            </div>
          </div>

          <!-- Download Section -->
          <h6 class="mb-3">{{ __('panel/setting_geolite2.geolite2_download') }}</h6>
          <div class="input-group mb-3" style="max-width: 700px;">
            <input
              type="text"
              class="form-control"
              id="geolite2-download-url"
              value="https://res.innoshop.net/GeoLite2-City.mmdb"
              placeholder="{{ __('panel/setting_geolite2.geolite2_download_url_placeholder') }}"
            />
            <button type="button" class="btn btn-primary" id="download-geolite2-btn" onclick="downloadGeoLite2()">
              <i class="bi bi-download"></i> {{ __('panel/setting_geolite2.download_geolite2_database') }}
            </button>
          </div>
          <div class="text-secondary mb-3">
            <small>{{ __('panel/setting_geolite2.geolite2_download_desc') }}</small>
          </div>

          <button type="button" class="btn btn-secondary" onclick="refreshGeoLite2Info()">
            <i class="bi bi-arrow-clockwise"></i> {{ __('panel/setting_geolite2.refresh_info') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

@push('footer')
<script>
function downloadGeoLite2() {
  var btn = document.getElementById('download-geolite2-btn');
  var originalText = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = '<i class="bi bi-hourglass-split"></i> {{ __("panel/setting_geolite2.downloading") }}';

  var url = document.getElementById('geolite2-download-url').value;

  axios.post('{{ panel_route("settings.download_geolite2") }}', { url: url })
    .then(function(data) {
      if (data.success) {
        inno.msg(data.message);
        refreshGeoLite2Info();
      } else {
        inno.alert({ msg: data.message || '{{ __("panel/setting_geolite2.download_failed", ["error" => ""]) }}', type: 'danger' });
      }
    })
    .catch(function(error) {
      var msg = (error.response && error.response.data && error.response.data.message) || error.message;
      inno.alert({ msg: msg, type: 'danger' });
    })
    .finally(function() {
      btn.disabled = false;
      btn.innerHTML = originalText;
    });
}

function refreshGeoLite2Info() {
  axios.get('{{ panel_route("settings.geolite2_info") }}', { params: { _t: Date.now() } })
    .then(function(data) {
      if (data.success) {
        var info = data.data;
        document.getElementById('geolite2-status').innerHTML = info.exists
          ? '<span class="badge bg-success">{{ __("panel/setting_geolite2.database_exists") }}</span>'
          : '<span class="badge bg-warning">{{ __("panel/setting_geolite2.database_not_exists") }}</span>';
        document.getElementById('geolite2-size').textContent = info.size_formatted;
        document.getElementById('geolite2-modified').textContent = info.modified_formatted;
        document.getElementById('geolite2-version').textContent = info.version || '-';
        document.getElementById('geolite2-path').textContent = info.path;
      }
    })
    .catch(function(error) {
      console.error('Failed to refresh GeoLite2 info:', error);
    });
}
</script>
@endpush
