$(function () {
  // header-box
  // 页面向下滚动时，给header-box添加 active
  const headerContentHeight = $('.header-box').outerHeight(true);

  $(window).scroll(function () {
    if ($(window).scrollTop() > 0) {
      $('.header-box').addClass('active');
      if (!$('.header-content-placeholder').length && !$('body').hasClass('page-home'))
      $('.header-box').before('<div class="header-content-placeholder" style="height: ' + headerContentHeight + 'px;"></div>');
    } else {
      $('.header-box').removeClass('active');
      $('.header-content-placeholder').remove();
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
