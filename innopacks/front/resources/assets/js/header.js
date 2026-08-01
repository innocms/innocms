$(function () {
  // header-box
  // 页面向下滚动时，给header-box添加 active（仅视觉收缩，不 fix 定位）
  $(window).scroll(function () {
    if ($(window).scrollTop() > 0) {
      $('.header-box').addClass('active');
    } else {
      $('.header-box').removeClass('active');
    }
  });

  // Hover mega-menu: toggle on click for touch devices (hover is CSS-only).
  $('.has-mega > .nav-link').on('click', function (e) {
    // Only intercept on coarse pointers (touch); let the link navigate on desktop click.
    if (window.matchMedia('(hover: none)').matches) {
      e.preventDefault();
      $(this).parent('.has-mega').toggleClass('is-open').siblings('.has-mega').removeClass('is-open');
    }
  });
  $(document).on('click', function (e) {
    if (!$(e.target).closest('.has-mega').length) {
      $('.has-mega').removeClass('is-open');
    }
  });
});
