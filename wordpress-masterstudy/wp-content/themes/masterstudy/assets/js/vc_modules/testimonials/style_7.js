"use strict";

(function ($) {
  "use strict";

  $(window).on('load', function () {
    simple_carousel_cfs();
  });

  function simple_carousel_cfs() {
    var owlRtl = false;

    if ($('body').hasClass('rtl')) {
      owlRtl = true;
    }

    $('.testimonials-carousel-init').each(function () {
      if ($(this).children().size() < 2) {
        $(this).closest('.testimonials_main_wrapper').find('.testimonials_control_bar').hide();
        return;
      }

      var $this = $(this);
      var per_row = $this.attr('data-items');
      $this.owlCarousel({
        rtl: owlRtl,
        dots: true,
        items: per_row,
        autoplay: false,
        loop: true,
        autoplayHoverPause: true,
        slideBy: 1,
        mouseDrag: false,
        responsive: {
          0: {
            items: 1
          },
          769: {
            items: per_row
          }
        }
      });
      $this.closest('.simple_carousel_wrapper').find('.simple_carousel_prev').on('click', function (e) {
        e.preventDefault();
        $this.trigger('prev.owl.carousel');
      });
      $this.closest('.simple_carousel_wrapper').find('.simple_carousel_next').on('click', function (e) {
        e.preventDefault();
        $this.trigger('next.owl.carousel');
      });
    });
  }
})(jQuery);