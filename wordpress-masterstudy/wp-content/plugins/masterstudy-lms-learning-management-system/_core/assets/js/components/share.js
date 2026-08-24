"use strict";

(function ($) {
  $(document).ready(function () {
    setTimeout(function () {
      $('.masterstudy-share-modal').removeAttr('style');
    }, 1000);
    $('.masterstudy-share__button').click(function () {
      $(this).parent().next().addClass('masterstudy-share-modal_open');
      $('body').addClass('masterstudy-share-body-hidden');
    });
    $('.masterstudy-share-modal').click(function (event) {
      if (event.target === this) {
        $('.masterstudy-share-modal').removeClass('masterstudy-share-modal_open');
        $('body').removeClass('masterstudy-share-body-hidden');
      }
    });
    $('.masterstudy-share-modal__close').on('click', function () {
      $('.masterstudy-share-modal').removeClass('masterstudy-share-modal_open');
      $('body').removeClass('masterstudy-share-body-hidden');
    });
    $('.masterstudy-share-modal__link_copy').click(function (event) {
      event.preventDefault();
      var tempInput = document.createElement("input");
      var _this = $(this);
      tempInput.style.position = "absolute";
      tempInput.style.left = "-9999px";
      tempInput.value = $(this).data('url');
      document.body.appendChild(tempInput);
      tempInput.select();
      document.execCommand("copy");
      document.body.removeChild(tempInput);
      var originalButtonText = _this.text();
      _this.text(share_data.copy_text);
      setTimeout(function () {
        _this.text(originalButtonText);
      }, 2000);
    });
  });
})(jQuery);