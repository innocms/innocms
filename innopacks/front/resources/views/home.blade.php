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
            <div class="home-banner-left">
              <h1 data-aos="fade-up" data-aos-duration="1000">InnoShop</h1>
              <p class="sub-title" data-aos="fade-up" data-aos-duration="1500">创新开源<span
                    class="text-primary">电商系统</span></p>
              <p class="sub-title-2" data-aos="fade-up" data-aos-duration="1800">
                - 面向全球的开源电商系统, 15年行业持续深耕集大成者。<br/>
                - 用户友好、界面直观、快速上手、拖拽式设计、无需复杂培训。<br/>
                - 基于最新技术, 深度集成 AI, 支持多语言和多货币等特性。<br>
                - 高内聚、低耦合的模块化设计, 简单方便快速开发插件。<br>
              </p>
              <div data-aos="fade-up" data-aos-duration="2000" class="left-btn">
                <a href="{{ url('products') }}" class="btn btn-lg btn-primary">立即探索</a>
                <div class="text-secondary">打造您面向海外市场的电商平台！</div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="home-banner-right"><img src="{{ asset('images/front/home/top-1.png') }}" class="img-fluid"
                                                data-aos="fade-up" data-aos-duration="2000"></div>
          </div>
        </div>
      </div>
    </div>
    <img src="{{ asset('images/front/home/top-bg.svg') }}" class="img-fluid home-banner-bg">
  </div>

  <div class="home-business">
    <div class="container home-business-container">
      <div class="business-top">
        <div class="module-title" data-aos="fade-up">我们的产品</div>
        <div class="module-sub-title" data-aos="fade-up">InnoShop
          是一款创新的开源电子商务平台, 通过集成AI技术、多语言支持、深入数据分析和开源可定制性, 为全球商家提供了一个高效、灵活且用户友好的在线销售和营销解决方案。
        </div>
      </div>
      <div class="business-info">
        <div class="row align-items-center business-1">
          <div class="col-12 col-md-6">
            <div class="business-img"><img src="{{ asset('images/front/home/home-1.png') }}" class="img-fluid"></div>
          </div>
          <div class="col-12 col-md-6">
            <div class="business-text text-right">
              <div data-aos="fade-up" class="text-item">
                <div class="icon"><i class="bi bi-star-fill"></i></div>
                <div class="title">AI 集成</div>
                <div class="sub-title">
                  通过先进的人工智能技术, 实现文案自动编写、描述翻译、图片生成和美化, 极大提升内容创作和商品展示的效率与质量。
                </div>
              </div>
              <div data-aos="fade-up" class="text-item">
                <div class="icon"><i class="bi bi-hexagon-fill"></i></div>
                <div class="title">多语言和多货币支持</div>
                <div class="sub-title">
                  平台支持多种语言和货币, 确保全球用户都能获得无障碍的购物体验, 拓宽了市场范围。
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row align-items-center">
          <div class="col-12 col-md-6">
            <div class="business-text text-left">
              <div data-aos="fade-up" class="text-item">
                <div class="icon"><i class="bi bi-star-fill"></i></div>
                <div class="title">扩展性和定制性</div>
                <div class="sub-title">
                  作为一个开源电商平台, 通过完善的插件机制提供高度的扩展性和定制性, 允许商家根据特定业务需求方便简单的进行个性化开发和调整, 以满足不断变化的市场需求。
                </div>
              </div>
              <div data-aos="fade-up" class="text-item">
                <div class="icon"><i class="bi bi-hexagon-fill"></i></div>
                <div class="title">营销与数据分析</div>
                <div class="sub-title">
                  结合丰富的促销活动与提供深入的数据分析工具, 帮助商家洞察用户行为和销售趋势, 从而制定更精准的市场策略和优化运营。同时通过精准的市场推广, 增强品牌吸引力和客户忠诚度。
                </div>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-6">
            <div class="img-home-2-box"><img src="{{ asset('images/front/home/home-2.png') }}" class="img-fluid img-home-2"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="home-core-function">
    @php
      $function = [
        ['icon' => 'bi bi-star-fill', 'title' => '商品管理', 'sub_title' => '包括商品分类、商品添加、库存管理、价格管理等'],
        ['icon' => 'bi bi-star-fill', 'title' => '订单管理', 'sub_title' => '包括订单查询、订单管理、订单状态处理、退款管理等'],
        ['icon' => 'bi bi-star-fill', 'title' => '用户管理', 'sub_title' => '包括用户注册、登录、个人信息管理、购物车管理等'],
        ['icon' => 'bi bi-star-fill', 'title' => '支付和结算', 'sub_title' => '支持多种支付方式, 包括支付宝、微信支付、银行卡等'],
        ['icon' => 'bi bi-star-fill', 'title' => '物流管理', 'sub_title' => '包括发货、物流跟踪、订单配送等'],
        ['icon' => 'bi bi-star-fill', 'title' => 'SEO优化', 'sub_title' => '支持SEO优化, 包括页面标题、关键词、描述等'],
      ];
    @endphp
    <div class="container function-info">
      <div class="module-title" data-aos="fade-up">更多核心功能</div>
      <div class="row">
        @foreach ($function as $item)
          <div class="col-12 col-lg-4">
            <div class="core-function-item" data-aos="fade-up">
              <div class="icon"><i class="{{ $item['icon'] }}"></i></div>
              <div class="item-text">
                <div class="title">{{ $item['title'] }}</div>
                <div class="sub-title">{{ $item['sub_title'] }}</div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
    <img src="{{ asset('images/front/home/top-bg.svg') }}" class="img-fluid function-bg">
  </div>

  <div class="home-contact">
    <div class="container">
      <div class="title">如果您需要与我们取得联系, 以下是我们的联系方式</div>
      <div class="contact-icon">
        <img src="{{ asset('images/front/home/home-3.png') }}" class="img-fluid" data-aos="fade-up"
             data-aos-duration="2000">
      </div>
      <div class="row">
        <div class="col-12 col-lg-4">
          <div class="contact-item" data-aos="fade-up" data-aos-duration="2000">
            <div class="icon"><i class="bi bi-telephone-fill"></i></div>
            <div class="right">
              <div class="text-1">联系电话</div>
              <div class="text-2">
                <a href="tel:17828469818"><i class="bi bi-telephone-fill text-primary"></i> 17828469818</a>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12 col-lg-4">
          <div class="contact-item" data-aos="fade-up" data-aos-duration="2000">
            <div class="icon"><i class="bi bi-envelope-fill"></i></div>
            <div class="right">
              <div class="text-1">联系邮箱</div>
              <div class="text-2">
                <a href="mailto:team@innoshop.com"><i class="bi bi-envelope-fill text-primary"></i> team@innoshop.com</a>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12 col-lg-4">
          <div class="contact-item wechat-box" data-aos="fade-up" data-aos-duration="2000">
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
      </div>
    </div>
  </div>

@endsection

@push('footer')
  <script>
    AOS.init({
      duration: 300,
      easing: 'ease-in-out',
      once: true,
      mirror: false
    });
  </script>
@endpush