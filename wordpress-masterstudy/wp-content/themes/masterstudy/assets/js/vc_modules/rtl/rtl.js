"use strict";

(function ($) {
  $(window).on('load', function () {
    setTimeout(function () {
      $(window).trigger('resize');
    }, 200);
  });
})(jQuery);