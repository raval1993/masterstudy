"use strict";

(function ($) {
  $(document).ready(function () {
    $('.masterstudy-pricing-item').each(function () {
      var $section = $(this);
      var $switcher = $section.find('.masterstudy-switcher.masterstudy-switcher-toggleable input[type="checkbox"]');
      var $content = $section.find('.masterstudy-pricing-item__content');
      if (!$switcher.is(':checked')) {
        $content.hide();
      }
      $switcher.on('change', function () {
        if ($(this).is(':checked')) {
          $content.slideDown(200);
        } else {
          $content.slideUp(200, function () {
            $content.find('input, select, textarea').each(function () {
              var type = $(this).attr('type');
              if (type === 'checkbox' || type === 'radio') {
                $(this).prop('checked', false);
              } else {
                $(this).val('');
              }
            });
          });
        }
      });
    });
    $(document).on('click', '.masterstudy-pricing-item__arrow-top', function () {
      var $input = $(this).siblings('input[type="number"]');
      var value = parseFloat($input.val()) || 0;
      $input.val(value + 1).trigger('input');
    });
    $(document).on('click', '.masterstudy-pricing-item__arrow-down', function () {
      var $input = $(this).siblings('input[type="number"]');
      var value = parseFloat($input.val()) || 0;
      value = Math.max(0, value - 1);
      $input.val(value).trigger('input');
    });
  });
})(jQuery);