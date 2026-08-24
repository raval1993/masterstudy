"use strict";

(function ($) {
  $(document).ready(function () {
    $(document).on('click', '[data-id="analytics-watch-video"]', function (event) {
      event.preventDefault();
      var $trigger = $(this);
      var $box = $trigger.closest('.wpcfto-box');
      var $popup = $box.find('.masterstudy-analytics-preview-page__popup').first();
      if (!$popup.length) {
        $popup = $box.siblings('.masterstudy-analytics-preview-page__popup').first();
      }
      if (!$popup.length) {
        $popup = $('.masterstudy-analytics-preview-page__popup').first();
      }
      var $iframe = $popup.find('iframe').first();
      if (!$iframe.length) {
        return;
      }
      var originalSrc = $iframe.data('original-src') || $iframe.attr('src');
      if (originalSrc) {
        $iframe.data('original-src', originalSrc);
      }
      if (!$iframe.attr('src') && originalSrc) {
        $iframe.attr('src', originalSrc);
      }
      $popup.addClass('masterstudy-analytics-preview-page__popup_show');
    });
    $(document).on('click', '.masterstudy-analytics-preview-page__popup', function (event) {
      var $popup = $(this);
      if (!$(event.target).closest('.masterstudy-analytics-preview-page__popup-video').length) {
        $popup.find('iframe').first().attr('src', '');
        $popup.removeClass('masterstudy-analytics-preview-page__popup_show');
      }
    });
  });
})(jQuery);