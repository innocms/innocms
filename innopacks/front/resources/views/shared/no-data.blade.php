<div class="d-flex align-items-center flex-column py-3">
  <img src="{{ asset('images/no-data.svg') }}" class="img-fluid wp-200">
  <span class="fs-4 text-secondary mt-2">{{ $text ?: __('front::common.no_data') }}</span>
</div>