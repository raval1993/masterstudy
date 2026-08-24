"use strict";

(function ($) {
  $(document).ready(function () {
    //expired component
    if (typeof expired_data !== 'undefined' && expired_data.load_scripts) {
      var cookie_name = "stm_lms_expired_course_".concat(expired_data.id);
      var cookie = $.cookie(cookie_name);
      $('.masterstudy-single-course-expired-popup').removeAttr('style');
      setTimeout(function () {
        if (cookie !== 'closed') {
          $('body').addClass('masterstudy-expired-popup');
          $('.masterstudy-single-course-expired-popup').addClass('masterstudy-single-course-expired-popup_active');
        }
      }, 200);
      $('.masterstudy-single-course-expired-popup').find('.masterstudy-button').on('click', function () {
        $('body').removeClass('masterstudy-expired-popup');
        $('.masterstudy-single-course-expired-popup').removeClass('masterstudy-single-course-expired-popup_active');
        var date = new Date();
        $.cookie(cookie_name, 'closed', {
          path: '/',
          expires: date.getTime() + 24 * 60 * 60 * 1000
        });
      });
      $('.masterstudy-single-course-expired-popup').on('click', function (event) {
        if (event.target === this) {
          $(this).removeClass('masterstudy-single-course-expired-popup_active');
          $('body').removeClass('masterstudy-expired-popup');
          var date = new Date();
          $.cookie(cookie_name, 'closed', {
            path: '/',
            expires: date.getTime() + 24 * 60 * 60 * 1000
          });
        }
      });
    }

    // Reviews component
    $('.masterstudy-single-course-reviews').each(function (index) {
      var offset = 0;
      var total = true;
      var reviewText = '';
      var userMark = '';
      var pp = reviews_data.style === 'grid' ? 6 : 5;
      var editors = {};
      var reviewContainer = $(this);
      var courseId = reviewContainer.data('course-id');
      var loadMoreButton = reviewContainer.find("[data-id='masterstudy-single-course-reviews-more']");
      var reviewsList = reviewContainer.find('.masterstudy-single-course-reviews__list-wrapper');
      var showAddReviewButton = reviewContainer.find('.masterstudy-single-course-reviews__add-button');
      var closeReviewButton = reviewContainer.find('.masterstudy-single-course-reviews__form-close');
      var submitReviewButton = reviewContainer.find("[data-id='masterstudy-single-course-reviews-submit']");
      var errorMessageBlock = reviewContainer.find('.masterstudy-single-course-reviews__form-message');
      var reviewFormStars = reviewContainer.find('.masterstudy-single-course-reviews__form-rating .masterstudy-single-course-reviews__star');
      if (typeof reviews_data !== 'undefined') {
        getReviews();
        if (typeof tinyMCE !== 'undefined') {
          getEditor(index);
        }
        loadMoreButton.click(function (event) {
          event.preventDefault();
          getReviews();
        });
        showAddReviewButton.click(function (event) {
          event.preventDefault();
          reviewContainer.find('.masterstudy-single-course-reviews__form').addClass('masterstudy-single-course-reviews__form_active');
        });
        closeReviewButton.click(function (event) {
          event.preventDefault();
          errorMessageBlock.removeClass('masterstudy-single-course-reviews__form-message_active').html('');
          reviewContainer.find('.masterstudy-single-course-reviews__form').removeClass('masterstudy-single-course-reviews__form_active');
          reviewFormStars.removeClass('masterstudy-single-course-reviews__star_clicked');
          if (editor) {
            editor.setContent('');
            $('.masterstudy-wp-editor__word-count').html('');
          }
        });
        submitReviewButton.click(function (event) {
          event.preventDefault();
          addReview(submitReviewButton, index);
        });
        reviewFormStars.click(function () {
          $(this).addClass('masterstudy-single-course-reviews__star_clicked');
          $(this).siblings().removeClass('masterstudy-single-course-reviews__star_clicked');
          $(this).prevAll().addBack().addClass('masterstudy-single-course-reviews__star_clicked');
          userMark = reviewContainer.find('.masterstudy-single-course-reviews__star_clicked').length;
        });
      }
      function getReviews() {
        var getReviewsUrl = "".concat(stm_lms_ajaxurl, "?action=stm_lms_get_reviews&nonce=").concat(stm_lms_nonces['stm_lms_get_reviews'], "&offset=").concat(offset, "&post_id=").concat(courseId, "&pp=").concat(pp);
        var reviewHtml = '';
        loadMoreButton.addClass('masterstudy-button_loading');
        $.get(getReviewsUrl, function (response) {
          if (response.posts.length > 0) {
            response.posts.forEach(function (review) {
              reviewHtml += generateReviewHtml(review);
            });
            reviewsList.html(reviewsList.html() + reviewHtml);
            offset++;
            total = response.total;
          }
          loadMoreButton.removeClass('masterstudy-button_loading');
          total ? loadMoreButton.parent().hide() : loadMoreButton.parent().show();
        });
      }
      function getEditor(index) {
        var editorId = reviews_data.editor_id;
        function initializeEditor() {
          editors[index] = tinyMCE.get(editorId);
          if (editors[index] && editors[index].initialized) {
            editors[index].theme.resizeTo(null, 200);
          } else {
            setTimeout(initializeEditor, 500);
          }
        }
        initializeEditor();
      }
      function addReview(buttonContainer, index) {
        var addReviewsUrl = stm_lms_ajaxurl + '?action=stm_lms_add_review&nonce=' + stm_lms_nonces['stm_lms_add_review'];
        var currentEditor = editors[index] || null;
        if (currentEditor) {
          reviewText = currentEditor.getContent();
        }
        buttonContainer.addClass('masterstudy-button_loading');
        $.post(addReviewsUrl, {
          post_id: courseId,
          mark: userMark,
          review: reviewText
        }, function (response) {
          if (response.status === 'success') {
            errorMessageBlock.html(response.message);
            errorMessageBlock.addClass('masterstudy-single-course-reviews__form-message_success masterstudy-single-course-reviews__form-message_active');
            setTimeout(function () {
              reviewContainer.find('.masterstudy-single-course-reviews__form').removeClass('masterstudy-single-course-reviews__form_active');
              if (currentEditor) {
                currentEditor.setContent('');
                $('.masterstudy-wp-editor__word-count').html('');
              }
            }, 1500);
          } else {
            errorMessageBlock.html(response.message);
            errorMessageBlock.addClass('masterstudy-single-course-reviews__form-message_active');
          }
          buttonContainer.removeClass('masterstudy-button_loading');
        });
      }
      function generateReviewHtml(review) {
        var starsHtml = '';
        for (var i = 1; i <= 5; i++) {
          starsHtml += "<span class=\"masterstudy-single-course-reviews__star".concat(i <= review.mark ? ' masterstudy-single-course-reviews__star_filled' : '', "\"></span>");
        }
        return "\n                    <div class=\"masterstudy-single-course-reviews__item\">\n                        <div class=\"masterstudy-single-course-reviews__item-header\">\n                            <div class=\"masterstudy-single-course-reviews__item-mark\">".concat(starsHtml, "</div>\n                            ").concat(review.status === 'pending' ? "<div class=\"masterstudy-single-course-reviews__item-status\">".concat(reviews_data.status, "</div>") : '', "\n                        </div>\n                        <div class=\"masterstudy-single-course-reviews__item-content\">").concat(review.content, "</div>\n                        <div class=\"masterstudy-single-course-reviews__item-row\">\n                            <a class=\"masterstudy-single-course-reviews__item-user\" ").concat(reviews_data.student_public_profile ? "href=\"".concat(review.user_url, "\"") : '', ">\n                                <span class=\"masterstudy-single-course-reviews__item-author\">").concat(reviews_data.author_label, "</span>\n                                <span class=\"masterstudy-single-course-reviews__item-author-name\">").concat(review.user, "</span>\n                            </a>\n                            <div class=\"masterstudy-single-course-reviews__item-date\">").concat(review.time, "</div>\n                        </div>\n                    </div>");
      }
    });

    //curriculum component
    $('.masterstudy-curriculum-list__toggler').click(function (event) {
      event.preventDefault();
      toggleContainer.call(this, true);
    });
    $('.masterstudy-curriculum-list__excerpt-toggler').click(function (event) {
      event.preventDefault();
      toggleContainer.call(this, false);
    });
    $('.masterstudy-curriculum-list__link_disabled').click(function (event) {
      event.preventDefault();
    });
    $('.masterstudy-hint').hover(function () {
      $(this).closest('.masterstudy-curriculum-list__materials').css('overflow', 'visible');
    }, function () {
      $(this).closest('.masterstudy-curriculum-list__materials').css('overflow', 'hidden');
    });

    //faq component
    $('.masterstudy-single-course-faq__item').click(function () {
      var content = $(this).find('.masterstudy-single-course-faq__answer'),
        isOpened = content.is(':visible'),
        openedClass = 'masterstudy-single-course-faq__container-wrapper_opened';
      if (isOpened) {
        content.animate({
          height: 0
        }, 100, function () {
          setTimeout(function () {
            content.css('display', 'none');
            content.css('height', '');
          }, 300);
        });
        $(this).find('.masterstudy-single-course-faq__container-wrapper').removeClass(openedClass);
      } else {
        content.css('display', 'block');
        var autoHeight = content.height('auto').height();
        content.height(0).animate({
          height: autoHeight
        }, 100, function () {
          setTimeout(function () {
            content.css('height', '');
          }, 300);
        });
        $(this).find('.masterstudy-single-course-faq__container-wrapper').addClass(openedClass);
      }
    });

    //materials component
    if (isSafari()) {
      $('.masterstudy-single-course-materials__link').remove();
    }
    $('.masterstudy-single-course-materials__link').click(handleDownloadClick);
    if (typeof MasterstudyAudioPlayer !== 'undefined') {
      MasterstudyAudioPlayer.init({
        selector: '.masterstudy-audio-player',
        showDeleteButton: false
      });
    }
    function handleDownloadClick() {
      $('.masterstudy-single-course-materials').find('.masterstudy-file-attachment__link').each(function () {
        var clickEvent = new MouseEvent('click', {
          bubbles: true,
          cancelable: true,
          view: window
        });
        this.dispatchEvent(clickEvent);
      });
    }

    //tabs component
    $('.masterstudy-single-course-tabs__item').click(function () {
      var targetId = $(this).data('id');
      $(this).siblings().removeClass('masterstudy-single-course-tabs__item_active');
      $(this).addClass('masterstudy-single-course-tabs__item_active');
      $(this).parent().next().find('.masterstudy-single-course-tabs__container').removeClass('masterstudy-single-course-tabs__container_active').filter(function () {
        return $(this).data('id') === targetId;
      }).addClass('masterstudy-single-course-tabs__container_active');
    });
    if ($('.masterstudy-single-course-tabs_style-sidebar').length && !window.matchMedia('(max-width: 1023.98px)').matches) {
      var allowScrollUpdate = true;
      $('.masterstudy-single-course-tabs_style-sidebar').find('.masterstudy-single-course-tabs__item').click(function () {
        $('html, body').stop();
        allowScrollUpdate = false;
        var targetId = $(this).data('id');
        $(this).siblings().removeClass('masterstudy-single-course-tabs__item_active');
        $(this).addClass('masterstudy-single-course-tabs__item_active');
        var activeContainer = $('body').find('.masterstudy-single-course-tabs__container').removeClass('masterstudy-single-course-tabs__container_active').filter(function () {
          return $(this).data('id') === targetId;
        }).addClass('masterstudy-single-course-tabs__container_active');
        $('html, body').animate({
          scrollTop: $(activeContainer).offset().top - 70
        }, 1000, function () {
          allowScrollUpdate = true;
        });
      });
      $(window).on('scroll', function () {
        if (!allowScrollUpdate) {
          return;
        }
        var scrollPosition = $(window).scrollTop();
        var isInViewport = false;
        $('.masterstudy-single-course-tabs__container').each(function () {
          var container = $(this);
          var containerTop = container.offset().top;
          var containerBottom = containerTop + container.height();
          if (scrollPosition >= containerTop - 70 && scrollPosition < containerBottom - 70) {
            var targetId = container.data('id');
            isInViewport = true;
            $('.masterstudy-single-course-tabs__item').each(function () {
              var tab = $(this);
              if (tab.data('id') === targetId) {
                tab.siblings().removeClass('masterstudy-single-course-tabs__item_active');
                tab.addClass('masterstudy-single-course-tabs__item_active');
              }
            });
          }
        });
        if (!isInViewport) {
          $('.masterstudy-single-course-tabs__item').removeClass('masterstudy-single-course-tabs__item_active');
        }
      });
    }
    if ($('.masterstudy-single-course-tabs_style-sidebar').length && window.matchMedia('(max-width: 1023.98px)').matches) {
      $('.masterstudy-single-course-tabs').removeClass('masterstudy-single-course-tabs_style-sidebar');
      $('.masterstudy-single-course-tabs__content').removeClass('masterstudy-single-course-tabs_style-sidebar');
      $('.masterstudy-single-course-tabs').children().first().addClass('masterstudy-single-course-tabs__item_active');
      $('.masterstudy-single-course-tabs__content').children().first().addClass('masterstudy-single-course-tabs__container_active');
    }

    //wishlist component
    $('body').on('click', '.masterstudy-single-course-wishlist', function () {
      var post_id = $(this).attr('data-id');
      if ($('body').hasClass('logged-in')) {
        $.ajax({
          url: stm_lms_ajaxurl,
          dataType: 'json',
          context: this,
          data: {
            action: 'stm_lms_wishlist',
            nonce: stm_lms_nonces['stm_lms_wishlist'],
            post_id: post_id
          },
          complete: function complete(response) {
            var data = response['responseJSON'];
            $(this).find('.masterstudy-single-course-wishlist__title').toggleClass('masterstudy-single-course-wishlist_added');
            if (!wishlist_data.without_title) {
              $(this).find('.masterstudy-single-course-wishlist__title').text(data.text);
            }
          }
        });
      }
    });
  });

  //curriculum component
  function toggleContainer(main) {
    var content = main ? $(this).parent().next() : $(this).parent().parent().next(),
      isOpened = content.is(':visible'),
      openedClass = main ? 'masterstudy-curriculum-list__wrapper_opened' : 'masterstudy-curriculum-list__container-wrapper_opened';
    if (isOpened) {
      content.animate({
        height: 0
      }, 100, function () {
        setTimeout(function () {
          content.css('display', 'none');
          content.css('height', '');
        }, 300);
      });
      $(this).parent().parent().removeClass(openedClass);
    } else {
      content.css('display', 'block');
      var autoHeight = content.height('auto').height();
      content.height(0).animate({
        height: autoHeight
      }, 100, function () {
        setTimeout(function () {
          content.css('height', '');
        }, 300);
      });
      $(this).parent().parent().addClass(openedClass);
    }
  }

  //materials component
  function isSafari() {
    return /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
  }

  //excerpt component
  $('.masterstudy-single-course-excerpt__more').click(function () {
    $(this).siblings('.masterstudy-single-course-excerpt__hidden').toggle();
    $(this).siblings('.masterstudy-single-course-excerpt__continue').toggle();
    $(this).text($(this).text().trim() === excerpt_data.more_title ? excerpt_data.less_title : excerpt_data.more_title);
  });

  //share button component
  setTimeout(function () {
    $('.masterstudy-single-course-share-button-modal').removeAttr('style');
  }, 1000);
  $('.masterstudy-single-course-share-button__title').click(function () {
    $(this).parent().next().addClass('masterstudy-single-course-share-button-modal_open');
    $('body').addClass('masterstudy-single-course-share-button-body-hidden');
  });
  $('.masterstudy-single-course-share-button-modal').click(function (event) {
    if (event.target === this) {
      $('.masterstudy-single-course-share-button-modal').removeClass('masterstudy-single-course-share-button-modal_open');
      $('body').removeClass('masterstudy-single-course-share-button-body-hidden');
    }
  });
  $('.masterstudy-single-course-share-button-modal__close').on('click', function () {
    $('.masterstudy-single-course-share-button-modal').removeClass('masterstudy-single-course-share-button-modal_open');
    $('body').removeClass('masterstudy-single-course-share-button-body-hidden');
  });
  $('.masterstudy-single-course-share-button-modal__link_copy').click(function (event) {
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
  $('.masterstudy-single-course-share-button__link_copy').click(function (event) {
    event.preventDefault();
    var tempInput = document.createElement("input");
    tempInput.style.position = "absolute";
    tempInput.style.left = "-9999px";
    tempInput.value = $(this).data('url');
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand("copy");
    document.body.removeChild(tempInput);
  });
})(jQuery);