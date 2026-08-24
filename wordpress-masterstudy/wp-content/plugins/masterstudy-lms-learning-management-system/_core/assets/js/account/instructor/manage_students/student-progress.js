"use strict";

(function ($) {
  $(document).ready(function () {
    var _spd$course_id, _spd$student_id;
    var progressContainer = $('.masterstudy-student-progress');
    var spd = window.student_progress_data || {};
    var urlParams = new URLSearchParams(window.location.search);
    var course_id = (_spd$course_id = spd.course_id) !== null && _spd$course_id !== void 0 ? _spd$course_id : urlParams.get('course_id');
    var student_id = (_spd$student_id = spd.student_id) !== null && _spd$student_id !== void 0 ? _spd$student_id : urlParams.get('student_id');
    $.each($('.masterstudy-student-progress-list__meta-checkbox'), function (i, input) {
      if ('assignment' === $(input).attr('data-type') && $(input).prop('checked')) {
        $(input).attr('disabled', true);
      }
      $(input).on('change', function (event) {
        event.preventDefault();
        var queryString = new URLSearchParams({
          completed: $(input).is(':checked'),
          item_id: $(input).attr('data-item-id')
        }).toString();
        var apiUrl = "".concat(ms_lms_resturl, "/student/progress/").concat(course_id, "/").concat(student_id, "/?").concat(queryString);
        if ('assignment' === $(input).attr('data-type')) {
          $(input).attr('disabled', true);
        }
        if ('quiz' === $(input).attr('data-type')) {
          $.each($('.masterstudy-student-progress__quiz'), function (i, item) {
            if (0 < i) {
              $(item).css({
                display: 'none'
              });
            }
          });
        }
        fetch(apiUrl, {
          method: 'PUT',
          headers: {
            'X-WP-Nonce': ms_lms_nonce,
            'Content-Type': 'application/json'
          }
        }).then(function (response) {
          if (response.ok) {
            return response.json();
          }
        }).then(function (response) {
          resetQuiz(input);
          setProgressPercent(response.progress_percent);
          if ('assignment' === $(input).attr('data-type') || 'quiz' === $(input).attr('data-type') && $(input).prop('checked')) {
            window.location.reload();
          }
        })["catch"](function (error) {
          throw error;
        });
      });
    });
    $('.masterstudy-student-progress-list__toggler').click(function (event) {
      event.preventDefault();
      toggleContainer.call(this, true);
    });
    $('.masterstudy-student-progress-list__content-toggler').click(function (event) {
      event.preventDefault();
      toggleContainer.call(this, false);
    });
    $('.masterstudy-student-progress-list__link_disabled').click(function (event) {
      event.preventDefault();
    });
    $('.masterstudy-hint').hover(function () {
      $(this).closest('.masterstudy-student-progress-list__materials').css('overflow', 'visible');
    }, function () {
      $(this).closest('.masterstudy-student-progress-list__materials').css('overflow', 'hidden');
    });
    function resetQuiz(input) {
      var item = $(input).parents('.masterstudy-student-progress-list__item');
      if ($(input).prop('checked')) {
        var result = item.find('.masterstudy-student-progress-list__item-content_result');
        item.find('.masterstudy-student-progress-list__item-content').addClass('masterstudy-student-progress-list__item-content_completed');
        item.find('.masterstudy-student-progress-list__item-no-answer').removeClass('masterstudy-student-progress-list__item_hidden');
        item.find('.masterstudy-student-progress-list__item-quiz').addClass('masterstudy-student-progress-list__item_hidden');
        result.removeClass('masterstudy-student-progress-list__item_hidden');
        result.find('.masterstudy-course-player-quiz__result-progress').text('100%');
        result.find('.masterstudy-course-player-quiz__result-answers strong:first').text('0');
      } else {
        item.find('.masterstudy-student-progress-list__item-content').removeClass('masterstudy-student-progress-list__item-content_completed');
      }
    }
    function resetProgress(input) {
      var item = $(input).parents('.masterstudy-student-progress-list__item');
      $(input).prop('checked', false);
      $(input).attr('disabled', false);
      item.find('.masterstudy-student-progress-list__item-content').removeClass('masterstudy-student-progress-list__item-content_completed');
    }
    function setProgressPercent(percent) {
      percent = percent ? percent : 0;
      progressContainer.find('.masterstudy-progress__bar-filled').css({
        width: "".concat(percent, "%")
      });
      progressContainer.find('.masterstudy-progress__percent').text(percent);
    }
    function toggleContainer(main) {
      var content = main ? $(this).parent().next() : $(this).parent().parent().next(),
        isOpened = content.is(':visible'),
        openedClass = main ? 'masterstudy-student-progress-list__wrapper_opened' : 'masterstudy-student-progress-list__container-wrapper_opened';
      if (isOpened) {
        content.slideUp(300);
        $(this).parent().parent().removeClass(openedClass);
      } else {
        content.slideDown(300);
        $(this).parent().parent().addClass(openedClass);
      }
    }
    var alertPopup = $("[data-id='masterstudy-manage-students-reset-progress']");
    alertPopup.css('display', 'none');
    $('body').on('click', '[data-id="masterstudy-progress-reset"]', function (e) {
      e.preventDefault();
      alertPopup.css('display', 'flex');
      alertPopup.addClass('masterstudy-alert_open');
    });
    alertPopup.find("[data-id='submit']").click(function (e) {
      e.preventDefault();
      fetch("".concat(ms_lms_resturl, "/student/progress/").concat(course_id, "/").concat(student_id), {
        method: 'DELETE',
        headers: {
          'X-WP-Nonce': ms_lms_nonce,
          'Content-Type': 'application/json'
        }
      }).then(function (response) {
        if (response.ok) {
          return response.json();
        }
      }).then(function (response) {
        if (response.progress_percent) {
          $.each($('.masterstudy-student-progress-list__meta-checkbox'), function (i, input) {
            resetProgress(input);
          });
          setProgressPercent(response.progress_percent);
          alertPopup.removeClass('masterstudy-alert_open');
        }
      })["catch"](function (error) {
        alertPopup.removeClass('masterstudy-alert_open');
        throw error;
      });
    });
    alertPopup.find("[data-id='cancel']").click(closeAlertPopup);
    alertPopup.find('.masterstudy-alert__header-close').click(closeAlertPopup);
    function closeAlertPopup(e) {
      e.preventDefault();
      alertPopup.removeClass('masterstudy-alert_open');
    }
  });
})(jQuery);