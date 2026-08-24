"use strict";

(function ($) {
  document.addEventListener('generatedCertificateUrl', function (event) {
    console.log(event);
    if (event.detail.className.includes('masterstudy-account-my-certificates__certificate-actions-view')) {
      window.open(event.detail.value, '_blank');
    } else {
      var a = document.createElement("a");
      a.href = event.detail.value;
      a.download = "";
      document.body.appendChild(a);
      a.click();
      a.remove();
    }
  });
  $('.masterstudy-account-my-certificates__certificate-actions-copy').on('click', function () {
    var code = $(this).attr('data-id');
    if (navigator.clipboard) {
      void navigator.clipboard.writeText(code);
    }
  });
})(jQuery);