<div class="d-flex align-items-center flex-column py-4">
  <img src="{{ asset('images/no-data.svg') }}" class="img-fluid wp-400">
  <span class="small text-secondary">{{ $text ?: __('panel/common.no_data').' ~' }}</span>
</div>