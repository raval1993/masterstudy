"use strict";

(function ($) {
  $(document).ready(function () {
    var completeBlock = $('.masterstudy-single-course-complete');
    var ratingForm = $('.masterstudy-single-course-complete__review-form');
    completeBlock.removeAttr('style');
    var currentRating = 0;
    if (course_completed.completed) {
      $('body').addClass('masterstudy-single-course-complete_hidden');
      completeBlock.addClass('masterstudy-single-course-complete_active');
      stmLmsInitProgress(completeBlock);
    }
    if (course_completed.block_enabled && course_completed.user_id) {
      stmLmsInitProgress(completeBlock);
    }
    $('.masterstudy-single-course-complete-block__details').on('click', function () {
      $('body').addClass('masterstudy-single-course-complete_hidden');
      var completeBlock = $(this).parent().next('.masterstudy-single-course-complete');
      if (completeBlock.length) {
        $('body').addClass('masterstudy-single-course-complete_hidden');
        completeBlock.addClass('masterstudy-single-course-complete_active');
        stmLmsInitProgress(completeBlock);
      }
    });
    $('.masterstudy-single-course-complete').on('click', function (event) {
      if ($(event.target).hasClass('masterstudy-single-course-complete')) {
        $('.masterstudy-single-course-complete').removeClass('masterstudy-single-course-complete_active');
        $('body').removeClass('masterstudy-single-course-complete_hidden');
        setTimeout(function () {
          $('.masterstudy-single-course-complete__wrapper').css('display', 'flex');
          $('.masterstudy-single-course-complete__review-form').toggle(false);
        }, 400);
      }
    });
    $('.masterstudy-single-course-complete__buttons, .masterstudy-single-course-complete__close').on('click', function (event) {
      $('.masterstudy-single-course-complete').removeClass('masterstudy-single-course-complete_active');
      $('body').removeClass('masterstudy-single-course-complete_hidden');
      setTimeout(function () {
        $('.masterstudy-single-course-complete__wrapper').css('display', 'flex');
        $('.masterstudy-single-course-complete__review-form').toggle(false);
      }, 400);
    });
    $('.masterstudy-single-course-complete__review-btn').on('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      $('.masterstudy-single-course-complete__wrapper').toggle(false);
      $('.masterstudy-single-course-complete__review-form').css('display', 'flex');
    });
    ratingForm.on('mouseenter', '.masterstudy-single-course-complete__review-rating .stmlms-star', function (e) {
      $(this).prevAll('.stmlms-star').addClass('masterstudy-single-course-complete__review-star-filled');
      $(this).nextAll('.stmlms-star').removeClass('masterstudy-single-course-complete__review-star-filled');
      $(this).addClass('masterstudy-single-course-complete__review-star-filled');
    });
    ratingForm.on('mouseout', '.masterstudy-single-course-complete__review-rating .stmlms-star', function (e) {
      e.stopPropagation();
    });
    ratingForm.on('click', '.masterstudy-single-course-complete__review-rating .stmlms-star', function (e) {
      currentRating = $(this).index() + 1;
      $('.masterstudy-single-course-complete__review-rating').removeClass('masterstudy-single-course-complete__review-rating_error');
    });
    ratingForm.on('mouseout', '.masterstudy-single-course-complete__review-rating', function () {
      $(this).children().each(function (i, elem) {
        if (currentRating < i + 1) {
          $(elem).removeClass('masterstudy-single-course-complete__review-star-filled');
        } else {
          $(elem).addClass('masterstudy-single-course-complete__review-star-filled');
        }
      });
    });
    ratingForm.on('click', "[data-id='masterstudy-single-course-complete__review-submit']", function () {
      if (!currentRating) {
        $('.masterstudy-single-course-complete__review-rating').addClass('masterstudy-single-course-complete__review-rating_error');
      } else {
        $('.masterstudy-single-course-complete__review-rating').removeClass('masterstudy-single-course-complete__review-rating_error');
      }
      addReview(currentRating, course_completed.course_id);
    });
    ratingForm.on('click', "[data-id='masterstudy-single-course-complete__review-back']", function () {
      $('.masterstudy-single-course-complete__wrapper').css('display', 'flex');
      $('.masterstudy-single-course-complete__review-form').toggle(false);
    });
    $('.masterstudy-single-course-complete__review-error-container').on('click', '.stmlms-close', function () {
      $('.masterstudy-single-course-complete__review-error-msg').html("");
      $('.masterstudy-single-course-complete__review-error-container').removeClass('masterstudy-single-course-complete__review-error-container_active');
    });
    $('.masterstudy-single-course-complete__review-success-container').on('click', '.stmlms-close', function () {
      $('.masterstudy-single-course-complete__review-success-msg').html('');
      $('.masterstudy-single-course-complete__review-success-container').removeClass('masterstudy-single-course-complete__review-success-container_active');
    });
  });
  function stmLmsInitProgress(statsContainer) {
    var course_id = course_completed.elementor_widget ? statsContainer.data('course-id') : course_completed.course_id;
    var loading = true;
    var stats = {};
    var ajaxUrl = course_completed.ajax_url + '?action=stm_lms_total_progress&course_id=' + course_id + '&nonce=' + course_completed.nonce;
    $.get(ajaxUrl, function (response) {
      stats = response;
      loading = false;
      course_completed_success(statsContainer, stats);
    });
    function course_completed_success(statsContainer, stats) {
      statsContainer.find('.masterstudy-single-course-complete__loading').hide();
      statsContainer.find('.masterstudy-single-course-complete__success').show();
      if ($('body').hasClass('rtl')) {
        statsContainer.find('.masterstudy-single-course-complete__opportunities-percent').html('%' + stats.course.progress_percent);
      } else {
        statsContainer.find('.masterstudy-single-course-complete__opportunities-percent').html(stats.course.progress_percent + '%');
      }
      if (stats.title) {
        statsContainer.find('h2').show().html(stats.title);
      }
      ['lesson', 'multimedia', 'quiz', 'assignment'].forEach(function (type) {
        if (stats.curriculum.hasOwnProperty(type)) {
          statsContainer.find(".masterstudy-single-course-complete__curiculum-statistic-item_type-".concat(type)).addClass('show-item');
          statsContainer.find(".masterstudy-single-course-complete__curiculum-statistic-item_type-".concat(type, " .masterstudy-single-course-complete__curiculum-statistic-item_completed")).html(stats.curriculum[type].completed);
          statsContainer.find(".masterstudy-single-course-complete__curiculum-statistic-item_type-".concat(type, " .masterstudy-single-course-complete__curiculum-statistic-item_total")).html(stats.curriculum[type].total);
        }
      });
      statsContainer.find('.masterstudy-button_course_button').attr('href', stats.url);
    }
  }
  function addReview(userMark, courseId) {
    var addReviewsUrl = stm_lms_ajaxurl + '?action=stm_lms_add_review&nonce=' + stm_lms_nonces['stm_lms_add_review'];
    var submitBtn = $("[data-id='masterstudy-single-course-complete__review-submit']");
    var currentEditor = tinyMCE.get('editor_add_review_complete_popup') || null;
    var reviewText;
    if (currentEditor) {
      reviewText = currentEditor.getContent();
    }
    if (!reviewText) {
      $('#editor_add_review_complete_popup_ifr').addClass('masterstudy-single-course-complete__review-editor_error');
      return;
    } else {
      $('#editor_add_review_complete_popup_ifr').removeClass('masterstudy-single-course-complete__review-editor_error');
    }
    submitBtn.addClass('masterstudy-button_loading');
    $.post(addReviewsUrl, {
      post_id: courseId,
      mark: userMark,
      review: reviewText
    }, function (response) {
      if (response.status === 'success') {
        $('.masterstudy-single-course-complete__review-success-msg').html(response.message);
        $('.masterstudy-single-course-complete__review-success-container').addClass('masterstudy-single-course-complete__review-success-container_active');
        $('.masterstudy-single-course-complete__wrapper').css('display', 'flex');
        $('.masterstudy-single-course-complete__review-form').toggle(false);
        $('.masterstudy-single-course-complete__review-btn').remove();
      } else {
        $('.masterstudy-single-course-complete__review-error-msg').html(response.message);
        $('.masterstudy-single-course-complete__review-error-container').addClass('masterstudy-single-course-complete__review-error-container_active');
      }
      submitBtn.removeClass('masterstudy-button_loading');
    });
  }
})(jQuery);