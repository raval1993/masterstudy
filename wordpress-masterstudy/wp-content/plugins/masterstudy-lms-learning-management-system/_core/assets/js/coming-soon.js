"use strict";

(function ($) {
  $(document).ready(function () {
    var notifyAlertButton = $('.coming-soon-notify-alert');
    var notifyContainer = $('.coming-soon-notify-container');
    var notifyModalWrapper = $('.masterstudy-coming-soon-modal');
    var notifyMeBtn = $('.coming-soon-notify-container').find('.masterstudy-button');
    $('.masterstudy-coming-soon-modal').removeAttr('style');
    notifyAlertButton.on('click', function () {
      $(this).toggleClass('notify-me');
      if (!stm_coming_soon_ajax_variable.is_logged) {
        notifyContainer.css('display', notifyContainer.css('display') === 'none' ? 'flex' : 'none');
      }
      $.ajax({
        type: 'POST',
        url: stm_coming_soon_ajax_variable.url,
        data: {
          action: 'coming_soon_notify_me',
          email: '',
          nonce: stm_coming_soon_ajax_variable.nonce,
          id: stm_coming_soon_ajax_variable.course_id
        },
        success: function success(response) {
          if (response.success) {
            notifyAlertButton.addClass('added-email');
            notifyModalWrapper.addClass('masterstudy-coming-soon-modal_active');
            $('body').addClass('masterstudy-coming-soon-popup');
            $('.masterstudy-coming-soon-modal__title').text(response.title);
            $('.masterstudy-coming-soon-modal__description').text(response.description);
          }
        }
      });
    });
    $('.coming-soon-notify-container input').on('input', function () {
      $(this).removeClass('coming-soon-notify-input_alert');
    });
    $('.coming-soon-notify-container input').on('keypress', function (event) {
      if (event.which === 13) {
        notifyMeBtn.trigger('click');
      }
    });
    $('.masterstudy-coming-soon-modal').on('click', function (event) {
      if (event.target === this) {
        $(this).removeClass('masterstudy-coming-soon-modal_active');
        $('body').removeClass('masterstudy-coming-soon-popup');
        $('.coming-soon-notify-container').css('display', 'none');
      }
    });
    notifyMeBtn.click(function (e) {
      e.preventDefault();
      var email = $('.coming-soon-notify-container input').val();
      if (!isValidEmail(email)) {
        notifyContainer.addClass('validation-error');
        $('.coming-soon-notify-container input').addClass('coming-soon-notify-input_alert');
        return;
      }
      $('.coming-soon-notify-container input').removeClass('coming-soon-notify-input_alert');
      notifyContainer.removeClass('validation-error');
      $.ajax({
        type: 'POST',
        url: stm_coming_soon_ajax_variable.url,
        data: {
          action: 'coming_soon_notify_me',
          email: email,
          nonce: stm_coming_soon_ajax_variable.nonce,
          id: stm_coming_soon_ajax_variable.course_id
        },
        beforeSend: function beforeSend() {
          $(this).addClass('masterstudy-button_loading');
        },
        success: function success(response) {
          if (response.success) {
            notifyAlertButton.addClass('added-email');
            notifyModalWrapper.addClass('masterstudy-coming-soon-modal_active');
            $('.masterstudy-coming-soon-modal__title').text(response.title);
            $('.masterstudy-coming-soon-modal__description').text(response.description);
            $('body').addClass('masterstudy-coming-soon-popup');
            $(this).removeClass('masterstudy-button_loading');
          }
        }
      });
    });
    $('.masterstudy-coming-soon-modal').find('.masterstudy-coming-soon-modal__close, .masterstudy-button').click(function (event) {
      event.preventDefault();
      $('.masterstudy-coming-soon-modal').removeClass('masterstudy-coming-soon-modal_active');
      $('body').removeClass('masterstudy-coming-soon-popup');
      $('.coming-soon-notify-container').css('display', 'none');
    });
    function isValidEmail(email) {
      var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
      return emailPattern.test(email);
    }
    $('.stm-curriculum-item .stm-curriculum-item__preview').css('display', 'none');
  });
})(jQuery);