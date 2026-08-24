"use strict";

(function ($) {
  $(document).ready(function () {
    var _window$_masterstudy_;
    if ((_window$_masterstudy_ = window._masterstudy_utils) !== null && _window$_masterstudy_ !== void 0 && (_window$_masterstudy_ = _window$_masterstudy_.slots) !== null && _window$_masterstudy_ !== void 0 && _window$_masterstudy_.render) {
      $('[data-masterstudy-modal-slot-id]').each(function () {
        var slotId = $(this).attr('data-masterstudy-modal-slot-id');
        $(this).css('display', '');
        window._masterstudy_utils.slots.render([slotId]);
        $(this).on('click', function (e) {
          if (e.target === e.currentTarget) {
            $(this).toggleClass('masterstudy-modal-component_open', false);
          }
        });
      });
    }
  });
})(jQuery);