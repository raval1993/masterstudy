(function () {
  'use strict';

  function element(tag, className, text) {
    var node = document.createElement(tag);
    if (className) node.className = className;
    if (typeof text === 'string') node.textContent = text;
    return node;
  }

  function init(widget) {
    if (widget.dataset.ready) return;
    var lesson = widget.closest('.masterstudy-course-player-lesson');
    var video = lesson && lesson.querySelector('video.masterstudy-plyr-video-player');
    if (!video || !widget.dataset.endpoint) return;
    widget.dataset.ready = 'loading';
    var headers = {};
    if (widget.dataset.nonce) headers['X-WP-Nonce'] = widget.dataset.nonce;

    fetch(widget.dataset.endpoint, { credentials: 'same-origin', headers: headers })
      .then(function (response) {
        if (!response.ok) throw new Error('Transcript unavailable');
        return response.json();
      })
      .then(function (data) {
        if (!Array.isArray(data.cues) || !data.cues.length || typeof data.vtt !== 'string') {
          throw new Error('Transcript unavailable');
        }
        render(widget, video, data);
      })
      .catch(function () {
        widget.dataset.ready = 'unavailable';
        var status = widget.querySelector('.mce-status');
        if (status) status.textContent = 'Transcript and captions are not available for this lesson yet.';
      });
  }

  function render(widget, video, data) {
    var status = widget.querySelector('.mce-status');
    var controls = widget.querySelector('.mce-controls');
    if (!status || !controls) return;

    var track = document.createElement('track');
    track.kind = 'captions';
    track.label = 'English';
    track.srclang = 'en';
    track.src = URL.createObjectURL(new Blob([data.vtt], { type: 'text/vtt;charset=utf-8' }));
    video.appendChild(track);
    video.addEventListener('emptied', function () { URL.revokeObjectURL(track.src); }, { once: true });

    controls.hidden = false;
    controls.appendChild(element('h4', '', 'Captions'));
    var captions = element('button', 'mce-caption-toggle', 'Turn captions on');
    captions.type = 'button';
    captions.setAttribute('aria-pressed', 'false');
    controls.appendChild(captions);
    var textTrack = track.track;
    textTrack.mode = 'hidden';
    captions.addEventListener('click', function () {
      var enabled = 'showing' !== textTrack.mode;
      textTrack.mode = enabled ? 'showing' : 'hidden';
      captions.textContent = enabled ? 'Turn captions off' : 'Turn captions on';
      captions.setAttribute('aria-pressed', enabled ? 'true' : 'false');
    });

    var nav = element('nav', 'mce-scenes');
    nav.setAttribute('aria-label', 'Video scenes');
    nav.appendChild(element('h4', '', 'Jump to a scene'));
    var strictMode = !!(window.video_player_data && window.video_player_data.strict_mode);
    if (strictMode) {
      nav.appendChild(element('p', 'mce-strict-note', 'Scene seeking is disabled by this course’s strict viewing settings.'));
    }
    var list = element('ol', 'mce-scene-list');
    data.cues.forEach(function (cue, index) {
      var item = element('li');
      var button = element('button', 'mce-scene-button');
      button.type = 'button';
      var time = Math.max(0, Number(cue.start) || 0);
      if (strictMode) {
        button.disabled = true;
        button.title = 'Scene seeking is disabled by the course viewing settings.';
      }
      var stamp = element('span', 'mce-scene-time', formatTime(time));
      var title = element('span', 'mce-scene-title', cue.label || ('Scene ' + (index + 1)));
      button.appendChild(stamp);
      button.appendChild(title);
      button.addEventListener('click', function () {
        video.currentTime = time;
        video.focus({ preventScroll: true });
      });
      item.appendChild(button);
      list.appendChild(item);
    });
    nav.appendChild(list);
    controls.appendChild(nav);

    var details = element('details', 'mce-transcript');
    details.appendChild(element('summary', '', 'Read video transcript'));
    var transcript = element('ol', 'mce-transcript-list');
    data.cues.forEach(function (cue, index) {
      var item = element('li');
      item.appendChild(element('h5', '', cue.label || ('Scene ' + (index + 1))));
      var paragraph = element('p');
      paragraph.textContent = cue.text || '';
      item.appendChild(paragraph);
      transcript.appendChild(item);
    });
    details.appendChild(transcript);
    controls.appendChild(details);
    status.textContent = data.cues.length + ' video scenes are ready.';
    widget.dataset.ready = 'true';
  }

  function formatTime(seconds) {
    var whole = Math.floor(seconds);
    var hours = Math.floor(whole / 3600);
    var minutes = Math.floor((whole % 3600) / 60);
    var remainder = whole % 60;
    return hours ? hours + ':' + pad(minutes) + ':' + pad(remainder) : minutes + ':' + pad(remainder);
  }

  function pad(value) { return value < 10 ? '0' + value : String(value); }

  function scan(root) {
    if (root.matches && root.matches('.mce-tools')) init(root);
    if (root.querySelectorAll) root.querySelectorAll('.mce-tools').forEach(init);
  }

  document.addEventListener('DOMContentLoaded', function () {
    scan(document);
    var observer = new MutationObserver(function (records) {
      records.forEach(function (record) {
        record.addedNodes.forEach(function (node) {
          if (node.nodeType === 1) scan(node);
        });
      });
    });
    observer.observe(document.body, { childList: true, subtree: true });
  });
}());
