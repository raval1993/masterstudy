"use strict";

(function ($) {
  $(document).ready(function () {
    if (typeof MasterstudyAudioPlayer !== 'undefined') {
      MasterstudyAudioPlayer.init({
        selector: '.masterstudy-audio-player',
        showDeleteButton: false
      });
    }
    if ('file' === audio_data.audio_type) {
      var audioPlayer = document.querySelector('.masterstudy-audio-player audio');
      if (audioPlayer) {
        addTimecodeClickHandler(audioPlayer);
      }
    }
    if ('ext_link' === audio_data.audio_type) {
      var onTimeUpdate = function onTimeUpdate(currentTime, duration) {
        if (initialLoad && userProgress > 0) {
          return;
        }
        initialLoad = false;
        if (duration > 0) {
          var progress = Math.floor(currentTime / duration * 100);
          if (userProgress >= requiredProgress) {
            hint.hide();
            submitButton.removeAttr('disabled');
            submitButton.removeClass('masterstudy-button_disabled');
          }
          if (userProgress > progress) {
            return;
          }
          if (progress > 100) userProgress = 100;
          userProgress = progress;
          if (dataQuery) {
            var queryObject = JSON.parse(dataQuery);
            queryObject.progress = userProgress;
            submitButton.attr('data-query', JSON.stringify(queryObject));
          }
          if (currentProgressContainer) {
            currentUserProgressContainer.text("".concat(userProgress, "%"));
            currentProgressContainer.css('width', "".concat(userProgress, "%"));
            currentProgressContainer.attr('data-progress', userProgress);
          }
        }
      };
      var finalizeProgress = function finalizeProgress() {
        if (currentProgressContainer) {
          userProgress = 100;
          if (dataQuery) {
            var queryObject = JSON.parse(dataQuery);
            queryObject.progress = userProgress;
            submitButton.attr('data-query', JSON.stringify(queryObject));
            hint.hide();
            submitButton.removeAttr('disabled');
            submitButton.removeClass('masterstudy-button_disabled');
          }
          currentUserProgressContainer.text("".concat(userProgress, "%"));
          currentProgressContainer.css('width', "".concat(userProgress, "%"));
          currentProgressContainer.attr('data-progress', userProgress);
        }
      };
      var _audioPlayer = document.querySelector('.audio-external-links-type');
      var currentProgressContainer = $('#current-audio-progress');
      var currentUserProgressContainer = $('#current-audio-progress-user');
      var userProgress = parseInt($('#current-audio-progress').data('progress'), 10) || 0;
      var requiredProgress = parseInt($('#required-audio-progress').data('required-progress'), 10) || 0;
      var submitButton = $('[data-id="masterstudy-course-player-lesson-submit"]');
      var hint = $('.masterstudy-course-player-navigation__next .masterstudy-hint');
      var dataQuery = submitButton.attr('data-query');
      var initialLoad = true;
      if (userProgress < requiredProgress) {
        submitButton.attr('disabled', 1);
        submitButton.addClass('masterstudy-button_disabled');
      }
      if (_audioPlayer) {
        if (audio_data.audio_progress) {
          _audioPlayer.addEventListener('timeupdate', function () {
            var currentTime = _audioPlayer.currentTime || 0;
            var duration = _audioPlayer.duration || 0;
            onTimeUpdate(currentTime, duration);
          });
          _audioPlayer.addEventListener('ended', finalizeProgress);
        }
        addTimecodeClickHandler(_audioPlayer);
      }
    }
    function addTimecodeClickHandler(audioPlayer) {
      $('body').on('click', '.masterstudy-timecode', function () {
        var timecode = parseInt($(this).data('timecode'), 10);
        if (!isNaN(timecode)) {
          audioPlayer.currentTime = timecode;
          audioPlayer.play();
          var playPauseButton = $(audioPlayer).closest('.masterstudy-audio-player').find('.masterstudy-audio-player__play-pause-btn__icon');
          if (playPauseButton.length) {
            playPauseButton.attr('d', 'M0 0h6v24H0zM12 0h6v24h-6z');
          }
        }
      });
    }
  });
})(jQuery);