"use strict";

(function ($) {
  $(document).ready(function () {
    $('select').each(function () {
      $(this).select2();
    });
  });
})(jQuery);