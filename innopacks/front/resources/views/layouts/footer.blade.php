<div class="footer-box">
  <div class="top-bg"><img src="{{ asset('images/front/home/footer-bg.svg') }}" class="img-fluid"></div>
  <div class="container">
    <div class="top-title">InnoCMS 轻量级企业官网建站系统</div>
    <div class="bottom-box">
      <div class="row">
        <div class="col-md-6">
          <div class="left-links">
            Powered By <a href="https://www.innocms.com" target="_blank">INNOCMS</a>
          </div>
        </div>
        <div class="col-md-6 copyright-text">
          <a href="https://www.innocms.com" class="ms-2" target="_blank">InnoCMS</a>
          &copy; {{ date('Y') }} All Rights Reserved
          @if(system_setting('icp_number'))
            <a href="https://beian.miit.gov.cn" class="ms-2" target="_blank">{{ system_setting('icp_number') }}</a>
          @endif
        </div>
    </div>
    </div>
  </div>
</div>

@if (system_setting('js_code'))
  {!! system_setting('js_code') !!}
@endif
