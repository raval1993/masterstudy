"use strict";

(function ($) {
  $(document).ready(function () {
    $('body').on('click', '.masterstudy-elementor-unlock-banner__button a', function (event) {
      event.preventDefault();
      var href = $(this).attr('href');
      if (href) {
        window.open(href, '_blank');
      }
    });
  });
})(jQuery);