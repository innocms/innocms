@extends('layouts.app')

@section('body-class', 'page-contact')

@section('title', __('front::common.contact_page_title').' - '.system_setting('meta_title', 'InnoCMS'))

@section('content')
  @include('shared.page-head', ['title' => __('front::common.contact_page_title')])

  <div class="page-contact-content py-5">
    <div class="container">
      <div class="row g-4">
        <div class="col-12 col-lg-5">
          <h3 class="mb-3">{{ __('front::common.contact_form_title') }}</h3>
          <p class="text-muted mb-4">{{ __('front::common.contact_form_sub') }}</p>
          <ul class="list-unstyled contact-info mb-4">
            @if($telephone)
              <li class="d-flex align-items-center mb-3">
                <i class="bi bi-telephone-fill me-2 text-primary"></i>
                <span><strong>{{ __('front::common.contact_phone_label') }}:</strong> <a href="tel:{{ preg_replace('/[^0-9+]/', '', $telephone) }}" class="text-decoration-none">{{ $telephone }}</a></span>
              </li>
            @endif
            @if($email)
              <li class="d-flex align-items-center mb-3">
                <i class="bi bi-envelope-fill me-2 text-primary"></i>
                <span><strong>{{ __('front::common.contact_email_label') }}:</strong> <a href="mailto:{{ $email }}" class="text-decoration-none">{{ $email }}</a></span>
              </li>
            @endif
            @if($address)
              <li class="d-flex align-items-center mb-3">
                <i class="bi bi-geo-alt-fill me-2 text-primary"></i>
                <span><strong>{{ __('front::common.contact_address_label') }}:</strong> {{ $address }}</span>
              </li>
            @endif
            @if($business_hours)
              <li class="d-flex align-items-center mb-3">
                <i class="bi bi-clock-fill me-2 text-primary"></i>
                <span><strong>{{ __('front::common.contact_hours_label') }}:</strong> {{ $business_hours }}</span>
              </li>
            @endif
          </ul>
        </div>

        <div class="col-12 col-lg-7">
          <div class="contact-form-wrap">
            <div id="contact-form-alert" class="alert d-none" role="alert"></div>
            <form id="contact-form" class="contact-form">
              @csrf
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="contact-name" class="form-label">{{ __('front::common.contact_name') }}</label>
                  <input type="text" class="form-control" id="contact-name" name="name" placeholder="{{ __('front::common.contact_name_ph') }}">
                </div>
                <div class="col-md-6">
                  <label for="contact-phone" class="form-label">{{ __('front::common.contact_phone') }}</label>
                  <input type="text" class="form-control" id="contact-phone" name="phone" placeholder="{{ __('front::common.contact_phone_ph') }}">
                </div>
                <div class="col-md-6">
                  <label for="contact-email" class="form-label">{{ __('front::common.contact_email') }}</label>
                  <input type="email" class="form-control" id="contact-email" name="email" placeholder="{{ __('front::common.contact_email_ph') }}" required>
                </div>
                <div class="col-md-6">
                  <label for="contact-company" class="form-label">{{ __('front::common.contact_company') }}</label>
                  <input type="text" class="form-control" id="contact-company" name="company" placeholder="{{ __('front::common.contact_company_ph') }}">
                </div>
                <div class="col-12">
                  <label for="contact-content" class="form-label">{{ __('front::common.contact_content') }}</label>
                  <textarea class="form-control" id="contact-content" name="content" rows="5" placeholder="{{ __('front::common.contact_content_ph') }}" required></textarea>
                </div>
                <div class="col-12">
                  <button type="submit" id="contact-submit" class="btn btn-primary">{{ __('front::common.contact_submit') }}</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('footer')
<script>
  (function () {
    var form    = document.getElementById('contact-form');
    var alertEl = document.getElementById('contact-form-alert');
    var submit  = document.getElementById('contact-submit');

    function showAlert(type, message) {
      alertEl.className = 'alert alert-' + type;
      alertEl.textContent = message;
      alertEl.classList.remove('d-none');
    }

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      alertEl.classList.add('d-none');

      var original = submit.textContent;
      submit.disabled = true;
      submit.textContent = '{{ __('front::common.contact_sending') }}';

      axios.post('{{ front_route('contacts.store') }}', new FormData(form))
        .then(function (response) {
          var data = response.data;
          if (data.success) {
            showAlert('success', data.message);
            form.reset();
          } else {
            showAlert('danger', data.message || '{{ __('front::common.contact_submit_fail') }}');
          }
        })
        .catch(function () {
          showAlert('danger', '{{ __('front::common.contact_submit_fail') }}');
        })
        .then(function () {
          submit.disabled = false;
          submit.textContent = original;
        });
    });
  })();
</script>
@endpush
