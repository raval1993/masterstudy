"use strict";

(function ($) {
  $(window).on('load', function () {
    t_carousel();
  });

  function t_carousel() {
    var owlRtl = false;

    if ($('body').hasClass('rtl')) {
      owlRtl = true;
    }

    $('.stm_testimonials_wrapper_style_8').each(function () {
      var slides = $(this).data('slides');
      var $this = $(this);
      $this.owlCarousel({
        rtl: owlRtl,
        nav: false,
        dots: true,
        items: slides,
        autoplay: false,
        autoplayHoverPause: true,
        loop: false,
        slideBy: 1,
        margin: 0,
        responsive: {
          0: {
            items: 1
          },
          700: {
            items: 2
          },
          992: {
            items: 3
          },
          1200: {
            items: slides
          }
        }
      });
    });
  }
})(jQuery);