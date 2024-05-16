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
<div class="page-product-content">
  <div class="container">
    <div class="title-box">
      <div class="title">我们的产品</div>
      <div class="sub-title">Our Product Range</div>
    </div>
    <div class="row">
      <div class="col-12 col-md-6">
        <div class="product-item">
          <div class="top">
            <div class="left"><i class="bi bi-box-seam-fill"></i></div>
            <div class="name">InnoShop</div>
          </div>
          <div class="content">
            InnoShop是一款面向中小企业的电子商务平台，提供一站式在线商店解决方案。它以用户友好的界面和强大的后台管理功能著称，帮助商家轻松管理商品、订单和客户关系。InnoShop支持多种支付方式，并集成了社交媒体营销工具，助力商家扩大市场影响力。
          </div>
        </div>
      </div>
      <div class="col-12 col-md-6">
        <div class="product-item">
          <div class="top">
            <div class="left"><i class="bi bi-box-seam-fill"></i></div>
            <div class="name">InnoShop Pro</div>
          </div>
          <div class="content">
            InnoShop Pro是InnoShop的高级版本，专为需要更高级功能和定制服务的企业设计。除了基础版所有功能外，Pro版本提供高级数据分析、个性化推荐引擎和API集成，以满足更复杂的业务需求。它还包含专业的客户支持和优先更新服务，确保商家能够充分利用平台潜力。
          </div>
        </div>
      </div>
      <div class="col-12 col-md-6">
        <div class="product-item">
          <div class="top">
            <div class="left"><i class="bi bi-wechat"></i></div>
            <div class="name">小程序</div>
          </div>
          <div class="content">
            我们的小程序为移动用户提供了便捷的购物体验。它轻量级、易于访问，特别适合快速浏览和购买。小程序与主流社交媒体和通讯工具无缝集成，支持一键分享和邀请朋友，通过社交网络快速传播，增加用户粘性和品牌曝光度。
          </div>
        </div>
      </div>
      <div class="col-12 col-md-6">
        <div class="product-item">
          <div class="top">
            <div class="left"><i class="bi bi-phone-fill"></i></div>
            <div class="name">APP</div>
          </div>
          <div class="content">
            我们的App是一款为移动设备优化的应用程序，提供更加丰富和个性化的用户体验。它不仅包含了小程序的所有功能，还增加了个性化推送、增强的搜索功能和更高级的用户互动元素。App的设计注重流畅性和互动性，确保用户在移动设备上也能享受到优质的购物和服务体验。
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
