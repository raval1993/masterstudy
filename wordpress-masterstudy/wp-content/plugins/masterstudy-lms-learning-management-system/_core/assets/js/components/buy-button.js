"use strict";

(function ($) {
  $(document).ready(function () {
    /* Show Dropdown Payments */
    $('.masterstudy-buy-button').each(function () {
      var button = $(this);
      var animationActive = false;
      button.on('click', function () {
        toggleAnimation(button);
      });
      button.find('.masterstudy-buy-button-dropdown').on('click', function (event) {
        event.stopPropagation();
      });
      button.find('.masterstudy-buy-button__link_disabled').on('click', function (event) {
        event.preventDefault();
      });
      button.find('.masterstudy-buy-button-dropdown__head').on('click', function () {
        $(this).parent().siblings().removeClass('masterstudy-buy-button-dropdown__section_open');
        $(this).parent().toggleClass('masterstudy-buy-button-dropdown__section_open');
      });
      function toggleAnimation(btn) {
        animationActive = !animationActive;
        $('.masterstudy-buy-button').not(btn).removeClass('dropdown-show');
        btn.toggleClass('dropdown-show', animationActive);
      }
      $(document).on('click', function (event) {
        if (!$(event.target).closest(button).length && animationActive) {
          toggleAnimation(button);
        }
      });
    });
    /* End Show Dropdown Payments */

    /* Link for LMS checkout */
    function handleButtonClick(event, attribute, ajaxAction, nonce) {
      event.preventDefault();
      var button = $(this);
      var item_id = button.data(attribute);
      if (typeof item_id === 'undefined') {
        window.location = button.attr('href');
        return false;
      }
      $.ajax({
        url: masterstudy_buy_button_data.ajax_url,
        dataType: 'json',
        context: button,
        data: {
          action: ajaxAction,
          nonce: nonce,
          item_id: item_id
        },
        beforeSend: function beforeSend() {
          button.addClass('masterstudy-purchase-button__loading');
          var titleButton = button.find('.masterstudy-buy-button__title');
          if (titleButton.length) {
            titleButton.addClass('masterstudy-buy-button__loading');
          }
        },
        complete: function complete(data) {
          var responseJSON = data['responseJSON'];
          button.removeClass('masterstudy-purchase-button__loading');
          var titleEl = button.find('.masterstudy-purchase-button__title');
          var titleButton = button.find('.masterstudy-buy-button__title');
          if (titleButton.length) {
            titleButton.removeClass('masterstudy-buy-button__loading');
          }
          if (titleEl.length) {
            titleEl.text(responseJSON['text']);
          } else {
            var altTitleEl = button.find('.masterstudy-buy-button__title');
            if (altTitleEl.length) {
              altTitleEl.text(responseJSON['text']);
            }
          }
          if (responseJSON['cart_url']) {
            if (responseJSON['redirect']) window.location = responseJSON['cart_url'];
            button.attr('href', responseJSON['cart_url']);
            button.removeAttr('data-' + attribute);
            button.removeData(attribute);
          }
        }
      });
    }
    $('[data-purchased-course]').on('click', function (event) {
      handleButtonClick.call(this, event, 'purchased-course', 'stm_lms_add_to_cart', masterstudy_buy_button_data.get_nonce);
    });
    $('[data-guest]').on('click', function (event) {
      handleButtonClick.call(this, event, 'guest', 'stm_lms_add_to_cart_guest', masterstudy_buy_button_data.get_guest_nonce);
      var button = $(this);
      var item_id = button.data('guest');
      var currentCart = getCookie('stm_lms_notauth_cart');
      currentCart = currentCart ? JSON.parse(decodeURIComponent(currentCart)) : [];
      var item_id_str = item_id.toString();
      currentCart = currentCart.map(String);
      if (!currentCart.includes(item_id_str)) {
        currentCart.push(item_id_str);
      }
      setCookie('stm_lms_notauth_cart', JSON.stringify(currentCart).replace(/"/g, ''), {
        path: '/'
      });
    });

    // Get cookies
    function getCookie(name) {
      var value = "; ".concat(document.cookie);
      var parts = value.split("; ".concat(name, "="));
      if (parts.length === 2) return parts.pop().split(';').shift();
    }

    // Install cookies
    function setCookie(name, value) {
      var options = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
      document.cookie = "".concat(name, "=").concat(encodeURIComponent(value), "; path=").concat(options.path);
    }
    /* End Link for LMS checkout */
  });
})(jQuery);