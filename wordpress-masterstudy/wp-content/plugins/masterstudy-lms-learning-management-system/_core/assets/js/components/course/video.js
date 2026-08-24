"use strict";

(function ($) {
  $(document).ready(function () {
    $('.masterstudy-single-course-video__wrapper_lazy').each(function () {
      var wrapper = $(this);
      var videoType = wrapper.data('video-type');
      var videoId = wrapper.data('video-id');
      var poster = wrapper.data('poster');
      var extraRaw = wrapper.data('extra');
      var extra = {};
      function isIOS() {
        var ua = window.navigator.userAgent || '';
        var platform = window.navigator.platform || '';
        var iosPlatforms = ['iPhone', 'iPad', 'iPod'];
        var isModernIpad = platform === 'MacIntel' && navigator.maxTouchPoints > 1;
        var isLegacyIOS = /iPad|iPhone|iPod/.test(ua);
        return iosPlatforms.includes(platform) || isModernIpad || isLegacyIOS;
      }
      var isMuted = isIOS();
      try {
        extra = extraRaw ? JSON.parse(extraRaw) : {};
      } catch (e) {}
      if (videoType === 'embed') {
        wrapper.addClass('masterstudy-single-course-video__wrapper_embed').empty().html(atob(videoId));
        return;
      }
      wrapper.on('click', function () {
        if (wrapper.hasClass('masterstudy-single-course-video-loaded')) return;
        wrapper.addClass('masterstudy-single-course-video-loaded');
        wrapper.find('.masterstudy-single-course-video__loader').addClass('masterstudy-single-course-video__loader_show');
        var videoElement;
        if (videoType === 'youtube') {
          videoElement = $('<div class="plyr__video-embed"></div>').append($('<iframe>', {
            src: "https://www.youtube.com/embed/".concat(videoId, "?autoplay=1&iv_load_policy=3&modestbranding=1&playsinline=1&showinfo=0&rel=0&enablejsapi=1&mute=").concat(isMuted),
            allow: 'autoplay; fullscreen',
            allowfullscreen: true,
            frameborder: 0
          }));
        } else if (videoType === 'vimeo') {
          videoElement = $('<div class="plyr__video-embed"></div>').append($('<iframe>', {
            src: "https://player.vimeo.com/video/".concat(videoId, "?autoplay=1&loop=false&byline=false&portrait=false&title=false"),
            allow: 'autoplay; fullscreen',
            allowfullscreen: true,
            frameborder: 0
          }));
        } else if (['html', 'external_url', 'ext_link'].includes(videoType)) {
          videoElement = $('<video>', {
            "class": 'masterstudy-plyr-video-player',
            controls: true,
            autoplay: true,
            poster: poster || '',
            controlsList: 'nodownload'
          });
          videoElement.append($('<source>', {
            src: videoId,
            type: "video/".concat(extra.type || 'mp4')
          }));
        }
        wrapper.append(videoElement);
        var playerEl = wrapper.find('.plyr__video-embed, video').get(0);
        if (playerEl) {
          var plyrInstance = new Plyr(playerEl, {
            muted: isMuted,
            invertTime: true
          });
          plyrInstance.on('play', function () {
            wrapper.find('.masterstudy-single-course-video__poster, .masterstudy-single-course-video__play-button').remove();
            wrapper.find('.masterstudy-single-course-video__loader').removeClass('masterstudy-single-course-video__loader_show');
          });
        }
      });
    });
  });
})(jQuery);