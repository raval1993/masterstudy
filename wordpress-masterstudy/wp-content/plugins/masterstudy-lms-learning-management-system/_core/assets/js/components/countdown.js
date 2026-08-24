"use strict";

(function ($) {
  $(document).ready(function () {
    // launch timers
    $('.masterstudy-countdown').each(function () {
      var $el = $(this);
      var $time = $el.data('timer');
      if ($time <= 0 || new Date() > $time) return;
      $(this).countdown({
        timestamp: $time
      });
    });
  });
})(jQuery);