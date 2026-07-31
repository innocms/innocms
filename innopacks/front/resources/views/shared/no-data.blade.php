<div class="d-flex align-items-center flex-column py-4">
  <img src="{{ asset('images/no-data.svg') }}" class="img-fluid wp-400">
  <span class="fs-4 text-secondary">{{ $text ?: __('front::common.no_data') }}</span>
</div>