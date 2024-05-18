@extends('front::layouts.app')
@section('body-class', 'page-home')
@section('content')

  @push('header')
    <script src="{{ asset('vendor/aos/aos.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('vendor/aos/aos.css') }}">
  @endpush

  <div class="home-banner">
    <div class="home-banner-info">
      <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="home-banner-right">
                    <img src="{{ asset('images/cms/services/top-bg-1.png') }}" class="img-fluid" data-aos="fade-left" data-aos-duration="2000">
                </div>
            </div>
          <div class="col-md-6">
            <div class="home-banner-left">
              <h1 data-aos="fade-up" data-aos-duration="1000">InnoCMS</h1>
              <p class="sub-title" data-aos="fade-up" data-aos-duration="1500">小体量
                <span class="text-primary">大服务</span></p>
              <p class="sub-title-2" data-aos="fade-up" data-aos-duration="1800">
                - 我们提供丰富的服务以满足您的定制需求！<br/>
                - 插件、二次开发、专属定制、托管部署、页面设计，一应俱全!<br/>
                - 专业团队，为您的业务保驾护航。<br>
              </p>
              <div data-aos="fade-up" data-aos-duration="2000" class="left-btn">
                <button type="button" class="btn btn-lg btn-primary">立即探索</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="bottom-bg">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1280 140" preserveAspectRatio="none"><path d="M320 28c320 0 320 84 640 84 160 0 240-21 320-42v70H0V70c80-21 160-42 320-42z"></path></svg>
    </div>
  </div>

  <div class="home-business">
    <div class="container home-business-container">
      <div class="business-top">
        <div class="module-title" data-aos="fade-up">我们的服务</div>
        <div class="module-sub-title" data-aos="fade-up">
            我们不仅提供定制化的解决方案，还以专业的技术知识、创新的思维方式和全方位的支持，确保您能够享受到卓越而高效的服务体验。我们承诺，无论您的需求如何变化，我们都能为您提供最匹配的专业服务。
        </div>
      </div>
      <div class="services-info">
          <div class="row row-cols-1 row-cols-md-4 g-4">
              <div data-aos="fade-up" data-aos-duration="600" class="col">
                  <div class="card h-100 border-0 shadow-sm">
                      <img src="{{asset('images/cms/services/icon-1.svg')}}" class="card-img-top w-75 mx-auto" alt="开源系统">
                      <div class="card-body">
                          <h5 class="card-title text-center">代码开源</h5>
                          <p class="card-text">致力于提供高度灵活和可定制的解决方案。利用开放源代码的优势，我们帮助企业构建可扩展的系统，同时确保透明度和社区支持。</p>
                      </div>
                  </div>
              </div>
              <div data-aos="fade-up" data-aos-duration="1000" class="col">
                  <div class="card h-100 border-0 shadow-sm">
                      <img src="{{asset('images/cms/services/icon-2.svg')}}" class="card-img-top w-75 mx-auto" alt="插件市场">
                      <div class="card-body">
                          <h5 class="card-title text-center">插件市场</h5>
                          <p class="card-text">通过我们的插件市场，用户可以轻松扩展其系统功能。我们提供丰富的插件选择，以满足不同的业务需求，让定制化服务触手可及</p>
                      </div>
                  </div>
              </div>
              <div data-aos="fade-up" data-aos-duration="1400" class="col">
                  <div class="card h-100 border-0 shadow-sm">
                      <img src="{{asset('images/cms/services/icon-3.svg')}}" class="card-img-top w-75 mx-auto" alt="定制开发">
                      <div class="card-body">
                          <h5 class="card-title text-center">定制开发</h5>
                          <p class="card-text">专注于根据您的具体需求，打造独一无二的软件解决方案。从概念到实现，我们与您紧密合作，确保最终产品超出您的期望。</p>
                      </div>
                  </div>
              </div>
              <div data-aos="fade-up" data-aos-duration="1800" class="col">
                  <div class="card h-100 border-0 shadow-sm">
                      <img src="{{asset('images/cms/services/icon-4.svg')}}" class="card-img-top w-75 mx-auto" alt="安装维护">
                      <div class="card-body">
                          <h5 class="card-title text-center">安装维护</h5>
                          <p class="card-text">我们的安装维护服务确保您的系统运行平稳，通过定期更新和故障排除，我们提供无忧的技术支持，让您专注于核心业务。</p>
                      </div>
                  </div>
              </div>
              <div data-aos="fade-up" data-aos-duration="600" class="col">
                  <div class="card h-100 border-0 shadow-sm">
                      <img src="{{asset('images/cms/services/icon-5.svg')}}" class="card-img-top w-75 mx-auto" alt="快速建站">
                      <div class="card-body">
                          <h5 class="card-title text-center">建站组件</h5>
                          <p class="card-text">InnoCMS 提供了一套完整的网站模板和定制选项，企业可以根据自己的品牌形象和需求，快速搭建起一个全新的官网。</p>
                      </div>
                  </div>
              </div>
              <div data-aos="fade-up" data-aos-duration="1000" class="col">
                  <div class="card h-100 border-0 shadow-sm">
                      <img src="{{asset('images/cms/services/icon-6.svg')}}" class="card-img-top w-75 mx-auto" alt="技术培训">
                      <div class="card-body">
                          <h5 class="card-title text-center">技术培训</h5>
                          <p class="card-text">通过我们的技术培训服务，您的团队将获得必要的技能和知识。我们的培训课程旨在提升效率，促进创新，并确保长期的技术自给自足。</p>
                      </div>
                  </div>
              </div>
              <div data-aos="fade-up" data-aos-duration="1400" class="col">
                  <div class="card h-100 border-0 shadow-sm">
                      <img src="{{asset('images/cms/services/icon-7.svg')}}" class="card-img-top w-75 mx-auto" alt="技术培训">
                      <div class="card-body">
                          <h5 class="card-title text-center">整站托管</h5>
                          <p class="card-text">我们提供一站式托管服务，从建站到部署上线，全部交由我们的专业人士完成，并提供后续的运维和升级，省时省力省心。</p>
                      </div>
                  </div>
              </div>
              <div data-aos="fade-up" data-aos-duration="1800" class="col">
                  <div class="card h-100 border-0 shadow-sm">
                      <img src="{{asset('images/cms/services/icon-8.svg')}}" class="card-img-top w-75 mx-auto" alt="技术培训">
                      <div class="card-body">
                          <h5 class="card-title text-center">主题设计</h5>
                          <p class="card-text d-inline">我们提供定制主题设计服务，根据您的业务需求和真实业务场景，为您设计专属的主题和配套UI以及页面布局。</p>
                      </div>
                  </div>
              </div>
          </div>
      </div>
    </div>
  </div>

  <div class="home-contact" id="contactUsContent">
    <div class="container">
      <div class="title" data-aos="fade-up">如果您需要与我们取得联系, 以下是我们的联系方式</div>
      <div class="contact-icon">
        <img src="{{ asset('images/front/home/home-3.png') }}" class="img-fluid" data-aos="fade-up">
      </div>
      <div class="row">
        <div class="col-12 col-lg-3">
          <div class="contact-item" data-aos="fade-up">
            <div class="icon"><i class="bi bi-telephone-fill"></i></div>
            <div class="right">
              <div class="text-1">联系电话</div>
              <div class="text-2">
                <a href="tel:17828469818"><i class="bi bi-telephone-fill text-primary"></i> 杨先生：17828469818</a>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12 col-lg-3">
          <div class="contact-item" data-aos="fade-up">
            <div class="icon"><i class="bi bi-envelope-fill"></i></div>
            <div class="right">
              <div class="text-1">联系邮箱</div>
              <div class="text-2">
                <a href="mailto:team@innoshop.com"><i class="bi bi-envelope-fill text-primary"></i> team@innoshop.com</a>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12 col-lg-3">
          <div class="contact-item wechat-box" data-aos="fade-up">
            <div class="icon"><i class="bi bi-wechat"></i></div>
            <div class="right">
              <div class="text-1">微信联系</div>
              <div class="text-2"><i class="bi bi-wechat text-primary"></i> innoshop666</div>
              <div class="w-code">
                <img src="{{ asset('images/front/home/w-code.png') }}" class="img-fluid">
              </div>
            </div>
          </div>
        </div>
          <div class="col-12 col-lg-3">
              <div class="contact-item wechat-box" data-aos="fade-up">
                  <div class="icon"><i class="bi bi-tencent-qq"></i></div>
                  <div class="right">
                      <div class="text-1">QQ交流群</div>
                      <div class="text-2"><i class="bi bi-tencent-qq text-primary"></i> 960062283</div>
                      <div class="w-code">
                          <img src="{{ asset('images/front/home/q-code.png') }}" class="img-fluid">
                      </div>
                  </div>
              </div>
          </div>
      </div>
    </div>
  </div>

  <div class="home-customized">
      <div class="home-banner-info">
          <div class="container">
              <div class="row">
                  <div class="col-md-6">
                      <div class="home-banner-right">
                          <img src="{{ asset('images/cms/home/top-bg-5.png') }}" class="img-fluid" data-aos="fade-up" data-aos-duration="2000">
                      </div>
                  </div>
                  <div class="col-md-6">
                      <div class="home-banner-left">
                          <h1 data-aos="fade-up" data-aos-duration="1000">不想亲自动手？</h1>
                          <p class="sub-title" data-aos="fade-up" data-aos-duration="1500">私人订制
                              <span class="text-primary">快速部署</span></p>
                          <p class="sub-title-2" data-aos="fade-up" data-aos-duration="1800">
                              - 如果您不想亲自动手，或者不懂开发技术，不必担心！<br/>
                              - SaaS托管，独立云服务器，一站式托管服务<br/>
                              - 专人部署，避开繁杂，全程无忧，7*12小时服务<br/>
                              - 备案、域名、解析、SSL、CDN...专业的事交给专业的人来做<br/>
                              - 专业团队满足您个性功能需求定制开发<br>
                          </p>
                          <div data-aos="fade-up" data-aos-duration="2000" class="left-btn">
                              <a href="#contactUsContent" type="button" class="btn btn-lg btn-primary">联系我们</a>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
      <div class="bottom-bg">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1280 140" preserveAspectRatio="none"><path d="M320 28c320 0 320 84 640 84 160 0 240-21 320-42v70H0V70c80-21 160-42 320-42z"></path></svg>
      </div>
  </div>

  <div class="home-contact" id="contactUsContent">
      <div class="container">
          <div class="title" data-aos="fade-up">加入开源社区，共建共享</div>
          <div class="contact-icon">
              <img src="{{ asset('images/front/home/home-4.png') }}" class="img-fluid" data-aos="fade-up">
          </div>
          <div class="row">
              <div class="col-12 col-lg-3">
                  <div class="contact-item" data-aos="fade-up">
                      <div class="icon"><i class="bi bi-github"></i></div>
                      <div class="right">
                          <div class="text-1">GITHUB 给个<i class="bi bi-heart-fill"></i>吧！</div>
                          <div class="text-2">
                              <a href="https://github.com/innocms/innocms"><i class="bi bi-github text-primary"></i> innocms/innocms</a>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="col-12 col-lg-3">
                  <div class="contact-item wechat-box" data-aos="fade-up">
                      <div class="icon"><i class="bi bi-journal-code"></i></div>
                      <div class="right">
                          <div class="text-1">开发手册</div>
                          <a href="#" class="text-2"><i class="bi bi-journal-code text-primary"></i> docs</a>
                      </div>
                  </div>
              </div>
              <div class="col-12 col-lg-3">
                  <div class="contact-item wechat-box" data-aos="fade-up">
                      <div class="icon"><i class="bi bi-filter-square"></i></div>
                      <div class="right">
                          <div class="text-1">开源协议</div>
                          <a href="https://github.com/innocms/innocms/blob/master/LICENSE.txt" class="text-2"><i class="bi bi-filter-square text-primary"></i> OSL-3.0</a>
                      </div>
                  </div>
              </div>
              <div class="col-12 col-lg-3">
                  <div class="contact-item wechat-box" data-aos="fade-up">
                      <div class="icon"><i class="bi bi-chat-left-heart"></i></div>
                      <div class="right">
                          <div class="text-1">吐槽建议</div>
                          <a href="https://github.com/innocms/innocms/issues" class="text-2"><i class="bi bi-chat-left-heart text-primary"></i> 提交issue</a>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
@endsection

@push('footer')
  <script>
    AOS.init({
      duration: 300,
      easing: 'ease-in-out',
      once: false, // whether animation should happen only once - while scrolling down
      mirror: true, // whether elements should animate out while scrolling past them
    });

    $(".home-banner .left-btn button").click(function () {
      $('html, body').animate({
        scrollTop: $(".home-business").offset().top - 100
      }, 200);
    });
  </script>
@endpush
