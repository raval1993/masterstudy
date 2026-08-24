"use strict";

(function ($) {
  var initCountdown = function initCountdown($scope) {
    $scope.find('.masterstudy-countdown').each(function () {
      var $el = $(this);
      var $time = $el.data('timer');
      if ($el.data('countdown-initialized') || $time <= 0 || new Date() > $time) return;
      $el.data('countdown-initialized', true);
      $el.countdown({
        timestamp: $time
      });
    });
  };
  $(window).on('elementor/frontend/init', function () {
    elementorFrontend.hooks.addAction('frontend/element_ready/global', initCountdown);
  });
})(jQuery);