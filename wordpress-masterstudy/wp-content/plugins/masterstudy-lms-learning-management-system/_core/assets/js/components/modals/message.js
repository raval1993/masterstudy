"use strict";

(function ($) {
  $(document).ready(function () {
    var messageModal = $('.masterstudy-message-modal');
    setTimeout(function () {
      messageModal.removeAttr('style');
    }, 1000);
    messageModal.on('click', function (event) {
      if (event.target === this) {
        $(this).removeClass('masterstudy-message-modal_open');
        $('body').removeClass('masterstudy-message-modal-body-hidden');
      }
    });
    $('.masterstudy-instructor-public__actions .masterstudy-button, .masterstudy-student-public__actions .masterstudy-button').on('click', function (e) {
      e.preventDefault();
      if (message_modal_data.logged_in) {
        messageModal.addClass('masterstudy-message-modal_open');
        $('body').addClass('masterstudy-message-modal-body-hidden');
      }
    });
    $('.masterstudy-message-modal__close, [data-id="masterstudy-message-modal-close-button"]').click(function (e) {
      e.preventDefault();
      $(this).closest('.masterstudy-message-modal').removeClass('masterstudy-message-modal_open');
      $('body').removeClass('masterstudy-message-modal-body-hidden');
    });
    $('#masterstudy-message-modal-text').on('input', function () {
      $('.masterstudy-message-modal__error').removeClass('masterstudy-message-modal__error_show');
    });
    $('[data-id="masterstudy-message-modal-confirm"]').click(function (e) {
      e.preventDefault();
      $('.masterstudy-message-modal__error').removeClass('masterstudy-message-modal__error_show');
      var _this = $(this);
      var message = $('#masterstudy-message-modal-text').val();
      if (message.length > 0) {
        _this.addClass('masterstudy-button_loading');
        var data = {
          to: message_modal_data.user_id,
          message: message
        };
        var endpoint = stm_lms_ajaxurl + '?action=stm_lms_send_message&nonce=' + stm_lms_nonces['stm_lms_send_message'];
        fetch(endpoint, {
          method: 'POST',
          headers: {
            "Content-Type": "application/json; charset=UTF-8"
          },
          body: JSON.stringify(data)
        }).then(function (response) {
          if (!response.ok) {
            throw new Error('There was a problem with the fetch operation');
          }
          return response.json();
        }).then(function (data) {
          _this.removeClass('masterstudy-button_loading');
          if (data.status === 'success') {
            open_success_message();
          }
        })["catch"](function (error) {
          console.error('There was a problem with the fetch operation:', error);
        });
      } else {
        $('.masterstudy-message-modal__error').addClass('masterstudy-message-modal__error_show');
      }
    });
    function open_success_message() {
      $('.masterstudy-message-modal__form').addClass('masterstudy-message-modal__form_hide');
      $('.masterstudy-message-modal__user').addClass('masterstudy-message-modal__user-hide');
      $('.masterstudy-message-modal__actions').addClass('masterstudy-message-modal__actions_hide');
      $('.masterstudy-message-modal__success').addClass('masterstudy-message-modal__success_show');
    }
  });
})(jQuery);