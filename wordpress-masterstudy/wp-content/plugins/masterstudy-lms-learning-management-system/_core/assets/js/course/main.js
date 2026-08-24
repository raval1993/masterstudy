"use strict";

(function ($) {
  $(document).ready(function () {
    if (window.matchMedia('(max-width: 1023.98px)').matches) {
      $('.masterstudy-single-course__sidebar').children().appendTo('.masterstudy-single-course__main');
      $('.masterstudy-single-course__sidebar').remove();
    }
  });
})(jQuery);